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

        $designations = [
            ['name' => 'Principal',             'description' => 'School Principal'],
            ['name' => 'Vice Principal',         'description' => 'Vice Principal'],
            ['name' => 'Teacher',                'description' => 'Academic Teaching Staff'],
            ['name' => 'Senior Teacher',         'description' => 'Senior Academic Teaching Staff'],
            ['name' => 'Assistant Teacher',      'description' => 'Assistant Teaching Staff'],
            ['name' => 'Subject Teacher',        'description' => 'Subject Specialist Teacher'],
            ['name' => 'Class Teacher',          'description' => 'Class / Form Teacher'],
            ['name' => 'Head of Department',     'description' => 'Head of Academic Department'],
            ['name' => 'Lecturer',               'description' => 'Academic Lecturer'],
            ['name' => 'Accountant',             'description' => 'Finance & Accounts'],
            ['name' => 'Clerk',                  'description' => 'Administrative Clerk'],
            ['name' => 'Librarian',              'description' => 'School Librarian'],
            ['name' => 'Receptionist',           'description' => 'Front Desk Receptionist'],
            ['name' => 'Peon',                   'description' => 'Office Support Staff'],
            ['name' => 'Driver',                 'description' => 'School Bus Driver'],
            ['name' => 'Lab Assistant',          'description' => 'Science / Computer Lab Assistant'],
            ['name' => 'Sports Coach',           'description' => 'Physical Education & Sports Coach'],
            ['name' => 'Counsellor',             'description' => 'Student Counsellor'],
        ];

        foreach ($designations as $designation) {
            Designation::firstOrCreate(
                ['school_id' => $school->id, 'name' => $designation['name']],
                ['description' => $designation['description']]
            );
        }
    }
}

