<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'students.view',
            'students.create',
            'students.edit',
            'students.delete',
            'students.import',
            'students.export',
            'attendance.mark',
            'attendance.view',
            'attendance.report',
            'attendance.export',
            'certificates.generate',
            'id_cards.generate',
            'staff.view',
            'staff.create',
            'staff.edit',
            'staff.delete',
            'login_logs.view',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }
    }
}
