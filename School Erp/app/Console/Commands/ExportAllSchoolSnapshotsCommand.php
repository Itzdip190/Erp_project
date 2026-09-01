<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Backup\SchoolSnapshotService;
use App\Models\School;
use Illuminate\Support\Facades\Log;
use Throwable;

class ExportAllSchoolSnapshotsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'snapshots:export-schools 
                            {--school= : Optional single school ID to export}
                            {--status= : Filter schools by status (e.g. active, trial)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export isolated read-only tenant snapshot archives for all schools with date-wise folders and tiered retention (48h intra-day rolling + 15-day daily EOD archive)';

    /**
     * Execute the console command.
     */
    public function handle(SchoolSnapshotService $snapshotService): int
    {
        // 1. Production safety configuration for shared hosting execution
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');
        @ini_set('max_execution_time', 0);

        $this->info("===============================================================");
        $this->info("    SchoolCloud ERP — Multi-School Automated Snapshot Engine   ");
        $this->info("===============================================================");
        $this->line("Execution Time: " . date('Y-m-d H:i:s T'));

        // 2. Fetch schools to process
        $query = School::query();

        if ($this->option('school')) {
            $schoolId = (int)$this->option('school');
            $query->where('id', $schoolId);
            $this->comment("Filtering by School ID: {$schoolId}");
        } elseif ($this->option('status')) {
            $status = (string)$this->option('status');
            $query->where('status', $status);
            $this->comment("Filtering by School Status: {$status}");
        }

        $schools = $query->orderBy('id')->get();
        $totalSchools = $schools->count();

        if ($totalSchools === 0) {
            $this->warn("No schools found matching the requested criteria.");
            return Command::SUCCESS;
        }

        $this->info("Found {$totalSchools} school(s) to process.\n");

        $results = [];
        $successfulCount = 0;
        $failedCount = 0;
        $totalRowsAllSchools = 0;
        $totalFilesAllSchools = 0;
        $totalPrunedFilesAllSchools = 0;

        // 3. Process schools sequentially with per-school fault tolerance
        foreach ($schools as $index => $school) {
            $currentNum = $index + 1;
            $schoolLabel = "School #{$school->id} [{$school->name}] ({$school->code})";
            $this->line("---------------------------------------------------------------");
            $this->info("[{$currentNum}/{$totalSchools}] Processing {$schoolLabel}...");

            try {
                $result = $snapshotService->exportSchoolSnapshot($school->id, function ($message, $pct) {
                    $this->output->write("\r  ↳ [{$pct}%] {$message}");
                });

                $this->newLine();
                $this->info("  ✅ SUCCESS: {$schoolLabel}");

                $stats = $result['manifest']['statistics'] ?? [];
                $rowsExported = (int)($stats['total_rows_exported'] ?? 0);
                $filesExported = (int)($stats['total_files_exported'] ?? 0);
                $bundleName = $result['bundle_name'] ?? 'N/A';
                $snapshotFile = $result['snapshot_file'] ?? 'N/A';
                $prunedFiles = $result['pruned_previous_files'] ?? [];
                $prunedCount = count($prunedFiles);

                $successfulCount++;
                $totalRowsAllSchools += $rowsExported;
                $totalFilesAllSchools += $filesExported;
                $totalPrunedFilesAllSchools += $prunedCount;

                // Detailed console output per school
                $this->line("     • New Archive: {$bundleName}.zip");
                $this->line("     • Destination: {$snapshotFile}");
                $this->line("     • Rows: " . number_format($rowsExported) . " | Files: " . number_format($filesExported));
                if ($prunedCount > 0) {
                    $this->comment("     • Pruned {$prunedCount} older snapshot(s) beyond tiered retention window: " . implode(', ', $prunedFiles));
                } else {
                    $this->line("     • Pruned older: 0 (All snapshots within tiered retention window retained)");
                }

                $results[] = [
                    'id' => $school->id,
                    'school' => $school->name . " ({$school->code})",
                    'status' => '<fg=green>SUCCESS</>',
                    'new_bundle' => $bundleName,
                    'rows' => number_format($rowsExported),
                    'files' => number_format($filesExported),
                    'old_pruned' => $prunedCount > 0 ? "<fg=yellow>{$prunedCount} file(s)</>" : '0',
                    'error' => 'None',
                ];

                Log::info("Automated snapshot succeeded for {$schoolLabel}", [
                    'school_id' => $school->id,
                    'school_code' => $school->code,
                    'school_name' => $school->name,
                    'bundle_name' => $bundleName,
                    'snapshot_file' => $snapshotFile,
                    'total_rows' => $rowsExported,
                    'total_files' => $filesExported,
                    'pruned_previous_count' => $prunedCount,
                    'pruned_previous_files' => $prunedFiles,
                ]);
            } catch (Throwable $e) {
                $this->newLine();
                $this->error("  ❌ FAILED: {$schoolLabel} — " . $e->getMessage());
                $this->warn("     ↳ Previous backup preserved safely (not deleted).");

                $failedCount++;
                $results[] = [
                    'id' => $school->id,
                    'school' => $school->name . " ({$school->code})",
                    'status' => '<fg=red>FAILED</>',
                    'new_bundle' => 'N/A',
                    'rows' => '0',
                    'files' => '0',
                    'old_pruned' => '0 (preserved)',
                    'error' => substr($e->getMessage(), 0, 50),
                ];

                Log::error("Automated snapshot failed for {$schoolLabel}: " . $e->getMessage(), [
                    'school_id' => $school->id,
                    'school_code' => $school->code,
                    'school_name' => $school->name,
                    'exception' => $e,
                ]);
            }
        }

        // 4. Render execution breakdown table
        $this->newLine();
        $this->line("===============================================================");
        $this->info("                     EXECUTION BREAKDOWN                       ");
        $this->line("===============================================================");

        $this->table(
            ['ID', 'School', 'Status', 'New Bundle', 'Rows', 'Files', 'Old Pruned', 'Error'],
            $results
        );

        // 5. Render overall execution summary
        $this->newLine();
        $this->info("===============================================================");
        $this->info("                   FINAL EXECUTION SUMMARY                     ");
        $this->line("===============================================================");

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Schools Processed', $totalSchools],
                ['Successful Snapshots', "<fg=green>{$successfulCount}</>"],
                ['Failed Snapshots', $failedCount > 0 ? "<fg=red>{$failedCount}</>" : "0"],
                ['Total Rows Exported Across All Schools', number_format($totalRowsAllSchools)],
                ['Total Files Bundled Across All Schools', number_format($totalFilesAllSchools)],
                ['Total Older Snapshots Pruned', number_format($totalPrunedFilesAllSchools)],
                ['Retention Rule Enforced', 'Tiered: 48h Intra-Day Rolling + 15-Day Daily EOD Archive'],
            ]
        );

        return $failedCount === 0 ? Command::SUCCESS : Command::FAILURE;
    }
}
