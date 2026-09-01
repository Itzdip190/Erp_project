<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Backup\BackupService;
use Exception;

class BackupVerifyCommand extends Command
{
    protected $signature = 'erp:backup-verify {path : Path to the backup folder}';
    protected $description = 'Verify the cryptographic integrity, checksums, and manifest of a full ERP backup';

    public function handle(BackupService $backupService): int
    {
        $path = $this->argument('path');
        $this->info("===============================================================");
        $this->info("    SchoolCloud ERP — Backup Integrity & Verification Engine   ");
        $this->info("===============================================================");
        $this->line("Verifying backup target: {$path}");

        try {
            $verification = $backupService->verifyBackup($path);

            if ($verification['valid']) {
                $manifest = $verification['manifest'];
                $this->info("STATUS: VALID & CRYPTOGRAPHICALLY VERIFIED [OK]");
                $this->table(
                    ['Property', 'Details'],
                    [
                        ['Backup Name', $manifest['backup_name'] ?? 'N/A'],
                        ['Timestamp', $manifest['timestamp'] ?? 'N/A'],
                        ['Database', $manifest['database_name'] ?? 'N/A'],
                        ['Total Tables', $manifest['table_count'] ?? 'N/A'],
                        ['Total Rows', number_format($manifest['total_rows'] ?? 0)],
                        ['Storage Files', number_format($manifest['storage_files_count'] ?? 0)],
                        ['Checksum SHA-256 (DB)', $manifest['checksums']['database.sql.gz'] ?? 'N/A'],
                        ['Checksum SHA-256 (Files)', $manifest['checksums']['storage_assets.zip'] ?? 'N/A'],
                    ]
                );
                return Command::SUCCESS;
            } else {
                $this->error("STATUS: CORRUPTED OR INVALID BACKUP!");
                foreach ($verification['errors'] ?? [] as $err) {
                    $this->line(" - [ERROR] " . $err);
                }
                return Command::FAILURE;
            }
        } catch (Exception $e) {
            $this->error("Verification failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
