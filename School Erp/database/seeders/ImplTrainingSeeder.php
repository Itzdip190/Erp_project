<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\School;

class ImplTrainingSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'Admin Role Management', 'Staff Management', 'Staff Attendance and Reports', 
            'Teacher Assignment', 'Time Table Management', 'Teacher Substitution', 
            'Student Management', 'Student Attendance and Reports', 'Fee Management', 
            'Transport Management', 'Diary', 'Download Statistics', 'I Card', 
            'Event Management', 'Certificate Management', 'Leave Management', 
            'Communication & Messaging', 'Examination', 'Admissions', 'Library', 
            'Gallery', 'Front Office', 'Income Expense', 'Payroll', 'Copy Checking'
        ];

        $schools = School::all();

        foreach ($schools as $school) {
            foreach ($modules as $module) {
                DB::table('impl_training')->updateOrInsert(
                    ['school_id' => $school->id, 'module_name' => $module],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
