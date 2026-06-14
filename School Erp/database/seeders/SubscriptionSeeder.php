<?php

namespace Database\Seeders;

use App\Models\Plan;
use App\Models\School;
use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::where('code', 'YIS2024')->firstOrFail();
        $plan = Plan::where('name', 'Basic')->firstOrFail();

        Subscription::firstOrCreate(
            [
                'school_id' => $school->id,
                'plan_id' => $plan->id,
            ],
            [
                'status' => 'active',
                'subscription_ends_at' => now()->addYear(),
            ]
        );
    }
}
