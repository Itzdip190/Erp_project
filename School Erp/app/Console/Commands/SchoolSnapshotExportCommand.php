<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Backup\SchoolSnapshotService;
use Exception;

class SchoolSnapshotExportCommand extends Command
{
    protected $signature = 'school:snapshot-export {school_id : The ID of the school to export}';
    protected $description = 'Export a complete, self-contained, read-only tenant snapshot (Data + Files + Manifest + Checksums)';

    public function handle(SchoolSnapshotService $snapshotService): int
    {
        $schoolId = (int)$this->argument('school_id');

        $this->info("===============================================================");
        $this->info("    SchoolCloud ERP — Single-School Snapshot Export Engine     ");
        $this->info("===============================================================");
        $this->line("Target School ID: {$schoolId}");

        try {
            $result = $snapshotService->exportSchoolSnapshot($schoolId, function ($message, $pct) {
                $this->output->write("\r  [{$pct}%] {$message}");
            });

            $this->newLine(2);
            $this->info("SUCCESS: School snapshot package generated successfully!");
            $this->line("Archive Path: " . $result['snapshot_file']);

            $stats = $result['manifest']['statistics'];
            $school = $result['manifest']['school'];

            $this->table(
                ['Attribute', 'Value'],
                [
                    ['School Title', $school['name']],
                    ['School Code', $school['code']],
                    ['Export Timestamp', $result['manifest']['export_timestamp']],
                    ['Direct Tables Scoped', $stats['direct_tables_count']],
                    ['Indirect Tables Scoped', $stats['indirect_tables_count']],
                    ['Total Tables Exported', $stats['total_tables_exported']],
                    ['Total Rows Exported', number_format($stats['total_rows_exported'])],
                    ['Physical Files Bundled', number_format($stats['total_files_exported'])],
                    ['File Assets Size', number_format($stats['total_file_bytes']) . ' bytes'],
                ]
            );

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->newLine();
            $this->error("ERROR: Snapshot export failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
