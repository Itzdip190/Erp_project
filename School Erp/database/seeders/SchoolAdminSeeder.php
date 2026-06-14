<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SchoolAdminSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'YIS2024')->firstOrFail();

        $user = User::firstOrCreate(
            ['email' => 'admin@yis.com'],
            [
                'name' => 'YIS School Admin',
                'password' => Hash::make('SchoolAdminSecurePass2026!'),
                'school_id' => $school->id,
                'is_active' => true,
            ]
        );

        $user->assignRole('school_admin');
    }
}
