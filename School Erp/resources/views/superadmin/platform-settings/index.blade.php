@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* Premium Panel Layout */
    .platform-card {
        border-radius: 20px !important;
        border: none !important;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.02) !important;
        background-color: #ffffff;
        overflow: hidden;
        margin-bottom: 30px;
    }
    .platform-body {
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
        margin-bottom: 20px;
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

    body.dark-mode .platform-card {
        background-color: #111827;
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
    body.dark-mode h5 {
        color: #f8fafc !important;
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
        <h1 class="h3 font-weight-bold text-dark m-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">Platform Settings</h1>
        <p class="text-muted m-0" style="font-size: 0.85rem;">Manage global maintenance state, session durations, and core SMTP outgoing mailers.</p>
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

    <div class="card platform-card">
        <form action="{{ route('superadmin.platform-settings.update') }}" method="POST">
            @csrf

            <div class="platform-body">
                
                <!-- 1. SYSTEM CONTROLS -->
                <h5 class="font-weight-bold mb-4"><i class="fas fa-sliders-h text-primary mr-2"></i> Global System Toggles</h5>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="custom-switch-premium">
                            <input type="hidden" name="maintenance_mode" value="0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="maintenanceEnabled" name="maintenance_mode" value="1" {{ $settings['maintenance_mode'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="maintenanceEnabled" style="cursor: pointer;"></label>
                            </div>
                            <div>
                                <strong class="text-dark d-block">Enable Maintenance Mode</strong>
                                <span class="text-muted" style="font-size: 12px;">Toggle to lock out tenants during system upgrades.</span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="custom-switch-premium">
                            <input type="hidden" name="enable_registration" value="0">
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="regEnabled" name="enable_registration" value="1" {{ $settings['enable_registration'] ? 'checked' : '' }}>
                                <label class="custom-control-label" for="regEnabled" style="cursor: pointer;"></label>
                            </div>
                            <div>
                                <strong class="text-dark d-block">Open Public Registration</strong>
                                <span class="text-muted" style="font-size: 12px;">Allow new schools to register accounts directly online.</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mb-5">
                    <div class="col-md-6 form-group">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">Login Session Lifetime (Minutes)</label>
                        <input type="number" name="session_lifetime" class="form-control" value="{{ $settings['session_lifetime'] }}" min="15" max="1440" style="border-radius: 10px; height: 42px;" required>
                        <small class="text-muted" style="font-size: 11px;">Automatically logs out idle users after the specified duration.</small>
                    </div>
                </div>

                <!-- 2. SMTP MAIL SERVER -->
                <h5 class="font-weight-bold mb-4"><i class="fas fa-envelope-open-text text-success mr-2"></i> Outgoing Mailer (SMTP)</h5>
                
                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">SMTP Host Server</label>
                        <input type="text" name="smtp_host" class="form-control" placeholder="e.g. smtp.mailgun.org" value="{{ $settings['smtp_host'] }}" style="border-radius: 10px; height: 42px;">
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">SMTP Port</label>
                        <input type="number" name="smtp_port" class="form-control" placeholder="e.g. 587" value="{{ $settings['smtp_port'] }}" style="border-radius: 10px; height: 42px;">
                    </div>
                    <div class="col-md-3 form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">Mail Encryption</label>
                        <select name="smtp_encryption" class="form-control" style="border-radius: 10px; height: 42px;">
                            <option value="none" {{ $settings['smtp_encryption'] == 'none' ? 'selected' : '' }}>None</option>
                            <option value="tls" {{ $settings['smtp_encryption'] == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ $settings['smtp_encryption'] == 'ssl' ? 'selected' : '' }}>SSL</option>
                        </select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">SMTP Account Username</label>
                        <input type="text" name="smtp_username" class="form-control" placeholder="Username..." value="{{ $settings['smtp_username'] }}" style="border-radius: 10px; height: 42px;">
                    </div>
                    <div class="col-md-6 form-group mb-3">
                        <label class="form-label font-weight-bold" style="font-size: 13px;">SMTP Account Password</label>
                        <div class="password-toggle-wrapper">
                            <input type="password" name="smtp_password" id="smtp_pass" class="form-control" placeholder="Password..." value="{{ $settings['smtp_password'] }}" style="border-radius: 10px; height: 42px; padding-right: 40px;">
                            <i class="fas fa-eye password-toggle-icon" onclick="togglePasswordVisibility('smtp_pass', this)"></i>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Card Footer Controls -->
            <div class="card-footer bg-white border-top-0 d-flex justify-content-end px-4 py-3">
                <button type="submit" class="btn btn-primary px-5" style="border-radius: 12px; font-weight: 700; height: 42px; box-shadow: 0 4px 12px rgba(59,130,246,0.15);">
                    <i class="fas fa-save mr-2"></i> Save Settings
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
