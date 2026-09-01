<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Backup\SchoolRestoreService;
use Exception;

class SchoolSnapshotRestoreCommand extends Command
{
    protected $signature = 'school:snapshot-restore 
                            {path : Path to the school snapshot zip package}
                            {--dry-run : Run simulation and collision analysis without modifying database}
                            {--staging : Restore into a dedicated staging database}
                            {--staging-db=staging : Staging database connection name}
                            {--confirm-production : Explicit mandatory flag required for production restore}';

    protected $description = 'Safely restore a single-school snapshot with dry-run collision detection and staging validation';

    public function handle(SchoolRestoreService $restoreService): int
    {
        $path = $this->argument('path');
        $isDryRun = $this->option('dry-run');
        $isStaging = $this->option('staging');
        $confirmProduction = $this->option('confirm-production');

        $this->info("===============================================================");
        $this->info("    SchoolCloud ERP — Single-School Snapshot Recovery Engine   ");
        $this->info("===============================================================");
        $this->line("Target Snapshot Archive: {$path}");

        try {
            // Mode 1: Dry-Run Simulation
            if ($isDryRun || (!$isStaging && !$confirmProduction)) {
                $this->info("\n--- EXECUTING READ-ONLY DRY-RUN SIMULATION ---");
                $report = $restoreService->dryRunValidate($path);

                $this->table(
                    ['Simulation Metric', 'Value'],
                    [
                        ['Target School ID', $report['school']['id']],
                        ['Target School Name', $report['school']['name']],
                        ['Target School Code', $report['school']['code']],
                        ['Snapshot Timestamp', $report['export_timestamp']],
                        ['Total Tables in Snapshot', $report['total_tables']],
                        ['Snapshot Rows to Insert', number_format($report['total_snapshot_rows'])],
                        ['Existing Rows to Replace', number_format($report['total_existing_rows_to_delete'])],
                        ['Cross-Tenant ID Collisions', $report['total_id_collisions']],
                        ['Physical Asset Files to Copy', number_format($report['total_files_in_snapshot'])],
                        ['Safety Verdict', $report['verdict']],
                    ]
                );

                if (!empty($report['collision_details'])) {
                    $this->error("\nWARNING: Collisions detected:");
                    foreach ($report['collision_details'] as $c) {
                        $this->line(" - {$c}");
                    }
                }

                if ($report['is_safe']) {
                    $this->info("\n[RESULT] Snapshot is 100% CLEAN and SAFE for recovery.");
                    $this->line("To test on staging: php artisan school:snapshot-restore \"{$path}\" --staging");
                    $this->line("To execute on production: php artisan school:snapshot-restore \"{$path}\" --confirm-production");
                } else {
                    $this->error("\n[RESULT] Snapshot recovery is BLOCKED due to ID collisions.");
                }

                return Command::SUCCESS;
            }

            // Mode 2: Staging Database Restore
            if ($isStaging) {
                $stagingDb = $this->option('staging-db');
                $this->info("\n--- EXECUTING STAGING RESTORATION (Connection: {$stagingDb}) ---");
                $result = $restoreService->restoreToStaging($path, $stagingDb);

                $this->info("SUCCESS: Snapshot restored and validated on staging database!");
                $this->table(
                    ['Property', 'Details'],
                    [
                        ['Staging Target', $result['staging_connection']],
                        ['School Name', $result['school']['name']],
                        ['Tables Restored', $result['tables_restored']],
                        ['Total Rows Restored', number_format($result['total_rows_restored'])],
                    ]
                );
                return Command::SUCCESS;
            }

            // Mode 3: Production Restore (With Mandatory Safety Confirmation)
            if ($confirmProduction) {
                $this->warn("\n***************************************************************");
                $this->warn("               CRITICAL PRODUCTION RESTORATION                 ");
                $this->warn("***************************************************************");

                // Run pre-validation
                $dryRun = $restoreService->dryRunValidate($path);
                if (!$dryRun['is_safe']) {
                    $this->error("ABORTED: Snapshot has ID collisions and cannot be restored into production.");
                    return Command::FAILURE;
                }

                $this->line("Target School: [{$dryRun['school']['name']}] (ID: {$dryRun['school']['id']})");
                $this->line("Rows to Replace: " . number_format($dryRun['total_existing_rows_to_delete']));
                $this->line("Rows to Insert: " . number_format($dryRun['total_snapshot_rows']));

                if (!$this->confirm("Are you ABSOLUTELY SURE you want to restore School [{$dryRun['school']['name']}] into production?")) {
                    $this->comment("Operation cancelled by user.");
                    return Command::SUCCESS;
                }

                $result = $restoreService->restoreToProduction($path, true, function ($message, $pct) {
                    $this->output->write("\r  [{$pct}%] {$message}");
                });

                $this->newLine(2);
                $this->info("SUCCESS: School [{$result['school']['name']}] has been restored successfully!");
                $this->line("A full safety backup was taken prior to restoration: " . $result['pre_restore_backup']);
                return Command::SUCCESS;
            }

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->newLine();
            $this->error("ERROR: Restoration failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
