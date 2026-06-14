<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        // Define roles
        $superadmin = Role::findOrCreate('superadmin', 'web');
        $schoolAdmin = Role::findOrCreate('school_admin', 'web');
        $teacher = Role::findOrCreate('teacher', 'web');
        $accountant = Role::findOrCreate('accountant', 'web');
        Role::findOrCreate('parent', 'web');
        Role::findOrCreate('student', 'web');
        Role::findOrCreate('driver', 'web');

        // Assign permissions
        $allPermissions = Permission::all();
        $superadmin->syncPermissions($allPermissions);

        $schoolAdminPermissions = Permission::where('name', '!=', 'login_logs.view')->get();
        $schoolAdmin->syncPermissions($schoolAdminPermissions);

        $teacher->syncPermissions([
            'students.view',
            'attendance.mark',
            'attendance.view',
            'id_cards.generate',
        ]);

        $accountant->syncPermissions([
            'students.view',
            'students.export',
            'attendance.view',
        ]);
    }
}
