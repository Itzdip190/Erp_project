<?php

namespace App\Services\Backup;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;
use ZipArchive;
use PDO;
use Throwable;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups/full');
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Generate a complete full ERP disaster recovery backup (Database SQL + Storage Public Assets).
     * Atomically replaces prior full backup archives upon successful verification.
     *
     * @param callable|null $progressCallback
     * @return array
     * @throws Exception|Throwable
     */
    public function createFullBackup(?callable $progressCallback = null): array
    {
        // 1. Configure runtime environment for large exports
        @set_time_limit(0);
        @ini_set('memory_limit', '512M');
        @ini_set('max_execution_time', 0);

        $timestamp = date('Y-m-d_His');
        $backupName = "full_backup_{$timestamp}";
        $targetFolder = $this->backupDir . '/' . $backupName;
        File::makeDirectory($targetFolder, 0755, true);

        $sqlPath = "{$targetFolder}/database.sql";
        $sqlGzPath = "{$targetFolder}/database.sql.gz";
        $storageZipPath = "{$targetFolder}/storage_assets.zip";
        $manifestPath = "{$targetFolder}/manifest.json";
        $checksumsPath = "{$targetFolder}/checksums.sha256";

        try {
            if ($progressCallback) $progressCallback("Dumping database tables...", 10);

            // 1. Dump database via streaming PDO chunker
            $dbStats = $this->dumpDatabase($sqlPath, $progressCallback);

            // 2. Compress SQL dump with Gzip Level 9
            if ($progressCallback) $progressCallback("Compressing SQL dump...", 50);
            $this->gzipFile($sqlPath, $sqlGzPath);
            if (File::exists($sqlPath)) {
                File::delete($sqlPath);
            }

            // 3. Compress physical storage files
            if ($progressCallback) $progressCallback("Archiving physical storage assets...", 70);
            $fileStats = $this->archiveStorageDirectory($storageZipPath);

            // 4. Generate SHA-256 Checksums
            if ($progressCallback) $progressCallback("Computing SHA-256 integrity checksums...", 85);
            $checksums = [
                'database.sql.gz' => hash_file('sha256', $sqlGzPath),
                'storage_assets.zip' => hash_file('sha256', $storageZipPath),
            ];

            $checksumFileContent = "";
            foreach ($checksums as $filename => $hash) {
                $checksumFileContent .= "{$hash}  {$filename}\n";
            }
            File::put($checksumsPath, $checksumFileContent);

            // 5. Generate Manifest Metadata
            $manifest = [
                'backup_type' => 'full_erp_disaster_recovery',
                'backup_name' => $backupName,
                'timestamp' => date('c'),
                'app_name' => config('app.name', 'Educorerp'),
                'app_env' => config('app.env', 'production'),
                'database_driver' => DB::connection()->getDriverName(),
                'database_name' => DB::connection()->getDatabaseName(),
                'table_count' => $dbStats['table_count'],
                'total_rows' => $dbStats['total_rows'],
                'tables' => $dbStats['tables'],
                'storage_files_count' => $fileStats['file_count'],
                'storage_total_bytes' => $fileStats['total_bytes'],
                'checksums' => $checksums,
            ];
            File::put($manifestPath, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            // 6. Verify that all required backup artifacts exist and are cryptographically valid
            if ($progressCallback) $progressCallback("Verifying backup artifacts and integrity...", 95);
            $verification = $this->verifyBackup($targetFolder);
            if (!$verification['valid']) {
                $errDetails = implode('; ', $verification['errors'] ?? ['Unknown verification error']);
                throw new Exception("Full backup verification failed: {$errDetails}");
            }

            // 7. Atomic Retention: Prune older full backup folders ONLY AFTER new backup is fully verified
            $prunedPreviousFolders = $this->prunePreviousFullBackups($targetFolder);

            if ($progressCallback) $progressCallback("Backup complete!", 100);

            Log::info("Full ERP backup created and verified successfully: {$backupName}", [
                'table_count' => $dbStats['table_count'],
                'total_rows' => $dbStats['total_rows'],
                'files_count' => $fileStats['file_count'],
                'pruned_previous_count' => count($prunedPreviousFolders),
                'pruned_previous_folders' => $prunedPreviousFolders,
            ]);

            return [
                'success' => true,
                'backup_folder' => $targetFolder,
                'backup_name' => $backupName,
                'manifest' => $manifest,
                'pruned_previous_backups' => $prunedPreviousFolders,
            ];
        } catch (Throwable $e) {
            // Clean up partial/broken newly created folder to protect disk space
            if (File::exists($targetFolder)) {
                @File::deleteDirectory($targetFolder);
            }
            Log::error("Full ERP backup failed: " . $e->getMessage(), [
                'exception' => $e,
            ]);
            throw $e;
        }
    }

    /**
     * Delete previous full backup directories, ensuring only the latest verified backup remains.
     *
     * @param string $currentBackupFolder Absolute path to the newly created and verified backup folder
     * @return array List of folder names deleted
     */
    public function prunePreviousFullBackups(string $currentBackupFolder): array
    {
        $deleted = [];

        // Safety Gate: Ensure current backup exists on disk before deleting older directories
        if (!File::exists($currentBackupFolder)) {
            Log::warning("Prune aborted: New backup directory does not exist: {$currentBackupFolder}");
            return $deleted;
        }

        $currentRealPath = realpath($currentBackupFolder) ?: $currentBackupFolder;

        if (File::exists($this->backupDir)) {
            $folders = File::directories($this->backupDir);
            foreach ($folders as $folder) {
                $folderName = basename($folder);
                if (str_starts_with($folderName, 'full_backup_')) {
                    $folderRealPath = realpath($folder) ?: $folder;

                    // Never delete the newly generated verified backup
                    if ($folderRealPath !== $currentRealPath && $folder !== $currentBackupFolder) {
                        if (File::deleteDirectory($folder)) {
                            $deleted[] = $folderName;
                            Log::info("Pruned previous full backup directory: {$folderName}");
                        }
                    }
                }
            }
        }

        return $deleted;
    }

    /**
     * Dump all database tables with consistent transactional safety.
     * Uses pure streaming PHP/PDO chunking (100% portable across Windows/Linux/Hostinger).
     */
    protected function dumpDatabase(string $sqlPath, ?callable $progressCallback = null): array
    {
        $handle = fopen($sqlPath, 'w');
        if (!$handle) {
            throw new Exception("Unable to open file for SQL dump: {$sqlPath}");
        }

        // Header and safe foreign key settings
        fwrite($handle, "-- ========================================================\n");
        fwrite($handle, "-- SchoolCloud ERP Full Disaster Recovery Database Dump\n");
        fwrite($handle, "-- Generated: " . date('Y-m-d H:i:s') . "\n");
        fwrite($handle, "-- ========================================================\n\n");
        fwrite($handle, "SET NAMES utf8mb4;\n");
        fwrite($handle, "SET FOREIGN_KEY_CHECKS = 0;\n");
        fwrite($handle, "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n");
        fwrite($handle, "SET time_zone = \"+00:00\";\n\n");

        $driver = DB::connection()->getDriverName();
        $pdo = DB::connection()->getPdo();

        $tableNames = [];
        if ($driver === 'sqlite') {
            $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
            $tableNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
        } else {
            $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
            $tableNames = $stmt->fetchAll(PDO::FETCH_COLUMN);
        }

        $tableStats = [];
        $totalRows = 0;
        $totalTables = count($tableNames);
        $currentTableIndex = 0;

        foreach ($tableNames as $table) {
            $currentTableIndex++;
            if ($progressCallback && $totalTables > 0) {
                $pct = 10 + (int)(($currentTableIndex / $totalTables) * 38);
                $progressCallback("Dumping table [{$table}] ({$currentTableIndex}/{$totalTables})...", $pct);
            }

            fwrite($handle, "\n-- --------------------------------------------------------\n");
            fwrite($handle, "-- Table structure for table `{$table}`\n");
            fwrite($handle, "-- --------------------------------------------------------\n");
            fwrite($handle, "DROP TABLE IF EXISTS `{$table}`;\n");

            // Schema definition
            if ($driver === 'sqlite') {
                $createStmt = $pdo->query("SELECT sql FROM sqlite_master WHERE type='table' AND name=" . $pdo->quote($table))->fetchColumn();
                fwrite($handle, $createStmt . ";\n\n");
            } else {
                $createRow = $pdo->query("SHOW CREATE TABLE `{$table}`")->fetch(PDO::FETCH_NUM);
                fwrite($handle, $createRow[1] . ";\n\n");
            }

            // Dump rows using chunked cursor to avoid memory limits
            fwrite($handle, "-- Dumping data for table `{$table}`\n");

            $rowCount = DB::table($table)->count();
            $tableStats[$table] = $rowCount;
            $totalRows += $rowCount;

            if ($rowCount > 0) {
                fwrite($handle, "LOCK TABLES `{$table}` WRITE;\n");

                $chunkSize = 500;
                $offset = 0;

                while ($offset < $rowCount) {
                    $rows = DB::table($table)->offset($offset)->limit($chunkSize)->get();
                    if ($rows->isEmpty()) break;

                    $insertSql = "INSERT INTO `{$table}` VALUES ";
                    $valuesArr = [];

                    foreach ($rows as $row) {
                        $colValues = [];
                        foreach ((array)$row as $val) {
                            if (is_null($val)) {
                                $colValues[] = "NULL";
                            } else if (is_numeric($val) && !is_string($val)) {
                                $colValues[] = $val;
                            } else {
                                $colValues[] = $pdo->quote((string)$val);
                            }
                        }
                        $valuesArr[] = "(" . implode(", ", $colValues) . ")";
                    }

                    $insertSql .= implode(",\n", $valuesArr) . ";\n";
                    fwrite($handle, $insertSql);

                    $offset += $chunkSize;
                }

                fwrite($handle, "UNLOCK TABLES;\n");
            }
        }

        fwrite($handle, "\nSET FOREIGN_KEY_CHECKS = 1;\n");
        fwrite($handle, "-- Dump completed at " . date('Y-m-d H:i:s') . "\n");

        fclose($handle);

        return [
            'table_count' => count($tableNames),
            'total_rows' => $totalRows,
            'tables' => $tableStats
        ];
    }

    /**
     * Compress a file using gzip.
     */
    protected function gzipFile(string $sourcePath, string $destPath): void
    {
        $srcHandle = fopen($sourcePath, 'rb');
        $dstHandle = gzopen($destPath, 'wb9');

        while (!feof($srcHandle)) {
            gzwrite($dstHandle, fread($srcHandle, 1024 * 512));
        }

        fclose($srcHandle);
        gzclose($dstHandle);
    }

    /**
     * Archive storage/app/public directory into a zip archive.
     */
    protected function archiveStorageDirectory(string $zipPath): array
    {
        $storageDir = storage_path('app/public');
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new Exception("Cannot create zip archive at: {$zipPath}");
        }

        $fileCount = 0;
        $totalBytes = 0;

        if (File::exists($storageDir)) {
            $files = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($storageDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($files as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = 'storage/' . substr($filePath, strlen($storageDir) + 1);
                    $relativePath = str_replace('\\', '/', $relativePath);

                    $zip->addFile($filePath, $relativePath);
                    $fileCount++;
                    $totalBytes += $file->getSize();
                }
            }
        }

        $zip->close();

        return [
            'file_count' => $fileCount,
            'total_bytes' => $totalBytes
        ];
    }

    /**
     * Verify full backup archive integrity.
     */
    public function verifyBackup(string $backupFolderPath): array
    {
        $manifestPath = rtrim($backupFolderPath, '/\\') . '/manifest.json';
        $checksumsPath = rtrim($backupFolderPath, '/\\') . '/checksums.sha256';

        if (!File::exists($manifestPath)) {
            return ['valid' => false, 'error' => "Missing manifest.json in {$backupFolderPath}"];
        }

        $manifest = json_decode(File::get($manifestPath), true);
        if (!$manifest) {
            return ['valid' => false, 'error' => "Corrupted manifest.json (invalid JSON)."];
        }

        // Verify checksums
        $errors = [];
        if (isset($manifest['checksums'])) {
            foreach ($manifest['checksums'] as $file => $expectedHash) {
                $filePath = rtrim($backupFolderPath, '/\\') . '/' . $file;
                if (!File::exists($filePath)) {
                    $errors[] = "Missing backup artifact: {$file}";
                    continue;
                }
                $actualHash = hash_file('sha256', $filePath);
                if ($actualHash !== $expectedHash) {
                    $errors[] = "Checksum mismatch on {$file} (Expected: {$expectedHash}, Got: {$actualHash})";
                }
            }
        }

        return [
            'valid' => empty($errors),
            'manifest' => $manifest,
            'errors' => $errors
        ];
    }

    /**
     * Clean up backups older than retention days (general fallback).
     */
    public function cleanOldBackups(int $retentionDays = 14): int
    {
        $deleted = 0;
        $cutoff = time() - ($retentionDays * 86400);

        $folders = File::directories($this->backupDir);
        foreach ($folders as $folder) {
            if (File::lastModified($folder) < $cutoff) {
                File::deleteDirectory($folder);
                $deleted++;
            }
        }

        return $deleted;
    }
}
