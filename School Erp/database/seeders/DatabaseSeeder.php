<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\School;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionOrder;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Roles using Spatie laravel-permission
        $roles = [
            'superadmin',
            'school_admin',
            'teacher',
            'accountant',
            'parent',
            'student',
            'driver'
        ];

        foreach ($roles as $roleName) {
            Role::firstOrCreate(['name' => $roleName]);
        }

        // 2. Create Plans
        $basicPlan = Plan::create([
            'name' => 'Basic Plan',
            'price' => 5000.00,
            'duration_days' => 30,
        ]);

        $standardPlan = Plan::create([
            'name' => 'Standard Plan',
            'price' => 15000.00,
            'duration_days' => 90,
        ]);

        $premiumPlan = Plan::create([
            'name' => 'Premium Plan',
            'price' => 50000.00,
            'duration_days' => 365,
        ]);

        $plans = [$basicPlan, $standardPlan, $premiumPlan];

        // 3. Create SuperAdmin User
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@schoolcloud.com',
            'mobile' => '9999999999',
            'password' => Hash::make('Sam123'),
            'role' => 'superadmin',
            'school_id' => null,
        ]);
        $superAdmin->assignRole('superadmin');

        // Create a test Student User for Admission ID login demo
        $testStudent = User::create([
            'name' => 'John Doe',
            'email' => 'student@schoolcloud.com',
            'mobile' => '9876543210',
            'admission_id' => 'ADM-2026-001',
            'password' => Hash::make('password'),
            'role' => 'student',
            'school_id' => null,
        ]);
        $testStudent->assignRole('student');

        // 4. Create Schools (spread across last 12 months to populate monthly school registrations chart)
        $schoolNames = [
            'Delhi Public School' => 'dps.schoolcloud.com',
            'Greenwood International' => 'greenwood.schoolcloud.com',
            'St. Xavier\'s Academy' => 'stxaviers.schoolcloud.com',
            'Oakridge School' => 'oakridge.schoolcloud.com',
            'Ryan International' => 'ryan.schoolcloud.com',
            'Vidyalaya High' => 'vidyalaya.schoolcloud.com',
            'Apex International' => 'apex.schoolcloud.com',
            'Sunrise Academy' => 'sunrise.schoolcloud.com',
            'Beacon High' => 'beacon.schoolcloud.com',
            'Legacy School' => 'legacy.schoolcloud.com',
        ];

        $statuses = ['active', 'active', 'active', 'active', 'active', 'suspended', 'trial', 'trial', 'active', 'active'];
        
        $index = 0;
        $schools = [];

        foreach ($schoolNames as $name => $domain) {
            // Distribute creation dates over the last 12 months
            $createdAt = Carbon::now()->subMonths(11 - $index)->subDays(rand(1, 20));
            $status = $statuses[$index];

            $school = School::create([
                'name' => $name,
                'custom_domain' => $domain,
                'status' => $status,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            $schools[] = $school;

            // Create School Admin
            $schoolAdminEmail = strtolower(str_replace([' ', '\''], '', $name)) . '@schoolcloud.com';
            $admin = User::create([
                'name' => $name . ' Admin',
                'email' => $schoolAdminEmail,
                'password' => Hash::make('password'),
                'role' => 'school_admin',
                'school_id' => $school->id,
                'created_at' => $createdAt,
            ]);
            $admin->assignRole('school_admin');

            // Create a few student records for counting (e.g. 50 per school)
            for ($s = 1; $s <= rand(50, 120); $s++) {
                Student::create([
                    'school_id' => $school->id,
                    'name' => "Student $s - " . $name,
                    'created_at' => $createdAt->copy()->addDays(rand(1, 10)),
                ]);
            }

            $index++;
        }

        // 5. Create Subscriptions
        foreach ($schools as $i => $school) {
            $plan = $plans[$i % 3];
            $status = $school->status == 'suspended' ? 'suspended' : 'active';
            
            // Set subscription expiry dates
            if ($i == 4) { // Expiring soon (within 7 days)
                $endsAt = Carbon::now()->addDays(rand(1, 6));
            } elseif ($i == 5) { // Expired
                $endsAt = Carbon::now()->subDays(rand(1, 10));
                $status = 'expired';
            } else { // Active long term
                $endsAt = Carbon::now()->addDays($plan->duration_days);
            }

            Subscription::create([
                'school_id' => $school->id,
                'plan_id' => $plan->id,
                'subscription_ends_at' => $endsAt,
                'status' => $status,
                'created_at' => $school->created_at,
            ]);

            // Create initial subscription order
            SubscriptionOrder::create([
                'school_id' => $school->id,
                'plan_id' => $plan->id,
                'amount' => $plan->price,
                'gateway' => ['stripe', 'razorpay', 'paypal', 'bank_transfer'][$i % 4],
                'status' => 'completed',
                'created_at' => $school->created_at,
            ]);
        }

        // 6. Generate More Historical Orders to populate monthly revenue stats
        // We'll add random orders spread out across the last 12 months
        $gateways = ['stripe', 'razorpay', 'paypal', 'bank_transfer'];
        for ($m = 0; $m < 12; $m++) {
            $monthDate = Carbon::now()->subMonths($m);
            $orderCount = rand(3, 8); // 3 to 8 orders per month

            for ($o = 0; $o < $orderCount; $o++) {
                $randomSchool = $schools[array_rand($schools)];
                $randomPlan = $plans[array_rand($plans)];
                $orderStatus = rand(1, 10) > 1 ? 'completed' : 'failed'; // 90% success rate

                SubscriptionOrder::create([
                    'school_id' => $randomSchool->id,
                    'plan_id' => $randomPlan->id,
                    'amount' => $randomPlan->price,
                    'gateway' => $gateways[array_rand($gateways)],
                    'status' => $orderStatus,
                    'created_at' => $monthDate->copy()->subDays(rand(1, 27)),
                ]);
            }
        }
    }
}
