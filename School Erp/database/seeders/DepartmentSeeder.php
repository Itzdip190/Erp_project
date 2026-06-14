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

        Department::firstOrCreate(
            [
                'school_id' => $school->id,
                'name' => 'Academic',
            ],
            [
                'description' => 'Academic Department',
            ]
        );

        Department::firstOrCreate(
            [
                'school_id' => $school->id,
                'name' => 'Administration',
            ],
            [
                'description' => 'School Administration',
            ]
        );
    }
}
