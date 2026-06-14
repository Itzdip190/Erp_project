<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        Plan::firstOrCreate(
            ['name' => 'Basic'],
            [
                'price' => 999.00,
                'duration_days' => 365,
                'features' => json_encode(['students_limit' => 1000, 'staff_limit' => 100]),
            ]
        );
    }
}
