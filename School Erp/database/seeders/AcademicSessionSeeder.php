<?php

namespace Database\Seeders;

use App\Models\AcademicSession;
use App\Models\School;
use Illuminate\Database\Seeder;

class AcademicSessionSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'YIS2024')->firstOrFail();

        AcademicSession::firstOrCreate(
            [
                'school_id' => $school->id,
                'name' => '2025-26',
            ],
            [
                'start_date' => '2025-04-01',
                'end_date' => '2026-03-31',
                'is_current' => true,
            ]
        );
    }
}
