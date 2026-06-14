<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class ClassSectionSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'YIS2024')->firstOrFail();
        $teacher = Staff::where('school_id', $school->id)->where('employee_id', 'EMP001')->first();

        // 1. Create Class 9
        $class9 = SchoolClass::firstOrCreate(
            [
                'school_id' => $school->id,
                'name' => 'Class 9',
            ],
            [
                'numeric_name' => 9,
            ]
        );

        // Class 9 - Section A
        Section::firstOrCreate(
            [
                'school_id' => $school->id,
                'class_id' => $class9->id,
                'name' => 'A',
            ],
            [
                'class_teacher_id' => $teacher?->id,
            ]
        );

        // Class 9 - Section B
        Section::firstOrCreate(
            [
                'school_id' => $school->id,
                'class_id' => $class9->id,
                'name' => 'B',
            ]
        );

        // 2. Create Class 10
        $class10 = SchoolClass::firstOrCreate(
            [
                'school_id' => $school->id,
                'name' => 'Class 10',
            ],
            [
                'numeric_name' => 10,
            ]
        );

        // Class 10 - Section A
        Section::firstOrCreate(
            [
                'school_id' => $school->id,
                'class_id' => $class10->id,
                'name' => 'A',
            ]
        );

        // Class 10 - Section B
        Section::firstOrCreate(
            [
                'school_id' => $school->id,
                'class_id' => $class10->id,
                'name' => 'B',
            ]
        );
    }
}
