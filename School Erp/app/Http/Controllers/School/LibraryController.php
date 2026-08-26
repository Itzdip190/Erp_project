<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicSession;
use App\Models\LibraryBook;
use App\Models\LibraryBookType;
use App\Models\LibraryRule;
use App\Models\LibrarySection;
use App\Models\LibraryTransaction;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Section;
use App\Models\Staff;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class LibraryController extends Controller
{
    /**
     * Helper to get active school ID.
     */
    protected function getSchoolId(): ?int
    {
        if (Auth::check() && Auth::user()->school_id) {
            return Auth::user()->school_id;
        }

        if (app()->bound('currentSchool')) {
            return app('currentSchool')?->id;
        }

        return request()->route('school')?->id;
    }

    /**
     * Ensure all library tables exist automatically.
     */
    protected function ensureLibraryTablesExist(): void
    {
        try {
            if (!Schema::hasTable('library_rules')) {
                Schema::create('library_rules', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                    $table->string('member_type', 30);
                    $table->unsignedInteger('borrow_period_days')->default(14);
                    $table->unsignedInteger('max_books_allowed')->default(3);
                    $table->decimal('late_fine_amount', 10, 2)->default(5.00);
                    $table->string('late_fine_type', 30)->default('per_day');
                    $table->decimal('lost_book_fine_amount', 10, 2)->default(200.00);
                    $table->string('lost_book_fine_type', 40)->default('fixed_amount');
                    $table->decimal('damaged_book_fine_amount', 10, 2)->default(100.00);
                    $table->string('damaged_book_fine_type', 40)->default('fixed_amount');
                    $table->unsignedInteger('grace_period_days')->default(0);
                    $table->unsignedInteger('max_renewal_count')->default(2);
                    $table->boolean('allow_issue_with_fine')->default(false);
                    $table->decimal('max_fine_threshold', 10, 2)->default(100.00);
                    $table->json('extra_config')->nullable();
                    $table->timestamps();
                    $table->unique(['school_id', 'member_type']);
                });
            }

            if (!Schema::hasTable('library_sections')) {
                Schema::create('library_sections', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                    $table->string('name', 150);
                    $table->string('code', 50)->nullable();
                    $table->string('rack_location', 100)->nullable();
                    $table->string('material_scope', 150)->nullable();
                    $table->text('description')->nullable();
                    $table->string('status', 20)->default('active');
                    $table->timestamps();
                });
            }

            if (!Schema::hasTable('library_book_types')) {
                Schema::create('library_book_types', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                    $table->string('name', 150);
                    $table->string('code', 50)->nullable();
                    $table->unsignedInteger('default_borrow_days_override')->nullable();
                    $table->decimal('fine_multiplier', 5, 2)->default(1.00);
                    $table->string('icon', 100)->nullable();
                    $table->text('description')->nullable();
                    $table->string('status', 20)->default('active');
                    $table->timestamps();
                });
            }

            if (!Schema::hasTable('library_books')) {
                Schema::create('library_books', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
                    $table->string('accession_no', 100)->nullable();
                    $table->string('isbn', 50)->nullable();
                    $table->string('title', 255);
                    $table->string('author', 200)->nullable();
                    $table->string('publisher', 200)->nullable();
                    $table->string('edition', 50)->nullable();
                    $table->string('publication_year', 20)->nullable();
                    $table->unsignedBigInteger('section_id')->nullable();
                    $table->unsignedBigInteger('book_type_id')->nullable();
                    $table->string('language', 50)->default('English');
                    $table->unsignedInteger('pages')->nullable();
                    $table->string('rack_location', 100)->nullable();
                    $table->decimal('price', 10, 2)->default(0.00);
                    $table->unsignedInteger('total_copies')->default(1);
                    $table->unsignedInteger('available_copies')->default(1);
                    $table->unsignedInteger('issued_copies')->default(0);
                    $table->unsignedInteger('lost_copies')->default(0);
                    $table->unsignedInteger('damaged_copies')->default(0);
                    $table->string('cover_image', 255)->nullable();
                    $table->text('description')->nullable();
                    $table->string('status', 30)->default('available');
                    $table->timestamps();
                });
            }

            if (!Schema::hasTable('library_transactions')) {
                Schema::create('library_transactions', function (Blueprint $table) {
                    $table->id();
                    $table->unsignedBigInteger('school_id')->nullable();
                    $table->string('transaction_code', 100)->nullable();
                    $table->unsignedBigInteger('book_id')->nullable();
                    $table->string('member_type', 50)->default('student');
                    $table->unsignedBigInteger('student_id')->nullable();
                    $table->unsignedBigInteger('staff_id')->nullable();
                    $table->date('issue_date')->nullable();
                    $table->date('due_date')->nullable();
                    $table->date('return_date')->nullable();
                    $table->unsignedInteger('renewed_count')->default(0);
                    $table->string('status', 50)->default('issued');
                    $table->unsignedInteger('late_days')->default(0);
                    $table->decimal('late_fine_amount', 10, 2)->default(0.00);
                    $table->decimal('damage_lost_fine', 10, 2)->default(0.00);
                    $table->decimal('total_fine', 10, 2)->default(0.00);
                    $table->string('fine_status', 50)->default('none');
                    $table->date('payment_date')->nullable();
                    $table->text('remarks')->nullable();
                    $table->unsignedBigInteger('issued_by')->nullable();
                    $table->unsignedBigInteger('received_by')->nullable();
                    $table->timestamps();
                });
            } else {
                $existingCols = collect(DB::select("SHOW COLUMNS FROM `library_transactions`"))->pluck('Field')->map(fn($c) => strtolower($c))->toArray();

                $txnColumns = [
                    'school_id' => 'BIGINT UNSIGNED NULL',
                    'transaction_code' => 'VARCHAR(100) NULL',
                    'book_id' => 'BIGINT UNSIGNED NULL',
                    'member_type' => "VARCHAR(50) NOT NULL DEFAULT 'student'",
                    'student_id' => 'BIGINT UNSIGNED NULL',
                    'staff_id' => 'BIGINT UNSIGNED NULL',
                    'issue_date' => 'DATE NULL',
                    'due_date' => 'DATE NULL',
                    'return_date' => 'DATE NULL',
                    'renewed_count' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                    'status' => "VARCHAR(50) NOT NULL DEFAULT 'issued'",
                    'late_days' => 'INT UNSIGNED NOT NULL DEFAULT 0',
                    'late_fine_amount' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00',
                    'damage_lost_fine' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00',
                    'total_fine' => 'DECIMAL(10,2) NOT NULL DEFAULT 0.00',
                    'fine_status' => "VARCHAR(50) NOT NULL DEFAULT 'none'",
                    'payment_date' => 'DATE NULL',
                    'remarks' => 'TEXT NULL',
                    'issued_by' => 'BIGINT UNSIGNED NULL',
                    'received_by' => 'BIGINT UNSIGNED NULL',
                ];

                foreach ($txnColumns as $col => $colDef) {
                    if (!in_array(strtolower($col), $existingCols)) {
                        try {
                            DB::statement("ALTER TABLE `library_transactions` ADD COLUMN `{$col}` {$colDef}");
                            $existingCols[] = strtolower($col);
                        } catch (\Throwable $ex) {
                            Log::warning("Column {$col} could not be added to library_transactions: " . $ex->getMessage());
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error("ensureLibraryTablesExist error: " . $e->getMessage());
        }
    }

    /**
     * Seed initial default sections & material types if the school has none.
     */
    protected function seedDefaultLibraryData(int $schoolId): void
    {
        if (LibrarySection::where('school_id', $schoolId)->count() === 0) {
            $defaultSections = [
                ['name' => 'Science & Technology', 'code' => 'SEC-SCI', 'rack_location' => 'Rack A1-A4', 'material_scope' => 'Science, Physics, Chemistry, Tech', 'description' => 'Books related to STEM, computers and technology.'],
                ['name' => 'Literature & Fiction', 'code' => 'SEC-LIT', 'rack_location' => 'Rack B1-B3', 'material_scope' => 'Novels, Classics, Poetry', 'description' => 'English, Hindi and regional language literature.'],
                ['name' => 'Reference & Encyclopedia', 'code' => 'SEC-REF', 'rack_location' => 'Rack C1-C2', 'material_scope' => 'Encyclopedias, Dictionaries, Yearbooks', 'description' => 'Reference materials for in-library reading only.'],
                ['name' => 'General Reading & Periodicals', 'code' => 'SEC-GEN', 'rack_location' => 'Display Bay 1', 'material_scope' => 'Magazines, Newspapers, Current Affairs', 'description' => 'Daily periodicals, magazines and general awareness books.'],
                ['name' => 'Primary & Junior Wing', 'code' => 'SEC-PRI', 'rack_location' => 'Junior Corner J1', 'material_scope' => 'Storybooks, Comics, Picture Books', 'description' => 'Illustrated books for elementary and junior students.'],
            ];

            foreach ($defaultSections as $sec) {
                LibrarySection::create(array_merge($sec, ['school_id' => $schoolId, 'status' => 'active']));
            }
        }

        if (LibraryBookType::where('school_id', $schoolId)->count() === 0) {
            $defaultTypes = [
                ['name' => 'Textbook', 'code' => 'TYP-TXT', 'fine_multiplier' => 1.00, 'icon' => 'fas fa-book', 'description' => 'Academic curriculum textbooks.'],
                ['name' => 'Reference Book', 'code' => 'TYP-REF', 'fine_multiplier' => 2.00, 'icon' => 'fas fa-bookmark', 'description' => 'Specialized reference books and atlases.'],
                ['name' => 'Fiction & Literature', 'code' => 'TYP-FIC', 'fine_multiplier' => 1.00, 'icon' => 'fas fa-feather', 'description' => 'Novels, dramas and story collections.'],
                ['name' => 'Journal & Periodical', 'code' => 'TYP-JRN', 'fine_multiplier' => 1.50, 'icon' => 'fas fa-newspaper', 'description' => 'Monthly/weekly educational journals.'],
                ['name' => 'Digital / CD-DVD', 'code' => 'TYP-DIG', 'fine_multiplier' => 1.00, 'icon' => 'fas fa-compact-disc', 'description' => 'Digital learning media and software discs.'],
            ];

            foreach ($defaultTypes as $type) {
                LibraryBookType::create(array_merge($type, ['school_id' => $schoolId, 'status' => 'active']));
            }
        }
    }

    /**
     * Display Library Basics page.
     */
    public function basics(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return redirect()->back()->with('error', 'School identification required.');
        }

        $this->ensureLibraryTablesExist();
        $this->seedDefaultLibraryData($schoolId);

        // Fetch or create student and staff rules
        $studentRule = LibraryRule::getRuleFor($schoolId, 'student');
        $staffRule = LibraryRule::getRuleFor($schoolId, 'staff');

        // Fetch library sections with book counts
        $sections = LibrarySection::where('school_id', $schoolId)
            ->withCount('books')
            ->orderBy('id', 'asc')
            ->get();

        // Fetch book/material types with book counts
        $bookTypes = LibraryBookType::where('school_id', $schoolId)
            ->withCount('books')
            ->orderBy('id', 'asc')
            ->get();

        // Calculate quick summary metrics
        $totalBooks = LibraryBook::where('school_id', $schoolId)->count();
        $totalCopies = (int) LibraryBook::where('school_id', $schoolId)->sum('total_copies');
        $activeBorrows = LibraryTransaction::where('school_id', $schoolId)->where('status', 'issued')->count();
        $overdueBorrows = LibraryTransaction::where('school_id', $schoolId)->where('status', 'overdue')->count();

        // General settings
        $generalSettings = [
            'auto_fine_calc' => SchoolSetting::getValue('library_auto_fine_calc', true, $schoolId),
            'allow_renewals' => SchoolSetting::getValue('library_allow_renewals', true, $schoolId),
            'max_renewals' => SchoolSetting::getValue('library_max_renewals', 2, $schoolId),
            'grace_period' => SchoolSetting::getValue('library_grace_period', 0, $schoolId),
            'lost_book_policy' => SchoolSetting::getValue('library_lost_book_policy', 'fixed', $schoolId),
        ];

        return view('school.library.basics', compact(
            'studentRule',
            'staffRule',
            'sections',
            'bookTypes',
            'totalBooks',
            'totalCopies',
            'activeBorrows',
            'overdueBorrows',
            'generalSettings'
        ));
    }

    /**
     * Save/Update Library Rules for Students and Staff.
     */
    public function saveRules(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized or missing school ID.'], 403);
        }

        $this->ensureLibraryTablesExist();

        $validated = $request->validate([
            // Student rules
            'student_borrow_period' => 'required|integer|min:1|max:365',
            'student_max_books'     => 'required|integer|min:1|max:50',
            'student_late_fine'     => 'required|numeric|min:0',
            'student_late_fine_type'=> 'required|string|in:per_day,fixed_amount,per_week',
            'student_lost_fine'     => 'required|numeric|min:0',
            'student_lost_fine_type'=> 'required|string|in:fixed_amount,percentage_price,full_price_plus_fee',
            'student_damaged_fine'  => 'required|numeric|min:0',
            'student_damaged_fine_type' => 'required|string|in:fixed_amount,percentage_price',

            // Staff rules
            'staff_borrow_period'   => 'required|integer|min:1|max:365',
            'staff_max_books'       => 'required|integer|min:1|max:100',
            'staff_late_fine'       => 'required|numeric|min:0',
            'staff_late_fine_type'  => 'required|string|in:per_day,fixed_amount,per_week',
            'staff_lost_fine'       => 'required|numeric|min:0',
            'staff_lost_fine_type'  => 'required|string|in:fixed_amount,percentage_price,full_price_plus_fee',
            'staff_damaged_fine'    => 'required|numeric|min:0',
            'staff_damaged_fine_type' => 'required|string|in:fixed_amount,percentage_price',

            // Optional General policies
            'grace_period_days'     => 'nullable|integer|min:0|max:30',
            'max_renewal_count'     => 'nullable|integer|min:0|max:10',
            'auto_fine_calc'        => 'nullable|boolean',
        ]);

        DB::beginTransaction();
        try {
            // 1. Update Student Rule
            LibraryRule::updateOrCreate(
                ['school_id' => $schoolId, 'member_type' => 'student'],
                [
                    'borrow_period_days'       => $validated['student_borrow_period'],
                    'max_books_allowed'        => $validated['student_max_books'],
                    'late_fine_amount'         => $validated['student_late_fine'],
                    'late_fine_type'           => $validated['student_late_fine_type'],
                    'lost_book_fine_amount'    => $validated['student_lost_fine'],
                    'lost_book_fine_type'      => $validated['student_lost_fine_type'],
                    'damaged_book_fine_amount' => $validated['student_damaged_fine'],
                    'damaged_book_fine_type'   => $validated['student_damaged_fine_type'],
                    'grace_period_days'        => $request->input('student_grace_period', 0),
                    'max_renewal_count'        => $request->input('student_max_renewals', 2),
                ]
            );

            // 2. Update Staff Rule
            LibraryRule::updateOrCreate(
                ['school_id' => $schoolId, 'member_type' => 'staff'],
                [
                    'borrow_period_days'       => $validated['staff_borrow_period'],
                    'max_books_allowed'        => $validated['staff_max_books'],
                    'late_fine_amount'         => $validated['staff_late_fine'],
                    'late_fine_type'           => $validated['staff_late_fine_type'],
                    'lost_book_fine_amount'    => $validated['staff_lost_fine'],
                    'lost_book_fine_type'      => $validated['staff_lost_fine_type'],
                    'damaged_book_fine_amount' => $validated['staff_damaged_fine'],
                    'damaged_book_fine_type'   => $validated['staff_damaged_fine_type'],
                    'grace_period_days'        => $request->input('staff_grace_period', 0),
                    'max_renewal_count'        => $request->input('staff_max_renewals', 3),
                ]
            );

            // Save global setting preferences
            if ($request->has('auto_fine_calc')) {
                SchoolSetting::setValue('library_auto_fine_calc', (bool)$request->auto_fine_calc, 'library', 'boolean', $schoolId);
            }
            if ($request->has('max_renewal_count')) {
                SchoolSetting::setValue('library_max_renewals', (int)$request->max_renewal_count, 'library', 'integer', $schoolId);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Library rules have been updated and synchronized successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error updating library rules: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new Library Section.
     */
    public function storeSection(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'code'           => 'nullable|string|max:50',
            'rack_location'  => 'nullable|string|max:100',
            'material_scope' => 'nullable|string|max:150',
            'description'    => 'nullable|string|max:500',
            'status'         => 'nullable|string|in:active,inactive',
        ]);

        $section = LibrarySection::create([
            'school_id'      => $schoolId,
            'name'           => $validated['name'],
            'code'           => $validated['code'] ?? ('SEC-' . strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $validated['name']), 0, 4))),
            'rack_location'  => $validated['rack_location'] ?? null,
            'material_scope' => $validated['material_scope'] ?? null,
            'description'    => $validated['description'] ?? null,
            'status'         => $validated['status'] ?? 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Library section created successfully.',
            'data'    => $section,
        ]);
    }

    /**
     * Update an existing Library Section.
     */
    public function updateSection(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $section = LibrarySection::where('school_id', $schoolId)->findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:150',
            'code'           => 'nullable|string|max:50',
            'rack_location'  => 'nullable|string|max:100',
            'material_scope' => 'nullable|string|max:150',
            'description'    => 'nullable|string|max:500',
            'status'         => 'nullable|string|in:active,inactive',
        ]);

        $section->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Library section updated successfully.',
            'data'    => $section,
        ]);
    }

    /**
     * Delete a Library Section.
     */
    public function deleteSection(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $section = LibrarySection::where('school_id', $schoolId)->findOrFail($id);

        // Check if books are assigned to this section
        if (LibraryBook::where('section_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete section because books are currently catalogued under it. Please reassign the books first.',
            ], 422);
        }

        $section->delete();

        return response()->json([
            'success' => true,
            'message' => 'Library section deleted successfully.',
        ]);
    }

    /**
     * Store a new Book / Material Type.
     */
    public function storeBookType(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'name'                         => 'required|string|max:150',
            'code'                         => 'nullable|string|max:50',
            'default_borrow_days_override' => 'nullable|integer|min:1|max:365',
            'fine_multiplier'              => 'nullable|numeric|min:0.1|max:10',
            'icon'                         => 'nullable|string|max:100',
            'description'                  => 'nullable|string|max:500',
            'status'                       => 'nullable|string|in:active,inactive',
        ]);

        $bookType = LibraryBookType::create([
            'school_id'                    => $schoolId,
            'name'                         => $validated['name'],
            'code'                         => $validated['code'] ?? ('TYP-' . strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $validated['name']), 0, 4))),
            'default_borrow_days_override' => $validated['default_borrow_days_override'] ?? null,
            'fine_multiplier'              => $validated['fine_multiplier'] ?? 1.00,
            'icon'                         => $validated['icon'] ?? 'fas fa-book',
            'description'                  => $validated['description'] ?? null,
            'status'                       => $validated['status'] ?? 'active',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Book / Material type created successfully.',
            'data'    => $bookType,
        ]);
    }

    /**
     * Update an existing Book / Material Type.
     */
    public function updateBookType(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $bookType = LibraryBookType::where('school_id', $schoolId)->findOrFail($id);

        $validated = $request->validate([
            'name'                         => 'required|string|max:150',
            'code'                         => 'nullable|string|max:50',
            'default_borrow_days_override' => 'nullable|integer|min:1|max:365',
            'fine_multiplier'              => 'nullable|numeric|min:0.1|max:10',
            'icon'                         => 'nullable|string|max:100',
            'description'                  => 'nullable|string|max:500',
            'status'                       => 'nullable|string|in:active,inactive',
        ]);

        $bookType->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Book / Material type updated successfully.',
            'data'    => $bookType,
        ]);
    }

    /**
     * Delete a Book / Material Type.
     */
    public function deleteBookType(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $bookType = LibraryBookType::where('school_id', $schoolId)->findOrFail($id);

        // Check if books are assigned to this book type
        if (LibraryBook::where('book_type_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete material type because books are linked to it. Please reassign the books first.',
            ], 422);
        }

        $bookType->delete();

        return response()->json([
            'success' => true,
            'message' => 'Material type deleted successfully.',
        ]);
    }

    /**
     * Live Dynamic Late Fine & Borrow Rule Calculator Engine.
     * Connected across Library Basics, Catalogue & Transactions.
     */
    public function calculateFine(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $this->ensureLibraryTablesExist();

        $memberType = strtolower($request->input('member_type', 'student')) === 'staff' ? 'staff' : 'student';
        $issueDateInput = $request->input('issue_date');
        $dueDateInput = $request->input('due_date');
        $returnDateInput = $request->input('return_date', now()->format('Y-m-d'));
        $daysLateManual = $request->input('days_late');
        $condition = $request->input('condition', 'returned'); // returned, lost, damaged
        $bookPrice = floatval($request->input('book_price', 0));
        $bookTypeId = $request->input('book_type_id');

        // Fetch configured rule for this member type
        $rule = LibraryRule::getRuleFor($schoolId, $memberType);

        // Determine borrow period (allow book type override if configured)
        $borrowDays = $rule->borrow_period_days;
        $fineMultiplier = 1.00;
        if ($bookTypeId) {
            $bookType = LibraryBookType::where('school_id', $schoolId)->find($bookTypeId);
            if ($bookType && $bookType->default_borrow_days_override) {
                $borrowDays = $bookType->default_borrow_days_override;
            }
            if ($bookType && $bookType->fine_multiplier) {
                $fineMultiplier = floatval($bookType->fine_multiplier);
            }
        }

        // Calculate late days
        $lateDays = 0;
        if (is_numeric($daysLateManual)) {
            $lateDays = max(0, intval($daysLateManual));
        } elseif ($dueDateInput && $returnDateInput) {
            $due = Carbon::parse($dueDateInput)->startOfDay();
            $ret = Carbon::parse($returnDateInput)->startOfDay();
            if ($ret->greaterThan($due)) {
                $lateDays = $due->diffInDays($ret);
            }
        } elseif ($issueDateInput && $returnDateInput) {
            $issue = Carbon::parse($issueDateInput)->startOfDay();
            $due = $issue->copy()->addDays($borrowDays);
            $ret = Carbon::parse($returnDateInput)->startOfDay();
            if ($ret->greaterThan($due)) {
                $lateDays = $due->diffInDays($ret);
            }
        }

        // Apply grace period
        $effectiveLateDays = max(0, $lateDays - ($rule->grace_period_days ?? 0));

        // Calculate Late Fine
        $lateFine = 0.00;
        if ($effectiveLateDays > 0) {
            if ($rule->late_fine_type === 'per_day') {
                $lateFine = $effectiveLateDays * floatval($rule->late_fine_amount) * $fineMultiplier;
            } elseif ($rule->late_fine_type === 'per_week') {
                $weeks = ceil($effectiveLateDays / 7);
                $lateFine = $weeks * floatval($rule->late_fine_amount) * $fineMultiplier;
            } else { // fixed_amount
                $lateFine = floatval($rule->late_fine_amount) * $fineMultiplier;
            }
        }

        // Calculate Lost / Damaged Book Fine
        $conditionFine = 0.00;
        if ($condition === 'lost') {
            if ($rule->lost_book_fine_type === 'percentage_price') {
                $conditionFine = ($bookPrice * floatval($rule->lost_book_fine_amount)) / 100.00;
            } elseif ($rule->lost_book_fine_type === 'full_price_plus_fee') {
                $conditionFine = $bookPrice + floatval($rule->lost_book_fine_amount);
            } else {
                $conditionFine = floatval($rule->lost_book_fine_amount);
            }
        } elseif ($condition === 'damaged') {
            if ($rule->damaged_book_fine_type === 'percentage_price') {
                $conditionFine = ($bookPrice * floatval($rule->damaged_book_fine_amount)) / 100.00;
            } else {
                $conditionFine = floatval($rule->damaged_book_fine_amount);
            }
        }

        $totalFine = round($lateFine + $conditionFine, 2);

        return response()->json([
            'success'               => true,
            'member_type'           => $memberType,
            'max_books_allowed'     => $rule->max_books_allowed,
            'standard_borrow_days'  => $borrowDays,
            'grace_period_days'     => $rule->grace_period_days ?? 0,
            'late_days'             => $lateDays,
            'effective_late_days'   => $effectiveLateDays,
            'late_fine_rate'        => floatval($rule->late_fine_amount),
            'late_fine_type'        => $rule->late_fine_type,
            'late_fine_amount'      => round($lateFine, 2),
            'condition'             => $condition,
            'condition_fine_amount' => round($conditionFine, 2),
            'total_fine'            => $totalFine,
            'formatted_total_fine'  => '₹ ' . number_format($totalFine, 2),
            'rule_applied'          => [
                'borrow_period'  => $rule->borrow_period_days . ' Days',
                'max_books'      => $rule->max_books_allowed . ' Books',
                'late_fine_rate' => '₹ ' . number_format($rule->late_fine_amount, 2) . ' (' . ucwords(str_replace('_', ' ', $rule->late_fine_type)) . ')',
                'lost_fine'      => '₹ ' . number_format($rule->lost_book_fine_amount, 2) . ' (' . ucwords(str_replace('_', ' ', $rule->lost_book_fine_type)) . ')',
                'damaged_fine'   => '₹ ' . number_format($rule->damaged_book_fine_amount, 2) . ' (' . ucwords(str_replace('_', ' ', $rule->damaged_book_fine_type)) . ')',
            ],
        ]);
    }

    /**
     * Get member borrow summary & limits check.
     */
    public function getMemberBorrowStatus(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $memberType = $request->input('member_type', 'student');
        $identifier = $request->input('identifier'); // Student admission_no or Staff employee_id or ID

        $rule = LibraryRule::getRuleFor($schoolId, $memberType);
        $memberName = 'Unknown Member';
        $memberId = null;
        $activeBorrowsCount = 0;
        $pendingFineTotal = 0.00;

        if ($memberType === 'student') {
            $student = Student::where('school_id', $schoolId)
                ->where(function ($q) use ($identifier) {
                    $q->where('admission_no', $identifier)
                        ->orWhere('id', $identifier);
                })
                ->first();

            if ($student) {
                $memberId = $student->id;
                $memberName = $student->first_name . ' ' . $student->last_name . ' (Adm: ' . ($student->admission_no ?? 'N/A') . ')';
                $activeBorrowsCount = LibraryTransaction::where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('status', 'issued')
                    ->count();

                $pendingFineTotal = (float) LibraryTransaction::where('school_id', $schoolId)
                    ->where('student_id', $student->id)
                    ->where('fine_status', 'pending')
                    ->sum('total_fine');
            }
        } else {
            $staff = Staff::where('school_id', $schoolId)
                ->where(function ($q) use ($identifier) {
                    $q->where('employee_code', $identifier)
                        ->orWhere('id', $identifier);
                })
                ->first();

            if ($staff) {
                $memberId = $staff->id;
                $memberName = $staff->first_name . ' ' . $staff->last_name . ' (' . ($staff->employee_code ?? 'Staff') . ')';
                $activeBorrowsCount = LibraryTransaction::where('school_id', $schoolId)
                    ->where('staff_id', $staff->id)
                    ->where('status', 'issued')
                    ->count();

                $pendingFineTotal = (float) LibraryTransaction::where('school_id', $schoolId)
                    ->where('staff_id', $staff->id)
                    ->where('fine_status', 'pending')
                    ->sum('total_fine');
            }
        }

        $canBorrow = ($activeBorrowsCount < $rule->max_books_allowed) && ($pendingFineTotal <= floatval($rule->max_fine_threshold ?? 100));

        return response()->json([
            'success'               => true,
            'member_id'             => $memberId,
            'member_name'           => $memberName,
            'member_type'           => $memberType,
            'active_borrows_count'  => $activeBorrowsCount,
            'max_books_allowed'     => $rule->max_books_allowed,
            'remaining_slots'       => max(0, $rule->max_books_allowed - $activeBorrowsCount),
            'borrow_period_days'    => $rule->borrow_period_days,
            'pending_fine_total'    => $pendingFineTotal,
            'can_borrow'            => $canBorrow,
            'late_fine_rate_desc'   => '₹ ' . number_format($rule->late_fine_amount, 2) . ' / ' . str_replace('_', ' ', $rule->late_fine_type),
        ]);
    }

    /**
     * Display Catalogue page.
     */
    public function catalogue(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return redirect()->back()->with('error', 'School identification required.');
        }

        $this->ensureLibraryTablesExist();
        $this->seedDefaultLibraryData($schoolId);

        $search = $request->input('search');
        $bookTypeId = $request->input('material_type');
        $sectionId = $request->input('section_id');
        $status = $request->input('status');
        $activeTab = $request->input('tab', 'book_id_wise'); // 'book_id_wise' or 'accession_wise'

        $query = LibraryBook::where('school_id', $schoolId)
            ->with(['section:id,name,rack_location', 'bookType:id,name,code,icon']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('accession_no', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%")
                  ->orWhere('publisher', 'like', "%{$search}%")
                  ->orWhere('rack_location', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        if (!empty($bookTypeId)) {
            $query->where('book_type_id', $bookTypeId);
        }

        if (!empty($sectionId)) {
            $query->where('section_id', $sectionId);
        }

        if (!empty($status)) {
            $query->where('status', $status);
        }

        if ($activeTab === 'accession_wise') {
            $query->orderBy('accession_no', 'asc');
        } else {
            $query->orderBy('id', 'desc');
        }

        $books = $query->paginate(50)->withQueryString();

        $sections = LibrarySection::where('school_id', $schoolId)->where('status', 'active')->orderBy('name')->get();
        $bookTypes = LibraryBookType::where('school_id', $schoolId)->where('status', 'active')->orderBy('name')->get();

        $totalBooksCount = LibraryBook::where('school_id', $schoolId)->count();
        $totalCopiesCount = (int) LibraryBook::where('school_id', $schoolId)->sum('total_copies');
        $availableCopiesCount = (int) LibraryBook::where('school_id', $schoolId)->sum('available_copies');
        $issuedCopiesCount = (int) LibraryBook::where('school_id', $schoolId)->sum('issued_copies');

        return view('school.library.catalogue', compact(
            'books',
            'sections',
            'bookTypes',
            'activeTab',
            'search',
            'bookTypeId',
            'sectionId',
            'totalBooksCount',
            'totalCopiesCount',
            'availableCopiesCount',
            'issuedCopiesCount'
        ));
    }

    /**
     * Store a new book in the catalogue.
     */
    public function storeBook(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'author'           => 'required|string|max:200',
            'accession_no'     => 'nullable|string|max:100',
            'isbn'             => 'nullable|string|max:50',
            'section_id'       => 'nullable|exists:library_sections,id',
            'book_type_id'     => 'nullable|exists:library_book_types,id',
            'publisher'        => 'nullable|string|max:200',
            'edition'          => 'nullable|string|max:50',
            'publication_year' => 'nullable|string|max:20',
            'rack_location'    => 'nullable|string|max:100',
            'price'            => 'nullable|numeric|min:0',
            'total_copies'     => 'required|integer|min:1|max:1000',
            'language'         => 'nullable|string|max:50',
            'pages'            => 'nullable|integer|min:1',
            'description'      => 'nullable|string|max:1000',
            'status'           => 'nullable|string|in:available,archived,out_of_stock',
        ]);

        // Validate Section & Book Type belong strictly to this school
        if (!empty($validated['section_id'])) {
            $secExists = LibrarySection::where('school_id', $schoolId)->where('id', $validated['section_id'])->exists();
            if (!$secExists) {
                return response()->json(['success' => false, 'message' => 'Selected section does not belong to your school library.'], 422);
            }
        }

        if (!empty($validated['book_type_id'])) {
            $typeExists = LibraryBookType::where('school_id', $schoolId)->where('id', $validated['book_type_id'])->exists();
            if (!$typeExists) {
                return response()->json(['success' => false, 'message' => 'Selected material type does not belong to your school library.'], 422);
            }
        }

        // Auto-generate Accession Number if empty, or verify uniqueness within this school
        if (empty($validated['accession_no'])) {
            $lastId = LibraryBook::where('school_id', $schoolId)->max('id') ?? 0;
            $validated['accession_no'] = 'ACC-' . str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
        } else {
            $isDuplicate = LibraryBook::where('school_id', $schoolId)->where('accession_no', $validated['accession_no'])->exists();
            if ($isDuplicate) {
                return response()->json(['success' => false, 'message' => 'Accession Number "' . $validated['accession_no'] . '" already exists in your school library.'], 422);
            }
        }

        $copies = intval($validated['total_copies']);

        $book = LibraryBook::create([
            'school_id'        => $schoolId,
            'title'            => $validated['title'],
            'author'           => $validated['author'],
            'accession_no'     => $validated['accession_no'],
            'isbn'             => $validated['isbn'] ?? null,
            'section_id'       => $validated['section_id'] ?? null,
            'book_type_id'     => $validated['book_type_id'] ?? null,
            'publisher'        => $validated['publisher'] ?? null,
            'edition'          => $validated['edition'] ?? null,
            'publication_year' => $validated['publication_year'] ?? null,
            'rack_location'    => $validated['rack_location'] ?? null,
            'price'            => $validated['price'] ?? 0.00,
            'total_copies'     => $copies,
            'available_copies' => $copies,
            'issued_copies'    => 0,
            'lost_copies'      => 0,
            'damaged_copies'   => 0,
            'language'         => $validated['language'] ?? 'English',
            'pages'            => $validated['pages'] ?? null,
            'description'      => $validated['description'] ?? null,
            'status'           => $validated['status'] ?? 'available',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Book "' . $book->title . '" added to your school catalogue successfully!',
            'data'    => $book->load(['section', 'bookType']),
        ]);
    }

    /**
     * Update an existing book in the catalogue.
     */
    public function updateBook(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $book = LibraryBook::where('school_id', $schoolId)->findOrFail($id);

        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'author'           => 'required|string|max:200',
            'accession_no'     => 'nullable|string|max:100',
            'isbn'             => 'nullable|string|max:50',
            'section_id'       => 'nullable|exists:library_sections,id',
            'book_type_id'     => 'nullable|exists:library_book_types,id',
            'publisher'        => 'nullable|string|max:200',
            'edition'          => 'nullable|string|max:50',
            'publication_year' => 'nullable|string|max:20',
            'rack_location'    => 'nullable|string|max:100',
            'price'            => 'nullable|numeric|min:0',
            'total_copies'     => 'required|integer|min:1|max:1000',
            'language'         => 'nullable|string|max:50',
            'pages'            => 'nullable|integer|min:1',
            'description'      => 'nullable|string|max:1000',
            'status'           => 'nullable|string|in:available,archived,out_of_stock',
        ]);

        // Validate Section & Book Type belong strictly to this school
        if (!empty($validated['section_id'])) {
            $secExists = LibrarySection::where('school_id', $schoolId)->where('id', $validated['section_id'])->exists();
            if (!$secExists) {
                return response()->json(['success' => false, 'message' => 'Selected section does not belong to your school library.'], 422);
            }
        }

        if (!empty($validated['book_type_id'])) {
            $typeExists = LibraryBookType::where('school_id', $schoolId)->where('id', $validated['book_type_id'])->exists();
            if (!$typeExists) {
                return response()->json(['success' => false, 'message' => 'Selected material type does not belong to your school library.'], 422);
            }
        }

        if (!empty($validated['accession_no'])) {
            $isDuplicate = LibraryBook::where('school_id', $schoolId)
                ->where('accession_no', $validated['accession_no'])
                ->where('id', '!=', $id)
                ->exists();
            if ($isDuplicate) {
                return response()->json(['success' => false, 'message' => 'Accession Number "' . $validated['accession_no'] . '" is already in use by another book in your school library.'], 422);
            }
        }

        $newTotal = intval($validated['total_copies']);
        $diff = $newTotal - $book->total_copies;
        $newAvailable = max(0, $book->available_copies + $diff);

        $validated['total_copies'] = $newTotal;
        $validated['available_copies'] = $newAvailable;

        $book->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Book details updated successfully!',
            'data'    => $book->load(['section', 'bookType']),
        ]);
    }

    /**
     * Delete a book from catalogue.
     */
    public function deleteBook(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $book = LibraryBook::where('school_id', $schoolId)->findOrFail($id);

        // Check if book currently has active loans
        if (LibraryTransaction::where('book_id', $id)->where('status', 'issued')->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete this book because copies are currently issued to students or staff. Please return the copies first.',
            ], 422);
        }

        $title = $book->title;
        $book->delete();

        return response()->json([
            'success' => true,
            'message' => 'Book "' . $title . '" removed from catalogue.',
        ]);
    }

    /**
     * Bulk Upload Books from CSV spreadsheet.
     */
    public function bulkUploadBooks(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        $path = $file->getRealPath();
        $handle = fopen($path, 'r');

        if (!$handle) {
            return response()->json(['success' => false, 'message' => 'Unable to read uploaded CSV file.'], 422);
        }

        $header = fgetcsv($handle, 2000, ',');
        if (!$header) {
            fclose($handle);
            return response()->json(['success' => false, 'message' => 'Empty CSV file.'], 422);
        }

        // Clean and normalize header keys
        $cleanHeader = array_map(function ($h) {
            return strtolower(trim(preg_replace('/[^a-zA-Z0-9_]/', '_', $h)));
        }, $header);

        $sectionsMap = LibrarySection::where('school_id', $schoolId)->pluck('id', 'name')->toArray();
        $bookTypesMap = LibraryBookType::where('school_id', $schoolId)->pluck('id', 'name')->toArray();

        $importedCount = 0;
        $skippedCount = 0;

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 2000, ',')) !== false) {
                if (count($row) < 2 || empty(trim($row[0]))) {
                    continue;
                }

                $data = [];
                foreach ($cleanHeader as $idx => $key) {
                    $data[$key] = isset($row[$idx]) ? trim($row[$idx]) : '';
                }

                $title = $data['title'] ?? $data['book_title'] ?? '';
                $author = $data['author'] ?? $data['author_name'] ?? 'Unknown Author';

                if (empty($title)) {
                    $skippedCount++;
                    continue;
                }

                $accessionNo = $data['accession_no'] ?? $data['accession_number'] ?? $data['book_id'] ?? '';
                if (empty($accessionNo)) {
                    $lastId = LibraryBook::where('school_id', $schoolId)->max('id') ?? 0;
                    $accessionNo = 'ACC-' . str_pad($lastId + $importedCount + 1, 5, '0', STR_PAD_LEFT);
                }

                // Match Section
                $sectionName = $data['section'] ?? $data['library_section'] ?? $data['category'] ?? '';
                $sectionId = null;
                if (!empty($sectionName) && isset($sectionsMap[$sectionName])) {
                    $sectionId = $sectionsMap[$sectionName];
                }

                // Match Book Type
                $typeName = $data['material_type'] ?? $data['book_type'] ?? $data['type'] ?? '';
                $bookTypeId = null;
                if (!empty($typeName) && isset($bookTypesMap[$typeName])) {
                    $bookTypeId = $bookTypesMap[$typeName];
                }

                $totalCopies = max(1, intval($data['total_copies'] ?? $data['copies'] ?? 1));
                $price = floatval($data['price'] ?? $data['book_price'] ?? 0.00);

                LibraryBook::create([
                    'school_id'        => $schoolId,
                    'title'            => $title,
                    'author'           => $author,
                    'accession_no'     => $accessionNo,
                    'isbn'             => $data['isbn'] ?? $data['isbn_issn'] ?? null,
                    'section_id'       => $sectionId,
                    'book_type_id'     => $bookTypeId,
                    'publisher'        => $data['publisher'] ?? null,
                    'edition'          => $data['edition'] ?? null,
                    'publication_year' => $data['publication_year'] ?? $data['year'] ?? null,
                    'rack_location'    => $data['rack_location'] ?? $data['location'] ?? null,
                    'price'            => $price,
                    'total_copies'     => $totalCopies,
                    'available_copies' => $totalCopies,
                    'issued_copies'    => 0,
                    'language'         => $data['language'] ?? 'English',
                    'pages'            => intval($data['pages'] ?? 0) ?: null,
                    'description'      => $data['description'] ?? null,
                    'status'           => 'available',
                ]);

                $importedCount++;
            }

            fclose($handle);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Successfully imported {$importedCount} books into the catalogue!" . ($skippedCount > 0 ? " ({$skippedCount} invalid rows skipped)" : ""),
                'imported_count' => $importedCount,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            if (is_resource($handle)) fclose($handle);
            return response()->json([
                'success' => false,
                'message' => 'Error during bulk upload: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export catalogue books to CSV.
     */
    public function exportBooksCsv(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return redirect()->back();
        }

        $search = $request->input('search');
        $bookTypeId = $request->input('material_type');
        $sectionId = $request->input('section_id');

        $query = LibraryBook::where('school_id', $schoolId)
            ->with(['section:id,name', 'bookType:id,name']);

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('author', 'like', "%{$search}%")
                  ->orWhere('accession_no', 'like', "%{$search}%")
                  ->orWhere('isbn', 'like', "%{$search}%");
            });
        }

        if (!empty($bookTypeId)) $query->where('book_type_id', $bookTypeId);
        if (!empty($sectionId)) $query->where('section_id', $sectionId);

        $books = $query->orderBy('id', 'desc')->get();

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="library_catalogue_export_' . date('Y-m-d_His') . '.csv"',
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($books) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Book ID',
                'Accession No',
                'Title',
                'Author',
                'ISBN',
                'Library Section',
                'Material Type',
                'Edition',
                'Publisher',
                'Publication Year',
                'Rack Location',
                'Price (INR)',
                'Total Copies',
                'Available Copies',
                'Issued Copies',
                'Status'
            ]);

            foreach ($books as $b) {
                fputcsv($handle, [
                    'BK-' . str_pad($b->id, 5, '0', STR_PAD_LEFT),
                    $b->accession_no,
                    $b->title,
                    $b->author,
                    $b->isbn,
                    $b->section?->name ?? 'Unassigned',
                    $b->bookType?->name ?? 'Standard',
                    $b->edition,
                    $b->publisher,
                    $b->publication_year,
                    $b->rack_location,
                    $b->price,
                    $b->total_copies,
                    $b->available_copies,
                    $b->issued_copies,
                    ucfirst($b->status),
                ]);
            }
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Download Sample CSV for Bulk Upload.
     */
    public function sampleBooksCsv()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="sample_books_bulk_import.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'Title',
                'Author',
                'Accession_No',
                'ISBN',
                'Section',
                'Material_Type',
                'Edition',
                'Publisher',
                'Publication_Year',
                'Rack_Location',
                'Price',
                'Total_Copies',
                'Language',
                'Pages',
                'Description'
            ]);

            // Sample rows
            fputcsv($handle, [
                'Concepts of Physics (Vol 1)',
                'H.C. Verma',
                'ACC-00101',
                '9788177091877',
                'Science & Technology',
                'Textbook',
                '1st Edition',
                'Bharati Bhawan',
                '2023',
                'Rack A1-Shelf 2',
                '450.00',
                '10',
                'English',
                '462',
                'Standard physics reference book for high school.'
            ]);

            fputcsv($handle, [
                'To Kill a Mockingbird',
                'Harper Lee',
                'ACC-00102',
                '9780061120084',
                'Literature & Fiction',
                'Fiction & Literature',
                'Reprint',
                'HarperCollins',
                '2020',
                'Rack B2-Shelf 1',
                '320.00',
                '5',
                'English',
                '336',
                'Classic English literature novel.'
            ]);

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display Transactions page.
     */
    public function transactions(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return redirect()->back()->with('error', 'School identification required.');
        }

        $this->ensureLibraryTablesExist();
        $this->seedDefaultLibraryData($schoolId);

        $studentRule = LibraryRule::getRuleFor($schoolId, 'student');
        $staffRule = LibraryRule::getRuleFor($schoolId, 'staff');

        $tab = $request->query('tab', 'students');
        $statusFilter = $request->query('status', 'all');
        $classFilter = $request->query('class_id');
        $sectionFilter = $request->query('section_id');
        $fromDate = $request->query('from_date');
        $toDate = $request->query('to_date');
        $search = $request->query('search');

        $studentRule = LibraryRule::getRuleFor($schoolId, 'student');
        $staffRule = LibraryRule::getRuleFor($schoolId, 'staff');

        // Dynamic Overdue Fine Calculator cron
        $today = Carbon::today();
        try {
            $overduePending = LibraryTransaction::where('school_id', $schoolId)
                ->where('status', 'issued')
                ->where('due_date', '<', $today)
                ->with(['book.bookType'])
                ->get();

            foreach ($overduePending as $txn) {
                $dueDate = Carbon::parse($txn->due_date);
                $lateDays = $dueDate->diffInDays($today);
                $rule = $txn->member_type === 'staff' ? $staffRule : $studentRule;
                $grace = (int) ($rule->grace_period_days ?? 0);

                if ($lateDays > $grace) {
                    $multiplier = (float) ($txn->book?->bookType?->fine_multiplier ?? 1.00);
                    $ratePerDay = (float) ($rule->late_fine_amount ?? 5.00);
                    $fineAmount = round($lateDays * $ratePerDay * $multiplier, 2);

                    if ($rule->max_fine_threshold && $fineAmount > (float)$rule->max_fine_threshold) {
                        $fineAmount = (float)$rule->max_fine_threshold;
                    }

                    $txn->update([
                        'status' => 'overdue',
                        'late_days' => $lateDays,
                        'late_fine_amount' => $fineAmount,
                        'total_fine' => $fineAmount + (float)$txn->damage_lost_fine,
                        'fine_status' => $txn->fine_status === 'paid' ? 'paid' : 'pending'
                    ]);
                }
            }
        } catch (\Throwable $e) {}

        // Base Query
        $query = LibraryTransaction::where('school_id', $schoolId)
            ->with(['book.section', 'book.bookType', 'student.schoolClass', 'student.section', 'staff.designation', 'issuer']);

        if ($tab === 'staffs') {
            $query->where('member_type', 'staff');
        } else {
            $query->where('member_type', 'student');
        }

        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('status', $statusFilter);
        }

        if ($fromDate) {
            $query->whereDate('issue_date', '>=', $fromDate);
        }

        if ($toDate) {
            $query->whereDate('issue_date', '<=', $toDate);
        }

        if ($classFilter && $tab === 'students') {
            $query->whereHas('student', function ($q) use ($classFilter) {
                $q->where('class_id', $classFilter);
            });
        }

        if ($sectionFilter && $tab === 'students') {
            $query->whereHas('student', function ($q) use ($sectionFilter) {
                $q->where('section_id', $sectionFilter);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('transaction_code', 'like', "%{$search}%")
                  ->orWhereHas('book', function ($bq) use ($search) {
                      $bq->where('title', 'like', "%{$search}%")
                         ->orWhere('author', 'like', "%{$search}%")
                         ->orWhere('isbn', 'like', "%{$search}%")
                         ->orWhere('accession_no', 'like', "%{$search}%")
                         ->orWhere('id', 'like', "%{$search}%");
                  })
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('first_name', 'like', "%{$search}%")
                         ->orWhere('last_name', 'like', "%{$search}%")
                         ->orWhere('admission_number', 'like', "%{$search}%")
                         ->orWhere('roll_number', 'like', "%{$search}%");
                  })
                  ->orWhereHas('staff', function ($stq) use ($search) {
                      $stq->where('first_name', 'like', "%{$search}%")
                          ->orWhere('last_name', 'like', "%{$search}%")
                          ->orWhere('employee_id', 'like', "%{$search}%");
                  });
            });
        }

        try {
            $transactions = $query->orderBy('id', 'desc')->paginate(15)->appends($request->all());
        } catch (\Throwable $e) {
            $transactions = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
        }

        // Counts for tabs and metrics
        $studentTxnCount = 0;
        $staffTxnCount = 0;
        $activeBorrowCount = 0;
        $overdueCount = 0;
        $returnedTodayCount = 0;

        try {
            $studentTxnCount = LibraryTransaction::where('school_id', $schoolId)->where('member_type', 'student')->count();
            $staffTxnCount = LibraryTransaction::where('school_id', $schoolId)->where('member_type', 'staff')->count();
            $activeBorrowCount = LibraryTransaction::where('school_id', $schoolId)->whereIn('status', ['issued', 'overdue'])->count();
            $overdueCount = LibraryTransaction::where('school_id', $schoolId)->where('status', 'overdue')->count();
            $returnedTodayCount = LibraryTransaction::where('school_id', $schoolId)->where('status', 'returned')->whereDate('return_date', $today)->count();
        } catch (\Throwable $e) {}

        // Dropdown entities
        $classes = SchoolClass::where('school_id', $schoolId)->orderBy('name', 'asc')->get();
        $sections = Section::where('school_id', $schoolId)->orderBy('name', 'asc')->get();
        $academicSessions = AcademicSession::where('school_id', $schoolId)->orderBy('id', 'desc')->get();
        $availableBooks = LibraryBook::where('school_id', $schoolId)->where('available_copies', '>', 0)->orderBy('title', 'asc')->get();

        return view('school.library.transactions', compact(
            'transactions',
            'tab',
            'statusFilter',
            'classFilter',
            'sectionFilter',
            'fromDate',
            'toDate',
            'search',
            'studentRule',
            'staffRule',
            'studentTxnCount',
            'staffTxnCount',
            'activeBorrowCount',
            'overdueCount',
            'returnedTodayCount',
            'classes',
            'sections',
            'academicSessions',
            'availableBooks'
        ));
    }

    /**
     * Issue one or multiple books to student or staff member.
     */
    public function issueBook(Request $request)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['success' => false, 'message' => 'School identification required.'], 403);
        }

        $this->ensureLibraryTablesExist();

        $validator = Validator::make($request->all(), [
            'member_type' => 'required|in:student,staff',
            'student_id' => 'nullable|integer',
            'staff_id' => 'nullable|integer',
            'book_id' => 'nullable|integer',
            'book_ids' => 'nullable|array',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'remarks' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $memberType = $request->input('member_type', 'student');
        $studentId = $request->input('student_id');
        $staffId = $request->input('staff_id');

        if ($memberType === 'student' && empty($studentId)) {
            return response()->json(['success' => false, 'message' => 'Please search and select a student from the list.'], 422);
        }

        if ($memberType === 'staff' && empty($staffId)) {
            return response()->json(['success' => false, 'message' => 'Please search and select a staff member from the list.'], 422);
        }

        $rawBookIds = $request->input('book_ids');
        $rawBookId = $request->input('book_id');

        $bookIds = [];
        if (is_array($rawBookIds) && count($rawBookIds) > 0) {
            $bookIds = array_filter(array_map('intval', $rawBookIds));
        } elseif ($rawBookId) {
            $bookIds = [(int)$rawBookId];
        }

        if (empty($bookIds)) {
            return response()->json(['success' => false, 'message' => 'Please search and add at least one book to the issue list.'], 422);
        }

        $rule = $memberType === 'staff' 
            ? LibraryRule::getRuleFor((int)$schoolId, 'staff') 
            : LibraryRule::getRuleFor((int)$schoolId, 'student');

        $borrowerKey = $memberType === 'staff' ? 'staff_id' : 'student_id';
        $borrowerVal = $memberType === 'staff' ? $staffId : $studentId;

        // Fetch live columns of library_transactions
        try {
            $existingCols = collect(DB::select("SHOW COLUMNS FROM `library_transactions`"))->pluck('Field')->toArray();
        } catch (\Throwable $e) {
            $existingCols = [];
        }
        $existingColsLower = array_map('strtolower', $existingCols);

        // Safe count query
        $activeBorrowCount = 0;
        try {
            $activeBorrowQuery = DB::table('library_transactions');
            if (in_array('school_id', $existingColsLower)) {
                $activeBorrowQuery->where('school_id', $schoolId);
            }
            if (in_array('member_type', $existingColsLower)) {
                $activeBorrowQuery->where('member_type', $memberType);
            }
            if (in_array(strtolower($borrowerKey), $existingColsLower)) {
                $activeBorrowQuery->where($borrowerKey, $borrowerVal);
            }
            if (in_array('status', $existingColsLower)) {
                $activeBorrowQuery->whereIn('status', ['issued', 'overdue']);
            }
            $activeBorrowCount = $activeBorrowQuery->count();
        } catch (\Throwable $e) {
            $activeBorrowCount = 0;
        }

        $maxAllowed = (int) ($rule->max_books_allowed ?? 3);
        $booksToIssueCount = count($bookIds);

        if (($activeBorrowCount + $booksToIssueCount) > $maxAllowed) {
            $availableQuota = max(0, $maxAllowed - $activeBorrowCount);
            return response()->json([
                'success' => false,
                'message' => "Borrowing quota exceeded! This {$memberType} currently has {$activeBorrowCount} active loans and can only borrow {$availableQuota} more book(s) (Max allowed: {$maxAllowed})."
            ], 422);
        }

        $issuedTransactions = [];
        $issuedTitles = [];

        DB::beginTransaction();
        try {
            foreach ($bookIds as $bId) {
                $book = LibraryBook::where('school_id', $schoolId)->find($bId);
                if (!$book) {
                    throw new \Exception("Book with ID #{$bId} was not found in library catalogue.");
                }

                if ($book->available_copies < 1) {
                    throw new \Exception("No copies of \"{$book->title}\" are currently available in the library.");
                }

                $transactionCode = 'TXN-' . date('Ymd') . '-' . strtoupper(Str::random(4));

                $rawTxnData = [
                    'school_id' => (int)$schoolId,
                    'transaction_code' => $transactionCode,
                    'book_id' => (int)$book->id,
                    'member_type' => $memberType,
                    'student_id' => $memberType === 'student' ? (int)$studentId : null,
                    'staff_id' => $memberType === 'staff' ? (int)$staffId : null,
                    'issue_date' => $request->input('issue_date', date('Y-m-d')),
                    'due_date' => $request->input('due_date', date('Y-m-d', strtotime('+14 days'))),
                    'return_date' => null,
                    'renewed_count' => 0,
                    'status' => 'issued',
                    'late_days' => 0,
                    'late_fine_amount' => 0.00,
                    'damage_lost_fine' => 0.00,
                    'total_fine' => 0.00,
                    'fine_status' => 'none',
                    'payment_date' => null,
                    'remarks' => $request->input('remarks'),
                    'issued_by' => Auth::id() ?: null,
                    'received_by' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Fetch detailed column metadata from live table
                $columnsInfo = DB::select("SHOW COLUMNS FROM `library_transactions`");
                $columnsMap = [];
                $requiredCols = [];
                foreach ($columnsInfo as $colInfo) {
                    $field = $colInfo->Field;
                    $type = strtolower($colInfo->Type);
                    $columnsMap[strtolower($field)] = $field;
                    if ($colInfo->Null === 'NO' && $colInfo->Default === null && $colInfo->Extra !== 'auto_increment') {
                        $requiredCols[$field] = $type;
                    }
                }

                // Match dynamically against live columns
                $safeInsertData = [];
                foreach ($rawTxnData as $k => $v) {
                    $lowerK = strtolower($k);
                    if (isset($columnsMap[$lowerK])) {
                        $actualColName = $columnsMap[$lowerK];
                        $safeInsertData[$actualColName] = $v;
                    }
                }

                // Fulfill any required NOT NULL columns
                foreach ($requiredCols as $reqField => $reqType) {
                    if (!array_key_exists($reqField, $safeInsertData) || $safeInsertData[$reqField] === null) {
                        if (str_contains($reqType, 'int') || str_contains($reqType, 'decimal') || str_contains($reqType, 'float') || str_contains($reqType, 'double')) {
                            $safeInsertData[$reqField] = 0;
                        } elseif (str_contains($reqType, 'date')) {
                            $safeInsertData[$reqField] = date('Y-m-d');
                        } else {
                            $safeInsertData[$reqField] = '';
                        }
                    }
                }

                // Temporarily disable foreign key checks to prevent legacy constraint mismatches
                try {
                    DB::statement('SET FOREIGN_KEY_CHECKS=0;');
                } catch (\Throwable $e) {}

                try {
                    $txnId = DB::table('library_transactions')->insertGetId($safeInsertData);
                } catch (\Throwable $insertEx) {
                    // Fallback to essential columns
                    $minimalData = [
                        'school_id' => (int)$schoolId,
                        'transaction_code' => $transactionCode,
                        'book_id' => (int)$book->id,
                        'member_type' => $memberType,
                        $borrowerKey => (int)$borrowerVal,
                        'issue_date' => $request->input('issue_date', date('Y-m-d')),
                        'due_date' => $request->input('due_date', date('Y-m-d', strtotime('+14 days'))),
                        'status' => 'issued',
                    ];
                    $safeMinimal = [];
                    foreach ($minimalData as $mk => $mv) {
                        if (isset($columnsMap[strtolower($mk)])) {
                            $safeMinimal[$columnsMap[strtolower($mk)]] = $mv;
                        }
                    }
                    $txnId = DB::table('library_transactions')->insertGetId($safeMinimal);
                }

                try {
                    DB::statement('SET FOREIGN_KEY_CHECKS=1;');
                } catch (\Throwable $e) {}

                $txn = LibraryTransaction::find($txnId);

                $book->available_copies = max(0, ((int)$book->available_copies) - 1);
                $book->issued_copies = ((int)$book->issued_copies) + 1;
                $book->save();

                $issuedTransactions[] = $txn ?? ['id' => $txnId];
                $issuedTitles[] = $book->title;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            try { DB::statement('SET FOREIGN_KEY_CHECKS=1;'); } catch (\Throwable $ex) {}
            Log::error('Issue Book Transaction Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        if ($memberType === 'student') {
            $student = Student::where('school_id', $schoolId)->find($studentId);
            $borrowerName = $student ? trim($student->first_name . ' ' . $student->last_name) : 'Student';
        } else {
            $staff = Staff::where('school_id', $schoolId)->find($staffId);
            $borrowerName = $staff ? trim($staff->first_name . ' ' . $staff->last_name) : 'Staff';
        }

        $issuedIds = collect($issuedTransactions)->map(function($t) {
            return is_object($t) ? $t->id : ($t['id'] ?? null);
        })->filter()->values();

        return response()->json([
            'success' => true,
            'message' => "Successfully issued {$booksSummary} to {$borrowerName} (Due: " . Carbon::parse($request->input('due_date'))->format('d M Y') . ").",
            'data' => $issuedTransactions,
            'issued_ids' => $issuedIds
        ]);
    }

    /**
     * Process return of an issued book with dynamic fine calculation.
     */
    public function returnBook(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['success' => false, 'message' => 'School identification required.'], 403);
        }

        $txn = LibraryTransaction::where('school_id', $schoolId)->with(['book.bookType'])->find($id);
        if (!$txn) {
            return response()->json(['success' => false, 'message' => 'Transaction record not found.'], 404);
        }

        if ($txn->status === 'returned') {
            return response()->json(['success' => false, 'message' => 'This book transaction has already been returned.'], 422);
        }

        $validated = $request->validate([
            'return_date' => 'nullable|date',
            'condition' => 'required|in:normal,damaged,lost',
            'damage_fine' => 'nullable|numeric|min:0',
            'late_fine_override' => 'nullable|numeric|min:0',
            'fine_status' => 'required|in:paid,waived,pending,none',
            'remarks' => 'nullable|string|max:500'
        ]);

        $returnDate = $validated['return_date'] ? Carbon::parse($validated['return_date']) : Carbon::today();
        $dueDate = Carbon::parse($txn->due_date);
        $lateDays = max(0, $dueDate->diffInDays($returnDate, false));

        $rule = $txn->member_type === 'staff' 
            ? LibraryRule::getRuleFor($schoolId, 'staff') 
            : LibraryRule::getRuleFor($schoolId, 'student');

        $grace = (int) ($rule->grace_period_days ?? 0);
        $lateFine = 0.00;

        if (isset($validated['late_fine_override'])) {
            $lateFine = (float) $validated['late_fine_override'];
        } elseif ($lateDays > $grace) {
            $multiplier = (float) ($txn->book?->bookType?->fine_multiplier ?? 1.00);
            $ratePerDay = (float) ($rule->late_fine_amount ?? 5.00);
            $lateFine = round($lateDays * $ratePerDay * $multiplier, 2);

            if ($rule->max_fine_threshold && $lateFine > (float)$rule->max_fine_threshold) {
                $lateFine = (float)$rule->max_fine_threshold;
            }
        }

        $damageFine = 0.00;
        $book = $txn->book;

        if ($validated['condition'] === 'damaged') {
            $damageFine = isset($validated['damage_fine']) 
                ? (float) $validated['damage_fine'] 
                : (float) ($rule->damaged_book_fine_amount ?? 100.00);
            if ($book) {
                $book->decrement('issued_copies');
                $book->increment('damaged_copies');
            }
        } elseif ($validated['condition'] === 'lost') {
            $damageFine = isset($validated['damage_fine']) 
                ? (float) $validated['damage_fine'] 
                : (float) ($rule->lost_book_fine_amount ?? 200.00);
            if ($book) {
                $book->decrement('issued_copies');
                $book->increment('lost_copies');
            }
        } else {
            // Normal condition
            if ($book) {
                $book->decrement('issued_copies');
                $book->increment('available_copies');
            }
        }

        if ($book) {
            $book->save();
        }

        $totalFine = round($lateFine + $damageFine, 2);
        $fineStatus = $totalFine > 0 ? $validated['fine_status'] : 'none';

        $txn->update([
            'return_date' => $returnDate->toDateString(),
            'status' => 'returned',
            'late_days' => $lateDays,
            'late_fine_amount' => $lateFine,
            'damage_lost_fine' => $damageFine,
            'total_fine' => $totalFine,
            'fine_status' => $fineStatus,
            'payment_date' => $fineStatus === 'paid' ? Carbon::today() : null,
            'received_by' => Auth::id(),
            'remarks' => $validated['remarks'] ?? $txn->remarks
        ]);

        return response()->json([
            'success' => true,
            'message' => "Book returned successfully. Total Fine: ₹" . number_format($totalFine, 2) . " ({$fineStatus})",
            'data' => $txn
        ]);
    }

    /**
     * Renew an issued book loan period.
     */
    public function renewBook(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        if (!$schoolId) {
            return response()->json(['success' => false, 'message' => 'School identification required.'], 403);
        }

        $txn = LibraryTransaction::where('school_id', $schoolId)->find($id);
        if (!$txn) {
            return response()->json(['success' => false, 'message' => 'Transaction not found.'], 404);
        }

        if ($txn->status === 'returned') {
            return response()->json(['success' => false, 'message' => 'Cannot renew a book that is already returned.'], 422);
        }

        $rule = $txn->member_type === 'staff' 
            ? LibraryRule::getRuleFor($schoolId, 'staff') 
            : LibraryRule::getRuleFor($schoolId, 'student');

        $maxRenewals = (int) ($rule->max_renewal_count ?? 2);
        if ($txn->renewed_count >= $maxRenewals) {
            return response()->json(['success' => false, 'message' => "Maximum loan renewal limit reached ({$maxRenewals} times)."], 422);
        }

        $loanDays = (int) ($rule->borrow_period_days ?? 14);
        $newDueDate = Carbon::parse($txn->due_date)->addDays($loanDays);

        $txn->update([
            'due_date' => $newDueDate->toDateString(),
            'renewed_count' => $txn->renewed_count + 1,
            'status' => 'issued',
            'late_days' => 0,
            'late_fine_amount' => 0.00
        ]);

        return response()->json([
            'success' => true,
            'message' => "Book loan renewed! New due date: " . $newDueDate->format('d M Y') . " (Renewal {$txn->renewed_count}/{$maxRenewals}).",
            'data' => $txn
        ]);
    }

    /**
     * Ajax lookup for books (ID, Barcode, Accession No, Title, ISBN).
     */
    public function lookupBookAjax(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $term = trim($request->query('term', ''));
        $searchType = $request->query('type', 'all');

        if (!$term) {
            return response()->json(['success' => true, 'books' => []]);
        }

        $this->ensureLibraryTablesExist();

        $query = LibraryBook::where('school_id', $schoolId)->with(['section', 'bookType']);

        if ($searchType === 'book_id') {
            $cleanedId = preg_replace('/[^0-9]/', '', $term);
            $query->where(function($q) use ($term, $cleanedId) {
                if ($cleanedId) {
                    $q->where('id', $cleanedId);
                }
                $q->orWhere('accession_no', 'like', "%{$term}%");
            });
        } elseif ($searchType === 'barcode') {
            $query->where(function($q) use ($term) {
                $q->where('isbn', 'like', "%{$term}%")
                  ->orWhere('accession_no', 'like', "%{$term}%");
            });
        } elseif ($searchType === 'accession') {
            $query->where('accession_no', 'like', "%{$term}%");
        } elseif ($searchType === 'isbn') {
            $query->where('isbn', 'like', "%{$term}%");
        } elseif ($searchType === 'title') {
            $query->where('title', 'like', "%{$term}%");
        } else {
            $cleanedId = preg_replace('/[^0-9]/', '', $term);
            $query->where(function($q) use ($term, $cleanedId) {
                $q->where('title', 'like', "%{$term}%")
                  ->orWhere('author', 'like', "%{$term}%")
                  ->orWhere('accession_no', 'like', "%{$term}%")
                  ->orWhere('isbn', 'like', "%{$term}%");
                if ($cleanedId) {
                    $q->orWhere('id', $cleanedId);
                }
            });
        }

        $books = $query->limit(15)->get();

        // Also check active transactions for return
        $transactions = collect([]);
        try {
            $transactions = LibraryTransaction::where('school_id', $schoolId)
                ->whereIn('status', ['issued', 'overdue'])
                ->whereIn('book_id', $books->pluck('id'))
                ->with(['book', 'student.schoolClass', 'student.section', 'staff.designation'])
                ->get();
        } catch (\Throwable $e) {}

        return response()->json([
            'success' => true,
            'books' => $books,
            'active_transactions' => $transactions
        ]);
    }

    /**
     * Ajax lookup for students or staff borrowers.
     */
    public function lookupBorrowerAjax(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $type = $request->query('type', 'student');
        $term = trim($request->query('term', ''));

        if (!$term) {
            return response()->json(['success' => true, 'results' => []]);
        }

        $this->ensureLibraryTablesExist();

        $rule = $type === 'staff' 
            ? LibraryRule::getRuleFor($schoolId, 'staff') 
            : LibraryRule::getRuleFor($schoolId, 'student');

        $maxAllowed = (int) ($rule->max_books_allowed ?? 3);

        if ($type === 'staff') {
            $staffs = Staff::where('school_id', $schoolId)
                ->where(function($q) use ($term) {
                    $q->where('first_name', 'like', "%{$term}%")
                      ->orWhere('last_name', 'like', "%{$term}%")
                      ->orWhere('employee_id', 'like', "%{$term}%")
                      ->orWhere('phone', 'like', "%{$term}%");
                })
                ->with(['designation', 'department'])
                ->limit(15)
                ->get();

            $results = $staffs->map(function($st) use ($schoolId, $maxAllowed) {
                $borrowed = 0;
                $overdue = 0;
                try {
                    $borrowed = LibraryTransaction::where('school_id', $schoolId)
                        ->where('member_type', 'staff')
                        ->where('staff_id', $st->id)
                        ->whereIn('status', ['issued', 'overdue'])
                        ->count();

                    $overdue = LibraryTransaction::where('school_id', $schoolId)
                        ->where('member_type', 'staff')
                        ->where('staff_id', $st->id)
                        ->where('status', 'overdue')
                        ->count();
                } catch (\Throwable $e) {}

                return [
                    'id' => $st->id,
                    'name' => trim($st->first_name . ' ' . $st->last_name),
                    'code' => $st->employee_id ?: ('EMP-' . $st->id),
                    'subtitle' => $st->designation?->name ?: 'Staff Member',
                    'borrowed_count' => $borrowed,
                    'max_allowed' => $maxAllowed,
                    'remaining_quota' => max(0, $maxAllowed - $borrowed),
                    'overdue_count' => $overdue,
                    'is_eligible' => $borrowed < $maxAllowed
                ];
            });

            return response()->json(['success' => true, 'results' => $results]);
        }

        // Student lookup
        $students = Student::where('school_id', $schoolId)
            ->where(function($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                  ->orWhere('last_name', 'like', "%{$term}%")
                  ->orWhere('admission_number', 'like', "%{$term}%")
                  ->orWhere('roll_number', 'like', "%{$term}%")
                  ->orWhere('phone', 'like', "%{$term}%");
            })
            ->with(['schoolClass', 'section'])
            ->limit(15)
            ->get();

        $results = $students->map(function($st) use ($schoolId, $maxAllowed) {
            $borrowed = 0;
            $overdue = 0;
            try {
                $borrowed = LibraryTransaction::where('school_id', $schoolId)
                    ->where('member_type', 'student')
                    ->where('student_id', $st->id)
                    ->whereIn('status', ['issued', 'overdue'])
                    ->count();

                $overdue = LibraryTransaction::where('school_id', $schoolId)
                    ->where('member_type', 'student')
                    ->where('student_id', $st->id)
                    ->where('status', 'overdue')
                    ->count();
            } catch (\Throwable $e) {}

            $classText = ($st->schoolClass?->name ?? 'Class') . ' - ' . ($st->section?->name ?? 'Section');

            return [
                'id' => $st->id,
                'name' => trim($st->first_name . ' ' . $st->last_name),
                'code' => $st->admission_number ?: ('ADM-' . $st->id),
                'subtitle' => $classText . ($st->roll_number ? " (Roll: {$st->roll_number})" : ""),
                'borrowed_count' => $borrowed,
                'max_allowed' => $maxAllowed,
                'remaining_quota' => max(0, $maxAllowed - $borrowed),
                'overdue_count' => $overdue,
                'is_eligible' => $borrowed < $maxAllowed
            ];
        });

        return response()->json(['success' => true, 'results' => $results]);
    }

    /**
     * Export Transactions to CSV.
     */
    public function exportTransactionsCsv(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $tab = $request->query('tab', 'students');
        $fileName = 'library_transactions_' . $tab . '_' . date('Ymd_His') . '.csv';

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename={$fileName}",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $query = LibraryTransaction::where('school_id', $schoolId)
            ->where('member_type', $tab === 'staffs' ? 'staff' : 'student')
            ->with(['book.section', 'student.schoolClass', 'student.section', 'staff.designation']);

        $callback = function () use ($query, $tab) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            $columns = [
                'Txn Code',
                'Borrower Name',
                $tab === 'staffs' ? 'Employee ID' : 'Admission No',
                $tab === 'staffs' ? 'Designation' : 'Class & Section',
                'Book ID',
                'Accession No',
                'Book Title',
                'Author',
                'Issue Date',
                'Due Date',
                'Return Date',
                'Status',
                'Late Days',
                'Late Fine (₹)',
                'Damage/Lost Fine (₹)',
                'Total Fine (₹)',
                'Fine Status'
            ];

            fputcsv($handle, $columns);

            $query->chunk(100, function ($txns) use ($handle, $tab) {
                foreach ($txns as $t) {
                    $borrowerName = $tab === 'staffs' 
                        ? ($t->staff ? $t->staff->first_name . ' ' . $t->staff->last_name : 'Staff') 
                        : ($t->student ? $t->student->first_name . ' ' . $t->student->last_name : 'Student');

                    $idNum = $tab === 'staffs' 
                        ? ($t->staff?->employee_id ?? '') 
                        : ($t->student?->admission_no ?? '');

                    $classOrDesig = $tab === 'staffs'
                        ? ($t->staff?->designation?->name ?? 'Staff')
                        : (($t->student?->schoolClass?->name ?? '') . ' - ' . ($t->student?->section?->name ?? ''));

                    fputcsv($handle, [
                        $t->transaction_code,
                        $borrowerName,
                        $idNum,
                        $classOrDesig,
                        'BK-' . str_pad($t->book_id, 5, '0', STR_PAD_LEFT),
                        $t->book?->accession_no ?? '',
                        $t->book?->title ?? '',
                        $t->book?->author ?? '',
                        $t->issue_date ? Carbon::parse($t->issue_date)->format('d-m-Y') : '',
                        $t->due_date ? Carbon::parse($t->due_date)->format('d-m-Y') : '',
                        $t->return_date ? Carbon::parse($t->return_date)->format('d-m-Y') : '',
                        strtoupper($t->status),
                        $t->late_days,
                        number_format($t->late_fine_amount, 2),
                        number_format($t->damage_lost_fine, 2),
                        number_format($t->total_fine, 2),
                        strtoupper($t->fine_status)
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Fetch formatted twin-receipt voucher payload for printing.
     */
    public function getReceiptData($id)
    {
        $schoolId = $this->getSchoolId();
        $this->ensureLibraryTablesExist();

        $txn = LibraryTransaction::where('school_id', $schoolId)
            ->with(['book.section', 'book.bookType', 'student.schoolClass', 'student.section', 'staff.designation', 'staff.department', 'issuer'])
            ->find($id);

        if (!$txn) {
            return response()->json(['success' => false, 'message' => 'Transaction record not found.'], 404);
        }

        $school = School::find($schoolId) ?? (Auth::user()?->school ?? null);
        $schoolLogo = $school?->logo_url ?? ($school?->logo ? asset('storage/' . ltrim($school->logo, '/')) : null);

        $borrower = $txn->member_type === 'staff' ? $txn->staff : $txn->student;
        $borrowerName = $borrower ? trim(($borrower->first_name ?? '') . ' ' . ($borrower->last_name ?? '')) : 'Borrower';
        $borrowerCode = $txn->member_type === 'staff' 
            ? ($borrower?->employee_id ?: ('EMP-' . $txn->staff_id))
            : ($borrower?->admission_number ?: ('ADM-' . $txn->student_id));
        
        $borrowerClassDept = $txn->member_type === 'staff'
            ? ($borrower?->designation?->name ?: 'Staff Member')
            : (($borrower?->schoolClass?->name ?? 'Class') . ' - ' . ($borrower?->section?->name ?? 'Section'));
        
        $borrowerPhoto = $borrower?->photo_url ?? null;
        if (!$borrowerPhoto && $borrower?->photo) {
            $cleanPhoto = ltrim($borrower->photo, '/');
            if (Storage::disk('public')->exists($cleanPhoto)) {
                $borrowerPhoto = Storage::disk('public')->url($cleanPhoto);
            } else {
                $borrowerPhoto = asset('storage/' . $cleanPhoto);
            }
        }

        $rule = $txn->member_type === 'staff' 
            ? LibraryRule::getRuleFor((int)$schoolId, 'staff') 
            : LibraryRule::getRuleFor((int)$schoolId, 'student');

        return response()->json([
            'success' => true,
            'school' => [
                'name' => $school?->name ?? 'Educore International School',
                'logo_url' => $schoolLogo,
                'address' => $school?->address ?? 'School Campus, Main Road',
                'phone' => $school?->phone ?? '',
                'email' => $school?->email ?? '',
                'code' => $school?->code ?? '',
            ],
            'transaction' => [
                'id' => $txn->id,
                'transaction_code' => $txn->transaction_code,
                'issue_date' => $txn->issue_date ? Carbon::parse($txn->issue_date)->format('d M Y') : date('d M Y'),
                'due_date' => $txn->due_date ? Carbon::parse($txn->due_date)->format('d M Y') : date('d M Y', strtotime('+14 days')),
                'return_date' => $txn->return_date ? Carbon::parse($txn->return_date)->format('d M Y') : null,
                'status' => strtoupper($txn->status ?? 'ISSUED'),
                'fine_amount' => (float)$txn->total_fine,
                'fine_status' => strtoupper($txn->fine_status ?? 'NONE'),
                'remarks' => $txn->remarks ?: 'Standard Library Borrow Loan',
                'issued_by' => $txn->issuer?->name ?? (Auth::user()?->name ?? 'Librarian'),
                'print_time' => now()->format('d M Y, h:i A'),
            ],
            'borrower' => [
                'type' => ucfirst($txn->member_type),
                'name' => $borrowerName,
                'code' => $borrowerCode,
                'class_dept' => $borrowerClassDept,
                'roll_no' => $borrower?->roll_number ?? null,
                'phone' => $borrower?->phone ?? '—',
                'photo_url' => $borrowerPhoto,
            ],
            'book' => [
                'id' => $txn->book_id,
                'title' => $txn->book?->title ?? 'Untitled Book',
                'accession_no' => $txn->book?->accession_no ?? ('ACC-' . str_pad($txn->book_id, 5, '0', STR_PAD_LEFT)),
                'book_code' => 'BK-' . str_pad($txn->book_id, 5, '0', STR_PAD_LEFT),
                'isbn' => $txn->book?->isbn ?? '—',
                'author' => $txn->book?->author ?? '—',
                'publisher' => $txn->book?->publisher ?? '—',
                'section' => $txn->book?->section?->name ?? 'General Collection',
                'rack' => $txn->book?->rack_location ?? 'Main Shelf',
            ],
            'rule' => [
                'borrow_period_days' => (int)($rule->borrow_period_days ?? 14),
                'late_fine_amount' => (float)($rule->late_fine_amount ?? 5.00),
            ]
        ]);
    }

    /**
     * Dedicated full-page print view for Twin Library Voucher.
     */
    public function printReceipt(Request $request, $id)
    {
        $schoolId = $this->getSchoolId();
        $this->ensureLibraryTablesExist();

        $txn = LibraryTransaction::where('school_id', $schoolId)
            ->with(['book.section', 'book.bookType', 'student.schoolClass', 'student.section', 'staff.designation', 'staff.department', 'issuer'])
            ->findOrFail($id);

        $school = School::find($schoolId) ?? (Auth::user()?->school ?? null);
        $schoolLogo = $school?->logo_url ?? ($school?->logo ? asset('storage/' . ltrim($school->logo, '/')) : null);

        $borrower = $txn->member_type === 'staff' ? $txn->staff : $txn->student;
        $borrowerName = $borrower ? trim(($borrower->first_name ?? '') . ' ' . ($borrower->last_name ?? '')) : 'Borrower';
        $borrowerCode = $txn->member_type === 'staff' 
            ? ($borrower?->employee_id ?: ('EMP-' . $txn->staff_id))
            : ($borrower?->admission_number ?: ('ADM-' . $txn->student_id));
        
        $borrowerClassDept = $txn->member_type === 'staff'
            ? ($borrower?->designation?->name ?: 'Staff Member')
            : (($borrower?->schoolClass?->name ?? 'Class') . ' - ' . ($borrower?->section?->name ?? 'Section'));
        
        $borrowerPhoto = $borrower?->photo_url ?? null;
        if (!$borrowerPhoto && $borrower?->photo) {
            $cleanPhoto = ltrim($borrower->photo, '/');
            if (Storage::disk('public')->exists($cleanPhoto)) {
                $borrowerPhoto = Storage::disk('public')->url($cleanPhoto);
            } else {
                $borrowerPhoto = asset('storage/' . $cleanPhoto);
            }
        }

        $rule = $txn->member_type === 'staff' 
            ? LibraryRule::getRuleFor((int)$schoolId, 'staff') 
            : LibraryRule::getRuleFor((int)$schoolId, 'student');

        return view('school.library.receipt-print', compact(
            'txn',
            'school',
            'schoolLogo',
            'borrower',
            'borrowerName',
            'borrowerCode',
            'borrowerClassDept',
            'borrowerPhoto',
            'rule'
        ));
    }

    /**
     * Display Library Dashboard page.
     */
    public function dashboard(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $this->ensureLibraryTablesExist();

        $today = date('Y-m-d');
        $selectedSessionId = $request->get('session_id');
        $academicSessions = AcademicSession::where('school_id', $schoolId)
            ->whereNotNull('name')
            ->where('name', '!=', '')
            ->orderBy('id', 'desc')
            ->get();

        $currentSession = null;
        if ($selectedSessionId) {
            $currentSession = $academicSessions->firstWhere('id', $selectedSessionId);
        }
        if (!$currentSession) {
            $currentSession = $academicSessions->firstWhere('is_current', 1) ?? $academicSessions->first();
        }

        // 1. Catalogue & Inventory Health
        $totalBooks = LibraryBook::where('school_id', $schoolId)->count();
        $totalCopies = (int) LibraryBook::where('school_id', $schoolId)->sum('total_copies');
        $availableCopies = (int) LibraryBook::where('school_id', $schoolId)->sum('available_copies');
        $issuedCopies = (int) LibraryBook::where('school_id', $schoolId)->sum('issued_copies');
        $utilizationRate = $totalCopies > 0 ? round(($issuedCopies / $totalCopies) * 100, 1) : 0;
        $totalSections = LibrarySection::where('school_id', $schoolId)->count();
        $totalBookTypes = LibraryBookType::where('school_id', $schoolId)->count();

        // 2. Circulation & Daily Activity
        $todayIssuedCount = 0;
        $todayReturnedCount = 0;
        $todayRenewedCount = 0;
        $activeLoansCount = 0;
        $activeStudentLoansCount = 0;
        $activeStaffLoansCount = 0;
        $overdueCount = 0;

        try {
            $todayIssuedCount = LibraryTransaction::where('school_id', $schoolId)->whereDate('issue_date', $today)->count();
            $todayReturnedCount = LibraryTransaction::where('school_id', $schoolId)->where('status', 'returned')->whereDate('return_date', $today)->count();
            $todayRenewedCount = LibraryTransaction::where('school_id', $schoolId)->where('renewed_count', '>', 0)->whereDate('updated_at', $today)->count();
            $activeLoansCount = LibraryTransaction::where('school_id', $schoolId)->whereIn('status', ['issued', 'overdue'])->count();
            $activeStudentLoansCount = LibraryTransaction::where('school_id', $schoolId)->where('member_type', 'student')->whereIn('status', ['issued', 'overdue'])->count();
            $activeStaffLoansCount = LibraryTransaction::where('school_id', $schoolId)->where('member_type', 'staff')->whereIn('status', ['issued', 'overdue'])->count();
            $overdueCount = LibraryTransaction::where('school_id', $schoolId)->where('status', 'overdue')->count();
        } catch (\Throwable $e) {}

        // 3. Financial Fine Analytics
        $totalFineCollected = 0.00;
        $totalFinePending = 0.00;
        $todayFineCollected = 0.00;
        $waivedFinesCount = 0;

        try {
            $totalFineCollected = (float) LibraryTransaction::where('school_id', $schoolId)->where('fine_status', 'paid')->sum('total_fine');
            $totalFinePending = (float) LibraryTransaction::where('school_id', $schoolId)->where('fine_status', 'pending')->sum('total_fine');
            $todayFineCollected = (float) LibraryTransaction::where('school_id', $schoolId)->where('fine_status', 'paid')->whereDate('payment_date', $today)->sum('total_fine');
            $waivedFinesCount = LibraryTransaction::where('school_id', $schoolId)->where('fine_status', 'waived')->count();
        } catch (\Throwable $e) {}

        // 4. Monthly Circulation Chart Data (Last 6 Months)
        $chartLabels = [];
        $chartIssuedData = [];
        $chartReturnedData = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthKey = $monthDate->format('M Y');
            $chartLabels[] = $monthKey;

            $monthStart = $monthDate->copy()->startOfMonth()->format('Y-m-d');
            $monthEnd = $monthDate->copy()->endOfMonth()->format('Y-m-d');

            try {
                $issuedInMonth = LibraryTransaction::where('school_id', $schoolId)
                    ->whereBetween('issue_date', [$monthStart, $monthEnd])
                    ->count();

                $returnedInMonth = LibraryTransaction::where('school_id', $schoolId)
                    ->where('status', 'returned')
                    ->whereBetween('return_date', [$monthStart, $monthEnd])
                    ->count();

                $chartIssuedData[] = $issuedInMonth;
                $chartReturnedData[] = $returnedInMonth;
            } catch (\Throwable $e) {
                $chartIssuedData[] = 0;
                $chartReturnedData[] = 0;
            }
        }

        // 5. Category / Section Distribution
        $categoryLabels = [];
        $categoryCounts = [];
        $sectionsWithCounts = [];
        try {
            $sections = LibrarySection::where('school_id', $schoolId)
                ->withCount('books')
                ->orderBy('books_count', 'desc')
                ->limit(6)
                ->get();

            foreach ($sections as $sec) {
                $categoryLabels[] = $sec->name;
                $categoryCounts[] = $sec->books_count;
                $sectionsWithCounts[] = $sec;
            }
        } catch (\Throwable $e) {}

        if (empty($categoryLabels)) {
            $categoryLabels = ['General Collection'];
            $categoryCounts = [$totalBooks];
        }

        // 6. Top Frequently Circulated Books
        $popularBooks = [];
        try {
            $popularBooks = LibraryBook::where('school_id', $schoolId)
                ->with(['section'])
                ->orderBy('issued_copies', 'desc')
                ->limit(5)
                ->get();
        } catch (\Throwable $e) {}

        // 7. Critical Overdue Defaulters Alert List
        $overdueTransactions = collect([]);
        try {
            $overdueTransactions = LibraryTransaction::where('school_id', $schoolId)
                ->where('status', 'overdue')
                ->with(['book', 'student.schoolClass', 'student.section', 'staff.designation'])
                ->orderBy('due_date', 'asc')
                ->limit(5)
                ->get();
        } catch (\Throwable $e) {}

        // 8. Recent Activity Stream
        $recentTransactions = collect([]);
        try {
            $recentTransactions = LibraryTransaction::where('school_id', $schoolId)
                ->with(['book', 'student.schoolClass', 'student.section', 'staff.designation'])
                ->orderBy('id', 'desc')
                ->limit(7)
                ->get();
        } catch (\Throwable $e) {}

        return view('school.library.dashboard', compact(
            'academicSessions',
            'currentSession',
            'totalBooks',
            'totalCopies',
            'availableCopies',
            'issuedCopies',
            'utilizationRate',
            'totalSections',
            'totalBookTypes',
            'todayIssuedCount',
            'todayReturnedCount',
            'todayRenewedCount',
            'activeLoansCount',
            'activeStudentLoansCount',
            'activeStaffLoansCount',
            'overdueCount',
            'totalFineCollected',
            'totalFinePending',
            'todayFineCollected',
            'waivedFinesCount',
            'chartLabels',
            'chartIssuedData',
            'chartReturnedData',
            'categoryLabels',
            'categoryCounts',
            'sectionsWithCounts',
            'popularBooks',
            'overdueTransactions',
            'recentTransactions'
        ));
    }
}

