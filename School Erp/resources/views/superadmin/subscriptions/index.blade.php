@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* Table & Badges Custom Styling */
    .sub-table-card {
        border-radius: 20px !important;
        border: none !important;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.02) !important;
        overflow: hidden;
    }
    .badge-plan {
        font-size: 11.5px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 30px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .plan-basic { background-color: rgba(59, 130, 246, 0.1); color: #2563eb; }
    .plan-standard { background-color: rgba(139, 92, 246, 0.1); color: #7c3aed; }
    .plan-premium { background-color: rgba(245, 158, 11, 0.1); color: #d97706; }
    .plan-default { background-color: rgba(100, 116, 139, 0.1); color: #475569; }

    .badge-status {
        font-size: 11.5px;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 30px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .status-active { background-color: #ecfdf5; color: #059669; }
    .status-expired { background-color: #fef2f2; color: #dc2626; }
    .status-suspended { background-color: #fffbeb; color: #d97706; }

    .btn-action-circle {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.2s ease;
        font-size: 13px;
    }
    .btn-action-extend { background-color: #f0fdf4; color: #16a34a; }
    .btn-action-extend:hover { background-color: #dcfce7; }
    .btn-action-plan { background-color: #eff6ff; color: #2563eb; }
    .btn-action-plan:hover { background-color: #dbeafe; }
    .btn-action-suspend { background-color: #fff5f5; color: #e11d48; }
    .btn-action-suspend:hover { background-color: #ffe4e6; }

    body.dark-mode .sub-table-card {
        background-color: #111827;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.3) !important;
    }
    body.dark-mode .btn-action-extend { background-color: rgba(22, 163, 74, 0.15); color: #4ade80; }
    body.dark-mode .btn-action-plan { background-color: rgba(37, 99, 235, 0.15); color: #60a5fa; }
    body.dark-mode .btn-action-suspend { background-color: rgba(225, 29, 72, 0.15); color: #fda4af; }
    body.dark-mode .form-control {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .form-control:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important;
    }
    body.dark-mode .form-label, body.dark-mode label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .text-dark {
        color: #f8fafc !important;
    }
    body.dark-mode .text-muted {
        color: #94a3b8 !important;
    }
    body.dark-mode table thead {
        background-color: #0f172a !important;
    }
    body.dark-mode table thead th {
        color: #cbd5e1 !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode table tbody td {
        color: #cbd5e1 !important;
        border-bottom-color: #1e293b !important;
    }
    body.dark-mode .modal-content {
        background-color: #111827 !important;
        color: #f8fafc !important;
        border: 1px solid #1e293b !important;
    }
    body.dark-mode .modal-header,
    body.dark-mode .modal-footer {
        background-color: #0f172a !important;
        border-color: #1e293b !important;
        color: #f8fafc !important;
    }
    body.dark-mode .modal-header .modal-title {
        color: #f8fafc !important;
    }
    body.dark-mode .modal-header .close {
        color: #f8fafc !important;
        text-shadow: none !important;
        opacity: 0.7;
    }
    body.dark-mode .modal-header .close:hover {
        opacity: 1;
    }
    body.dark-mode .btn-outline-secondary {
        border-color: #374151 !important;
        color: #94a3b8 !important;
    }
    body.dark-mode .btn-outline-secondary:hover {
        background-color: #1f2937 !important;
        color: #f8fafc !important;
    }
    table thead th {
        color: #475569 !important;
    }
    table thead {
        background-color: #f8fafc !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header Area -->
    <div class="mb-4">
        <h1 class="h3 font-weight-bold text-dark m-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">Active Subscriptions</h1>
        <p class="text-muted m-0" style="font-size: 0.85rem;">Monitor expiration timelines and manually override school membership states.</p>
    </div>

    <!-- Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; font-size: 13.5px; background-color: #ecfdf5; color: #065f46;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; font-size: 13.5px; background-color: #fef2f2; color: #991b1b;">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Subscriptions Table Card -->
    <div class="card sub-table-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle m-0" style="font-size: 13.5px;">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4" style="font-weight: 700; width: 250px;">School Name</th>
                        <th class="py-3" style="font-weight: 700;">Active Tier</th>
                        <th class="py-3" style="font-weight: 700;">Subscription Period</th>
                        <th class="py-3" style="font-weight: 700;">Time Remaining</th>
                        <th class="py-3" style="font-weight: 700;">Status</th>
                        <th class="py-3 text-right px-4" style="font-weight: 700; width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schools as $school)
                        @php
                            $sub = $school->subscriptions->first();
                            $hasActiveSub = $sub && $sub->status == 'active' && !$sub->subscription_ends_at->isPast();
                            
                            $daysLeft = 0;
                            if ($sub && $sub->subscription_ends_at) {
                                $daysLeft = now()->diffInDays($sub->subscription_ends_at, false);
                            }
                            
                            $planClass = 'plan-default';
                            if ($sub && $sub->plan) {
                                $pName = strtolower($sub->plan->name);
                                if (strpos($pName, 'basic') !== false) $planClass = 'plan-basic';
                                elseif (strpos($pName, 'standard') !== false || strpos($pName, 'medium') !== false) $planClass = 'plan-standard';
                                elseif (strpos($pName, 'premium') !== false || strpos($pName, 'pro') !== false) $planClass = 'plan-premium';
                            }
                        @endphp
                        <tr>
                            <td class="py-3 px-4 font-weight-bold" style="color: #1e293b;">
                                <div>{{ $school->name }}</div>
                                <small class="text-muted" style="font-size: 10.5px;">Code: {{ $school->code }}</small>
                            </td>
                            <td class="py-3">
                                @if($sub && $sub->plan)
                                    <span class="badge-plan {{ $planClass }}">{{ $sub->plan->name }}</span>
                                @else
                                    <span class="text-muted" style="font-style: italic;">No Plan Allocated</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($sub && $sub->created_at)
                                    <div class="font-weight-600">{{ $sub->created_at->format('M d, Y') }}</div>
                                    <small class="text-muted" style="font-size: 11px;">Ends: {{ $sub->subscription_ends_at->format('M d, Y') }}</small>
                                @else
                                    <span class="text-muted">&ndash;</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($sub && $sub->subscription_ends_at)
                                    @if($daysLeft > 30)
                                        <span class="text-success font-weight-bold"><i class="fas fa-hourglass-half"></i> {{ $daysLeft }} Days</span>
                                    @elseif($daysLeft > 0)
                                        <span class="text-warning font-weight-bold"><i class="fas fa-hourglass-half"></i> {{ $daysLeft }} Days (Expiring)</span>
                                    @else
                                        <span class="text-danger font-weight-bold"><i class="fas fa-exclamation-triangle"></i> Expired</span>
                                    @endif
                                @else
                                    <span class="text-muted">&ndash;</span>
                                @endif
                            </td>
                            <td class="py-3">
                                @if($sub)
                                    @if($sub->status == 'active' && !$sub->subscription_ends_at->isPast())
                                        <span class="badge-status status-active"><span class="dot" style="width: 6px; height: 6px; border-radius: 50%; background-color: #059669; display: inline-block;"></span> Active</span>
                                    @elseif($sub->status == 'suspended')
                                        <span class="badge-status status-suspended"><span class="dot" style="width: 6px; height: 6px; border-radius: 50%; background-color: #d97706; display: inline-block;"></span> Suspended</span>
                                    @else
                                        <span class="badge-status status-expired"><span class="dot" style="width: 6px; height: 6px; border-radius: 50%; background-color: #dc2626; display: inline-block;"></span> Expired</span>
                                    @endif
                                @else
                                    <span class="badge-status status-expired">Expired</span>
                                @endif
                            </td>
                            <td class="py-3 text-right px-4">
                                <div class="d-flex justify-content-end gap-2">
                                    <!-- Action: Extend -->
                                    <button class="btn-action-circle btn-action-extend" 
                                            title="Extend Subscription" 
                                            data-toggle="modal" 
                                            data-target="#extendSubModal" 
                                            data-id="{{ $school->id }}" 
                                            data-name="{{ $school->name }}">
                                        <i class="fas fa-clock"></i>
                                    </button>
                                    <!-- Action: Change Plan -->
                                    <button class="btn-action-circle btn-action-plan" 
                                            title="Change Membership Plan" 
                                            data-toggle="modal" 
                                            data-target="#changePlanModal" 
                                            data-id="{{ $school->id }}" 
                                            data-name="{{ $school->name }}"
                                            data-plan="{{ $sub->plan_id ?? '' }}">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                    <!-- Action: Suspend -->
                                    @if($sub && $sub->status == 'active' && !$sub->subscription_ends_at->isPast())
                                        <form action="{{ route('superadmin.subscriptions.cancel') }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to suspend subscription for \'{{ $school->name }}\'?');">
                                            @csrf
                                            <input type="hidden" name="school_id" value="{{ $school->id }}">
                                            <button type="submit" class="btn-action-circle btn-action-suspend" title="Suspend Subscription">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">No schools found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- EXTEND SUBSCRIPTION MODAL -->
<div class="modal fade" id="extendSubModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header border-0 bg-light px-4 py-3" style="background-color: #f8fafc !important;">
                <h5 class="modal-title font-weight-bold" style="font-family: 'Plus Jakarta Sans', sans-serif; color: #0f172a;">Extend Subscription</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('superadmin.subscriptions.extend') }}" method="POST">
                @csrf
                <input type="hidden" name="school_id" id="extend_school_id">
                <div class="modal-body px-4 py-3">
                    <p style="font-size: 13.5px;" class="text-secondary mb-3">Manually add days to the active membership duration for: <strong class="text-dark" id="extend_school_name"></strong></p>
                    
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13.5px;">Days to Extend <span class="text-danger">*</span></label>
                        <input type="number" name="days" class="form-control" placeholder="e.g. 30" min="1" style="border-radius: 10px; height: 42px;" required>
                        <small class="text-muted" style="font-size: 11px;">Note: If the current subscription has already expired, a new 30-day active period starting today will be initialized.</small>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3" style="background-color: #f8fafc !important;">
                    <button type="button" class="btn btn-outline-secondary px-4" style="border-radius: 10px;" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4" style="border-radius: 10px; font-weight: 700;">Extend Now</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- CHANGE PLAN MODAL -->
<div class="modal fade" id="changePlanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden;">
            <div class="modal-header border-0 bg-light px-4 py-3" style="background-color: #f8fafc !important;">
                <h5 class="modal-title font-weight-bold" style="font-family: 'Plus Jakarta Sans', sans-serif; color: #0f172a;">Change Subscription Plan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('superadmin.subscriptions.change-plan') }}" method="POST">
                @csrf
                <input type="hidden" name="school_id" id="change_school_id">
                <div class="modal-body px-4 py-3">
                    <p style="font-size: 13.5px;" class="text-secondary mb-3">Re-allocate membership pricing tier for: <strong class="text-dark" id="change_school_name"></strong></p>
                    
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13.5px;">Select Subscription Plan <span class="text-danger">*</span></label>
                        <select name="plan_id" id="change_plan_id" class="form-control" style="border-radius: 10px; height: 42px; font-weight: 600;" required>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->id }}">
                                    {{ $plan->name }} &ndash; ₹{{ number_format($plan->price, 0) }} ({{ $plan->duration_days }} Days)
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted" style="font-size: 11px;">Important: Changing a plan resets the duration of active subscription to the new plan's default duration (days).</small>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3" style="background-color: #f8fafc !important;">
                    <button type="button" class="btn btn-outline-secondary px-4" style="border-radius: 10px;" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px; font-weight: 700;">Change Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Extend Modal fields population
        $('#extendSubModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');
            
            const modal = $(this);
            modal.find('#extend_school_id').val(id);
            modal.find('#extend_school_name').text(name);
        });

        // Change Plan Modal fields population
        $('#changePlanModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');
            const planId = button.data('plan');
            
            const modal = $(this);
            modal.find('#change_school_id').val(id);
            modal.find('#change_school_name').text(name);
            if (planId) {
                modal.find('#change_plan_id').val(planId);
            }
        });
    });
</script>
@endsection
