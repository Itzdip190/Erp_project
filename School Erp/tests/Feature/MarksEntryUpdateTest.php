<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamAssessment;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Student;
use App\Models\StudentMark;
use App\Models\Subject;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarksEntryUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_save_and_retrieve_marks_update_drawer_data(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;
        $school = School::find($schoolId);

        $class = SchoolClass::where('school_id', $schoolId)->first();
        $section = Section::where('school_id', $schoolId)->where('class_id', $class->id)->first();
        $subject = Subject::where('school_id', $schoolId)->first();
        $subject->update(['class_id' => $class->id, 'type' => 'Scholastic']);

        $student = Student::where('school_id', $schoolId)->where('class_id', $class->id)->first();

        $exam = Exam::create([
            'school_id' => $schoolId,
            'name' => 'Term 2 Final Exam',
            'status' => 'Ongoing & Completed',
            'class_id' => $class->id,
            'section_id' => $section ? $section->id : null,
        ]);

        $assessment = ExamAssessment::create([
            'school_id' => $schoolId,
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'name' => 'Half Yearly1',
            'max_marks' => 50,
            'pass_marks' => 17,
        ]);

        // 1. Post updated marks data
        $postData = [
            'subject_id' => $subject->id,
            'records' => [
                [
                    'student_id' => $student->id,
                    'assessments' => [
                        [
                            'assessment_id' => $assessment->id,
                            'assessment_name' => 'Half Yearly1',
                            'marks_obtained' => '45.5',
                            'max_marks' => 50,
                            'attendance_status' => 'present',
                            'remarks' => 'Good performance',
                        ]
                    ]
                ]
            ]
        ];

        $headers = [
            'X-School-Code' => $school ? $school->code : 'YIS2024'
        ];

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders($headers)
            ->postJson(
                "/school/examination/exams/{$exam->id}/save-update-marks-drawer-data",
                $postData
            );

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // 2. Verify record exists in student_marks table
        $savedMark = StudentMark::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('subject_id', $subject->id)
            ->where('exam_name', 'Term 2 Final Exam')
            ->first();

        $this->assertNotNull($savedMark, 'StudentMark record should be saved in database');
        $this->assertEquals(45.5, (float)$savedMark->marks_obtained);
        $this->assertEquals('Good performance', $savedMark->remarks);

        // 3. Retrieve drawer data and check marks_map contains saved values
        $getResponse = $this->actingAs($schoolAdmin)
            ->withHeaders($headers)
            ->getJson(
                "/school/examination/exams/{$exam->id}/update-marks-drawer-data?class_id={$class->id}&subject_id={$subject->id}&subject_type=Scholastic"
            );

        $getResponse->assertStatus(200);
        $data = $getResponse->json();

        $this->assertArrayHasKey('marks_map', $data);
        $studentKey = (string)$student->id;
        $this->assertTrue(isset($data['marks_map'][$studentKey]) || isset($data['marks_map'][$student->id]), 'Marks map should contain student entry');
        
        $stuMap = $data['marks_map'][$studentKey] ?? $data['marks_map'][$student->id];
        $this->assertNotEmpty($stuMap);
        
        $markInfo = $stuMap[$assessment->id] ?? ($stuMap['Half Yearly1'] ?? null);
        $this->assertNotNull($markInfo);
        $this->assertEquals(45.5, (float)$markInfo['marks_obtained']);

        // 4. Test Updating Marks Second Time (Edit Existing Mark)
        $updatePostData = [
            'subject_id' => $subject->id,
            'records' => [
                [
                    'student_id' => $student->id,
                    'assessments' => [
                        [
                            'assessment_id' => $assessment->id,
                            'assessment_name' => 'Half Yearly1',
                            'marks_obtained' => '48.0',
                            'max_marks' => 50,
                            'attendance_status' => 'present',
                            'remarks' => 'Excellent improvement',
                        ]
                    ]
                ]
            ]
        ];

        $updateResponse = $this->actingAs($schoolAdmin)
            ->withHeaders($headers)
            ->postJson(
                "/school/examination/exams/{$exam->id}/save-update-marks-drawer-data",
                $updatePostData
            );

        $updateResponse->assertStatus(200);
        $updateResponse->assertJson(['success' => true]);

        $updatedMark = StudentMark::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('subject_id', $subject->id)
            ->where('exam_name', 'Term 2 Final Exam')
            ->first();

        $this->assertEquals(48.0, (float)$updatedMark->marks_obtained);
        $this->assertEquals('Excellent improvement', $updatedMark->remarks);

        // Verify total records count is 1 (updated, no duplicates)
        $count = StudentMark::where('school_id', $schoolId)
            ->where('student_id', $student->id)
            ->where('subject_id', $subject->id)
            ->where('exam_name', 'Term 2 Final Exam')
            ->count();
        $this->assertEquals(1, $count);
    }

    public function test_same_exam_name_different_classes_do_not_mix(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;
        $school = School::find($schoolId);

        $class1 = SchoolClass::where('school_id', $schoolId)->orderBy('id')->first();
        $class2 = SchoolClass::where('school_id', $schoolId)->where('id', '!=', $class1->id)->orderBy('id')->first();

        if (!$class2) {
            $class2 = SchoolClass::create([
                'school_id' => $schoolId,
                'name' => 'Class-2',
            ]);
        }

        $examClass1 = Exam::create([
            'school_id' => $schoolId,
            'name' => 'PA-1',
            'status' => 'Ongoing & Completed',
            'class_id' => $class1->id,
        ]);

        $examClass2 = Exam::create([
            'school_id' => $schoolId,
            'name' => 'PA-1',
            'status' => 'Ongoing & Completed',
            'class_id' => $class2->id,
        ]);

        // Check assigned_classes attribute on models
        $this->assertCount(1, $examClass1->assigned_classes);
        $this->assertEquals($class1->id, $examClass1->assigned_classes->first()->id);

        $this->assertCount(1, $examClass2->assigned_classes);
        $this->assertEquals($class2->id, $examClass2->assigned_classes->first()->id);

        $headers = [
            'X-School-Code' => $school ? $school->code : 'YIS2024'
        ];

        // Fetch drawer data for Exam Class 2
        $response = $this->actingAs($schoolAdmin)
            ->withHeaders($headers)
            ->getJson(
                "/school/examination/exams/{$examClass2->id}/update-marks-drawer-data"
            );

        $response->assertStatus(200);
        $data = $response->json();

        // Selected class should strictly be Class 2, not Class 1
        $this->assertEquals($class2->id, $data['selected_class_id']);
    }

    public function test_teacher_name_recorded_in_exam_updater_and_notification_sent(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;
        $school = School::find($schoolId);

        $class = SchoolClass::where('school_id', $schoolId)->first();
        $subject = Subject::where('school_id', $schoolId)->first();
        $student = Student::where('school_id', $schoolId)->where('class_id', $class->id)->first();

        $exam = Exam::create([
            'school_id' => $schoolId,
            'name' => 'Unit Test 1',
            'status' => 'Ongoing & Completed',
            'class_id' => $class->id,
            'created_by' => $schoolAdmin->id,
        ]);

        $assessment = ExamAssessment::create([
            'school_id' => $schoolId,
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'name' => 'Unit Test Marks',
            'max_marks' => 50,
            'pass_marks' => 17,
        ]);

        $postData = [
            'subject_id' => $subject->id,
            'records' => [
                [
                    'student_id' => $student->id,
                    'marks_obtained' => '42',
                    'max_marks' => 50,
                    'attendance_status' => 'present',
                ]
            ]
        ];

        $headers = [
            'X-School-Code' => $school ? $school->code : 'YIS2024'
        ];

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders($headers)
            ->postJson(
                "/school/examination/exams/{$exam->id}/save-update-marks-drawer-data",
                $postData
            );

        $response->assertStatus(200);

        $exam->refresh();
        $this->assertEquals($schoolAdmin->id, $exam->updated_by);
        $this->assertEquals($schoolAdmin->name, $exam->updater->name);

        $notification = \App\Models\Notification::where('school_id', $schoolId)
            ->where('related_id', $exam->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($notification);
        $this->assertStringContainsString($schoolAdmin->name, $notification->message);
    }

    public function test_reject_non_zero_marks_for_leave_or_absent_status(): void
    {
        $schoolAdmin = User::where('email', 'admin@yis.com')->first();
        $schoolId = $schoolAdmin->school_id;
        $school = School::find($schoolId);

        $class = SchoolClass::where('school_id', $schoolId)->first();
        $subject = Subject::where('school_id', $schoolId)->first();
        $student = Student::where('school_id', $schoolId)->where('class_id', $class->id)->first();

        $exam = Exam::create([
            'school_id' => $schoolId,
            'name' => 'Validation Test Exam',
            'status' => 'Ongoing & Completed',
            'class_id' => $class->id,
        ]);

        $assessment = ExamAssessment::create([
            'school_id' => $schoolId,
            'exam_id' => $exam->id,
            'subject_id' => $subject->id,
            'class_id' => $class->id,
            'name' => 'Assessment 1',
            'max_marks' => 50,
            'pass_marks' => 17,
        ]);

        $postData = [
            'subject_id' => $subject->id,
            'records' => [
                [
                    'student_id' => $student->id,
                    'assessments' => [
                        [
                            'assessment_id' => $assessment->id,
                            'assessment_name' => 'Assessment 1',
                            'marks_obtained' => '25',
                            'max_marks' => 50,
                            'attendance_status' => 'medical_leave',
                        ]
                    ]
                ]
            ]
        ];

        $response = $this->actingAs($schoolAdmin)
            ->withHeaders(['X-School-Code' => $school ? $school->code : 'YIS2024'])
            ->postJson("/school/examination/exams/{$exam->id}/save-update-marks-drawer-data", $postData);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Marks must be 0 when the student is marked as Absent or on Leave.'
        ]);
    }
}
