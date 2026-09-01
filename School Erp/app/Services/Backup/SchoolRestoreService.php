<?php

namespace App\Services\Backup;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;
use ZipArchive;
use PDO;

class SchoolRestoreService
{
    protected BackupService $backupService;

    public function __construct(?BackupService $backupService = null)
    {
        $this->backupService = $backupService ?? new BackupService();
    }

    /**
     * Perform a dry-run validation of a school snapshot without modifying the database.
     *
     * @param string $snapshotZipPath
     * @return array
     * @throws Exception
     */
    public function dryRunValidate(string $snapshotZipPath): array
    {
        $extracted = $this->extractAndVerifySnapshot($snapshotZipPath);
        $manifest = $extracted['manifest'];
        $tempDir = $extracted['temp_dir'];
        $targetSchoolId = (int)$manifest['school']['id'];

        $driver = DB::connection()->getDriverName();
        $pdo = DB::connection()->getPdo();

        $tableReports = [];
        $totalExistingRowsToDelete = 0;
        $totalSnapshotRowsToInsert = 0;
        $totalIdCollisions = 0;
        $collisionDetails = [];

        $tables = $manifest['tables'] ?? [];
        foreach ($tables as $table => $snapshotRowCount) {
            $jsonFile = "{$tempDir}/data/{$table}.json";
            if (!File::exists($jsonFile)) {
                continue;
            }

            $rows = json_decode(File::get($jsonFile), true) ?? [];
            $totalSnapshotRowsToInsert += count($rows);

            // 1. Check existing rows in destination database for this school
            $existingCount = 0;
            try {
                if ($table === 'designation_staff') {
                    $existingCount = DB::table('designation_staff')->whereIn('staff_id', function ($q) use ($targetSchoolId) {
                        $q->select('id')->from('staff')->where('school_id', $targetSchoolId);
                    })->count();
                } else if ($table === 'inventory_sale_items') {
                    $existingCount = DB::table('inventory_sale_items')->whereIn('sale_id', function ($q) use ($targetSchoolId) {
                        $q->select('id')->from('inventory_sales')->where('school_id', $targetSchoolId);
                    })->count();
                } else if ($table === 'login_logs') {
                    $existingCount = DB::table('login_logs')->whereIn('user_id', function ($q) use ($targetSchoolId) {
                        $q->select('id')->from('users')->where('school_id', $targetSchoolId);
                    })->count();
                } else if ($table === 'model_has_roles' || $table === 'model_has_permissions') {
                    $existingCount = DB::table($table)->where('model_type', 'App\\Models\\User')->whereIn('model_id', function ($q) use ($targetSchoolId) {
                        $q->select('id')->from('users')->where('school_id', $targetSchoolId);
                    })->count();
                } else if ($table === 'survey_questions' || $table === 'survey_options' || $table === 'survey_responses') {
                    $existingCount = DB::table($table)->whereIn('survey_id', function ($q) use ($targetSchoolId) {
                        $q->select('id')->from('surveys')->where('school_id', $targetSchoolId);
                    })->count();
                } else if ($table === 'teacher_assignment_submissions') {
                    $existingCount = DB::table('teacher_assignment_submissions')->whereIn('assignment_id', function ($q) use ($targetSchoolId) {
                        $q->select('id')->from('teacher_assignments')->where('school_id', $targetSchoolId);
                    })->count();
                } else {
                    // Direct table
                    $existingCount = DB::table($table)->where('school_id', $targetSchoolId)->count();
                }
            } catch (Exception $e) {
                // Table might not exist in target database
                $existingCount = 0;
            }

            $totalExistingRowsToDelete += $existingCount;

            // 2. Check for primary key ID collisions with other schools
            $tableCollisions = 0;
            if (!empty($rows) && isset($rows[0]['id']) && $table !== 'users') {
                $ids = array_column($rows, 'id');
                // Check if any of these IDs belong to ANOTHER school in destination database
                try {
                    $hasSchoolIdCol = false;
                    if ($driver === 'sqlite') {
                        $cols = $pdo->query("PRAGMA table_info(`{$table}`)")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($cols as $c) {
                            if ($c['name'] === 'school_id') { $hasSchoolIdCol = true; break; }
                        }
                    } else {
                        $cols = $pdo->query("SHOW COLUMNS FROM `{$table}` LIKE 'school_id'")->fetchAll(PDO::FETCH_ASSOC);
                        $hasSchoolIdCol = !empty($cols);
                    }

                    if ($hasSchoolIdCol) {
                        $collidingCount = DB::table($table)
                            ->whereIn('id', array_slice($ids, 0, 1000))
                            ->where('school_id', '!=', $targetSchoolId)
                            ->count();
                        if ($collidingCount > 0) {
                            $tableCollisions = $collidingCount;
                            $totalIdCollisions += $collidingCount;
                            $collisionDetails[] = "Table [{$table}] has {$collidingCount} ID collisions with other schools.";
                        }
                    }
                } catch (Exception $e) {}
            }

            $tableReports[$table] = [
                'snapshot_rows' => count($rows),
                'existing_rows_to_replace' => $existingCount,
                'id_collisions' => $tableCollisions
            ];
        }

        // Clean up temp extracted folder
        File::deleteDirectory($tempDir);

        $isSafe = ($totalIdCollisions === 0);

        return [
            'is_safe' => $isSafe,
            'school' => $manifest['school'],
            'export_timestamp' => $manifest['export_timestamp'],
            'total_tables' => count($tableReports),
            'total_snapshot_rows' => $totalSnapshotRowsToInsert,
            'total_existing_rows_to_delete' => $totalExistingRowsToDelete,
            'total_id_collisions' => $totalIdCollisions,
            'collision_details' => $collisionDetails,
            'total_files_in_snapshot' => $manifest['statistics']['total_files_exported'] ?? 0,
            'tables' => $tableReports,
            'verdict' => $isSafe ? 'READY_FOR_RESTORE' : 'BLOCKED_BY_COLLISIONS'
        ];
    }

    /**
     * Restore snapshot into a dedicated Staging Database.
     */
    public function restoreToStaging(string $snapshotZipPath, string $stagingConnection = 'staging'): array
    {
        // Dry-run validate first
        $dryRun = $this->dryRunValidate($snapshotZipPath);

        $extracted = $this->extractAndVerifySnapshot($snapshotZipPath);
        $manifest = $extracted['manifest'];
        $tempDir = $extracted['temp_dir'];
        $targetSchoolId = (int)$manifest['school']['id'];

        $stagingPdo = DB::connection($stagingConnection)->getPdo();
        $stagingDriver = DB::connection($stagingConnection)->getDriverName();

        if ($stagingDriver !== 'sqlite') {
            $stagingPdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        }

        DB::connection($stagingConnection)->beginTransaction();

        try {
            // Restore tables
            $tables = $manifest['tables'] ?? [];
            foreach ($tables as $table => $count) {
                $jsonFile = "{$tempDir}/data/{$table}.json";
                if (!File::exists($jsonFile)) continue;
                $rows = json_decode(File::get($jsonFile), true) ?? [];
                if (empty($rows)) continue;

                // Chunk insert
                foreach (array_chunk($rows, 200) as $chunk) {
                    DB::connection($stagingConnection)->table($table)->insert($chunk);
                }
            }

            DB::connection($stagingConnection)->commit();

            if ($stagingDriver !== 'sqlite') {
                $stagingPdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            }

            File::deleteDirectory($tempDir);

            return [
                'success' => true,
                'staging_connection' => $stagingConnection,
                'school' => $manifest['school'],
                'tables_restored' => count($tables),
                'total_rows_restored' => $dryRun['total_snapshot_rows']
            ];
        } catch (Exception $e) {
            DB::connection($stagingConnection)->rollBack();
            File::deleteDirectory($tempDir);
            throw $e;
        }
    }

    /**
     * Execute transactional in-place restoration on Production.
     * STRICT REQUIREMENT: Must pass $confirmProduction = true.
     */
    public function restoreToProduction(string $snapshotZipPath, bool $confirmProduction = false, ?callable $progressCallback = null): array
    {
        if (!$confirmProduction) {
            throw new Exception("CRITICAL SAFETY BLOCK: Production restoration requires explicit confirmation (--confirm-production). Operation aborted.");
        }

        if ($progressCallback) $progressCallback("Performing mandatory pre-restore full database safety backup...", 5);

        // 1. Mandatory Pre-Restore Safety Full Backup
        $safetyBackup = $this->backupService->createFullBackup();
        Log::info("Pre-restore full safety backup created: " . $safetyBackup['backup_name']);

        if ($progressCallback) $progressCallback("Validating snapshot archive checksums...", 20);

        // 2. Extract and verify snapshot
        $extracted = $this->extractAndVerifySnapshot($snapshotZipPath);
        $manifest = $extracted['manifest'];
        $tempDir = $extracted['temp_dir'];
        $targetSchoolId = (int)$manifest['school']['id'];

        $driver = DB::connection()->getDriverName();
        $pdo = DB::connection()->getPdo();

        if ($progressCallback) $progressCallback("Beginning atomic database restore transaction...", 30);

        if ($driver !== 'sqlite') {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        }

        DB::beginTransaction();

        try {
            $tables = $manifest['tables'] ?? [];
            $totalTables = count($tables);
            $currentTableIdx = 0;

            // 3. Delete existing records for this school in reverse dependency order
            foreach (array_reverse(array_keys($tables)) as $table) {
                try {
                    if ($table === 'designation_staff') {
                        DB::table('designation_staff')->whereIn('staff_id', function ($q) use ($targetSchoolId) {
                            $q->select('id')->from('staff')->where('school_id', $targetSchoolId);
                        })->delete();
                    } else if ($table === 'inventory_sale_items') {
                        DB::table('inventory_sale_items')->whereIn('sale_id', function ($q) use ($targetSchoolId) {
                            $q->select('id')->from('inventory_sales')->where('school_id', $targetSchoolId);
                        })->delete();
                    } else if ($table === 'login_logs') {
                        DB::table('login_logs')->whereIn('user_id', function ($q) use ($targetSchoolId) {
                            $q->select('id')->from('users')->where('school_id', $targetSchoolId);
                        })->delete();
                    } else if ($table === 'model_has_roles' || $table === 'model_has_permissions') {
                        DB::table($table)->where('model_type', 'App\\Models\\User')->whereIn('model_id', function ($q) use ($targetSchoolId) {
                            $q->select('id')->from('users')->where('school_id', $targetSchoolId);
                        })->delete();
                    } else if ($table === 'survey_questions' || $table === 'survey_options' || $table === 'survey_responses') {
                        DB::table($table)->whereIn('survey_id', function ($q) use ($targetSchoolId) {
                            $q->select('id')->from('surveys')->where('school_id', $targetSchoolId);
                        })->delete();
                    } else if ($table === 'teacher_assignment_submissions') {
                        DB::table('teacher_assignment_submissions')->whereIn('assignment_id', function ($q) use ($targetSchoolId) {
                            $q->select('id')->from('teacher_assignments')->where('school_id', $targetSchoolId);
                        })->delete();
                    } else {
                        DB::table($table)->where('school_id', $targetSchoolId)->delete();
                    }
                } catch (Exception $e) {
                    // Ignore if table does not exist
                }
            }

            // 4. Insert snapshot rows table by table
            foreach ($tables as $table => $count) {
                $currentTableIdx++;
                if ($progressCallback && $totalTables > 0) {
                    $pct = 30 + (int)(($currentTableIdx / $totalTables) * 50);
                    $progressCallback("Restoring records for [{$table}]...", $pct);
                }

                $jsonFile = "{$tempDir}/data/{$table}.json";
                if (!File::exists($jsonFile)) continue;
                $rows = json_decode(File::get($jsonFile), true) ?? [];
                if (empty($rows)) continue;

                foreach (array_chunk($rows, 200) as $chunk) {
                    DB::table($table)->insert($chunk);
                }
            }

            // 5. Restore physical asset files
            if ($progressCallback) $progressCallback("Restoring physical asset files to storage...", 85);

            $snapshotFilesDir = "{$tempDir}/files";
            $targetStorageDir = storage_path('app/public');

            if (File::exists($snapshotFilesDir)) {
                $allFiles = new \RecursiveIteratorIterator(
                    new \RecursiveDirectoryIterator($snapshotFilesDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                    \RecursiveIteratorIterator::LEAVES_ONLY
                );

                foreach ($allFiles as $file) {
                    if (!$file->isDir()) {
                        $srcPath = $file->getRealPath();
                        $relPath = substr($srcPath, strlen($snapshotFilesDir) + 1);
                        $destPath = "{$targetStorageDir}/{$relPath}";
                        $destDir = dirname($destPath);
                        if (!File::exists($destDir)) {
                            File::makeDirectory($destDir, 0755, true);
                        }
                        File::copy($srcPath, $destPath);
                    }
                }
            }

            DB::commit();

            if ($driver !== 'sqlite') {
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            }

            File::deleteDirectory($tempDir);

            if ($progressCallback) $progressCallback("Production restore completed successfully!", 100);

            Log::info("School snapshot restored to production successfully", [
                'school_id' => $targetSchoolId,
                'pre_restore_backup' => $safetyBackup['backup_name']
            ]);

            return [
                'success' => true,
                'school' => $manifest['school'],
                'pre_restore_backup' => $safetyBackup['backup_name'],
                'tables_restored' => count($tables)
            ];
        } catch (Exception $e) {
            DB::rollBack();
            if ($driver !== 'sqlite') {
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            }
            File::deleteDirectory($tempDir);
            Log::error("Production restore failed and was rolled back: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extract and verify cryptographic checksums of a snapshot archive.
     */
    protected function extractAndVerifySnapshot(string $zipPath): array
    {
        if (!File::exists($zipPath)) {
            throw new Exception("Snapshot zip file not found: {$zipPath}");
        }

        $tempDir = storage_path('app/temp/restore_' . uniqid());
        File::makeDirectory($tempDir, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            File::deleteDirectory($tempDir);
            throw new Exception("Failed to open snapshot archive: {$zipPath}");
        }

        $zip->extractTo($tempDir);
        $zip->close();

        $manifestPath = "{$tempDir}/manifest.json";
        if (!File::exists($manifestPath)) {
            File::deleteDirectory($tempDir);
            throw new Exception("Invalid snapshot archive: manifest.json is missing.");
        }

        $manifest = json_decode(File::get($manifestPath), true);
        if (!$manifest || !isset($manifest['school']['id'])) {
            File::deleteDirectory($tempDir);
            throw new Exception("Invalid snapshot archive: manifest.json is corrupted or incomplete.");
        }

        // Verify checksums
        $checksums = $manifest['checksums'] ?? [];
        foreach ($checksums as $relFile => $expectedHash) {
            $fullPath = "{$tempDir}/{$relFile}";
            if (!File::exists($fullPath)) {
                File::deleteDirectory($tempDir);
                throw new Exception("Snapshot integrity check failed: missing file [{$relFile}]");
            }
            $actualHash = hash_file('sha256', $fullPath);
            if ($actualHash !== $expectedHash) {
                File::deleteDirectory($tempDir);
                throw new Exception("Snapshot integrity check failed: checksum mismatch on [{$relFile}]");
            }
        }

        return [
            'manifest' => $manifest,
            'temp_dir' => $tempDir
        ];
    }
}
