<?php

use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Section;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $schools = School::all();
        if ($schools->isEmpty()) {
            return;
        }

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

        foreach ($schools as $school) {
            foreach ($classesData as $classInfo) {
                $class = SchoolClass::firstOrCreate([
                    'school_id' => $school->id,
                    'name' => $classInfo['name'],
                ], [
                    'numeric_name' => $classInfo['numeric_name'],
                ]);

                // Create default sections A and B if they don't exist
                Section::firstOrCreate([
                    'school_id' => $school->id,
                    'class_id' => $class->id,
                    'name' => 'A',
                ]);

                Section::firstOrCreate([
                    'school_id' => $school->id,
                    'class_id' => $class->id,
                    'name' => 'B',
                ]);
            }
        }
    }

    public function down(): void
    {
        // No down action to preserve data integrity
    }
};
