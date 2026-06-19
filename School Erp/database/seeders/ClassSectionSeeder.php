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

        $classesData = [
            ['name' => 'Nursery', 'numeric_name' => 0],
            ['name' => 'LKG', 'numeric_name' => 0],
            ['name' => 'UKG', 'numeric_name' => 0],
            ['name' => 'Class 1', 'numeric_name' => 1],
            ['name' => 'Class 2', 'numeric_name' => 2],
            ['name' => 'Class 3', 'numeric_name' => 3],
            ['name' => 'Class 4', 'numeric_name' => 4],
            ['name' => 'Class 5', 'numeric_name' => 5],
            ['name' => 'Class 6', 'numeric_name' => 6],
            ['name' => 'Class 7', 'numeric_name' => 7],
            ['name' => 'Class 8', 'numeric_name' => 8],
            ['name' => 'Class 9', 'numeric_name' => 9],
            ['name' => 'Class 10', 'numeric_name' => 10],
            ['name' => 'Class 11', 'numeric_name' => 11],
            ['name' => 'Class 12', 'numeric_name' => 12],
        ];

        foreach ($classesData as $data) {
            $class = SchoolClass::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'name' => $data['name'],
                ],
                [
                    'numeric_name' => $data['numeric_name'],
                ]
            );

            // Class-specific teacher assignments if applicable
            $teacherId = null;
            if ($data['name'] === 'Class 9') {
                $teacherId = $teacher?->id;
            }

            // Create default sections A and B
            Section::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'class_id' => $class->id,
                    'name' => 'A',
                ],
                [
                    'class_teacher_id' => $teacherId,
                ]
            );

            Section::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'class_id' => $class->id,
                    'name' => 'B',
                ]
            );
        }
    }
}
