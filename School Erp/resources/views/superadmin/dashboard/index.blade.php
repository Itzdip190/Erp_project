@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* Stats Cards Styles */
    .stat-card {
        border-radius: 16px !important;
        border: none !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.015) !important;
        background-color: #ffffff;
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
        position: relative;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.035) !important;
    }

    .stat-icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    /* Accents colors from design */
    .bg-light-blue { background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .bg-light-green { background-color: rgba(16, 185, 129, 0.1); color: #10b981; }
    .bg-light-teal { background-color: rgba(6, 182, 212, 0.1); color: #06b6d4; }
    .bg-light-orange { background-color: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .bg-light-yellow { background-color: rgba(234, 179, 8, 0.1); color: #eab308; }
    .bg-light-red { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .bg-light-purple { background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

    .stat-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1e1b4b;
        letter-spacing: -0.5px;
    }

    .stat-trend {
        font-size: 0.78rem;
        font-weight: 600;
    }

    .trend-up { color: #10b981; }
    .trend-down { color: #ef4444; }

    /* SVG Sparkline Sparkle */
    .sparkline-container {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 35px;
    }

    /* Charts styling */
    .chart-container-card {
        padding: 1.5rem;
    }

    .chart-title-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }

    .chart-title-text {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e1b4b;
    }

    .chart-filter-select {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 4px 12px;
        color: #4b5563;
        outline: none;
        background-color: #ffffff;
    }

    /* Tables styling */
    .table-custom th {
        font-size: 0.72rem;
        font-weight: 800;
        text-transform: uppercase;
        color: #64748b;
        letter-spacing: 0.8px;
        border-top: none !important;
        border-bottom: 2px solid #f3f4f6 !important;
        padding: 12px 16px !important;
    }

    .table-custom td {
        font-size: 0.88rem;
        color: #1e1b4b;
        vertical-align: middle !important;
        padding: 14px 16px !important;
        border-bottom: 1px solid #f3f4f6 !important;
        border-top: none !important;
    }

    .school-name-td {
        font-weight: 700;
        color: #1e1b4b;
    }

    .badge-premium-trial {
        background-color: rgba(139, 92, 246, 0.12);
        color: #8b5cf6;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .badge-status-active {
        background-color: #ecfdf5;
        color: #10b981;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .badge-status-suspended {
        background-color: #fef2f2;
        color: #ef4444;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .badge-status-trial {
        background-color: #fef9c3;
        color: #ca8a04;
        font-weight: 700;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 8px;
    }

    .btn-view-action {
        background-color: #f3f4f6;
        border: none;
        color: #4b5563;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 5px 12px;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .btn-view-action:hover {
        background-color: #e5e7eb;
        color: #1e1b4b;
    }

    /* Quick Actions */
    .quick-action-card {
        background: #ffffff;
        border: 1px solid rgba(229, 231, 235, 0.5);
        border-radius: 16px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-align: center;
        text-decoration: none !important;
        transition: all 0.2s;
        box-shadow: 0 4px 15px rgba(0,0,0,0.005);
    }

    .quick-action-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.025);
        border-color: rgba(229, 186, 115, 0.3);
    }

    .quick-action-icon-circle {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
        margin-bottom: 0.75rem;
    }

    .quick-action-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #1e1b4b;
    }

    /* Bottom Promotion Banner */
    .bottom-promo-banner {
        background: linear-gradient(135deg, #161329 0%, #0d0c18 100%);
        border-radius: 20px;
        padding: 1.8rem 2.2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 1.5rem;
        border: 1px solid rgba(255, 255, 255, 0.04);
        position: relative;
        overflow: hidden;
    }

    .bottom-promo-banner::before {
        content: "";
        position: absolute;
        width: 250px;
        height: 250px;
        background: radial-gradient(circle, rgba(229, 186, 115, 0.05) 0%, transparent 70%);
        top: -125px;
        right: -100px;
    }

    .promo-content {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .promo-icon-box {
        width: 54px;
        height: 54px;
        background: rgba(229, 186, 115, 0.1);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #e5ba73;
        font-size: 1.4rem;
    }

    .promo-title-main {
        font-family: 'Syne', sans-serif;
        font-size: 1.15rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 0.2rem;
    }

    .promo-text-sub {
        font-size: 0.88rem;
        color: #94a3b8;
        margin-bottom: 0;
    }

    .btn-gold-banner {
        background: linear-gradient(135deg, #e5ba73, #c59b27);
        color: #0c1024 !important;
        border: none;
        font-weight: 700;
        border-radius: 12px;
        font-size: 0.88rem;
        padding: 10px 22px;
        box-shadow: 0 4px 15px rgba(229, 186, 115, 0.25);
        transition: all 0.2s;
        white-space: nowrap;
    }

    .btn-gold-banner:hover {
        transform: scale(1.02);
        box-shadow: 0 6px 20px rgba(229, 186, 115, 0.35);
    }
</style>
@endsection

@section('content')

<!-- Row 1: High-level Metrics Stats -->
<div class="row mt-4">
    <!-- Card 1: Total Schools -->
    <div class="col-xl-3 col-md-6 col-12 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrapper bg-light-blue">
                        <i class="fas fa-school"></i>
                    </div>
                    <span class="stat-trend trend-up">
                        <i class="fas fa-arrow-up me-1"></i>
                        <span>12.5%</span>
                    </span>
                </div>
                <div class="stat-label">Total Schools</div>
                <div class="stat-value" id="val-schools">{{ $totalSchools }}</div>
            </div>
            <!-- Sparkline SVG -->
            <div class="sparkline-container">
                <svg viewBox="0 0 100 30" width="100%" height="30" preserveAspectRatio="none">
                    <path d="M 0 25 Q 15 15, 30 22 T 60 10 T 80 18 T 100 5" fill="none" stroke="#3b82f6" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M 0 25 Q 15 15, 30 22 T 60 10 T 80 18 T 100 5 L 100 30 L 0 30 Z" fill="rgba(59, 130, 246, 0.05)"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 2: Active Subscriptions -->
    <div class="col-xl-3 col-md-6 col-12 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrapper bg-light-green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <span class="stat-trend trend-up">
                        <i class="fas fa-arrow-up me-1"></i>
                        <span>8.4%</span>
                    </span>
                </div>
                <div class="stat-label">Active Subscriptions</div>
                <div class="stat-value" id="val-subs">{{ $activeSubscriptions }}</div>
            </div>
            <!-- Sparkline SVG -->
            <div class="sparkline-container">
                <svg viewBox="0 0 100 30" width="100%" height="30" preserveAspectRatio="none">
                    <path d="M 0 20 Q 20 8, 40 15 T 70 5 T 100 10" fill="none" stroke="#10b981" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M 0 20 Q 20 8, 40 15 T 70 5 T 100 10 L 100 30 L 0 30 Z" fill="rgba(16, 185, 129, 0.05)"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Students -->
    <div class="col-xl-3 col-md-6 col-12 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrapper bg-light-teal">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <span class="stat-trend trend-up">
                        <i class="fas fa-arrow-up me-1"></i>
                        <span>3.2%</span>
                    </span>
                </div>
                <div class="stat-label">Total Students</div>
                <div class="stat-value" id="val-students">{{ number_format($totalStudents) }}</div>
            </div>
            <!-- Sparkline SVG -->
            <div class="sparkline-container">
                <svg viewBox="0 0 100 30" width="100%" height="30" preserveAspectRatio="none">
                    <path d="M 0 28 Q 15 22, 35 25 T 65 12 T 85 8 T 100 15" fill="none" stroke="#06b6d4" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M 0 28 Q 15 22, 35 25 T 65 12 T 85 8 T 100 15 L 100 30 L 0 30 Z" fill="rgba(6, 182, 212, 0.05)"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Card 4: Monthly Revenue -->
    <div class="col-xl-3 col-md-6 col-12 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="stat-icon-wrapper bg-light-orange">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <span class="stat-trend trend-up">
                        <i class="fas fa-arrow-up me-1"></i>
                        <span>15.3%</span>
                    </span>
                </div>
                <div class="stat-label">Monthly Revenue</div>
                <div class="stat-value" id="val-revenue">{{ $formattedRevenue }}</div>
            </div>
            <!-- Sparkline SVG -->
            <div class="sparkline-container">
                <svg viewBox="0 0 100 30" width="100%" height="30" preserveAspectRatio="none">
                    <path d="M 0 22 C 20 28, 40 10, 60 15 C 80 20, 90 2, 100 5" fill="none" stroke="#f59e0b" stroke-width="2" stroke-linecap="round"></path>
                    <path d="M 0 22 C 20 28, 40 10, 60 15 C 80 20, 90 2, 100 5 L 100 30 L 0 30 Z" fill="rgba(245, 158, 11, 0.05)"></path>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Row 2: Secondary Status Cards -->
<div class="row">
    <!-- Card 5: Expiring Soon -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="stat-icon-wrapper bg-light-yellow">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <div class="stat-label text-truncate">Expiring Soon (&le; 7 days)</div>
                    <div class="stat-value">{{ $expiringSoon }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 6: Suspended Schools -->
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="stat-icon-wrapper bg-light-red">
                    <i class="fas fa-ban"></i>
                </div>
                <div>
                    <div class="stat-label text-truncate">Suspended Schools</div>
                    <div class="stat-value">{{ $suspendedSchools }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 7: New Schools This Month -->
    <div class="col-lg-4 col-md-12 mb-4">
        <div class="card stat-card">
            <div class="card-body p-4 d-flex align-items-center gap-3">
                <div class="stat-icon-wrapper bg-light-purple">
                    <i class="fas fa-plus"></i>
                </div>
                <div>
                    <div class="stat-label text-truncate">New Schools This Month</div>
                    <div class="stat-value">{{ $newSchoolsThisMonth }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 3: Charts Section -->
<div class="row">
    <!-- Line Chart: Monthly School Registrations -->
    <div class="col-lg-8 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Monthly School Registrations</h5>
                <select class="chart-filter-select">
                    <option>Last 12 Months</option>
                    <option>Last 6 Months</option>
                </select>
            </div>
            <div class="card-body-custom">
                <div style="height: 300px; width: 100%; position: relative;">
                    <canvas id="schoolRegistrationsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Doughnut Chart: Plan Distribution -->
    <div class="col-lg-4 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Subscription Plans</h5>
                <span class="badge" style="background-color: #ecfdf5; color: #10b981; font-weight: 700; font-size: 0.72rem; padding: 4px 8px; border-radius: 6px;">Active Share</span>
            </div>
            <div class="card-body-custom d-flex flex-column justify-content-center">
                <div style="height: 220px; width: 100%; position: relative;" class="mb-3">
                    <canvas id="plansDistributionChart"></canvas>
                </div>
                <!-- Custom Legends styled nicely -->
                <div class="d-flex justify-content-center gap-3 mt-2" style="font-size: 0.8rem; font-weight: 600;">
                    @foreach($planLabels as $index => $label)
                        <div class="d-flex align-items-center gap-1">
                            <span style="display: inline-block; width: 10px; height: 10px; border-radius: 50%; background-color: {{ ['#3b82f6', '#10b981', '#f59e0b'][$index % 3] }}"></span>
                            <span class="text-muted">{{ $label }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 4: Tables Section -->
<div class="row">
    <!-- Left Table: Recent Schools -->
    <div class="col-xl-7 col-12 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Recent Schools Registered</h5>
                <a href="#" style="font-size: 0.85rem; font-weight: 700; color: #e5ba73; text-decoration: none;">View All</a>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-custom m-0">
                        <thead>
                            <tr>
                                <th>School Name</th>
                                <th>Plan</th>
                                <th>Status</th>
                                <th>Expiry Date</th>
                                <th class="text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentSchools as $school)
                            @php
                                $sub = $school->subscriptions->first();
                            @endphp
                            <tr>
                                <td class="school-name-td">{{ $school->name }}</td>
                                <td>
                                    @if($sub && $sub->plan)
                                        <span class="badge-premium-trial">{{ $sub->plan->name }}</span>
                                    @else
                                        <span class="text-muted">&ndash;</span>
                                    @endif
                                </td>
                                <td>
                                    @if($school->status == 'active')
                                        <span class="badge-status-active">Active</span>
                                    @elseif($school->status == 'suspended')
                                        <span class="badge-status-suspended">Suspended</span>
                                    @else
                                        <span class="badge-status-trial">Trial</span>
                                    @endif
                                </td>
                                <td>
                                    @if($sub && $sub->subscription_ends_at)
                                        {{ $sub->subscription_ends_at->format('M d, Y') }}
                                    @else
                                        <span class="text-muted">&ndash;</span>
                                    @endif
                                </td>
                                <td class="text-right">
                                    <button class="btn btn-view-action btn-sm">View</button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No schools registered yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Table: Recent Orders -->
    <div class="col-xl-5 col-12 mb-4">
        <div class="card card-custom h-100">
            <div class="card-header-custom">
                <h5 class="card-title-custom">Recent Subscription Orders</h5>
                <a href="#" style="font-size: 0.85rem; font-weight: 700; color: #e5ba73; text-decoration: none;">View All</a>
            </div>
            <div class="card-body-custom p-0">
                <div class="table-responsive">
                    <table class="table table-custom m-0">
                        <thead>
                            <tr>
                                <th>School</th>
                                <th>Amount</th>
                                <th>Gateway</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td class="font-weight-bold">{{ $order->school->name ?? 'Deleted School' }}</td>
                                <td>&nbsp;₹{{ number_format($order->amount, 0) }}</td>
                                <td><span class="text-capitalize text-muted" style="font-size: 0.8rem; font-weight: 600;">{{ $order->gateway }}</span></td>
                                <td>
                                    @if($order->status == 'completed')
                                        <span class="badge-status-active">Completed</span>
                                    @elseif($order->status == 'failed')
                                        <span class="badge-status-suspended">Failed</span>
                                    @else
                                        <span class="badge-status-trial">Pending</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">No orders processed yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Row 5: Quick Actions Bar -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card card-custom">
            <div class="card-header-custom py-3">
                <h5 class="card-title-custom">Quick Operations Bar</h5>
            </div>
            <div class="card-body-custom">
                <div class="row">
                    <!-- Action 1: Add New School -->
                    <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                        <a href="{{ route('superadmin.schools.create') }}" class="quick-action-card">
                            <div class="quick-action-icon-circle bg-light-blue">
                                <i class="fas fa-plus"></i>
                            </div>
                            <span class="quick-action-title">Add New School</span>
                        </a>
                    </div>
                    <!-- Action 2: Create Plan -->
                    <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                        <a href="{{ route('superadmin.plans.create') }}" class="quick-action-card">
                            <div class="quick-action-icon-circle bg-light-purple">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <span class="quick-action-title">Create Plan</span>
                        </a>
                    </div>
                    <!-- Action 3: Send Broadcast -->
                    <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                        <a href="{{ route('superadmin.broadcast') }}" class="quick-action-card">
                            <div class="quick-action-icon-circle bg-light-orange">
                                <i class="fas fa-paper-plane"></i>
                            </div>
                            <span class="quick-action-title">Send Broadcast</span>
                        </a>
                    </div>
                    <!-- Action 4: View Server Status -->
                    <div class="col-md-3 col-sm-6">
                        <a href="{{ route('superadmin.server-status') }}" class="quick-action-card">
                            <div class="quick-action-icon-circle bg-light-teal">
                                <i class="fas fa-server"></i>
                            </div>
                            <span class="quick-action-title">View Server Status</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Banner -->
<div class="row">
    <div class="col-12">
        <div class="bottom-promo-banner">
            <div class="promo-content">
                <div class="promo-icon-box">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div>
                    <h4 class="promo-title-main">Streamline Your School Operations</h4>
                    <p class="promo-text-sub">Configure notification preferences, edit templates, and review analytics logs to manage global institutions.</p>
                </div>
            </div>
            <div>
                <button class="btn btn-gold-banner mt-3 mt-lg-0">Explore Features <i class="fas fa-arrow-right ms-2"></i></button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Line Chart: Monthly School Registrations
    const regCtx = document.getElementById('schoolRegistrationsChart').getContext('2d');
    
    // Create soft gold/yellow gradient
    const regGradient = regCtx.createLinearGradient(0, 0, 0, 300);
    regGradient.addColorStop(0, 'rgba(229, 186, 115, 0.4)');
    regGradient.addColorStop(1, 'rgba(229, 186, 115, 0.0)');

    new Chart(regCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartMonths) !!},
            datasets: [{
                label: 'Schools Registered',
                data: {!! json_encode($chartSchoolCounts) !!},
                borderColor: '#e5ba73',
                borderWidth: 3,
                backgroundColor: regGradient,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#0c1024',
                pointBorderColor: '#e5ba73',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0c1024',
                    titleColor: '#ffffff',
                    bodyColor: '#e5ba73',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b',
                        font: {
                            family: 'Plus Jakarta Sans',
                            weight: 600,
                            size: 11
                        }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(229, 231, 235, 0.4)'
                    },
                    ticks: {
                        color: '#64748b',
                        stepSize: 1,
                        font: {
                            family: 'Plus Jakarta Sans',
                            weight: 600,
                            size: 11
                        }
                    }
                }
            }
        }
    });

    // 2. Doughnut Chart: Plan Distribution
    const plansCtx = document.getElementById('plansDistributionChart').getContext('2d');
    new Chart(plansCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($planLabels) !!},
            datasets: [{
                data: {!! json_encode($planCounts) !!},
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#0c1024',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    padding: 12,
                    cornerRadius: 8,
                    displayColors: true,
                    boxWidth: 8,
                    boxHeight: 8
                }
            }
        }
    });

    // 3. Count Up Animation for Stats
    function animateValue(id, start, end, duration) {
        if (start === end) return;
        const range = end - start;
        let current = start;
        const increment = end > start ? 1 : -1;
        const stepTime = Math.abs(Math.floor(duration / range));
        const obj = document.getElementById(id);
        
        // If range is extremely large, step faster
        const steps = 50;
        const stepVal = range / steps;
        let step = 0;

        const timer = setInterval(function() {
            step++;
            current = Math.floor(start + (stepVal * step));
            
            if (step >= steps) {
                clearInterval(timer);
                obj.innerHTML = end.toLocaleString();
            } else {
                obj.innerHTML = current.toLocaleString();
            }
        }, 30);
    }

    window.addEventListener('DOMContentLoaded', () => {
        animateValue('val-schools', 0, {{ $totalSchools }}, 1500);
        animateValue('val-subs', 0, {{ $activeSubscriptions }}, 1500);
        animateValue('val-students', 0, {{ $totalStudents }}, 1500);
    });
</script>
@endsection
