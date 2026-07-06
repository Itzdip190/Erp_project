@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* Premium Styling for Plans Grid */
    .plan-card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
        margin-bottom: 30px;
    }
    .plan-premium-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 30px;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .plan-premium-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.04);
        border-color: #cbd5e1;
    }
    .plan-premium-card.popular {
        border: 2px solid #e5ba73;
        box-shadow: 0 8px 24px rgba(229, 186, 115, 0.15);
    }
    .popular-badge {
        position: absolute;
        top: 15px;
        right: -30px;
        background: linear-gradient(135deg, #e5ba73, #c59b53);
        color: #0c1024;
        font-size: 10px;
        font-weight: 800;
        padding: 4px 30px;
        transform: rotate(45deg);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .plan-header {
        margin-bottom: 20px;
    }
    .plan-name {
        font-size: 20px;
        font-weight: 800;
        color: #1e1b4b;
        margin-bottom: 8px;
    }
    .plan-price-wrapper {
        display: flex;
        align-items: baseline;
        gap: 4px;
        margin-bottom: 12px;
    }
    .plan-currency {
        font-size: 20px;
        font-weight: 700;
        color: #475569;
    }
    .plan-price {
        font-size: 36px;
        font-weight: 900;
        color: #0f172a;
        line-height: 1;
    }
    .plan-duration {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
    }
    .plan-features-list {
        list-style: none;
        padding: 0;
        margin: 0 0 24px 0;
        flex-grow: 1;
    }
    .plan-feature-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        font-size: 13.5px;
        color: #475569;
        margin-bottom: 12px;
        line-height: 1.4;
    }
    .plan-feature-icon {
        color: #10b981;
        font-size: 14px;
        margin-top: 3px;
    }
    .plan-actions {
        display: flex;
        gap: 10px;
        border-top: 1px solid #f1f5f9;
        padding-top: 20px;
    }
    .btn-plan-action {
        flex: 1;
        height: 40px;
        border-radius: 12px;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-plan-edit {
        background-color: #f1f5f9;
        color: #475569;
        border: none;
    }
    .btn-plan-edit:hover {
        background-color: #e2e8f0;
        color: #1e293b;
    }
    .btn-plan-delete {
        background-color: #fef2f2;
        color: #ef4444;
        border: none;
    }
    .btn-plan-delete:hover {
        background-color: #fee2e2;
        color: #dc2626;
    }

    /* Dark Mode Overrides */
    body.dark-mode .plan-premium-card {
        background-color: #111827;
        border-color: #1e293b;
    }
    body.dark-mode .plan-premium-card:hover {
        border-color: #374151;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }
    body.dark-mode .plan-name,
    body.dark-mode .plan-price {
        color: #f8fafc;
    }
    body.dark-mode .plan-feature-item {
        color: #94a3b8;
    }
    body.dark-mode .btn-plan-edit {
        background-color: #1f2937;
        color: #94a3b8;
    }
    body.dark-mode .btn-plan-edit:hover {
        background-color: #374151;
        color: #f8fafc;
    }
    body.dark-mode .btn-plan-delete {
        background-color: rgba(239, 68, 68, 0.1);
        color: #fca5a5;
    }
    body.dark-mode .btn-plan-delete:hover {
        background-color: rgba(239, 68, 68, 0.2);
        color: #f87171;
    }
    body.dark-mode .plan-actions {
        border-color: #1e293b;
    }
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
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header Area -->
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h1 class="h3 font-weight-bold text-dark m-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">Subscription Plans</h1>
            <p class="text-muted m-0" style="font-size: 0.85rem;">Manage global pricing packages and available school features.</p>
        </div>
        <button class="btn btn-primary px-4" style="border-radius: 12px; font-weight: 700; height: 42px; display: inline-flex; align-items: center; gap: 8px;" data-toggle="modal" data-target="#createPlanModal">
            <i class="fas fa-plus"></i> Create Pricing Plan
        </button>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; font-size: 13.5px; border: none; background-color: #ecfdf5; color: #065f46;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 12px; font-size: 13.5px; border: none; background-color: #fef2f2; color: #991b1b;">
            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Plans Display Grid -->
    <div class="plan-card-grid">
        @forelse($plans as $plan)
            <div class="plan-premium-card {{ $loop->index == 1 ? 'popular' : '' }}">
                @if($loop->index == 1)
                    <div class="popular-badge">Popular</div>
                @endif
                <div>
                    <div class="plan-header">
                        <div class="plan-name">{{ $plan->name }}</div>
                        <div class="plan-price-wrapper">
                            <span class="plan-currency">₹</span>
                            <span class="plan-price">{{ number_format($plan->price, 0) }}</span>
                            <span class="plan-duration">/ {{ $plan->duration_days }} Days</span>
                        </div>
                        <div style="font-size: 11.5px; font-weight: 700; color: #8b5cf6; background: rgba(139,92,246,0.1); padding: 4px 10px; border-radius: 30px; display: inline-block;">
                            {{ $plan->subscriptions_count }} Active Subscriptions
                        </div>
                    </div>

                    <ul class="plan-features-list">
                        @if(is_array($plan->features) && count($plan->features) > 0)
                            @foreach($plan->features as $feat)
                                <li class="plan-feature-item">
                                    <i class="fas fa-check-circle plan-feature-icon"></i>
                                    <span>{{ $feat }}</span>
                                </li>
                            @endforeach
                        @else
                            <li class="plan-feature-item text-muted" style="font-style: italic;">
                                No features defined for this plan.
                            </li>
                        @endif
                    </ul>
                </div>

                <div class="plan-actions">
                    <button class="btn btn-plan-action btn-plan-edit" 
                            data-toggle="modal" 
                            data-target="#editPlanModal" 
                            data-id="{{ $plan->id }}"
                            data-name="{{ $plan->name }}"
                            data-price="{{ $plan->price }}"
                            data-duration="{{ $plan->duration_days }}"
                            data-features="{{ json_encode($plan->features) }}">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <form action="{{ route('superadmin.plans.destroy', $plan->id) }}" method="POST" class="d-inline flex-grow-1" onsubmit="return confirm('Are you sure you want to delete the plan \'{{ $plan->name }}\'? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-plan-action btn-plan-delete w-100">
                            <i class="fas fa-trash-alt"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5 bg-white border rounded shadow-sm" style="border-radius: 16px !important;">
                <div style="font-size: 40px; color: #cbd5e1;" class="mb-3">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h5 class="text-secondary font-weight-bold">No Plans Configured</h5>
                <p class="text-muted" style="font-size: 0.85rem;">Click the button above to create your first pricing plan tier.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- CREATE PLAN MODAL -->
<div class="modal fade" id="createPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 bg-light px-4 py-3" style="background-color: #f8fafc !important;">
                <h5 class="modal-title font-weight-bold" style="font-family: 'Plus Jakarta Sans', sans-serif; color: #0f172a;">Create Pricing Plan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('superadmin.plans.store') }}" method="POST">
                @csrf
                <div class="modal-body px-4 py-3">
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13.5px;">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Premium Pro" style="border-radius: 10px; height: 42px;" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13.5px;">Price (INR) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" placeholder="e.g. 1999" min="0" step="1" style="border-radius: 10px; height: 42px;" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13.5px;">Duration (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" class="form-control" placeholder="e.g. 365" min="1" style="border-radius: 10px; height: 42px;" required>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label font-weight-bold d-flex justify-content-between align-items-center" style="font-size: 13.5px;">
                            <span>Key Features Included</span>
                            <button type="button" class="btn btn-outline-success btn-xs px-2 py-1 add-feature-btn" style="font-size: 10px; font-weight: 700; border-radius: 6px;">
                                <i class="fas fa-plus"></i> Add Line
                            </button>
                        </label>
                        <div class="features-input-container">
                            <div class="d-flex align-items-center gap-2 mb-2 feature-input-row">
                                <input type="text" name="features[]" class="form-control" placeholder="e.g. AI-grounded School Assistant" style="border-radius: 10px; height: 38px;">
                                <button type="button" class="btn btn-link text-danger remove-feature-btn" style="padding: 0 10px;"><i class="fas fa-times-circle"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3" style="background-color: #f8fafc !important;">
                    <button type="button" class="btn btn-outline-secondary px-4" style="border-radius: 10px;" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px; font-weight: 700;">Save Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT PLAN MODAL -->
<div class="modal fade" id="editPlanModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 20px; border: none; overflow: hidden; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 bg-light px-4 py-3" style="background-color: #f8fafc !important;">
                <h5 class="modal-title font-weight-bold" style="font-family: 'Plus Jakarta Sans', sans-serif; color: #0f172a;">Edit Pricing Plan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editPlanForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body px-4 py-3">
                    <div class="form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13.5px;">Plan Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" style="border-radius: 10px; height: 42px;" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13.5px;">Price (INR) <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="edit_price" class="form-control" min="0" step="1" style="border-radius: 10px; height: 42px;" required>
                        </div>
                        <div class="col-md-6 form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13.5px;">Duration (Days) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_days" id="edit_duration" class="form-control" min="1" style="border-radius: 10px; height: 42px;" required>
                        </div>
                    </div>

                    <div class="form-group mb-2">
                        <label class="form-label font-weight-bold d-flex justify-content-between align-items-center" style="font-size: 13.5px;">
                            <span>Key Features Included</span>
                            <button type="button" class="btn btn-outline-success btn-xs px-2 py-1 add-feature-btn" style="font-size: 10px; font-weight: 700; border-radius: 6px;">
                                <i class="fas fa-plus"></i> Add Line
                            </button>
                        </label>
                        <div class="features-input-container" id="edit_features_container">
                            <!-- Populated via Javascript -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light px-4 py-3" style="background-color: #f8fafc !important;">
                    <button type="button" class="btn btn-outline-secondary px-4" style="border-radius: 10px;" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary px-4" style="border-radius: 10px; font-weight: 700;">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Add dynamic feature line
        $('.add-feature-btn').click(function() {
            const container = $(this).closest('.form-group').find('.features-input-container');
            const row = `
                <div class="d-flex align-items-center gap-2 mb-2 feature-input-row">
                    <input type="text" name="features[]" class="form-control" placeholder="e.g. 24/7 Priority Support" style="border-radius: 10px; height: 38px;">
                    <button type="button" class="btn btn-link text-danger remove-feature-btn" style="padding: 0 10px;"><i class="fas fa-times-circle"></i></button>
                </div>
            `;
            container.append(row);
        });

        // Remove dynamic feature line
        $(document).on('click', '.remove-feature-btn', function() {
            const container = $(this).closest('.features-input-container');
            // Keep at least one row in creation modal if needed, or simply delete
            $(this).closest('.feature-input-row').remove();
        });

        // Populate edit modal values
        $('#editPlanModal').on('show.bs.modal', function(event) {
            const button = $(event.relatedTarget);
            const id = button.data('id');
            const name = button.data('name');
            const price = button.data('price');
            const duration = button.data('duration');
            const features = button.data('features') || [];

            const modal = $(this);
            modal.find('#editPlanForm').attr('action', `/superadmin/plans/${id}`);
            modal.find('#edit_name').val(name);
            modal.find('#edit_price').val(price);
            modal.find('#edit_duration').val(duration);

            const container = modal.find('#edit_features_container');
            container.empty();

            if (features && features.length > 0) {
                features.forEach(function(feat) {
                    container.append(`
                        <div class="d-flex align-items-center gap-2 mb-2 feature-input-row">
                            <input type="text" name="features[]" class="form-control" value="${feat}" style="border-radius: 10px; height: 38px;">
                            <button type="button" class="btn btn-link text-danger remove-feature-btn" style="padding: 0 10px;"><i class="fas fa-times-circle"></i></button>
                        </div>
                    `);
                });
            } else {
                container.append(`
                    <div class="d-flex align-items-center gap-2 mb-2 feature-input-row">
                        <input type="text" name="features[]" class="form-control" placeholder="e.g. Support" style="border-radius: 10px; height: 38px;">
                        <button type="button" class="btn btn-link text-danger remove-feature-btn" style="padding: 0 10px;"><i class="fas fa-times-circle"></i></button>
                    </div>
                `);
            }
        });
    });
</script>
@endsection
