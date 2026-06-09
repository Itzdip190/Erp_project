<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Student;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\SubscriptionOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the SuperAdmin Dashboard.
     */
    public function index()
    {
        // 1. Stats Row 1
        $totalSchools = School::count();
        
        $activeSubscriptions = Subscription::where('subscription_ends_at', '>', Carbon::now())
            ->where('status', 'active')
            ->count();
            
        $totalStudents = Student::count();
        
        // Sum completed orders this month
        $revenueThisMonth = SubscriptionOrder::where('status', 'completed')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('amount');
            
        // Format revenue (e.g. ₹ 18.40L)
        $formattedRevenue = $this->formatRevenue($revenueThisMonth);

        // Calculate percentage changes (comparing this month vs last month) - mock / dynamic
        $prevMonthSchools = School::whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->whereYear('created_at', Carbon::now()->subMonth()->year)
            ->count();
        $schoolChange = $prevMonthSchools > 0 ? (($totalSchools - $prevMonthSchools) / $prevMonthSchools) * 100 : 12.5;

        // 2. Stats Row 2
        $expiringSoon = Subscription::where('status', 'active')
            ->whereBetween('subscription_ends_at', [Carbon::now(), Carbon::now()->addDays(7)])
            ->count();
            
        $suspendedSchools = School::where('status', 'suspended')->count();
        
        $newSchoolsThisMonth = School::whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();

        // 3. Line Chart: Monthly School Registrations (Last 12 Months)
        $schoolsLastYear = School::where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())->get();
        $monthlyRegistrations = [];
        for ($i = 11; $i >= 0; $i--) {
            $monthKey = Carbon::now()->subMonths($i)->format('Y-m');
            $monthLabel = Carbon::now()->subMonths($i)->format('M Y');
            $monthlyRegistrations[$monthLabel] = 0;
        }
        foreach ($schoolsLastYear as $school) {
            $label = $school->created_at->format('M Y');
            if (isset($monthlyRegistrations[$label])) {
                $monthlyRegistrations[$label]++;
            }
        }
        $chartMonths = array_keys($monthlyRegistrations);
        $chartSchoolCounts = array_values($monthlyRegistrations);

        // 4. Doughnut Chart: Subscription Plans Distribution
        $plans = Plan::withCount(['subscriptions' => function ($query) {
            $query->where('status', 'active');
        }])->get();
        
        $planLabels = [];
        $planCounts = [];
        foreach ($plans as $plan) {
            $planLabels[] = $plan->name;
            $planCounts[] = $plan->subscriptions_count;
        }

        // 5. Recent Schools Table (latest 5 schools with current plan, status, expiry)
        $recentSchools = School::with(['subscriptions' => function($q) {
            $q->latest();
        }, 'subscriptions.plan'])
        ->latest()
        ->take(5)
        ->get();

        // 6. Recent Orders Table (latest 5 completed/failed subscription orders)
        $recentOrders = SubscriptionOrder::with(['school', 'plan'])
            ->latest()
            ->take(5)
            ->get();

        return view('superadmin.dashboard.index', compact(
            'totalSchools',
            'activeSubscriptions',
            'totalStudents',
            'revenueThisMonth',
            'formattedRevenue',
            'schoolChange',
            'expiringSoon',
            'suspendedSchools',
            'newSchoolsThisMonth',
            'chartMonths',
            'chartSchoolCounts',
            'planLabels',
            'planCounts',
            'recentSchools',
            'recentOrders'
        ));
    }

    /**
     * Format revenue to lakh / generic format.
     */
    protected function formatRevenue($amount)
    {
        if ($amount >= 100000) {
            $lakhs = $amount / 100000;
            return '₹ ' . number_format($lakhs, 2) . 'L';
        }
        return '₹ ' . number_format($amount, 0);
    }
}
