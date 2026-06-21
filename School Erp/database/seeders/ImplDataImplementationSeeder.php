<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\School;

class ImplDataImplementationSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            'Student Data', 'Staff Data', 'Student Photos', 'Staff Photos', 
            'Subject Teacher Mapping', 'Time Table Data', 'Fee Data', 
            'Transport Data', 'Library Data', 'Inventory Data', 
            'Report Card Structure', 'Admission Process', 'Leave Data', 
            'Payroll Data', 'Event', 'Holiday Data'
        ];

        $schools = School::all();

        foreach ($schools as $school) {
            foreach ($modules as $module) {
                DB::table('impl_data_implementation')->updateOrInsert(
                    ['school_id' => $school->id, 'module_name' => $module],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
