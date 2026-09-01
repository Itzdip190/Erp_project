<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Backup\BackupService;
use Illuminate\Support\Facades\Log;
use Throwable;

class FullBackupCommand extends Command
{
    protected $signature = 'erp:backup-full';
    protected $description = 'Generate a complete full ERP disaster recovery backup (MySQL Database + Physical Storage Assets), atomically retaining only the latest verified backup';

    public function handle(BackupService $backupService): int
    {
        $this->info("===============================================================");
        $this->info("    SchoolCloud ERP — Full Disaster Recovery Backup Engine    ");
        $this->info("===============================================================");

        try {
            $result = $backupService->createFullBackup(function ($message, $pct) {
                $this->output->write("\r  [{$pct}%] {$message}");
            });

            $this->newLine(2);
            $this->info("SUCCESS: Full ERP backup completed and verified successfully!");
            $this->line("Backup Location: " . $result['backup_folder']);
            $this->table(
                ['Metric', 'Value'],
                [
                    ['Backup Name', $result['manifest']['backup_name']],
                    ['Timestamp', $result['manifest']['timestamp']],
                    ['Database Name', $result['manifest']['database_name']],
                    ['Total Tables Dumped', $result['manifest']['table_count']],
                    ['Total Rows Dumped', number_format($result['manifest']['total_rows'])],
                    ['Physical Files Archived', number_format($result['manifest']['storage_files_count'])],
                    ['Total Storage Bytes', number_format($result['manifest']['storage_total_bytes']) . ' bytes'],
                ]
            );

            // Display retention replacement summary
            $pruned = $result['pruned_previous_backups'] ?? [];
            if (!empty($pruned)) {
                $this->comment("Retention: Replaced and purged " . count($pruned) . " previous full backup folder(s): " . implode(', ', $pruned));
            } else {
                $this->line("Retention: No previous backup folders found to replace (first backup).");
            }

            return Command::SUCCESS;
        } catch (Throwable $e) {
            $this->newLine();
            $this->error("ERROR: Full backup failed: " . $e->getMessage());
            $this->warn("Previous successful backup remains preserved and untouched.");

            Log::error("Artisan command erp:backup-full failed: " . $e->getMessage(), [
                'exception' => $e,
            ]);

            return Command::FAILURE;
        }
    }
}
