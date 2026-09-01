<?php

namespace App\Services\Backup;

use App\Models\School;
use App\Models\SchoolRestoreLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Exception;
use ZipArchive;
use PDO;
use Throwable;

class SchoolSelectiveRestoreService
{
    protected string $snapshotsDir;
    protected BackupService $backupService;

    // 9 Indirect tables with their explicit parent query logic
    protected array $indirectTableQueries = [
        'designation_staff' => [
            'delete' => "DELETE FROM designation_staff WHERE staff_id IN (SELECT id FROM staff WHERE school_id = :school_id)",
            'select' => "SELECT * FROM designation_staff WHERE staff_id IN (SELECT id FROM staff WHERE school_id = :school_id)",
            'parent_table' => 'staff',
            'parent_key' => 'staff_id',
        ],
        'inventory_sale_items' => [
            'delete' => "DELETE FROM inventory_sale_items WHERE sale_id IN (SELECT id FROM inventory_sales WHERE school_id = :school_id)",
            'select' => "SELECT * FROM inventory_sale_items WHERE sale_id IN (SELECT id FROM inventory_sales WHERE school_id = :school_id)",
            'parent_table' => 'inventory_sales',
            'parent_key' => 'sale_id',
        ],
        'login_logs' => [
            'delete' => "DELETE FROM login_logs WHERE user_id IN (SELECT id FROM users WHERE school_id = :school_id)",
            'select' => "SELECT * FROM login_logs WHERE user_id IN (SELECT id FROM users WHERE school_id = :school_id)",
            'parent_table' => 'users',
            'parent_key' => 'user_id',
        ],
        'model_has_roles' => [
            'delete' => "DELETE FROM model_has_roles WHERE model_type = 'App\\\\Models\\\\User' AND model_id IN (SELECT id FROM users WHERE school_id = :school_id)",
            'select' => "SELECT * FROM model_has_roles WHERE model_type = 'App\\\\Models\\\\User' AND model_id IN (SELECT id FROM users WHERE school_id = :school_id)",
            'parent_table' => 'users',
            'parent_key' => 'model_id',
        ],
        'model_has_permissions' => [
            'delete' => "DELETE FROM model_has_permissions WHERE model_type = 'App\\\\Models\\\\User' AND model_id IN (SELECT id FROM users WHERE school_id = :school_id)",
            'select' => "SELECT * FROM model_has_permissions WHERE model_type = 'App\\\\Models\\\\User' AND model_id IN (SELECT id FROM users WHERE school_id = :school_id)",
            'parent_table' => 'users',
            'parent_key' => 'model_id',
        ],
        'survey_questions' => [
            'delete' => "DELETE FROM survey_questions WHERE survey_id IN (SELECT id FROM surveys WHERE school_id = :school_id)",
            'select' => "SELECT * FROM survey_questions WHERE survey_id IN (SELECT id FROM surveys WHERE school_id = :school_id)",
            'parent_table' => 'surveys',
            'parent_key' => 'survey_id',
        ],
        'survey_options' => [
            'delete' => "DELETE FROM survey_options WHERE survey_id IN (SELECT id FROM surveys WHERE school_id = :school_id)",
            'select' => "SELECT * FROM survey_options WHERE survey_id IN (SELECT id FROM surveys WHERE school_id = :school_id)",
            'parent_table' => 'surveys',
            'parent_key' => 'survey_id',
        ],
        'survey_responses' => [
            'delete' => "DELETE FROM survey_responses WHERE survey_id IN (SELECT id FROM surveys WHERE school_id = :school_id)",
            'select' => "SELECT * FROM survey_responses WHERE survey_id IN (SELECT id FROM surveys WHERE school_id = :school_id)",
            'parent_table' => 'surveys',
            'parent_key' => 'survey_id',
        ],
        'teacher_assignment_submissions' => [
            'delete' => "DELETE FROM teacher_assignment_submissions WHERE assignment_id IN (SELECT id FROM teacher_assignments WHERE school_id = :school_id)",
            'select' => "SELECT * FROM teacher_assignment_submissions WHERE assignment_id IN (SELECT id FROM teacher_assignments WHERE school_id = :school_id)",
            'parent_table' => 'teacher_assignments',
            'parent_key' => 'assignment_id',
        ],
    ];

    public function __construct(?BackupService $backupService = null)
    {
        $this->snapshotsDir = storage_path('app/snapshots');
        if (!File::exists($this->snapshotsDir)) {
            File::makeDirectory($this->snapshotsDir, 0755, true);
        }
        $this->backupService = $backupService ?? new BackupService();
    }

    /**
     * Get the authoritative catalog of all 21 ERP modules mapped to their actual database tables.
     *
     * @return array<string, array>
     */
    public function getModuleDefinitions(): array
    {
        return [
            'student_attendance' => [
                'name' => 'Student Attendance',
                'category' => 'Academics & Students',
                'icon' => 'fas fa-user-check',
                'color' => '#10b981',
                'description' => 'Daily student attendance logs, session marks, attendance statuses (present/absent/late), and remarks.',
                'tables' => [
                    'student_attendances',
                ],
                'asset_folders' => [],
                'dependencies' => ['students', 'academics'],
                'dependency_note' => 'Relies on existing student enrollment and class structures.',
            ],
            'staff_attendance' => [
                'name' => 'Staff Attendance',
                'category' => 'Staff & HR',
                'icon' => 'fas fa-clipboard-user',
                'color' => '#06b6d4',
                'description' => 'Staff biometric punches, manual attendance registers, clock-in/out times, and daily staff attendance logs.',
                'tables' => [
                    'staff_attendances',
                    'staff_attendance_registers',
                ],
                'asset_folders' => [],
                'dependencies' => ['staff'],
                'dependency_note' => 'Relies on existing staff directory records.',
            ],
            'students' => [
                'name' => 'Students & Admissions',
                'category' => 'Academics & Students',
                'icon' => 'fas fa-user-graduate',
                'color' => '#3b82f6',
                'description' => 'Student master directory, admission details, session enrollments, categories, houses, ID cards, certificates, and documents.',
                'tables' => [
                    'student_categories',
                    'student_houses',
                    'students',
                    'student_sessions',
                    'student_documents',
                    'student_cards',
                    'student_certificates',
                    'student_optional_subjects',
                    'student_deletion_requests',
                ],
                'asset_folders' => ['students/photos', 'students/documents'],
                'dependencies' => ['academics'],
                'dependency_note' => 'Core student master data. Restoring this restores all student profiles and uploaded docs.',
            ],
            'fees' => [
                'name' => 'Fees & Payments',
                'category' => 'Finance & Accounts',
                'icon' => 'fas fa-receipt',
                'color' => '#f59e0b',
                'description' => 'Fee structures, schedules, components, student fee balances, invoices, receipts, payment links, discounts, fines, and pending cheques.',
                'tables' => [
                    'fee_categories',
                    'fee_components',
                    'fee_configurations',
                    'fee_schedules',
                    'fee_structures',
                    'class_wise_fees',
                    'misc_fees',
                    'optional_fee_mappings',
                    'fee_discounts',
                    'fee_fines',
                    'student_fees',
                    'fee_invoices',
                    'fee_receipts',
                    'fee_refunds',
                    'pending_cheques',
                    'payment_links',
                    'late_fine_audit_logs',
                ],
                'asset_folders' => [],
                'dependencies' => ['students', 'academics'],
                'dependency_note' => 'Comprehensive fee ledger. Restoring this recalculates and resets school fee history from snapshot.',
            ],
            'staff' => [
                'name' => 'Staff & HR Directory',
                'category' => 'Staff & HR',
                'icon' => 'fas fa-id-badge',
                'color' => '#8b5cf6',
                'description' => 'Staff profiles, teacher directory, departments, designations, designation mappings, and staff module permissions.',
                'tables' => [
                    'departments',
                    'designations',
                    'staff',
                    'designation_staff',
                    'staff_module_access',
                ],
                'asset_folders' => ['staff-photos', 'staff-documents'],
                'dependencies' => [],
                'dependency_note' => 'Master staff registry, credentials, and organizational hierarchy.',
            ],
            'payroll' => [
                'name' => 'HR & Payroll',
                'category' => 'Staff & HR',
                'icon' => 'fas fa-money-check-dollar',
                'color' => '#14b8a6',
                'description' => 'Monthly staff payroll generation, salary structures, payroll deduction settings, salary slips, deposits, and payments.',
                'tables' => [
                    'payroll_deduction_settings',
                    'staff_salary_structures',
                    'staff_payrolls',
                    'staff_payroll_deposits',
                    'staff_payroll_payments',
                ],
                'asset_folders' => [],
                'dependencies' => ['staff'],
                'dependency_note' => 'Salary records and historical payroll disbursements for staff.',
            ],
            'leave_management' => [
                'name' => 'Leave Management',
                'category' => 'Staff & HR',
                'icon' => 'fas fa-calendar-minus',
                'color' => '#ec4899',
                'description' => 'Staff and student leave types, leave balances, leave applications, declarations, and leave approval workflows.',
                'tables' => [
                    'leave_types',
                    'staff_leave_balances',
                    'staff_leave_applications',
                    'student_leave_settings',
                    'student_leave_declarations',
                    'student_leave_applications',
                ],
                'asset_folders' => [],
                'dependencies' => ['staff', 'students'],
                'dependency_note' => 'Leave history, balances, and submitted applications.',
            ],
            'academics' => [
                'name' => 'Academics (Classes & Subjects)',
                'category' => 'Academics & Students',
                'icon' => 'fas fa-chalkboard',
                'color' => '#6366f1',
                'description' => 'Academic sessions, class levels, section allocations, subjects master, and section-subject-staff teacher assignments.',
                'tables' => [
                    'academic_sessions',
                    'school_classes',
                    'sections',
                    'subjects',
                    'section_subject_staff',
                    'class_subject_teachers',
                ],
                'asset_folders' => [],
                'dependencies' => [],
                'dependency_note' => 'Foundational academic structure of classes, sections, and subjects.',
            ],
            'timetable' => [
                'name' => 'Timetable Management',
                'category' => 'Academics & Students',
                'icon' => 'fas fa-clock',
                'color' => '#0ea5e9',
                'description' => 'Class period schedules, group timetables, master timetable matrix, timetable cells, and teacher substitutions.',
                'tables' => [
                    'timetables',
                    'timetable_groups',
                    'timetable_group_periods',
                    'class_timetable_cells',
                    'timetable_substitutions',
                ],
                'asset_folders' => [],
                'dependencies' => ['academics', 'staff'],
                'dependency_note' => 'Weekly period allocations and teacher substitution records.',
            ],
            'examinations' => [
                'name' => 'Examinations, Tests & Marks',
                'category' => 'Academics & Students',
                'icon' => 'fas fa-file-signature',
                'color' => '#e11d48',
                'description' => 'Exams setup, exam classes/subjects, assessments, sub-assessments, student marks, offline tests, grade scales, and report cards.',
                'tables' => [
                    'grade_scales',
                    'exams',
                    'exam_classes',
                    'exam_subjects',
                    'exam_assessments',
                    'exam_sub_assessments',
                    'student_marks',
                    'offline_tests',
                    'report_card_templates',
                    'report_card_template_mappings',
                    'report_card_histories',
                    'report_card_history_students',
                ],
                'asset_folders' => [],
                'dependencies' => ['academics', 'students'],
                'dependency_note' => 'Examination schedules, student marks entry, and historical report cards.',
            ],
            'transport' => [
                'name' => 'Transport & Fleet',
                'category' => 'Operations',
                'icon' => 'fas fa-bus',
                'color' => '#d97706',
                'description' => 'Vehicles fleet, vehicle compliance documents, transport routes, bus stops, route stops, transport fee schedules, vehicle expenses, trips, and bus attendance.',
                'tables' => [
                    'vehicles',
                    'vehicle_documents',
                    'vehicle_expenses',
                    'vehicle_trips',
                    'transport_routes',
                    'stops',
                    'route_stops',
                    'transport_fee_schedules',
                    'bus_attendances',
                ],
                'asset_folders' => ['vehicle_documents'],
                'dependencies' => [],
                'dependency_note' => 'Vehicle registry, route pricing, maintenance expenses, and daily bus attendance.',
            ],
            'library' => [
                'name' => 'Library Management',
                'category' => 'Operations',
                'icon' => 'fas fa-book-bookmark',
                'color' => '#059669',
                'description' => 'Book catalog, book types, library sections, circulation rules, and student/staff book issuing and return transactions.',
                'tables' => [
                    'library_book_types',
                    'library_sections',
                    'library_books',
                    'library_rules',
                    'library_transactions',
                ],
                'asset_folders' => [],
                'dependencies' => ['students', 'staff'],
                'dependency_note' => 'Library catalog and active/returned book transactions.',
            ],
            'inventory' => [
                'name' => 'Inventory & Sales',
                'category' => 'Operations',
                'icon' => 'fas fa-boxes-stacked',
                'color' => '#475569',
                'description' => 'Inventory categories, products/items, stock balances, stock audit logs, inventory sales, and sale item line items.',
                'tables' => [
                    'inventory_categories',
                    'inventory_products',
                    'inventory_stocks',
                    'inventory_stock_logs',
                    'inventory_sales',
                    'inventory_sale_items',
                ],
                'asset_folders' => [],
                'dependencies' => [],
                'dependency_note' => 'Item stocks, sales registers, and inventory logs.',
            ],
            'accounts' => [
                'name' => 'Income & Expense (Accounts)',
                'category' => 'Finance & Accounts',
                'icon' => 'fas fa-wallet',
                'color' => '#15803d',
                'description' => 'Income heads, income vouchers, voucher receipts, expense heads, expense vouchers, voucher payments, account transfers, and bank accounts.',
                'tables' => [
                    'school_banks',
                    'income_heads',
                    'income_vouchers',
                    'school_incomes',
                    'voucher_receipts',
                    'expense_heads',
                    'expense_vouchers',
                    'school_expenses',
                    'voucher_payments',
                    'account_transfers',
                ],
                'asset_folders' => [],
                'dependencies' => [],
                'dependency_note' => 'Non-fee school income and operational expense accounting vouchers.',
            ],
            'assignments' => [
                'name' => 'Assignments, Homework & Diary',
                'category' => 'Academics & Students',
                'icon' => 'fas fa-book-open-reader',
                'color' => '#84cc16',
                'description' => 'Teacher homework assignments, student homework submissions, study materials, digital diaries, and daily task evaluations.',
                'tables' => [
                    'teacher_assignments',
                    'teacher_assignment_submissions',
                    'study_materials',
                    'digital_diaries',
                    'daily_task_heads',
                    'daily_task_questions',
                    'daily_task_reviews',
                    'daily_task_evaluations',
                ],
                'asset_folders' => ['assignments', 'study_materials', 'diary_attachments'],
                'dependencies' => ['academics', 'students', 'staff'],
                'dependency_note' => 'Class assignments, student submissions, digital diaries, and study attachments.',
            ],
            'communication' => [
                'name' => 'Notices & Communication',
                'category' => 'Operations',
                'icon' => 'fas fa-bullhorn',
                'color' => '#f97316',
                'description' => 'School notice boards, notice templates, push notifications, teacher broadcast notices, device tokens, and in-app chat messages.',
                'tables' => [
                    'notices',
                    'notice_templates',
                    'notifications',
                    'teacher_notifications',
                    'fcm_device_tokens',
                    'chat_messages',
                ],
                'asset_folders' => [],
                'dependencies' => [],
                'dependency_note' => 'Announcements, broadcast logs, and push notification tokens.',
            ],
            'front_desk' => [
                'name' => 'Front Desk & Gate Pass',
                'category' => 'Operations',
                'icon' => 'fas fa-door-open',
                'color' => '#0284c7',
                'description' => 'Visitor passes, front desk inquiries/leads, student gate passes, and staff gate passes with photos.',
                'tables' => [
                    'enquiry_leads',
                    'visitors',
                    'student_gate_passes',
                    'staff_gate_passes',
                ],
                'asset_folders' => ['visitors'],
                'dependencies' => ['students', 'staff'],
                'dependency_note' => 'Campus entry logs, visitor cards, and departure gate passes.',
            ],
            'certificates' => [
                'name' => 'Certificates & ID Templates',
                'category' => 'Operations',
                'icon' => 'fas fa-award',
                'color' => '#eab308',
                'description' => 'Certificate visual templates, student ID card design templates, and issued student card records.',
                'tables' => [
                    'certificate_templates',
                    'card_templates',
                ],
                'asset_folders' => [],
                'dependencies' => [],
                'dependency_note' => 'Card and certificate layout templates.',
            ],
            'surveys' => [
                'name' => 'Surveys & Feedback',
                'category' => 'Operations',
                'icon' => 'fas fa-square-poll-vertical',
                'color' => '#7c3aed',
                'description' => 'Feedback surveys, survey question banks, survey choice options, and submitted parent/student survey responses.',
                'tables' => [
                    'surveys',
                    'survey_questions',
                    'survey_options',
                    'survey_responses',
                ],
                'asset_folders' => [],
                'dependencies' => [],
                'dependency_note' => 'Stakeholder feedback questionnaires and survey submissions.',
            ],
            'settings' => [
                'name' => 'School Settings & Branding',
                'category' => 'System',
                'icon' => 'fas fa-gears',
                'color' => '#64748b',
                'description' => 'School general configurations, AI tutor settings, mobile app banner sliders, gallery photos, and facial recognition vectors.',
                'tables' => [
                    'school_settings',
                    'school_ai_settings',
                    'mobile_app_banners',
                    'gallery_posts',
                    'face_vectors',
                ],
                'asset_folders' => ['school-logos', 'school-stamps', 'school-signatures'],
                'dependencies' => [],
                'dependency_note' => 'Custom settings, school branding logos, AI rules, and banner images.',
            ],
            'users' => [
                'name' => 'School User Accounts & Logins',
                'category' => 'System',
                'icon' => 'fas fa-users-gear',
                'color' => '#4338ca',
                'description' => 'School admin, teacher, staff, and parent login accounts, user role assignments, user permissions, and user login logs.',
                'tables' => [
                    'users',
                    'model_has_roles',
                    'model_has_permissions',
                    'login_logs',
                ],
                'asset_folders' => [],
                'dependencies' => [],
                'dependency_note' => 'Restores user logins scoped strictly to this school. Platform Super Admin accounts are never affected.',
            ],
        ];
    }

    /**
     * Scan and return all available snapshot archives for a specific school ID.
     * Verifies that each snapshot file strictly belongs to this school.
     *
     * @param int $schoolId
     * @return array
     */
    public function getAvailableSnapshots(int $schoolId): array
    {
        $school = School::find($schoolId);
        if (!$school) {
            return [];
        }

        $snapshots = [];
        if (!File::exists($this->snapshotsDir)) {
            return [];
        }

        // 1. Gather snapshot files from school-specific subdirectories and root
        $candidateFiles = [];

        // Check school subdirectories (e.g. storage/app/snapshots/school_{id}_{code}/**)
        $subdirs = File::directories($this->snapshotsDir);
        foreach ($subdirs as $dir) {
            $dirName = basename($dir);
            if (preg_match("/^school_{$schoolId}(_|$)/i", $dirName)) {
                foreach (File::allFiles($dir) as $f) {
                    if (preg_match("/^school_{$schoolId}_.*\.zip$/i", $f->getFilename())) {
                        $candidateFiles[] = $f;
                    }
                }
            }
        }

        // Check root snapshots directory for any legacy/direct files
        foreach (File::files($this->snapshotsDir) as $f) {
            if (preg_match("/^school_{$schoolId}_.*\.zip$/i", $f->getFilename())) {
                $candidateFiles[] = $f;
            }
        }

        $processedFilenames = [];

        foreach ($candidateFiles as $file) {
            $filename = $file->getFilename();
            if (in_array($filename, $processedFilenames)) {
                continue;
            }
            $processedFilenames[] = $filename;

            $filePath = $file->getRealPath() ?: $file->getPathname();
            $fileSize = $file->getSize();
            $fileMTime = $file->getMTime();

            // Inspect manifest from inside zip without extracting entire file
            $manifest = null;
            $isValid = false;
            $zip = new ZipArchive();

            if ($zip->open($filePath) === true) {
                $manifestContent = $zip->getFromName('manifest.json');
                if ($manifestContent !== false) {
                    $manifest = json_decode($manifestContent, true);
                    if ($manifest && isset($manifest['school']['id']) && (int)$manifest['school']['id'] === $schoolId) {
                        $isValid = true;
                    }
                }
                $zip->close();
            }

            // Parse timestamp from filename if available (e.g. school_4_VPSUP01_2026-08-29_060007.zip)
            $parsedDate = null;
            if (preg_match('/(\d{4}-\d{2}-\d{2})_(\d{2})(\d{2})(\d{2})/', $filename, $m)) {
                $parsedDate = "{$m[1]} {$m[2]}:{$m[3]}:{$m[4]}";
            } elseif ($manifest && isset($manifest['export_timestamp'])) {
                $parsedDate = date('Y-m-d H:i:s', strtotime($manifest['export_timestamp']));
            } else {
                $parsedDate = date('Y-m-d H:i:s', $fileMTime);
            }

            $stats = $manifest['statistics'] ?? [];
            $tableCatalog = $manifest['tables'] ?? [];

            $snapshots[] = [
                'filename' => $filename,
                'path' => $filePath,
                'size_bytes' => $fileSize,
                'size_formatted' => $this->formatBytes($fileSize),
                'mtime' => $fileMTime,
                'created_at' => $parsedDate,
                'is_valid' => $isValid,
                'is_latest' => false,
                'school_id' => $schoolId,
                'school_code' => $manifest['school']['code'] ?? $school->code,
                'school_name' => $manifest['school']['name'] ?? $school->name,
                'total_tables' => $stats['total_tables_exported'] ?? count($tableCatalog),
                'total_rows' => $stats['total_rows_exported'] ?? array_sum($tableCatalog),
                'total_files' => $stats['total_files_exported'] ?? 0,
                'tables' => $tableCatalog,
            ];
        }

        // Sort descending: newest mtime/timestamp first
        usort($snapshots, fn($a, $b) => $b['mtime'] <=> $a['mtime']);

        if (!empty($snapshots)) {
            $snapshots[0]['is_latest'] = true;
        }

        return $snapshots;
    }

    /**
     * Inspect a specific snapshot archive and compute per-module row counts.
     *
     * @param string $snapshotZipPath
     * @param int $expectedSchoolId
     * @return array
     * @throws Exception
     */
    public function inspectSnapshotModules(string $snapshotZipPath, int $expectedSchoolId): array
    {
        $resolvedPath = $this->resolveSnapshotPath($snapshotZipPath, $expectedSchoolId);
        $manifest = $this->readManifestFromZip($resolvedPath, $expectedSchoolId);

        $tablesInSnapshot = $manifest['tables'] ?? [];
        $modules = $this->getModuleDefinitions();
        $inspectedModules = [];
        $totalSelectedRows = 0;

        foreach ($modules as $modKey => $modDef) {
            $modTables = $modDef['tables'];
            $modRowCount = 0;
            $tableBreakdown = [];

            foreach ($modTables as $tbl) {
                $count = (int)($tablesInSnapshot[$tbl] ?? 0);
                $modRowCount += $count;
                $tableBreakdown[$tbl] = $count;
            }

            $inspectedModules[$modKey] = array_merge($modDef, [
                'key' => $modKey,
                'snapshot_rows' => $modRowCount,
                'has_data' => $modRowCount > 0,
                'tables_breakdown' => $tableBreakdown,
            ]);
        }

        return [
            'success' => true,
            'snapshot_file' => basename($resolvedPath),
            'snapshot_path' => $resolvedPath,
            'school' => $manifest['school'],
            'export_timestamp' => $manifest['export_timestamp'],
            'total_tables' => count($tablesInSnapshot),
            'total_rows' => array_sum($tablesInSnapshot),
            'modules' => $inspectedModules,
        ];
    }

    /**
     * Perform selective module-level restoration for a single school.
     *
     * @param int $schoolId
     * @param string $snapshotZipPath
     * @param array $selectedModuleKeys
     * @param int|null $userId SuperAdmin user ID initiating restore
     * @param bool $createSafetyBackup Whether to create full safety backup before restore
     * @param callable|null $progressCallback
     * @return array
     * @throws Exception
     */
    public function executeSelectiveRestore(
        int $schoolId,
        string $snapshotZipPath,
        array $selectedModuleKeys,
        ?int $userId = null,
        bool $createSafetyBackup = true,
        ?callable $progressCallback = null
    ): array {
        $startTime = microtime(true);

        // 1. Verify school exists
        $school = School::find($schoolId);
        if (!$school) {
            throw new Exception("Target School with ID [{$schoolId}] does not exist.");
        }

        // 2. Validate selected modules
        $allModules = $this->getModuleDefinitions();
        $selectedModuleKeys = array_values(array_unique(array_filter($selectedModuleKeys)));

        if (empty($selectedModuleKeys)) {
            throw new Exception("Please select at least one module to restore.");
        }

        foreach ($selectedModuleKeys as $k) {
            if (!isset($allModules[$k])) {
                throw new Exception("Invalid module key requested: [{$k}].");
            }
        }

        // 3. Resolve snapshot file and verify school ownership strictly
        $resolvedZipPath = $this->resolveSnapshotPath($snapshotZipPath, $schoolId);

        if ($progressCallback) $progressCallback("Verifying snapshot integrity and cryptographic checksums...", 10);

        // 4. Extract and verify snapshot package
        $extracted = $this->extractAndVerifySnapshot($resolvedZipPath, $schoolId);
        $manifest = $extracted['manifest'];
        $tempDir = $extracted['temp_dir'];

        // 5. Gather tables to restore from selected modules
        $tablesToRestore = [];
        foreach ($selectedModuleKeys as $modKey) {
            foreach ($allModules[$modKey]['tables'] as $tbl) {
                $tablesToRestore[] = $tbl;
            }
        }
        $tablesToRestore = array_values(array_unique($tablesToRestore));

        // Optional safety backup before making changes
        $safetyBackupName = null;
        if ($createSafetyBackup) {
            if ($progressCallback) $progressCallback("Creating automatic pre-restore safety backup...", 20);
            try {
                $backup = $this->backupService->createFullBackup();
                $safetyBackupName = $backup['backup_name'] ?? 'safety_backup_' . date('Ymd_His');
                Log::info("Pre-restore safety backup created successfully: {$safetyBackupName}");
            } catch (Throwable $e) {
                Log::warning("Pre-restore full backup was skipped or encountered a non-fatal warning: " . $e->getMessage());
            }
        }

        $driver = DB::connection()->getDriverName();
        $pdo = DB::connection()->getPdo();

        if ($progressCallback) $progressCallback("Beginning atomic transactional database restoration...", 30);

        // 6. Begin Transaction
        if ($driver !== 'sqlite') {
            $pdo->exec("SET FOREIGN_KEY_CHECKS = 0;");
        }

        DB::beginTransaction();

        $tablesRestoredStats = [];
        $totalRowsDeleted = 0;
        $totalRowsInserted = 0;
        $totalFilesRestored = 0;
        $restoredModuleBreakdown = [];

        try {
            // STEP A: Delete existing records for target school in reverse order across selected tables
            $tablesInReverse = array_reverse($tablesToRestore);
            foreach ($tablesInReverse as $table) {
                $deletedCount = 0;
                try {
                    if (isset($this->indirectTableQueries[$table])) {
                        // Indirect table deletion
                        $delSql = $this->indirectTableQueries[$table]['delete'];
                        $stmt = $pdo->prepare($delSql);
                        $stmt->execute(['school_id' => $schoolId]);
                        $deletedCount = $stmt->rowCount();
                    } else {
                        // Direct table deletion: strictly scoped to school_id
                        $deletedCount = DB::table($table)->where('school_id', $schoolId)->delete();
                    }
                } catch (Throwable $e) {
                    // Table might not exist or might be empty
                    $deletedCount = 0;
                }
                $totalRowsDeleted += $deletedCount;
            }

            // STEP B: Insert snapshot records table by table
            $currentTblIdx = 0;
            $totalSelectedTables = count($tablesToRestore);

            foreach ($tablesToRestore as $table) {
                $currentTblIdx++;
                if ($progressCallback && $totalSelectedTables > 0) {
                    $pct = 30 + (int)(($currentTblIdx / $totalSelectedTables) * 50);
                    $progressCallback("Restoring records for table [{$table}]...", $pct);
                }

                $jsonFile = "{$tempDir}/data/{$table}.json";
                if (!File::exists($jsonFile)) {
                    $tablesRestoredStats[$table] = ['inserted' => 0, 'status' => 'no_snapshot_data'];
                    continue;
                }

                $rows = json_decode(File::get($jsonFile), true) ?? [];
                if (empty($rows)) {
                    $tablesRestoredStats[$table] = ['inserted' => 0, 'status' => 'empty'];
                    continue;
                }

                // Security & tenant isolation: Sanitize rows to enforce tenant boundary
                $cleanedRows = [];
                $isIndirect = isset($this->indirectTableQueries[$table]);

                foreach ($rows as $row) {
                    // For direct tables, strictly verify or force school_id
                    if (!$isIndirect && isset($row['school_id'])) {
                        if ((int)$row['school_id'] !== $schoolId) {
                            // Skip any rogue row not belonging to this school
                            continue;
                        }
                    } elseif (!$isIndirect) {
                        $row['school_id'] = $schoolId;
                    }

                    // Special protection: Never overwrite platform superadmin users
                    if ($table === 'users') {
                        if (isset($row['role']) && $row['role'] === 'superadmin') {
                            continue;
                        }
                        if (isset($row['email']) && in_array(strtolower($row['email']), ['superadmin@schoolcloud.com', 'admin@educorerp.com'])) {
                            continue;
                        }
                    }

                    $cleanedRows[] = $row;
                }

                $insertedForTable = 0;
                if (!empty($cleanedRows)) {
                    // Batch chunk insert
                    foreach (array_chunk($cleanedRows, 200) as $chunk) {
                        DB::table($table)->insert($chunk);
                    }
                    $insertedForTable = count($cleanedRows);
                }

                $tablesRestoredStats[$table] = [
                    'inserted' => $insertedForTable,
                    'status' => 'restored'
                ];
                $totalRowsInserted += $insertedForTable;
            }

            // STEP C: Restore physical asset files belonging to selected modules
            if ($progressCallback) $progressCallback("Restoring physical storage assets...", 85);

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
                        $relPath = str_replace('\\', '/', $relPath);

                        // Check if this file matches selected modules
                        $shouldRestoreFile = false;
                        foreach ($selectedModuleKeys as $modKey) {
                            $assetFolders = $allModules[$modKey]['asset_folders'] ?? [];
                            if (empty($assetFolders)) {
                                // If module doesn't specify folders, check if file is general
                                continue;
                            }
                            foreach ($assetFolders as $folder) {
                                if (strpos($relPath, $folder) === 0) {
                                    $shouldRestoreFile = true;
                                    break 2;
                                }
                            }
                        }

                        // If files match, copy over
                        if ($shouldRestoreFile || empty($selectedModuleKeys)) {
                            $destPath = "{$targetStorageDir}/{$relPath}";
                            $destDir = dirname($destPath);
                            if (!File::exists($destDir)) {
                                File::makeDirectory($destDir, 0755, true);
                            }
                            File::copy($srcPath, $destPath);
                            $totalFilesRestored++;
                        }
                    }
                }
            }

            // STEP D: Calculate summary per module
            foreach ($selectedModuleKeys as $modKey) {
                $modDef = $allModules[$modKey];
                $modInserted = 0;
                foreach ($modDef['tables'] as $tbl) {
                    $modInserted += $tablesRestoredStats[$tbl]['inserted'] ?? 0;
                }
                $restoredModuleBreakdown[$modKey] = [
                    'name' => $modDef['name'],
                    'icon' => $modDef['icon'],
                    'color' => $modDef['color'],
                    'tables_count' => count($modDef['tables']),
                    'rows_restored' => $modInserted,
                ];
            }

            // Commit Transaction
            DB::commit();

            if ($driver !== 'sqlite') {
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            }

            // Clear cache for target school
            Cache::forget("students_list_version_{$schoolId}");
            Cache::forget("school_settings_{$schoolId}");
            Cache::forget("school_dashboard_stats_{$schoolId}");

            $durationMs = (int)round((microtime(true) - $startTime) * 1000);

            // STEP E: Record Disaster Recovery Audit Log
            try {
                SchoolRestoreLog::create([
                    'user_id' => $userId,
                    'school_id' => $schoolId,
                    'school_code' => $school->code,
                    'snapshot_file' => basename($resolvedZipPath),
                    'snapshot_timestamp' => $manifest['export_timestamp'] ?? date('Y-m-d H:i:s'),
                    'modules_selected' => $selectedModuleKeys,
                    'tables_restored' => $tablesRestoredStats,
                    'total_rows_deleted' => $totalRowsDeleted,
                    'total_rows_inserted' => $totalRowsInserted,
                    'total_files_restored' => $totalFilesRestored,
                    'status' => 'success',
                    'error_message' => null,
                    'duration_ms' => $durationMs,
                    'pre_restore_backup' => $safetyBackupName,
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                ]);
            } catch (Throwable $e) {
                Log::error("Failed to write to SchoolRestoreLog: " . $e->getMessage());
            }

            Log::info("Single-school selective restore executed successfully", [
                'school_id' => $schoolId,
                'school_code' => $school->code,
                'snapshot' => basename($resolvedZipPath),
                'modules' => $selectedModuleKeys,
                'rows_deleted' => $totalRowsDeleted,
                'rows_inserted' => $totalRowsInserted,
                'files_restored' => $totalFilesRestored,
                'duration_ms' => $durationMs,
            ]);

            if ($progressCallback) $progressCallback("Selective restore completed successfully!", 100);

            return [
                'success' => true,
                'school' => [
                    'id' => $school->id,
                    'name' => $school->name,
                    'code' => $school->code,
                ],
                'snapshot_file' => basename($resolvedZipPath),
                'snapshot_timestamp' => $manifest['export_timestamp'] ?? null,
                'modules_restored' => $restoredModuleBreakdown,
                'total_modules_count' => count($selectedModuleKeys),
                'total_tables_count' => count($tablesToRestore),
                'total_rows_deleted' => $totalRowsDeleted,
                'total_rows_inserted' => $totalRowsInserted,
                'total_files_restored' => $totalFilesRestored,
                'pre_restore_backup' => $safetyBackupName,
                'duration_ms' => $durationMs,
            ];

        } catch (Throwable $e) {
            DB::rollBack();
            if ($driver !== 'sqlite') {
                $pdo->exec("SET FOREIGN_KEY_CHECKS = 1;");
            }

            $durationMs = (int)round((microtime(true) - $startTime) * 1000);

            // Record failed audit log
            try {
                SchoolRestoreLog::create([
                    'user_id' => $userId,
                    'school_id' => $schoolId,
                    'school_code' => $school->code,
                    'snapshot_file' => basename($resolvedZipPath),
                    'snapshot_timestamp' => $manifest['export_timestamp'] ?? null,
                    'modules_selected' => $selectedModuleKeys,
                    'tables_restored' => $tablesRestoredStats,
                    'total_rows_deleted' => 0,
                    'total_rows_inserted' => 0,
                    'total_files_restored' => 0,
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'duration_ms' => $durationMs,
                    'pre_restore_backup' => $safetyBackupName,
                    'ip_address' => request()->ip() ?? '127.0.0.1',
                ]);
            } catch (Throwable $logEx) {}

            Log::error("Selective restore failed for School [{$schoolId}] and was rolled back: " . $e->getMessage(), [
                'exception' => $e,
                'school_id' => $schoolId,
                'snapshot' => basename($resolvedZipPath),
                'modules' => $selectedModuleKeys,
            ]);

            throw $e;
        } finally {
            if (isset($tempDir) && File::exists($tempDir)) {
                File::deleteDirectory($tempDir);
            }
        }
    }

    /**
     * Resolve and validate absolute snapshot zip path for a school.
     * Prevents cross-school snapshot tampering.
     *
     * @param string $snapshotZipPath
     * @param int $expectedSchoolId
     * @return string Absolute real path
     * @throws Exception
     */
    public function resolveSnapshotPath(string $snapshotZipPath, int $expectedSchoolId): string
    {
        $filename = basename($snapshotZipPath);

        // Security check: Must start with school_{expectedSchoolId}_
        if (!preg_match("/^school_{$expectedSchoolId}_.*\.zip$/i", $filename)) {
            throw new Exception("Security block: Snapshot archive [{$filename}] does not belong to School [ID: {$expectedSchoolId}]. Cross-school restoration is strictly prohibited.");
        }

        // 1. Direct path check
        if (File::exists($snapshotZipPath)) {
            return realpath($snapshotZipPath) ?: $snapshotZipPath;
        }

        // 2. School-specific subdirectory check (including date subfolders)
        if (File::exists($this->snapshotsDir)) {
            $subdirs = File::directories($this->snapshotsDir);
            foreach ($subdirs as $dir) {
                $dirName = basename($dir);
                if (preg_match("/^school_{$expectedSchoolId}(_|$)/i", $dirName)) {
                    $subPath = "{$dir}/{$filename}";
                    if (File::exists($subPath)) {
                        return realpath($subPath) ?: $subPath;
                    }

                    // Check recursively in date subfolders
                    foreach (File::allFiles($dir) as $f) {
                        if ($f->getFilename() === $filename) {
                            return $f->getRealPath() ?: $f->getPathname();
                        }
                    }
                }
            }
        }

        // 3. Root snapshots directory check
        $fullPath = "{$this->snapshotsDir}/{$filename}";
        if (File::exists($fullPath)) {
            return realpath($fullPath) ?: $fullPath;
        }

        throw new Exception("Snapshot archive not found on disk: {$filename}");
    }

    /**
     * Extract snapshot archive into sandbox and verify cryptographic checksums.
     *
     * @param string $zipPath
     * @param int $expectedSchoolId
     * @return array
     * @throws Exception
     */
    protected function extractAndVerifySnapshot(string $zipPath, int $expectedSchoolId): array
    {
        if (!File::exists($zipPath)) {
            throw new Exception("Snapshot archive does not exist: {$zipPath}");
        }

        $tempDir = storage_path('app/temp/restore_' . uniqid());
        File::makeDirectory($tempDir, 0755, true);

        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            File::deleteDirectory($tempDir);
            throw new Exception("Failed to open snapshot zip package: " . basename($zipPath));
        }

        $zip->extractTo($tempDir);
        $zip->close();

        $manifestPath = "{$tempDir}/manifest.json";
        if (!File::exists($manifestPath)) {
            File::deleteDirectory($tempDir);
            throw new Exception("Corrupted snapshot archive: manifest.json is missing.");
        }

        $manifest = json_decode(File::get($manifestPath), true);
        if (!$manifest || !isset($manifest['school']['id'])) {
            File::deleteDirectory($tempDir);
            throw new Exception("Corrupted snapshot archive: manifest.json contains invalid data.");
        }

        if ((int)$manifest['school']['id'] !== $expectedSchoolId) {
            File::deleteDirectory($tempDir);
            throw new Exception("Security mismatch: Archive manifest belongs to School [ID: {$manifest['school']['id']}], but restoration was requested for School [ID: {$expectedSchoolId}]. Operation terminated.");
        }

        // Verify Checksums
        $checksums = $manifest['checksums'] ?? [];
        foreach ($checksums as $relFile => $expectedHash) {
            $filePath = "{$tempDir}/{$relFile}";
            if (!File::exists($filePath)) {
                File::deleteDirectory($tempDir);
                throw new Exception("Integrity verification failed: Missing required file [{$relFile}] from snapshot bundle.");
            }
            $actualHash = hash_file('sha256', $filePath);
            if ($actualHash !== $expectedHash) {
                File::deleteDirectory($tempDir);
                throw new Exception("Integrity verification failed: Checksum mismatch on file [{$relFile}]. The archive may have been modified or corrupted.");
            }
        }

        return [
            'manifest' => $manifest,
            'temp_dir' => $tempDir,
        ];
    }

    /**
     * Read manifest.json directly from ZIP without extracting entire file to disk.
     *
     * @param string $zipPath
     * @param int $expectedSchoolId
     * @return array
     * @throws Exception
     */
    protected function readManifestFromZip(string $zipPath, int $expectedSchoolId): array
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath) !== true) {
            throw new Exception("Failed to read snapshot package: " . basename($zipPath));
        }

        $content = $zip->getFromName('manifest.json');
        $zip->close();

        if ($content === false) {
            throw new Exception("Snapshot package is missing manifest.json.");
        }

        $manifest = json_decode($content, true);
        if (!$manifest || !isset($manifest['school']['id'])) {
            throw new Exception("Snapshot package contains an invalid manifest.");
        }

        if ((int)$manifest['school']['id'] !== $expectedSchoolId) {
            throw new Exception("Snapshot package does not belong to School [ID: {$expectedSchoolId}].");
        }

        return $manifest;
    }

    /**
     * Format bytes into human readable format.
     */
    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
