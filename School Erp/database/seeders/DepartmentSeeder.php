<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\School;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'YIS2024')->firstOrFail();

        $departments = [
            ['name' => 'Administration',       'description' => 'School Administration'],
            ['name' => 'Academic',             'description' => 'General Academic Department'],
            ['name' => 'Science',              'description' => 'Science Department'],
            ['name' => 'Mathematics',          'description' => 'Mathematics Department'],
            ['name' => 'English',              'description' => 'English & Literature Department'],
            ['name' => 'Social Studies',       'description' => 'Social Studies & History Department'],
            ['name' => 'Arts & Craft',         'description' => 'Arts & Craft Department'],
            ['name' => 'Physical Education',   'description' => 'Physical Education & Sports'],
            ['name' => 'Computer Science',     'description' => 'Computer Science & IT Department'],
            ['name' => 'Commerce',             'description' => 'Commerce & Accountancy Department'],
            ['name' => 'Library',              'description' => 'School Library'],
            ['name' => 'Finance',              'description' => 'Finance & Accounts Department'],
            ['name' => 'Non-Teaching',         'description' => 'Non-Teaching Support Staff'],
        ];

        foreach ($departments as $department) {
            Department::firstOrCreate(
                ['school_id' => $school->id, 'name' => $department['name']],
                ['description' => $department['description']]
            );
        }
    }
}

