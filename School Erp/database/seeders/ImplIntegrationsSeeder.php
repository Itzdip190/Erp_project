<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\School;

class ImplIntegrationsSeeder extends Seeder
{
    public function run(): void
    {
        $integrations = [
            'Biometric', 'GPS', 'Payment Gateway', 'Email', 'Whatsapp', 
            'Admission Widget', 'CCTV'
        ];

        $schools = School::all();

        foreach ($schools as $school) {
            foreach ($integrations as $integration) {
                DB::table('impl_integrations')->updateOrInsert(
                    ['school_id' => $school->id, 'integration_name' => $integration],
                    ['created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
