<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\School;

class ImplTemplateImplementationSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            'Fee Receipt', 'Fee Invoice', 'Report Card', 'Student ID Card', 
            'Staff ID Card', 'Transfer Certificate', 'Other Certificate', 
            'Salary Slip', 'Admit Card', 'Library Card', 'Transport ID Card', 
            'Gate Pass', 'Visitor Pass', 'Student Registration Form', 
            'Enquiry and Registration Form'
        ];

        $schools = School::all();

        foreach ($schools as $school) {
            foreach ($templates as $template) {
                DB::table('impl_template_implementation')->updateOrInsert(
                    ['school_id' => $school->id, 'template_name' => $template],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
