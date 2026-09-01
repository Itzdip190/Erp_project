<?php

namespace App\Services\Backup;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Exception;
use ZipArchive;
use PDO;

class SchoolSnapshotService
{
    protected string $snapshotsDir;

    // 9 Indirect tables with their explicit parent query logic
    protected array $indirectTableQueries = [
        'designation_staff' => "SELECT * FROM designation_staff WHERE staff_id IN (SELECT id FROM staff WHERE school_id = :school_id)",
        'inventory_sale_items' => "SELECT * FROM inventory_sale_items WHERE sale_id IN (SELECT id FROM inventory_sales WHERE school_id = :school_id)",
        'login_logs' => "SELECT * FROM login_logs WHERE user_id IN (SELECT id FROM users WHERE school_id = :school_id)",
        'model_has_roles' => "SELECT * FROM model_has_roles WHERE model_type = 'App\\\\Models\\\\User' AND model_id IN (SELECT id FROM users WHERE school_id = :school_id)",
        'model_has_permissions' => "SELECT * FROM model_has_permissions WHERE model_type = 'App\\\\Models\\\\User' AND model_id IN (SELECT id FROM users WHERE school_id = :school_id)",
        'survey_questions' => "SELECT * FROM survey_questions WHERE survey_id IN (SELECT id FROM surveys WHERE school_id = :school_id)",
        'survey_options' => "SELECT * FROM survey_options WHERE survey_id IN (SELECT id FROM surveys WHERE school_id = :school_id)",
        'survey_responses' => "SELECT * FROM survey_responses WHERE survey_id IN (SELECT id FROM surveys WHERE school_id = :school_id)",
        'teacher_assignment_submissions' => "SELECT * FROM teacher_assignment_submissions WHERE assignment_id IN (SELECT id FROM teacher_assignments WHERE school_id = :school_id)",
    ];

    // Known asset file column candidates
    protected array $assetColumnCandidates = [
        'photo', 'profile_photo', 'avatar', 'logo', 'favicon', 'signature', 'stamp',
        'file_path', 'document', 'document_path', 'attachment', 'attachment_path',
        'banner_image', 'image', 'receipt_path', 'slip_path'
    ];

    public function __construct()
    {
        $this->snapshotsDir = storage_path('app/snapshots');
        if (!File::exists($this->snapshotsDir)) {
            File::makeDirectory($this->snapshotsDir, 0755, true);
        }
    }

    /**
     * Resolve physical storage file path for candidate asset references.
     * Searches storage/app/public, public/uploads, public/storage, and public root.
     *
     * @param string $val
     * @return string|null Absolute path on disk or null if not found
     */
    public function resolveAssetFilePath(string $val): ?string
    {
        $cleanPath = ltrim(str_replace(['storage/', 'public/'], '', $val), '/\\');

        $candidatePaths = [
            storage_path("app/public/{$cleanPath}"),
            public_path("uploads/{$cleanPath}"),
            public_path("uploads/" . basename($cleanPath)),
            public_path("storage/{$cleanPath}"),
            public_path($cleanPath),
            storage_path("app/{$cleanPath}"),
        ];

        foreach ($candidatePaths as $path) {
            if (File::exists($path) && !is_dir($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Export a complete, self-describing, read-only snapshot for a single school.
     * Automatically purges previous snapshot ZIP file(s) for this school once the new snapshot succeeds.
     *
     * @param int $schoolId
     * @param callable|null $progressCallback
     * @return array
     * @throws Exception
     */
    public function exportSchoolSnapshot(int $schoolId, ?callable $progressCallback = null): array
    {
        // 1. Verify school exists
        $school = DB::table('schools')->where('id', $schoolId)->first();
        if (!$school) {
            throw new Exception("School with ID [{$schoolId}] not found in database.");
        }

        $schoolCode = preg_replace('/[^a-zA-Z0-9_-]/', '', $school->code ?? "SCH{$schoolId}");
        $timestamp = date('Y-m-d_His');
        $bundleName = "school_{$schoolId}_{$schoolCode}_{$timestamp}";
        $tempDir = storage_path("app/temp/{$bundleName}");
        $dataDir = "{$tempDir}/data";
        $filesDir = "{$tempDir}/files";

        File::makeDirectory($dataDir, 0755, true);
        File::makeDirectory($filesDir, 0755, true);

        $pdo = DB::connection()->getPdo();
        $driver = DB::connection()->getDriverName();
        $zipArchivePath = null;

        try {
            // 2. Discover all tables in database
            $allTables = [];
            if ($driver === 'sqlite') {
                $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name NOT LIKE 'sqlite_%' ORDER BY name");
                $allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            } else {
                $stmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
                $allTables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            }

            // Identify direct tables with school_id
            $directTables = [];
            foreach ($allTables as $table) {
                if ($table === 'schools') continue; // schools is global tenant root anchor

                $hasSchoolId = false;
                if ($driver === 'sqlite') {
                    $cols = $pdo->query("PRAGMA table_info(`{$table}`)")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($cols as $c) {
                        if ($c['name'] === 'school_id') {
                            $hasSchoolId = true;
                            break;
                        }
                    }
                } else {
                    $cols = $pdo->query("SHOW COLUMNS FROM `{$table}` LIKE 'school_id'")->fetchAll(PDO::FETCH_ASSOC);
                    $hasSchoolId = !empty($cols);
                }

                if ($hasSchoolId) {
                    $directTables[] = $table;
                }
            }
            sort($directTables);

            $tablesToExport = array_merge($directTables, array_keys($this->indirectTableQueries));
            $tablesToExport = array_unique($tablesToExport);
            sort($tablesToExport);

            $totalTables = count($tablesToExport);
            $exportedTableStats = [];
            $totalRowsExported = 0;
            $exportedAssetPaths = [];
            $checksums = [];
            $currentTableIndex = 0;

            if ($progressCallback) $progressCallback("Starting read-only tenant extraction for School [{$school->name}]...", 5);

            // 3. Extract data table by table
            foreach ($tablesToExport as $table) {
                $currentTableIndex++;
                if ($progressCallback && $totalTables > 0) {
                    $pct = 5 + (int)(($currentTableIndex / $totalTables) * 65);
                    $progressCallback("Exporting table [{$table}] ({$currentTableIndex}/{$totalTables})...", $pct);
                }

                $rows = [];
                if (isset($this->indirectTableQueries[$table])) {
                    // Indirect table query
                    $sql = $this->indirectTableQueries[$table];
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(['school_id' => $schoolId]);
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                } else if (in_array($table, $directTables)) {
                    // Direct table query
                    $stmt = $pdo->prepare("SELECT * FROM `{$table}` WHERE school_id = :school_id");
                    $stmt->execute(['school_id' => $schoolId]);
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

                $rowCount = count($rows);
                $exportedTableStats[$table] = $rowCount;
                $totalRowsExported += $rowCount;

                // Save table data to JSON
                $tableJsonPath = "{$dataDir}/{$table}.json";
                $jsonContent = json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
                File::put($tableJsonPath, $jsonContent);
                $checksums["data/{$table}.json"] = hash('sha256', $jsonContent);

                // Scan for file asset paths in rows
                foreach ($rows as $row) {
                    foreach ($row as $colName => $val) {
                        if (is_string($val) && !empty($val) && strlen($val) > 3) {
                            $isAssetCol = false;
                            foreach ($this->assetColumnCandidates as $candidate) {
                                if (strpos(strtolower($colName), $candidate) !== false) {
                                    $isAssetCol = true;
                                    break;
                                }
                            }
                            if ($isAssetCol || preg_match('/\.(jpg|jpeg|png|gif|svg|webp|pdf|doc|docx|xls|xlsx|csv|zip)$/i', $val)) {
                                $resolvedDiskPath = $this->resolveAssetFilePath($val);
                                if ($resolvedDiskPath) {
                                    $cleanPath = ltrim(str_replace(['storage/', 'public/'], '', $val), '/\\');
                                    $exportedAssetPaths[$cleanPath] = $resolvedDiskPath;
                                }
                            }
                        }
                    }
                }

                unset($rows, $jsonContent);
            }

            // Also check direct school profile assets (logo, favicon, stamp, signature)
            foreach (['logo', 'favicon', 'signature', 'stamp'] as $attr) {
                if (!empty($school->$attr)) {
                    $resolvedDiskPath = $this->resolveAssetFilePath($school->$attr);
                    if ($resolvedDiskPath) {
                        $cleanPath = ltrim(str_replace(['storage/', 'public/'], '', $school->$attr), '/\\');
                        $exportedAssetPaths[$cleanPath] = $resolvedDiskPath;
                    }
                }
            }

            // 4. Copy physical assets into snapshot package
            if ($progressCallback) $progressCallback("Bundling " . count($exportedAssetPaths) . " physical asset files...", 75);

            $totalAssetBytes = 0;
            foreach ($exportedAssetPaths as $relPath => $srcPath) {
                $destPath = "{$filesDir}/{$relPath}";
                $destDir = dirname($destPath);
                if (!File::exists($destDir)) {
                    File::makeDirectory($destDir, 0755, true);
                }
                File::copy($srcPath, $destPath);
                $totalAssetBytes += filesize($srcPath);
                $checksums["files/{$relPath}"] = hash_file('sha256', $destPath);
            }

            // 5. Generate manifest.json
            $manifest = [
                'manifest_version' => '1.0',
                'export_type' => 'single_school_snapshot',
                'export_timestamp' => date('c'),
                'school' => [
                    'id' => $school->id,
                    'name' => $school->name,
                    'code' => $school->code,
                    'custom_domain' => $school->custom_domain ?? null,
                    'email' => $school->email ?? null,
                ],
                'source_environment' => [
                    'app_name' => config('app.name', 'Educorerp'),
                    'app_env' => config('app.env', 'production'),
                    'database_driver' => $driver,
                    'database_name' => DB::connection()->getDatabaseName(),
                ],
                'statistics' => [
                    'direct_tables_count' => count($directTables),
                    'indirect_tables_count' => count($this->indirectTableQueries),
                    'total_tables_exported' => count($exportedTableStats),
                    'total_rows_exported' => $totalRowsExported,
                    'total_files_exported' => count($exportedAssetPaths),
                    'total_file_bytes' => $totalAssetBytes,
                ],
                'tables' => $exportedTableStats,
                'checksums' => $checksums,
            ];

            File::put("{$tempDir}/manifest.json", json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            // Generate checksums.sha256
            $checksumFileContent = "";
            foreach ($checksums as $filename => $hash) {
                $checksumFileContent .= "{$hash}  {$filename}\n";
            }
            File::put("{$tempDir}/checksums.sha256", $checksumFileContent);

            // 6. Compress entire snapshot into .zip archive
            if ($progressCallback) $progressCallback("Creating final compressed snapshot zip archive...", 90);

            $dateFolder = date('Y-m-d');
            $schoolFolder = "school_{$schoolId}_{$schoolCode}";
            $dateDir = "{$this->snapshotsDir}/{$schoolFolder}/{$dateFolder}";
            if (!File::exists($dateDir)) {
                File::makeDirectory($dateDir, 0755, true);
            }

            $zipArchivePath = "{$dateDir}/{$bundleName}.zip";
            $zip = new ZipArchive();
            if ($zip->open($zipArchivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                throw new Exception("Cannot create zip archive: {$zipArchivePath}");
            }

            $allTempFiles = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($tempDir, \RecursiveDirectoryIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::LEAVES_ONLY
            );

            foreach ($allTempFiles as $file) {
                if (!$file->isDir()) {
                    $filePath = $file->getRealPath();
                    $relativePath = substr($filePath, strlen($tempDir) + 1);
                    $relativePath = str_replace('\\', '/', $relativePath);
                    $zip->addFile($filePath, $relativePath);
                }
            }

            $zip->close();

            // 7. Tiered Retention Policy:
            // - Intra-day intermediate backups (pehle ke 5 backups): 48-hour rolling window
            // - Daily EOD / Last backup of the day: Retained for 15 days
            // - Automatically cleans up empty date folders
            $prunedPreviousFiles = $this->prunePreviousSchoolSnapshots($schoolId, $zipArchivePath);

            if ($progressCallback) $progressCallback("Snapshot successfully created!", 100);

            Log::info("School snapshot exported successfully", [
                'school_id' => $schoolId,
                'school_code' => $schoolCode,
                'zip_path' => $zipArchivePath,
                'date_folder' => $dateFolder,
                'total_rows' => $totalRowsExported,
                'total_files' => count($exportedAssetPaths),
                'pruned_previous_count' => count($prunedPreviousFiles),
            ]);

            return [
                'success' => true,
                'snapshot_file' => $zipArchivePath,
                'bundle_name' => $bundleName,
                'manifest' => $manifest,
                'pruned_previous_files' => $prunedPreviousFiles,
            ];
        } catch (\Throwable $e) {
            // Clean up partial zip archive if creation failed midway
            if ($zipArchivePath && File::exists($zipArchivePath)) {
                @File::delete($zipArchivePath);
            }
            throw $e;
        } finally {
            // Always clean up temp directory to protect shared hosting disk space
            if (File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
        }
    }

    /**
     * Tiered Retention Policy for single-school snapshots:
     * 1. Intra-Day Intermediate Backups: Retained for 48 hours (2 days). Older intermediate backups are flushed.
     * 2. Daily End-Of-Day (EOD) Milestones: The last snapshot of each date is retained for 15 days.
     * 3. Clean empty date directories once all their snapshots have been pruned.
     *
     * @param int $schoolId
     * @param string $currentZipPath Absolute path to the newly created snapshot file
     * @param int $intermediateRetentionHours Retention for non-EOD intra-day backups (default: 48)
     * @param int $dailyEodRetentionDays Retention for EOD daily backups (default: 15)
     * @return array List of filenames of deleted previous snapshot archives
     */
    public function prunePreviousSchoolSnapshots(
        int $schoolId,
        string $currentZipPath,
        int $intermediateRetentionHours = 48,
        int $dailyEodRetentionDays = 15
    ): array {
        $deleted = [];

        // Safety check: ensure the new snapshot actually exists on disk before pruning older archives
        if (!File::exists($currentZipPath) || filesize($currentZipPath) === 0) {
            Log::warning("Prune aborted: New snapshot file does not exist or is empty: {$currentZipPath}");
            return $deleted;
        }

        $currentRealPath = realpath($currentZipPath) ?: $currentZipPath;
        $now = time();
        $intermediateCutoff = $now - ($intermediateRetentionHours * 3600);
        $dailyEodCutoff = $now - ($dailyEodRetentionDays * 86400);

        // Gather all existing snapshot files for this school (date subfolders and legacy flat folders)
        $existingFiles = [];
        $scannedDirs = [];

        if (File::exists($this->snapshotsDir)) {
            // 1. Scan school-dedicated subdirectories (e.g. storage/app/snapshots/school_1_*/**)
            $subdirs = File::directories($this->snapshotsDir);
            foreach ($subdirs as $dir) {
                $dirName = basename($dir);
                if (preg_match("/^school_{$schoolId}(_|$)/i", $dirName)) {
                    $scannedDirs[] = $dir;
                    // Gather all files recursively (both in date subfolders and flat school folder)
                    foreach (File::allFiles($dir) as $f) {
                        if (preg_match("/^school_{$schoolId}_.*\.zip$/i", $f->getFilename())) {
                            $existingFiles[] = $f;
                        }
                    }
                }
            }

            // 2. Scan root snapshots directory for legacy root files
            foreach (File::files($this->snapshotsDir) as $f) {
                if (preg_match("/^school_{$schoolId}_.*\.zip$/i", $f->getFilename())) {
                    $existingFiles[] = $f;
                }
            }
        }

        // De-duplicate by real path
        $uniqueFiles = [];
        foreach ($existingFiles as $f) {
            $rPath = $f->getRealPath() ?: $f->getPathname();
            $uniqueFiles[$rPath] = $f;
        }
        $existingFiles = array_values($uniqueFiles);

        // Group files by date (Y-m-d)
        $groupedByDate = [];
        foreach ($existingFiles as $file) {
            $filename = $file->getFilename();
            $fileTimestamp = null;
            $dateKey = null;

            // Extract date and timestamp from filename: school_{id}_{code}_YYYY-MM-DD_His.zip
            if (preg_match('/_(\d{4}-\d{2}-\d{2})_(\d{2})(\d{2})(\d{2})\.zip$/i', $filename, $m)) {
                $dateKey = $m[1];
                $fileTimestamp = strtotime("{$m[1]} {$m[2]}:{$m[3]}:{$m[4]}");
            }

            if (!$fileTimestamp) {
                $fileTimestamp = $file->getMTime();
            }
            if (!$dateKey) {
                $dateKey = date('Y-m-d', $fileTimestamp);
            }

            $groupedByDate[$dateKey][] = [
                'file' => $file,
                'path' => $file->getPathname(),
                'real_path' => $file->getRealPath() ?: $file->getPathname(),
                'filename' => $filename,
                'timestamp' => $fileTimestamp,
                'date_key' => $dateKey,
            ];
        }

        // For each date group, identify the EOD (newest on that day) vs Intermediate snapshots
        foreach ($groupedByDate as $dateKey => &$filesInDate) {
            // Sort newest first within the day
            usort($filesInDate, function ($a, $b) {
                return $b['timestamp'] <=> $a['timestamp'];
            });

            // Mark the first entry (newest of the day) as Daily EOD, and others as Intermediate
            foreach ($filesInDate as $index => $item) {
                $isDailyEod = ($index === 0);
                $filePath = $item['path'];
                $fileRealPath = $item['real_path'];
                $filename = $item['filename'];
                $ts = $item['timestamp'];

                // Never delete the newly generated snapshot
                if ($fileRealPath === $currentRealPath || $filePath === $currentZipPath) {
                    continue;
                }

                $shouldDelete = false;
                $reason = '';

                if ($isDailyEod) {
                    // Daily EOD milestone backup: delete if older than 15 days
                    if ($ts < $dailyEodCutoff) {
                        $shouldDelete = true;
                        $reason = "Daily EOD milestone older than {$dailyEodRetentionDays} days";
                    }
                } else {
                    // Intra-day intermediate backup: delete if older than 48 hours
                    if ($ts < $intermediateCutoff) {
                        $shouldDelete = true;
                        $reason = "Intra-day intermediate snapshot older than {$intermediateRetentionHours} hours";
                    }
                }

                if ($shouldDelete) {
                    if (File::delete($filePath)) {
                        $deleted[] = $filename;
                        Log::info("Pruned snapshot for School [ID: {$schoolId}]: {$filename} ({$reason})");
                    }
                }
            }
        }
        unset($filesInDate);

        // 3. Clean up empty date subdirectories inside school folders
        foreach ($scannedDirs as $schoolDir) {
            $subDirs = File::directories($schoolDir);
            foreach ($subDirs as $d) {
                // If it's a date folder (e.g. 2026-08-28) and now empty, remove it
                if (preg_match('/^\d{4}-\d{2}-\d{2}$/', basename($d))) {
                    $remainingFiles = File::files($d);
                    if (empty($remainingFiles)) {
                        @File::deleteDirectory($d);
                        Log::info("Removed empty date folder: " . basename($d) . " in {$schoolDir}");
                    }
                }
            }
        }

        return $deleted;
    }

    /**
     * Prune school snapshot archives older than retention days (general fallback).
     *
     * @param int $retentionDays
     * @return int Number of deleted snapshot archives
     */
    public function cleanOldSnapshots(int $retentionDays = 15): int
    {
        if ($retentionDays <= 0) {
            return 0;
        }

        $deleted = 0;
        $cutoff = time() - ($retentionDays * 86400);

        if (File::exists($this->snapshotsDir)) {
            $files = File::allFiles($this->snapshotsDir);
            foreach ($files as $file) {
                if ($file->getExtension() === 'zip' && $file->getMTime() < $cutoff) {
                    File::delete($file->getPathname());
                    $deleted++;
                }
            }
        }

        return $deleted;
    }
}
