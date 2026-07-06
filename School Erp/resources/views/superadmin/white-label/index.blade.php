@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* Premium Panel Styling */
    .branding-card {
        border-radius: 20px !important;
        border: none !important;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.02) !important;
        background-color: #ffffff;
        overflow: hidden;
        margin-bottom: 30px;
    }
    .branding-body {
        padding: 30px;
    }

    /* Live Preview Card */
    .preview-canvas {
        background-color: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .preview-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 12px;
        border-bottom: 1px solid #e2e8f0;
    }
    .preview-logo {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        object-fit: cover;
    }
    .preview-title {
        font-weight: 800;
        font-size: 14px;
        color: #0f172a;
    }
    .preview-button {
        border: none;
        border-radius: 8px;
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        padding: 8px 16px;
        transition: opacity 0.2s;
    }
    .preview-text {
        font-size: 12.5px;
        color: #64748b;
        line-height: 1.4;
        margin-top: 15px;
    }
    .preview-footer {
        font-size: 10.5px;
        color: #94a3b8;
        margin-top: 20px;
        border-top: 1px solid #f1f5f9;
        padding-top: 10px;
        display: flex;
        justify-content: space-between;
    }

    body.dark-mode .branding-card {
        background-color: #111827;
    }
    body.dark-mode .preview-canvas {
        background-color: #0f172a;
        border-color: #1e293b;
    }
    body.dark-mode .preview-title {
        color: #f8fafc;
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
    body.dark-mode .col-lg-8 {
        border-color: #1e293b !important;
    }
    body.dark-mode .col-lg-4 {
        background-color: #0f172a !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h3 font-weight-bold text-dark m-0" style="font-family: 'Plus Jakarta Sans', sans-serif;">White-Label Settings</h1>
        <p class="text-muted m-0" style="font-size: 0.85rem;">Modify logo assets, copyright attributes, support channels, and main theme colors.</p>
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

    <div class="card branding-card">
        <form action="{{ route('superadmin.white-label.update') }}" method="POST">
            @csrf

            <div class="row no-gutters">
                <!-- Branding Inputs Form (Left side) -->
                <div class="col-lg-8 border-right" style="border-color: #f1f5f9 !important;">
                    <div class="branding-body">
                        <h5 class="font-weight-bold mb-4"><i class="fas fa-sliders-h text-primary mr-2"></i> Identity & Config</h5>
                        
                        <div class="row">
                            <div class="col-md-12 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13px;">App Platform Name <span class="text-danger">*</span></label>
                                <input type="text" name="app_name" id="input_app_name" class="form-control" value="{{ $settings['app_name'] }}" style="border-radius: 10px; height: 42px;" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13px;">Logo URL</label>
                                <input type="url" name="logo_url" id="input_logo_url" class="form-control" placeholder="https://..." value="{{ $settings['logo_url'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13px;">Favicon URL</label>
                                <input type="url" name="favicon_url" class="form-control" placeholder="https://..." value="{{ $settings['favicon_url'] }}" style="border-radius: 10px; height: 42px;">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13px;">Support Email <span class="text-danger">*</span></label>
                                <input type="email" name="support_email" id="input_support_email" class="form-control" value="{{ $settings['support_email'] }}" style="border-radius: 10px; height: 42px;" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13px;">Support Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="support_phone" class="form-control" value="{{ $settings['support_phone'] }}" style="border-radius: 10px; height: 42px;" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12 form-group">
                                <label class="form-label font-weight-bold" style="font-size: 13px;">Copyright Footer Text <span class="text-danger">*</span></label>
                                <input type="text" name="copyright_text" id="input_copyright" class="form-control" value="{{ $settings['copyright_text'] }}" style="border-radius: 10px; height: 42px;" required>
                            </div>
                        </div>

                        <h5 class="font-weight-bold mb-3 mt-4"><i class="fas fa-palette text-success mr-2"></i> Color Scheme</h5>
                        
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13px;">Primary Color Hex <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="color" id="picker_primary" value="{{ $settings['primary_color'] }}" style="width: 42px; height: 42px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" name="primary_color" id="input_primary" class="form-control text-uppercase font-weight-bold" value="{{ $settings['primary_color'] }}" style="border-radius: 10px; height: 42px; width: 120px;" required>
                                </div>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label font-weight-bold" style="font-size: 13px;">Secondary Color Hex <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="color" id="picker_secondary" value="{{ $settings['secondary_color'] }}" style="width: 42px; height: 42px; border: none; border-radius: 8px; cursor: pointer;">
                                    <input type="text" name="secondary_color" id="input_secondary" class="form-control text-uppercase font-weight-bold" value="{{ $settings['secondary_color'] }}" style="border-radius: 10px; height: 42px; width: 120px;" required>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Live Preview Panel (Right side) -->
                <div class="col-lg-4 bg-light">
                    <div class="branding-body h-100 d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="font-weight-bold mb-4"><i class="fas fa-eye text-info mr-2"></i> Live Portal Preview</h5>
                            
                            <div class="preview-canvas">
                                <div>
                                    <div class="preview-header">
                                        <img src="{{ $settings['logo_url'] ?: 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=100' }}" id="preview_logo" class="preview-logo" alt="Logo">
                                        <span class="preview-title" id="preview_title">{{ $settings['app_name'] }}</span>
                                    </div>
                                    
                                    <div class="preview-text">
                                        Welcome to your white-labeled student administration panel. Custom styling values have been applied successfully.
                                    </div>
                                </div>

                                <div>
                                    <button type="button" class="preview-button w-100" id="preview_btn" style="background-color: {{ $settings['primary_color'] }};">
                                        Login to Dashboard
                                    </button>

                                    <div class="preview-footer">
                                        <span id="preview_email">{{ $settings['support_email'] }}</span>
                                        <span id="preview_copyright">{{ Str::limit($settings['copyright_text'], 20) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 border-top mt-4 text-center">
                            <button type="submit" class="btn btn-primary px-5 w-100" style="border-radius: 12px; font-weight: 700; height: 44px; box-shadow: 0 4px 12px rgba(59,130,246,0.15);">
                                <i class="fas fa-save mr-2"></i> Save Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Sync color input box and color picker tool
        $('#picker_primary').on('input', function() {
            const val = $(this).val();
            $('#input_primary').val(val);
            $('#preview_btn').css('background-color', val);
        });
        $('#input_primary').on('input', function() {
            const val = $(this).val();
            if (/^#[a-fA-F0-9]{6}$/.test(val)) {
                $('#picker_primary').val(val);
                $('#preview_btn').css('background-color', val);
            }
        });

        $('#picker_secondary').on('input', function() {
            const val = $(this).val();
            $('#input_secondary').val(val);
        });
        $('#input_secondary').on('input', function() {
            const val = $(this).val();
            if (/^#[a-fA-F0-9]{6}$/.test(val)) {
                $('#picker_secondary').val(val);
            }
        });

        // Sync Text previews
        $('#input_app_name').on('input', function() {
            $('#preview_title').text($(this).val());
        });
        $('#input_support_email').on('input', function() {
            $('#preview_email').text($(this).val());
        });
        $('#input_copyright').on('input', function() {
            $('#preview_copyright').text($(this).val().substring(0, 20));
        });
        $('#input_logo_url').on('input', function() {
            $('#preview_logo').attr('src', $(this).val() || 'https://images.unsplash.com/photo-1546410531-bb4caa6b424d?w=100');
        });
    });
</script>
@endsection
