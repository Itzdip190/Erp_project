<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::firstOrCreate(
            ['code' => 'YIS2024'],
            [
                'name' => 'Yash International School',
                'custom_domain' => 'yis.schoolcloud.com',
                'logo' => null,
                'address' => '123 Academic Block, Education City',
                'phone' => '9876543210',
                'dashboard_theme' => 'blue',
                'status' => 'active',
                'sms_config' => [
                    'gateway' => 'mock',
                    'api_key' => 'mock_key_123',
                ],
                'late_grace_minutes' => 15,
                'staff_punch_in_start' => '08:00:00',
                'staff_punch_in_end' => '18:00:00',
            ]
        );
    }
}
