<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'superadmin@schoolcloud.com'],
            [
                'name'      => 'Super Admin',
                'password'  => Hash::make('SuperAdminSecurePass2026!'),
                'role'      => 'superadmin',
                'school_id' => null,
                'is_active' => true,
            ]
        );

        // Always ensure both the role column AND the Spatie role are set
        $user->update(['role' => 'superadmin']);
        $user->syncRoles(['superadmin']);
    }
}
