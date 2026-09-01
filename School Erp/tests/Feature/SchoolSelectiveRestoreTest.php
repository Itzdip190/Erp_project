<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\School;
use App\Models\User;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\AcademicSession;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\FeeCategory;
use App\Models\FeeComponent;
use App\Models\FeeSchedule;
use App\Models\StudentFee;
use App\Models\SchoolRestoreLog;
use App\Services\Backup\SchoolSnapshotService;
use App\Services\Backup\SchoolSelectiveRestoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Spatie\Permission\Models\Role;

class SchoolSelectiveRestoreTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected School $schoolA;
    protected School $schoolB;
    protected AcademicSession $sessionA;
    protected AcademicSession $sessionB;
    protected SchoolClass $classA;
    protected Section $sectionA;
    protected SchoolClass $classB;
    protected Section $sectionB;
    protected Student $studentA1;
    protected Student $studentA2;
    protected Student $studentB1;
    protected SchoolSnapshotService $snapshotService;
    protected SchoolSelectiveRestoreService $restoreService;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Super Admin role & user
        $superAdminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $this->superAdmin = User::factory()->create([
            'email' => 'superadmin@schoolcloud.com',
            'role' => 'superadmin',
            'school_id' => null,
        ]);
        $this->superAdmin->assignRole($superAdminRole);

        // Create School A (e.g. Vedant Public School)
        $this->schoolA = School::create([
            'name' => 'VEDANT PUBLIC SCHOOL',
            'code' => 'VPSUP01',
            'status' => 'active',
            'email' => 'admin@vedant.com',
        ]);

        // Create School B (e.g. Saswati School)
        $this->schoolB = School::create([
            'name' => 'Saswati School',
            'code' => 'YIS2026',
            'status' => 'active',
            'email' => 'admin@saswati.com',
        ]);

        // Academic Sessions
        $this->sessionA = AcademicSession::create([
            'school_id' => $this->schoolA->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => 1,
        ]);

        $this->sessionB = AcademicSession::create([
            'school_id' => $this->schoolB->id,
            'name' => '2026-2027',
            'start_date' => '2026-04-01',
            'end_date' => '2027-03-31',
            'is_current' => 1,
        ]);

        // Classes & Sections for School A
        $this->classA = SchoolClass::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Class 10',
            'numeric_name' => 10,
        ]);

        $this->sectionA = Section::create([
            'school_id' => $this->schoolA->id,
            'class_id' => $this->classA->id,
            'name' => 'A',
        ]);

        // Classes & Sections for School B
        $this->classB = SchoolClass::create([
            'school_id' => $this->schoolB->id,
            'name' => 'Class 9',
            'numeric_name' => 9,
        ]);

        $this->sectionB = Section::create([
            'school_id' => $this->schoolB->id,
            'class_id' => $this->classB->id,
            'name' => 'A',
        ]);

        // Students for School A
        $this->studentA1 = $this->createStudent([
            'school_id' => $this->schoolA->id,
            'first_name' => 'Aarav',
            'last_name' => 'Sharma',
            'admission_number' => 'ADM-A-001',
            'class_id' => $this->classA->id,
            'section_id' => $this->sectionA->id,
            'academic_session_id' => $this->sessionA->id,
        ]);

        $this->studentA2 = $this->createStudent([
            'school_id' => $this->schoolA->id,
            'first_name' => 'Diya',
            'last_name' => 'Patel',
            'admission_number' => 'ADM-A-002',
            'class_id' => $this->classA->id,
            'section_id' => $this->sectionA->id,
            'academic_session_id' => $this->sessionA->id,
        ]);

        // Student for School B
        $this->studentB1 = $this->createStudent([
            'school_id' => $this->schoolB->id,
            'first_name' => 'Rohan',
            'last_name' => 'Sen',
            'admission_number' => 'ADM-B-001',
            'class_id' => $this->classB->id,
            'section_id' => $this->sectionB->id,
            'academic_session_id' => $this->sessionB->id,
        ]);

        $this->snapshotService = new SchoolSnapshotService();
        $this->restoreService = new SchoolSelectiveRestoreService();
    }

    protected function createStudent(array $attributes): Student
    {
        return Student::create(array_merge([
            'date_of_birth' => '2012-05-15',
            'gender' => 'male',
            'guardian_name' => 'Rajesh Sharma',
            'guardian_phone' => '9876543210',
            'guardian_relationship' => 'father',
            'address' => '123 Test Street',
            'city' => 'Noida',
            'state' => 'UP',
            'pincode' => '201301',
            'admission_date' => '2026-04-01',
            'is_active' => 1,
        ], $attributes));
    }

    protected function createAttendance(array $attributes): StudentAttendance
    {
        return StudentAttendance::create(array_merge([
            'class_id' => $this->classA->id,
            'section_id' => $this->sectionA->id,
            'academic_session_id' => $this->sessionA->id,
            'attendance_type' => 'manual',
            'marked_by' => $this->superAdmin->id,
            'date' => '2026-08-25',
            'status' => 'present',
        ], $attributes));
    }

    protected function tearDown(): void
    {
        // Clean up any test snapshots created
        $dir = storage_path('app/snapshots');
        if (File::exists($dir)) {
            $files = File::files($dir);
            foreach ($files as $f) {
                if (str_contains($f->getFilename(), 'VPSUP01') || str_contains($f->getFilename(), 'YIS2026')) {
                    @File::delete($f->getPathname());
                }
            }
            $subdirs = File::directories($dir);
            foreach ($subdirs as $d) {
                if (str_contains(basename($d), 'VPSUP01') || str_contains(basename($d), 'YIS2026')) {
                    @File::deleteDirectory($d);
                }
            }
        }
        parent::tearDown();
    }

    /**
     * Test 1: Super Admin can access the scoped restore page.
     */
    public function test_superadmin_can_view_scoped_restore_page(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.schools.restore-data', $this->schoolA->id));

        $response->assertStatus(200);
        $response->assertSee('VEDANT PUBLIC SCHOOL');
        $response->assertSee('VPSUP01');
        $response->assertSee('Student Attendance');
        $response->assertSee('Fees & Payments');
    }

    /**
     * Test 2: Non-superadmin cannot access the restore center.
     */
    public function test_non_superadmin_cannot_access_restore_page(): void
    {
        $regularUser = User::factory()->create([
            'role' => 'admin',
            'school_id' => $this->schoolA->id,
        ]);

        $response = $this->actingAs($regularUser)
            ->get(route('superadmin.schools.restore-data', $this->schoolA->id));

        // Should be rejected by Spatie role:superadmin middleware
        $this->assertTrue(in_array($response->status(), [302, 403]));
    }

    /**
     * Test 3: Selective restore of Student Attendance ONLY.
     * - School A attendance is restored to snapshot state.
     * - School A unselected data (fees) is NOT restored/modified.
     * - School B data is NOT modified.
     */
    public function test_selective_restore_attendance_only(): void
    {
        $date = '2026-08-25';

        // 1. Initial State: School A has 2 students present on $date
        $this->createAttendance([
            'school_id' => $this->schoolA->id,
            'student_id' => $this->studentA1->id,
            'date' => $date,
            'status' => 'present',
            'remark' => 'Initial Snapshot Attendance',
        ]);

        $this->createAttendance([
            'school_id' => $this->schoolA->id,
            'student_id' => $this->studentA2->id,
            'date' => $date,
            'status' => 'present',
            'remark' => 'Initial Snapshot Attendance',
        ]);

        // School B has 1 student present on $date
        $this->createAttendance([
            'school_id' => $this->schoolB->id,
            'student_id' => $this->studentB1->id,
            'class_id' => $this->classB->id,
            'section_id' => $this->sectionB->id,
            'academic_session_id' => $this->sessionB->id,
            'date' => $date,
            'status' => 'present',
            'remark' => 'School B Attendance',
        ]);

        // Create Fee Category, Fee Component & StudentFee for School A
        $feeCatA = FeeCategory::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Tuition',
        ]);

        $feeCompA = FeeComponent::create([
            'school_id' => $this->schoolA->id,
            'academic_session_id' => $this->sessionA->id,
            'fee_category_id' => $feeCatA->id,
            'component_name' => 'Tuition Fee',
            'head_name' => 'Tuition',
        ]);

        $feeA = StudentFee::create([
            'school_id' => $this->schoolA->id,
            'student_id' => $this->studentA1->id,
            'fee_category_id' => $feeCatA->id,
            'fee_component_id' => $feeCompA->id,
            'amount' => 5000,
            'due_date' => '2026-09-10',
            'status' => 'unpaid',
        ]);

        // 2. Export Snapshot of School A in its clean state
        $snapResult = $this->snapshotService->exportSchoolSnapshot($this->schoolA->id);
        $this->assertTrue($snapResult['success']);
        $snapshotZip = $snapResult['snapshot_file'];
        $this->assertFileExists($snapshotZip);

        // 3. Simulate Data Changes / Corruption in Production:
        // - Corrupt School A attendance: delete Diya's attendance and mark Aarav as 'absent'
        StudentAttendance::where('school_id', $this->schoolA->id)->where('student_id', $this->studentA2->id)->delete();
        StudentAttendance::where('school_id', $this->schoolA->id)->where('student_id', $this->studentA1->id)->update(['status' => 'absent']);

        // - Modify School A fee: mark fee as paid (an unselected module change)
        $feeA->update(['status' => 'paid', 'paid_amount' => 5000]);

        // - Modify School B attendance: change status to 'late'
        StudentAttendance::where('school_id', $this->schoolB->id)->update(['status' => 'late']);

        // Verify corrupted state before restore
        $this->assertEquals(1, StudentAttendance::where('school_id', $this->schoolA->id)->count());
        $this->assertEquals('absent', StudentAttendance::where('school_id', $this->schoolA->id)->first()->status);
        $this->assertEquals('paid', $feeA->fresh()->status);
        $this->assertEquals('late', StudentAttendance::where('school_id', $this->schoolB->id)->first()->status);

        // 4. Perform Selective Restore of School A -> Student Attendance ONLY
        $restoreResult = $this->restoreService->executeSelectiveRestore(
            schoolId: $this->schoolA->id,
            snapshotZipPath: $snapshotZip,
            selectedModuleKeys: ['student_attendance'],
            userId: $this->superAdmin->id,
            createSafetyBackup: false
        );

        $this->assertTrue($restoreResult['success']);
        $this->assertEquals(2, $restoreResult['total_rows_inserted']);

        // 5. Assertions:
        // A) School A attendance is RESTORED to 2 records, both 'present'
        $attA = StudentAttendance::where('school_id', $this->schoolA->id)->get();
        $this->assertCount(2, $attA);
        foreach ($attA as $a) {
            $this->assertEquals('present', $a->status);
            $this->assertEquals('Initial Snapshot Attendance', $a->remark);
        }

        // B) School A unselected fee data was NOT restored/overwritten (remains 'paid')
        $this->assertEquals('paid', $feeA->fresh()->status);

        // C) School B attendance was NOT modified (remains 'late')
        $attB = StudentAttendance::where('school_id', $this->schoolB->id)->first();
        $this->assertEquals('late', $attB->status);

        // D) Verify audit log created
        $this->assertDatabaseHas('school_restore_logs', [
            'school_id' => $this->schoolA->id,
            'status' => 'success',
        ]);
    }

    /**
     * Test 4: Selective restore of Multi-Modules (Student Attendance + Fees).
     */
    public function test_multi_module_selective_restore(): void
    {
        // 1. Initial State
        $att = $this->createAttendance([
            'school_id' => $this->schoolA->id,
            'student_id' => $this->studentA1->id,
            'date' => '2026-08-20',
            'status' => 'present',
        ]);

        $feeCat = FeeCategory::create([
            'school_id' => $this->schoolA->id,
            'name' => 'Exam',
        ]);

        $feeComp = FeeComponent::create([
            'school_id' => $this->schoolA->id,
            'academic_session_id' => $this->sessionA->id,
            'fee_category_id' => $feeCat->id,
            'component_name' => 'Exam Fee',
            'head_name' => 'Exam',
        ]);

        $fee = StudentFee::create([
            'school_id' => $this->schoolA->id,
            'student_id' => $this->studentA1->id,
            'fee_category_id' => $feeCat->id,
            'fee_component_id' => $feeComp->id,
            'amount' => 1500,
            'due_date' => '2026-09-15',
            'status' => 'unpaid',
        ]);

        // Export Snapshot
        $snapResult = $this->snapshotService->exportSchoolSnapshot($this->schoolA->id);
        $snapshotZip = $snapResult['snapshot_file'];

        // Corrupt both Attendance and Fee in production
        $att->delete();
        $fee->update(['amount' => 9999]);

        // Verify corrupted
        $this->assertEquals(0, StudentAttendance::where('school_id', $this->schoolA->id)->count());
        $this->assertEquals(9999, $fee->fresh()->amount);

        // Restore Attendance + Fees
        $restoreResult = $this->restoreService->executeSelectiveRestore(
            schoolId: $this->schoolA->id,
            snapshotZipPath: $snapshotZip,
            selectedModuleKeys: ['student_attendance', 'fees'],
            userId: $this->superAdmin->id,
            createSafetyBackup: false
        );

        $this->assertTrue($restoreResult['success']);

        // Verify Attendance restored
        $this->assertEquals(1, StudentAttendance::where('school_id', $this->schoolA->id)->count());
        // Verify Fee restored
        $this->assertEquals(1500, StudentFee::where('school_id', $this->schoolA->id)->first()->amount);
        $this->assertEquals('unpaid', StudentFee::where('school_id', $this->schoolA->id)->first()->status);
    }

    /**
     * Test 5: Cross-school snapshot tampering is rejected by server.
     */
    public function test_cross_school_snapshot_tampering_is_rejected(): void
    {
        // Generate snapshot for School B
        $snapResultB = $this->snapshotService->exportSchoolSnapshot($this->schoolB->id);
        $snapshotZipB = $snapResultB['snapshot_file'];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessageMatches('/Cross-school restoration is strictly prohibited|does not belong to School/i');

        // Attempt to restore School B's snapshot into School A
        $this->restoreService->executeSelectiveRestore(
            schoolId: $this->schoolA->id,
            snapshotZipPath: $snapshotZipB,
            selectedModuleKeys: ['student_attendance'],
            userId: $this->superAdmin->id,
            createSafetyBackup: false
        );
    }

    /**
     * Test 6: Empty module selection is rejected.
     */
    public function test_empty_module_selection_is_rejected(): void
    {
        $snapResultA = $this->snapshotService->exportSchoolSnapshot($this->schoolA->id);
        $snapshotZipA = $snapResultA['snapshot_file'];

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Please select at least one module to restore.');

        $this->restoreService->executeSelectiveRestore(
            schoolId: $this->schoolA->id,
            snapshotZipPath: $snapshotZipA,
            selectedModuleKeys: [],
            userId: $this->superAdmin->id,
            createSafetyBackup: false
        );
    }

    /**
     * Test 7: HTTP Form submission endpoint executes restore successfully.
     */
    public function test_http_endpoint_executes_restore_successfully(): void
    {
        $this->createAttendance([
            'school_id' => $this->schoolA->id,
            'student_id' => $this->studentA1->id,
            'date' => '2026-08-22',
            'status' => 'present',
        ]);

        $snapResultA = $this->snapshotService->exportSchoolSnapshot($this->schoolA->id);
        $snapshotZipA = $snapResultA['snapshot_file'];

        // Delete attendance
        StudentAttendance::where('school_id', $this->schoolA->id)->delete();
        $this->assertEquals(0, StudentAttendance::where('school_id', $this->schoolA->id)->count());

        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.schools.restore-data.execute', $this->schoolA->id), [
                'snapshot_file' => basename($snapshotZipA),
                'restore_modules' => ['student_attendance'],
                'confirm_scope' => '1',
            ]);

        $response->assertRedirect(route('superadmin.schools.restore-data', $this->schoolA->id));
        $response->assertSessionHas('success');

        // Attendance restored
        $this->assertEquals(1, StudentAttendance::where('school_id', $this->schoolA->id)->count());
    }

    /**
     * Test 8: Snapshots are stored inside school-dedicated date folders.
     */
    public function test_snapshots_are_stored_in_school_dedicated_date_folder(): void
    {
        $snapResult = $this->snapshotService->exportSchoolSnapshot($this->schoolA->id);
        $this->assertTrue($snapResult['success']);
        
        $snapshotZip = $snapResult['snapshot_file'];
        $this->assertFileExists($snapshotZip);

        // Verify it is placed inside school_{id}_{code}/{YYYY-MM-DD} folder
        $todayDate = date('Y-m-d');
        $expectedFolder = "school_{$this->schoolA->id}_{$this->schoolA->code}" . DIRECTORY_SEPARATOR . $todayDate;
        $normalizedPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $snapshotZip);
        $this->assertStringContainsString($expectedFolder, $normalizedPath);

        // Verify getAvailableSnapshots finds it
        $available = $this->restoreService->getAvailableSnapshots($this->schoolA->id);
        $this->assertNotEmpty($available);
        $this->assertEquals(basename($snapshotZip), $available[0]['filename']);
    }

    /**
     * Test 9: Tiered retention keeps daily EOD backups for 15 days and prunes older intermediate backups.
     */
    public function test_tiered_retention_policy_for_intermediate_and_daily_eod_backups(): void
    {
        $schoolFolder = "school_{$this->schoolA->id}_{$this->schoolA->code}";
        $schoolDir = storage_path("app/snapshots/{$schoolFolder}");
        File::makeDirectory($schoolDir, 0755, true);

        // Date 1: 3 days ago (e.g. Monday relative to Thursday) -> 6 backups created
        $threeDaysAgo = date('Y-m-d', time() - (3 * 86400));
        $dateDir3Days = "{$schoolDir}/{$threeDaysAgo}";
        File::makeDirectory($dateDir3Days, 0755, true);

        $threeDaysFiles = [];
        for ($i = 1; $i <= 6; $i++) {
            $numStr = str_pad((string)($i * 4), 2, '0', STR_PAD_LEFT);
            $filename = "school_{$this->schoolA->id}_{$this->schoolA->code}_{$threeDaysAgo}_{$numStr}0000.zip";
            $filePath = "{$dateDir3Days}/{$filename}";
            
            $zip = new \ZipArchive();
            $zip->open($filePath, \ZipArchive::CREATE);
            $zip->addFromString('manifest.json', json_encode(['school' => ['id' => $this->schoolA->id]]));
            $zip->close();
            
            // Artificial mtime matching the 3 days ago timestamp
            $ts = strtotime("{$threeDaysAgo} {$numStr}:00:00");
            touch($filePath, $ts);
            $threeDaysFiles[] = $filePath;
        }

        // Date 2: 16 days ago -> 1 daily backup created (should be pruned as > 15 days)
        $sixteenDaysAgo = date('Y-m-d', time() - (16 * 86400));
        $dateDir16Days = "{$schoolDir}/{$sixteenDaysAgo}";
        File::makeDirectory($dateDir16Days, 0755, true);
        $oldFile = "{$dateDir16Days}/school_{$this->schoolA->id}_{$this->schoolA->code}_{$sixteenDaysAgo}_200000.zip";
        $zip = new \ZipArchive();
        $zip->open($oldFile, \ZipArchive::CREATE);
        $zip->addFromString('manifest.json', json_encode(['school' => ['id' => $this->schoolA->id]]));
        $zip->close();
        touch($oldFile, strtotime("{$sixteenDaysAgo} 20:00:00"));

        // Now trigger new snapshot export (today)
        $result = $this->snapshotService->exportSchoolSnapshot($this->schoolA->id);
        $this->assertTrue($result['success']);

        // Check file existence:
        // 1. From 3 days ago: The first 5 intermediate backups must be pruned (older than 48h)
        for ($i = 0; $i < 5; $i++) {
            $this->assertFileDoesNotExist($threeDaysFiles[$i], "Intermediate file {$threeDaysFiles[$i]} should have been pruned.");
        }
        // 2. From 3 days ago: The 6th (last/EOD) backup MUST BE RETAINED (within 15 days)
        $this->assertFileExists($threeDaysFiles[5], "Daily EOD milestone file {$threeDaysFiles[5]} should be retained for 15 days.");

        // 3. From 16 days ago: Must be pruned (>15 days) and empty date directory removed
        $this->assertFileDoesNotExist($oldFile, "16-day-old backup should have been pruned.");
        $this->assertDirectoryDoesNotExist($dateDir16Days, "Empty 16-day-old date folder should have been removed.");

        // 4. New snapshot is retained
        $this->assertFileExists($result['snapshot_file']);
    }
}
