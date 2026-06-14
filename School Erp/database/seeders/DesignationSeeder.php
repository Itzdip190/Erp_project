<?php

namespace Database\Seeders;

use App\Models\Designation;
use App\Models\School;
use Illuminate\Database\Seeder;

class DesignationSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'YIS2024')->firstOrFail();

        Designation::firstOrCreate(
            [
                'school_id' => $school->id,
                'name' => 'Teacher',
            ],
            [
                'description' => 'Academic Teaching Staff',
            ]
        );

        Designation::firstOrCreate(
            [
                'school_id' => $school->id,
                'name' => 'Principal',
            ],
            [
                'description' => 'School Principal',
            ]
        );
    }
}
