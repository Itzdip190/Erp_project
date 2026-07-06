@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* Premium Tab Panel Styling */
    .gateway-config-card {
        border-radius: 20px !important;
        border: none !important;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.02) !important;
        background-color: #ffffff;
        overflow: hidden;
        margin-bottom: 30px;
    }
    .nav-tabs-premium {
        border-bottom: 2px solid #f1f5f9;
        display: flex;
        gap: 16px;
        padding: 0 24px;
        background-color: #f8fafc;
        flex-wrap: nowrap;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    .nav-tabs-premium::-webkit-scrollbar {
        height: 4px;
    }
    .nav-tabs-premium::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
    .nav-tabs-premium .nav-link {
        border: none;
        background: transparent;
        font-weight: 700;
        font-size: 13.5px;
        color: #64748b;
        padding: 16px 8px;
        position: relative;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .nav-tabs-premium .nav-link:hover {
        color: #0f172a;
    }
    .nav-tabs-premium .nav-link.active {
        color: #3b82f6;
        background: transparent;
    }
    .nav-tabs-premium .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #3b82f6;
        border-radius: 2px;
    }

    .gateway-body {
        padding: 30px;
    }

    /* Switch styling */
    .custom-switch-premium {
        display: flex;
        align-items: center;
        gap: 12px;
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 16px 20px;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    
    .password-toggle-wrapper {
        position: relative;
    }
    .password-toggle-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        cursor: pointer;
        font-size: 15px;
        transition: color 0.2s;
    }
    .password-toggle-icon:hover {
        color: #475569;
    }

    body.dark-mode .gateway-config-card {
        background-color: #111827;
    }
    body.dark-mode .nav-tabs-premium {
        background-color: #0f172a;
        border-color: #1e293b;
    }
    body.dark-mode .nav-tabs-premium::-webkit-scrollbar-thumb {
        background: #475569;
    }
    body.dark-mode .nav-tabs-premium .nav-link {
        color: #94a3b8;
    }
    body.dark-mode .nav-tabs-premium .nav-link:hover {
        color: #f8fafc;
    }
    body.dark-mode .nav-tabs-premium .nav-link.active {
        color: #3b82f6;
    }
    body.dark-mode .custom-switch-premium {
        background-color: #0f172a;
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
    body.dark-mode .card-footer {
        background-color: #111827 !important;
        border-color: #1e293b !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 font-weight-bold text-dark m-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">Payment Gateways</h1>
        <p class="text-muted m-0" style="font-size: 0.85rem;">Set up Stripe, Razorpay, or custom Bank Details to accept subscription payments.</p>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 12px; border: none; font-size: 13.5px; background-color: #ecfdf5; color: #065f46;">
            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="card gateway-config-card">
        <form action="{{ route('superadmin.gateways.update') }}" method="POST">
            @csrf

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs nav-tabs-premium" id="gatewayTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="stripe-tab" data-toggle="tab" href="#stripe" role="tab" aria-controls="stripe" aria-selected="true">
                        <i class="fab fa-stripe" style="font-size: 16px;"></i> Stripe Payments
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="razorpay-tab" data-toggle="tab" href="#razorpay" role="tab" aria-controls="razorpay" aria-selected="false">
                        <i class="fas fa-credit-card"></i> Razorpay (India)
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="bank-tab" data-toggle="tab" href="#bank" role="tab" aria-controls="bank" aria-selected="false">
                        <i class="fas fa-university"></i> Bank Transfer
                    </a>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="gatewayTabsContent">
                
                <!-- STRIPE TAB -->
                <div class="tab-pane fade show active" id="stripe" role="tabpanel" aria-labelledby="stripe-tab">
                    <div class="gateway-body">
                        <!-- Enable Switch -->
                        <div class="custom-switch-premium">
                            <input type="hidden" name="stripe[enabled]" value="0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="stripeEnabled" name="stripe[enabled]" value="1" {{ $settings['stripe']['enabled'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="stripeEnabled" style="cursor: pointer;"></label>
                            </div>
                            <div>
                                <strong class="text-dark d-block">Enable Stripe Payments</strong>
                                <span class="text-muted" style="font-size: 12px;">Toggle to show or hide Stripe as an checkout option for schools.</span>
                            </div>
                        </div>

                        <!-- Config Parameters -->
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Environment Mode</label>
                                <select name="stripe[mode]" class="form-control" style="border-radius: 10px; height: 42px;">
                                    <option value="sandbox" {{ $settings['stripe']['mode'] == 'sandbox' ? 'selected' : '' }}>Sandbox / Test</option>
                                    <option value="live" {{ $settings['stripe']['mode'] == 'live' ? 'selected' : '' }}>Production / Live</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Stripe Publishable Key</label>
                                <input type="text" name="stripe[publishable_key]" class="form-control" placeholder="pk_test_..." value="{{ $settings['stripe']['publishable_key'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Stripe Secret Key</label>
                                <div class="password-toggle-wrapper">
                                    <input type="password" name="stripe[secret_key]" id="stripe_secret" class="form-control" placeholder="sk_test_..." value="{{ $settings['stripe']['secret_key'] }}" style="border-radius: 10px; height: 42px; padding-right: 40px;">
                                    <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('stripe_secret', this)"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RAZORPAY TAB -->
                <div class="tab-pane fade" id="razorpay" role="tabpanel" aria-labelledby="razorpay-tab">
                    <div class="gateway-body">
                        <!-- Enable Switch -->
                        <div class="custom-switch-premium">
                            <input type="hidden" name="razorpay[enabled]" value="0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="razorpayEnabled" name="razorpay[enabled]" value="1" {{ $settings['razorpay']['enabled'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="razorpayEnabled" style="cursor: pointer;"></label>
                            </div>
                            <div>
                                <strong class="text-dark d-block">Enable Razorpay API</strong>
                                <span class="text-muted" style="font-size: 12px;">Toggle to offer Razorpay payment processing for domestic INR transactions.</span>
                            </div>
                        </div>

                        <!-- Config Parameters -->
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Environment Mode</label>
                                <select name="razorpay[mode]" class="form-control" style="border-radius: 10px; height: 42px;">
                                    <option value="sandbox" {{ $settings['razorpay']['mode'] == 'sandbox' ? 'selected' : '' }}>Sandbox / Test</option>
                                    <option value="live" {{ $settings['razorpay']['mode'] == 'live' ? 'selected' : '' }}>Production / Live</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Razorpay Key ID</label>
                                <input type="text" name="razorpay[key_id]" class="form-control" placeholder="rzp_test_..." value="{{ $settings['razorpay']['key_id'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Razorpay Key Secret</label>
                                <div class="password-toggle-wrapper">
                                    <input type="password" name="razorpay[key_secret]" id="razorpay_secret" class="form-control" placeholder="Key Secret..." value="{{ $settings['razorpay']['key_secret'] }}" style="border-radius: 10px; height: 42px; padding-right: 40px;">
                                    <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('razorpay_secret', this)"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- BANK TRANSFER TAB -->
                <div class="tab-pane fade" id="bank" role="tabpanel" aria-labelledby="bank-tab">
                    <div class="gateway-body">
                        <!-- Enable Switch -->
                        <div class="custom-switch-premium">
                            <input type="hidden" name="bank_transfer[enabled]" value="0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="bankEnabled" name="bank_transfer[enabled]" value="1" {{ $settings['bank_transfer']['enabled'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="bankEnabled" style="cursor: pointer;"></label>
                            </div>
                            <div>
                                <strong class="text-dark d-block">Enable Offline Bank Transfers</strong>
                                <span class="text-muted" style="font-size: 12px;">Enable schools to register payments manually by wire transferring to your corporate account.</span>
                            </div>
                        </div>

                        <!-- Config Parameters -->
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Account Holder Name</label>
                                <input type="text" name="bank_transfer[account_name]" class="form-control" value="{{ $settings['bank_transfer']['account_name'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Account Number</label>
                                <input type="text" name="bank_transfer[account_number]" class="form-control" value="{{ $settings['bank_transfer']['account_number'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Bank Name</label>
                                <input type="text" name="bank_transfer[bank_name]" class="form-control" value="{{ $settings['bank_transfer']['bank_name'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">IFSC Code</label>
                                <input type="text" name="bank_transfer[ifsc_code]" class="form-control" value="{{ $settings['bank_transfer']['ifsc_code'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label font-weight-bold" style="font-size: 13.5px;">Transfer Instructions / Notes</label>
                            <textarea name="bank_transfer[instructions]" class="form-control" rows="3" style="border-radius: 10px;">{{ $settings['bank_transfer']['instructions'] }}</textarea>
                            <small class="text-muted" style="font-size: 11px;">These instructions will be displayed to schools when checking out via offline bank transfer.</small>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Card Footer Controls -->
            <div class="card-footer bg-white border-top-0 d-flex justify-content-end px-4 py-3" style="background-color: #f8fafc !important;">
                <button type="submit" class="btn btn-primary px-5" style="border-radius: 12px; font-weight: 700; height: 42px;">
                    <i class="fas fa-save mr-2"></i> Save Configurations
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle API credential characters visibility
    function togglePasswordVisibility(fieldId, iconElement) {
        const input = document.getElementById(fieldId);
        if (input) {
            if (input.type === 'password') {
                input.type = 'text';
                iconElement.className = 'fas fa-eye-slash password-toggle-icon';
            } else {
                input.type = 'password';
                iconElement.className = 'fas fa-eye password-toggle-icon';
            }
        }
    }
</script>
@endsection
