@extends('superadmin.layouts.master')

@section('styles')
<style>
    .settings-card {
        border-radius: 16px !important;
        border: 1px solid rgba(229, 231, 235, 0.5) !important;
        box-shadow: 0 4px 20px rgba(0,0,0,0.01) !important;
        background-color: #ffffff;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .section-title-custom {
        font-size: 1.15rem;
        font-weight: 800;
        color: #0c1024;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 8px;
    }

    .form-group-custom label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }

    .form-control-custom {
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        font-size: 0.9rem;
        color: #1e1b4b;
        transition: border-color 0.2s;
    }

    .form-control-custom:focus {
        border-color: #e5ba73;
        box-shadow: 0 0 0 3px rgba(229, 186, 115, 0.15);
        outline: none;
    }

    .btn-save-settings {
        background: linear-gradient(135deg, #1d193d, #2f2960);
        color: #ffffff !important;
        border: none;
        font-weight: 700;
        border-radius: 12px;
        padding: 10px 24px;
        font-size: 0.9rem;
        box-shadow: 0 4px 12px rgba(29, 25, 61, 0.15);
        transition: all 0.2s;
    }

    .btn-save-settings:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 15px rgba(29, 25, 61, 0.25);
    }

    .custom-switch-label {
        font-size: 0.88rem;
        font-weight: 700;
        color: #334155;
        cursor: pointer;
    }

    /* Dark Mode rules */
    body.dark-mode .settings-card {
        background-color: #111827 !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .section-title-custom {
        color: #f8fafc !important;
        border-color: #1e293b !important;
    }
    body.dark-mode .form-group-custom label {
        color: #cbd5e1 !important;
    }
    body.dark-mode .form-control-custom {
        background-color: #1f2937 !important;
        border-color: #374151 !important;
        color: #f8fafc !important;
    }
    body.dark-mode .form-control-custom:focus {
        border-color: #e5ba73 !important;
        box-shadow: 0 0 0 3px rgba(229, 186, 115, 0.15) !important;
    }
    body.dark-mode h2, body.dark-mode h6, body.dark-mode p {
        color: #f8fafc !important;
    }
    body.dark-mode .text-muted {
        color: #94a3b8 !important;
    }
    body.dark-mode .custom-switch-label {
        color: #cbd5e1 !important;
    }
</style>
@endsection

@section('content')
<div class="row pt-4">
    <div class="col-12">
        <h2 class="mb-1 font-weight-extrabold" style="font-size: 1.8rem; letter-spacing: -0.5px;">Account & Preferences</h2>
        <p class="text-muted" style="font-size: 0.88rem;">Configure global platform monitoring behaviors, notifications, and default targets.</p>
    </div>
</div>

<div class="row mt-3">
    <div class="col-12">
        <div class="settings-card">
            <h5 class="section-title-custom"><i class="fas fa-sliders-h mr-2 text-primary"></i> SuperAdmin Settings</h5>

            @if ($errors->any())
                <div class="alert alert-danger border-0 mb-4" style="border-radius: 10px;">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('superadmin.settings.update') }}" method="POST">
                @csrf
                <div class="row">
                    <!-- Timezone Setup -->
                    <div class="col-md-6 form-group form-group-custom">
                        <label for="timezone">System Display Timezone</label>
                        <select name="timezone" id="timezone" class="form-control form-control-custom" required>
                            <option value="UTC" {{ $settings['timezone'] === 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                            <option value="Asia/Kolkata" {{ $settings['timezone'] === 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                            <option value="America/New_York" {{ $settings['timezone'] === 'America/New_York' ? 'selected' : '' }}>Eastern Time (EST/EDT)</option>
                            <option value="Europe/London" {{ $settings['timezone'] === 'Europe/London' ? 'selected' : '' }}>London (BST/GMT)</option>
                            <option value="Asia/Singapore" {{ $settings['timezone'] === 'Asia/Singapore' ? 'selected' : '' }}>Singapore (SGT)</option>
                        </select>
                    </div>

                    <!-- Currency Code -->
                    <div class="col-md-6 form-group form-group-custom">
                        <label for="currency">Default Currency Symbol</label>
                        <select name="currency" id="currency" class="form-control form-control-custom" required>
                            <option value="INR" {{ $settings['currency'] === 'INR' ? 'selected' : '' }}>₹ (INR)</option>
                            <option value="USD" {{ $settings['currency'] === 'USD' ? 'selected' : '' }}>$ (USD)</option>
                            <option value="EUR" {{ $settings['currency'] === 'EUR' ? 'selected' : '' }}>€ (EUR)</option>
                            <option value="GBP" {{ $settings['currency'] === 'GBP' ? 'selected' : '' }}>£ (GBP)</option>
                        </select>
                    </div>
                </div>

                <div class="row mt-2">
                    <!-- Default items per page -->
                    <div class="col-md-6 form-group form-group-custom">
                        <label for="default_per_page">Default Records Per Page</label>
                        <input type="number" name="default_per_page" id="default_per_page" class="form-control form-control-custom" value="{{ old('default_per_page', $settings['default_per_page']) }}" required>
                    </div>

                    <!-- Target MRR -->
                    <div class="col-md-6 form-group form-group-custom">
                        <label for="mrr_target">Monthly Revenue Target (INR)</label>
                        <input type="number" name="mrr_target" id="mrr_target" class="form-control form-control-custom" value="{{ old('mrr_target', $settings['mrr_target']) }}" required>
                    </div>
                </div>

                <!-- Notifications Toggle -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6 class="font-weight-extrabold mb-3" style="font-size: 0.82rem; color: #475569; letter-spacing: 0.5px; text-transform: uppercase;">Notification Channels</h6>
                        
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" name="notification_email" class="custom-control-input" id="switchEmail" value="1" {{ $settings['notification_email'] ? 'checked' : '' }}>
                            <label class="custom-control-label custom-switch-label" for="switchEmail">Enable Email Alerts for System Warnings</label>
                        </div>

                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" name="notification_system" class="custom-control-input" id="switchSystem" value="1" {{ $settings['notification_system'] ? 'checked' : '' }}>
                            <label class="custom-control-label custom-switch-label" for="switchSystem">Show Real-Time System Notifications in Navbar</label>
                        </div>
                    </div>
                </div>

                <div class="text-left mt-4">
                    <button type="submit" class="btn btn-save-settings">
                        <i class="fas fa-check mr-1"></i> Save Preferences
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
