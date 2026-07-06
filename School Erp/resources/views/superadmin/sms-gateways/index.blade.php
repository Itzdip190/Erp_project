@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* Premium Panel Styling */
    .sms-config-card {
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
        color: #10b981;
        background: transparent;
    }
    .nav-tabs-premium .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #10b981;
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

    body.dark-mode .sms-config-card {
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
        color: #10b981;
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
        border-color: #10b981 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
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
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 font-weight-bold text-dark m-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">SMS Gateways</h1>
        <p class="text-muted m-0" style="font-size: 0.85rem;">Configure provider credentials to push text alerts to staff, parents, and students.</p>
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

    <div class="card sms-config-card">
        <form action="{{ route('superadmin.sms-gateways.update') }}" method="POST">
            @csrf

            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs nav-tabs-premium" id="smsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="twilio-tab" data-toggle="tab" href="#twilio" role="tab" aria-controls="twilio" aria-selected="true">
                        <i class="fas fa-comments"></i> Twilio
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="msg91-tab" data-toggle="tab" href="#msg91" role="tab" aria-controls="msg91" aria-selected="false">
                        <i class="fas fa-sms"></i> Msg91 (India)
                    </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="fast2sms-tab" data-toggle="tab" href="#fast2sms" role="tab" aria-controls="fast2sms" aria-selected="false">
                        <i class="fas fa-paper-plane"></i> Fast2SMS
                    </a>
                </li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content" id="smsTabsContent">
                
                <!-- TWILIO TAB -->
                <div class="tab-pane fade show active" id="twilio" role="tabpanel" aria-labelledby="twilio-tab">
                    <div class="gateway-body">
                        <div class="custom-switch-premium">
                            <input type="hidden" name="twilio[enabled]" value="0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="twilioEnabled" name="twilio[enabled]" value="1" {{ $settings['twilio']['enabled'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="twilioEnabled" style="cursor: pointer;"></label>
                            </div>
                            <div>
                                <strong class="text-dark d-block">Enable Twilio Gateway</strong>
                                <span class="text-muted" style="font-size: 12px;">Activate to deliver SMS alerts globally using Twilio APIs.</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Twilio Account SID</label>
                                <input type="text" name="twilio[account_sid]" class="form-control" placeholder="AC..." value="{{ $settings['twilio']['account_sid'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Twilio Auth Token</label>
                                <div class="password-toggle-wrapper">
                                    <input type="password" name="twilio[auth_token]" id="twilio_token" class="form-control" placeholder="Auth Token..." value="{{ $settings['twilio']['auth_token'] }}" style="border-radius: 10px; height: 42px; padding-right: 40px;">
                                    <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('twilio_token', this)"></i>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Sender Number (Twilio Phone Number)</label>
                                <input type="text" name="twilio[sender_number]" class="form-control" placeholder="+1..." value="{{ $settings['twilio']['sender_number'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MSG91 TAB -->
                <div class="tab-pane fade" id="msg91" role="tabpanel" aria-labelledby="msg91-tab">
                    <div class="gateway-body">
                        <div class="custom-switch-premium">
                            <input type="hidden" name="msg91[enabled]" value="0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="msg91Enabled" name="msg91[enabled]" value="1" {{ $settings['msg91']['enabled'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="msg91Enabled" style="cursor: pointer;"></label>
                            </div>
                            <div>
                                <strong class="text-dark d-block">Enable Msg91 Gateway</strong>
                                <span class="text-muted" style="font-size: 12px;">Activate to deliver SMS alerts in India using Msg91.</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Msg91 Authentication Key</label>
                                <div class="password-toggle-wrapper">
                                    <input type="password" name="msg91[auth_key]" id="msg91_key" class="form-control" placeholder="Auth Key..." value="{{ $settings['msg91']['auth_key'] }}" style="border-radius: 10px; height: 42px; padding-right: 40px;">
                                    <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('msg91_key', this)"></i>
                                </div>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Msg91 Sender ID</label>
                                <input type="text" name="msg91[sender_id]" class="form-control" placeholder="e.g. SCHCLD" value="{{ $settings['msg91']['sender_id'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Msg91 Route</label>
                                <input type="text" name="msg91[route]" class="form-control" placeholder="e.g. 4 (Transactional)" value="{{ $settings['msg91']['route'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- FAST2SMS TAB -->
                <div class="tab-pane fade" id="fast2sms" role="tabpanel" aria-labelledby="fast2sms-tab">
                    <div class="gateway-body">
                        <div class="custom-switch-premium">
                            <input type="hidden" name="fast2sms[enabled]" value="0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="fast2smsEnabled" name="fast2sms[enabled]" value="1" {{ $settings['fast2sms']['enabled'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="fast2smsEnabled" style="cursor: pointer;"></label>
                            </div>
                            <div>
                                <strong class="text-dark d-block">Enable Fast2SMS Gateway</strong>
                                <span class="text-muted" style="font-size: 12px;">Activate to deliver quick, budget-friendly SMS alerts in India.</span>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Fast2SMS Authorization Key</label>
                                <div class="password-toggle-wrapper">
                                    <input type="password" name="fast2sms[authorization_key]" id="fast2sms_key" class="form-control" placeholder="API Key..." value="{{ $settings['fast2sms']['authorization_key'] }}" style="border-radius: 10px; height: 42px; padding-right: 40px;">
                                    <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('fast2sms_key', this)"></i>
                                </div>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13.5px;">Fast2SMS Sender ID</label>
                                <input type="text" name="fast2sms[sender_id]" class="form-control" placeholder="e.g. FSTSMS" value="{{ $settings['fast2sms']['sender_id'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Card Footer Controls -->
            <div class="card-footer bg-white border-top-0 d-flex justify-content-end px-4 py-3" style="background-color: #f8fafc !important;">
                <button type="submit" class="btn btn-success px-5" style="border-radius: 12px; font-weight: 700; height: 42px; background-color: #10b981; border: none;">
                    <i class="fas fa-save mr-2"></i> Save Configurations
                </button>
            </div>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle API credential visibility
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
