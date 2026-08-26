@extends('layouts.app')

@section('title', 'Visitor Registration - Front Desk')
@section('page-title', 'Visitor Registration')

@section('content')
<style>
    :root {
        --theme-blue: #1d4ed8;
        --theme-blue-gradient: linear-gradient(135deg, #1e40af 0%, #1d4ed8 50%, #2563eb 100%);
        --theme-blue-hover: #1e40af;
        --theme-blue-light: #eff6ff;
        --theme-blue-border: #bfdbfe;
        --input-border: #cbd5e1;
        --input-focus: #1d4ed8;
        --discard-red: #ef4444;
        --discard-red-hover: #dc2626;
    }

    /* Page Entrance Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(16px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .visitor-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 10px 30px -5px rgba(29, 78, 216, 0.07), 0 4px 12px rgba(0, 0, 0, 0.03);
        overflow: hidden;
        margin-bottom: 100px; /* Space for floating buttons */
        animation: fadeInUp 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    /* Top Card Header Banner - Royal Blue & White Theme */
    .visitor-card-header {
        background: var(--theme-blue-gradient);
        color: #ffffff;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: inset 0 -1px 0 rgba(255, 255, 255, 0.15);
    }

    .visitor-card-header .header-title {
        font-size: 16.5px;
        font-weight: 800;
        letter-spacing: -0.2px;
        display: flex;
        align-items: center;
        gap: 12px;
        color: #ffffff;
        margin: 0;
    }

    .visitor-card-header .header-title .header-icon-circle {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.22);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        color: #ffffff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        transition: transform 0.25s ease;
    }

    .visitor-card:hover .header-icon-circle {
        transform: rotate(90deg) scale(1.05);
    }

    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.35);
        backdrop-filter: blur(6px);
        padding: 6px 14px;
        border-radius: 24px;
        font-size: 12.5px;
        font-weight: 700;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }

    .visitor-card-body {
        padding: 26px 30px;
    }

    /* Sub-panels for Side-by-Side Sections */
    .form-column-panel {
        background: #fafcff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 22px 24px;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
        transition: all 0.25s ease;
    }

    .form-column-panel:hover {
        box-shadow: 0 6px 18px rgba(29, 78, 216, 0.06);
        border-color: #cbd5e1;
    }

    /* Section Headings with Royal Blue Badges */
    .form-section-header {
        font-size: 14px;
        font-weight: 800;
        color: #1e293b;
        margin-top: 20px;
        margin-bottom: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        padding-bottom: 8px;
        border-bottom: 1.5px solid #e2e8f0;
        position: relative;
    }

    .form-section-header::after {
        content: '';
        position: absolute;
        bottom: -1.5px;
        left: 0;
        width: 42px;
        height: 2px;
        background: var(--theme-blue);
        border-radius: 2px;
    }

    .section-icon-badge {
        width: 26px;
        height: 26px;
        border-radius: 6px;
        background: var(--theme-blue-light);
        color: var(--theme-blue);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }

    .form-section-header.blue-accent {
        color: var(--theme-blue);
    }

    .form-section-header:first-of-type {
        margin-top: 0;
    }

    /* Form Labels */
    .custom-label {
        font-size: 12.5px;
        font-weight: 700;
        color: #334155;
        margin-bottom: 5px;
        display: block;
        transition: color 0.2s;
    }

    .custom-label .required-star {
        color: #ef4444;
        margin-left: 2px;
        font-weight: 800;
    }

    /* Form Controls */
    .custom-input, .custom-select, .custom-textarea {
        width: 100%;
        border: 1.5px solid #cbd5e1;
        border-radius: 9px;
        padding: 8.5px 13px;
        font-size: 12.8px;
        color: #0f172a;
        background-color: #ffffff;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        outline: none;
    }

    .custom-input:hover, .custom-select:hover, .custom-textarea:hover {
        border-color: #94a3b8;
    }

    .custom-input:focus, .custom-select:focus, .custom-textarea:focus {
        border-color: var(--theme-blue);
        background-color: #ffffff;
        box-shadow: 0 0 0 4px rgba(29, 78, 216, 0.14);
        transform: translateY(-1px);
    }

    .custom-input::placeholder, .custom-textarea::placeholder {
        color: #94a3af;
        font-size: 12.5px;
    }

    .custom-textarea {
        resize: vertical;
        min-height: 52px;
    }

    /* Photo Upload Area & Preview Box */
    .photo-preview-wrapper {
        margin-top: 10px;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .photo-preview-box {
        width: 86px;
        height: 86px;
        border: 2px dashed #94a3b8;
        border-radius: 12px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        position: relative;
        cursor: pointer;
        transition: all 0.25s ease;
        flex-shrink: 0;
    }

    .photo-preview-box:hover {
        border-color: var(--theme-blue);
        border-style: solid;
        background: var(--theme-blue-light);
        transform: scale(1.04);
        box-shadow: 0 6px 16px rgba(29, 78, 216, 0.12);
    }

    .photo-preview-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .photo-preview-box:hover img {
        transform: scale(1.05);
    }

    .photo-preview-box .placeholder-icon {
        color: #94a3b8;
        font-size: 30px;
        transition: color 0.2s;
    }

    .photo-preview-box:hover .placeholder-icon {
        color: var(--theme-blue);
    }

    .btn-camera-toggle {
        background: #ffffff;
        border: 1.5px solid var(--theme-blue-border);
        color: var(--theme-blue);
        border-radius: 9px;
        padding: 7px 13px;
        font-size: 12px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        box-shadow: 0 2px 4px rgba(29, 78, 216, 0.06);
    }

    .btn-camera-toggle:hover {
        background: var(--theme-blue);
        color: #ffffff;
        border-color: var(--theme-blue);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(29, 78, 216, 0.2);
    }

    /* Fixed Floating Action Bar */
    .floating-action-bar {
        position: fixed;
        bottom: 24px;
        right: 32px;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1.5px solid rgba(226, 232, 240, 0.95);
        padding: 8px 18px;
        border-radius: 50px;
        box-shadow: 0 14px 36px -4px rgba(0, 0, 0, 0.18), 0 4px 14px rgba(29, 78, 216, 0.16);
        display: flex;
        align-items: center;
        gap: 12px;
        z-index: 9999;
        animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .btn-submit-custom {
        background: var(--theme-blue-gradient);
        color: #ffffff;
        border: none;
        border-radius: 30px;
        padding: 10px 28px;
        font-size: 13.5px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 14px rgba(29, 78, 216, 0.35);
    }

    .btn-submit-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(29, 78, 216, 0.45);
        color: #ffffff;
    }

    .btn-submit-custom:active {
        transform: translateY(0);
    }

    .btn-discard-custom {
        background: var(--discard-red);
        color: #ffffff;
        border: none;
        border-radius: 30px;
        padding: 10px 24px;
        font-size: 13.5px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        box-shadow: 0 4px 14px rgba(239, 68, 68, 0.3);
    }

    .btn-discard-custom:hover {
        background: var(--discard-red-hover);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        color: #ffffff;
    }

    .btn-discard-custom:active {
        transform: translateY(0);
    }

    /* Modal Overlays (100% hidden by default) */
    .custom-modal-overlay {
        display: none !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: rgba(15, 23, 42, 0.75) !important;
        backdrop-filter: blur(8px) !important;
        -webkit-backdrop-filter: blur(8px) !important;
        z-index: 999999 !important;
        align-items: center !important;
        justify-content: center !important;
        padding: 20px !important;
        box-sizing: border-box !important;
    }

    .custom-modal-overlay.active {
        display: flex !important;
    }

    .custom-camera-dialog {
        background: #ffffff;
        border: 2px solid var(--theme-blue);
        border-radius: 18px;
        width: 100%;
        max-width: 500px;
        overflow: hidden;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.35);
        animation: fadeInScale 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .custom-camera-header {
        background: var(--theme-blue-gradient);
        color: #ffffff;
        padding: 14px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .custom-camera-header h5 {
        margin: 0;
        font-size: 15.5px;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .custom-camera-close-btn {
        background: transparent;
        border: none;
        color: #ffffff;
        font-size: 22px;
        line-height: 1;
        cursor: pointer;
        opacity: 0.85;
        transition: opacity 0.2s;
    }

    .custom-camera-close-btn:hover {
        opacity: 1;
    }

    .custom-camera-body {
        padding: 20px;
        text-align: center;
    }

    .camera-view-frame {
        width: 100%;
        max-width: 440px;
        height: 320px;
        background: #0f172a;
        border-radius: 14px;
        overflow: hidden;
        margin: 0 auto 16px auto;
        position: relative;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        border: 2px solid #334155;
    }

    #webcamVideo {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Exact Visitor Pass Scan Card in Modal */
    .scan-card-container {
        width: 380px;
        max-width: 100%;
        background: #ffffff;
        border: 2px dashed #475569;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3);
        position: relative;
        animation: fadeInScale 0.35s cubic-bezier(0.16, 1, 0.3, 1) both;
    }

    .card-top-header {
        background: #0056b3;
        color: #ffffff;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .header-left-brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .header-logo-circle {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0,0,0,0.15);
    }
    .header-logo-circle img {
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 2px;
    }
    .header-logo-circle i {
        color: #0056b3;
        font-size: 17px;
    }
    .header-school-name {
        font-size: 13px;
        font-weight: 800;
        letter-spacing: -0.2px;
        text-transform: uppercase;
        color: #ffffff;
        line-height: 1.2;
    }
    .header-code-badge {
        background: #ffffff;
        color: #0056b3;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        overflow: hidden;
        border: 1px solid #ffffff;
    }
    .header-code-badge span.code-label {
        background: #dc2626;
        color: #ffffff;
        padding: 2px 5px;
        font-size: 9px;
    }
    .header-code-badge span.code-val {
        padding: 2px 6px;
        color: #0056b3;
        font-weight: 800;
    }

    .card-gold-stripe {
        background: #f59e0b;
        height: 4px;
        width: 100%;
    }

    .card-sub-banner {
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        text-align: center;
        padding: 6px 12px;
        font-size: 11.5px;
        font-weight: 800;
        color: #334155;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .card-body-content {
        padding: 16px 18px;
    }

    .visitor-profile-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 12px;
    }
    .visitor-meta-left {
        flex: 1;
    }
    .v-label-small {
        font-size: 9.5px;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }
    .v-name-bold {
        font-size: 16px;
        font-weight: 800;
        color: #0056b3;
        line-height: 1.25;
        margin-top: 2px;
        word-break: break-word;
    }
    .v-type-pill {
        display: inline-block;
        background: #0056b3;
        color: #ffffff;
        padding: 2px 9px;
        border-radius: 12px;
        font-size: 10px;
        font-weight: 700;
        margin-top: 4px;
        margin-bottom: 5px;
    }
    .v-detail-line {
        font-size: 11px;
        color: #1e293b;
        font-weight: 600;
        line-height: 1.4;
    }

    .qr-code-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        border: 1.5px dashed #0056b3;
        border-radius: 10px;
        padding: 5px;
        background: #ffffff;
        flex-shrink: 0;
    }
    .qr-code-img {
        width: 95px;
        height: 95px;
        object-fit: contain;
        display: block;
    }
    .qr-sub-text {
        font-size: 9px;
        font-weight: 800;
        color: #dc2626;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-top: 3px;
    }

    .card-details-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 9px 12px;
        margin-bottom: 8px;
        font-size: 10.5px;
    }
    .detail-row-2col {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    .detail-row-2col:last-child {
        margin-bottom: 0;
    }
    .ref-code-red {
        color: #dc2626;
        font-weight: 800;
        font-size: 12px;
    }
    .pill-black {
        background: #0f172a;
        color: #ffffff;
        padding: 1px 7px;
        border-radius: 10px;
        font-size: 9.5px;
        font-weight: 800;
        display: inline-block;
    }
    .pill-cyan {
        background: #06b6d4;
        color: #ffffff;
        padding: 1px 7px;
        border-radius: 10px;
        font-size: 9.5px;
        font-weight: 800;
        display: inline-block;
    }

    .card-purpose-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 12px;
        font-size: 10.5px;
        margin-bottom: 12px;
    }
    .purpose-highlight {
        color: #0056b3;
        font-weight: 700;
    }

    .card-actions-row {
        display: flex;
        gap: 10px;
        justify-content: center;
        padding-top: 4px;
    }
    .btn-card-print {
        flex: 1;
        background: #0056b3;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 11.5px;
        font-weight: 800;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }
    .btn-card-print:hover {
        background: #004494;
        color: #fff;
    }
    .btn-card-new {
        flex: 1;
        background: #ffffff;
        color: #334155;
        border: 1.5px solid #cbd5e1;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 11.5px;
        font-weight: 800;
        cursor: pointer;
        text-align: center;
        text-decoration: none;
        text-transform: uppercase;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .btn-card-new:hover {
        background: #f1f5f9;
        color: #0f172a;
    }

    @media (max-width: 991px) {
        .floating-action-bar {
            left: 16px;
            right: 16px;
            bottom: 16px;
            justify-content: center;
            border-radius: 16px;
        }
        .form-column-panel {
            margin-bottom: 20px;
        }
    }
</style>

<div class="container-fluid px-0">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between shadow-sm mb-4" role="alert" style="border-radius: 12px; border: 1.5px solid #86efac; background: #f0fdf4;">
        <div class="d-flex align-items-center text-success-emphasis fw-semibold">
            <i class="fas fa-check-circle me-2 font-size-18 text-success"></i> {{ session('success') }}
        </div>
        @if(session('registered_visitor_id'))
        <button type="button" class="btn btn-sm btn-success fw-bold text-white px-3" style="border-radius: 8px;" onclick="openScanCardModalDirect()">
            <i class="fas fa-id-card me-1"></i> View Scan Pass
        </button>
        @endif
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show shadow-sm mb-4" role="alert" style="border-radius: 12px; border: 1.5px solid #fca5a5; background: #fef2f2;">
        <div class="fw-bold mb-1 text-danger"><i class="fas fa-exclamation-triangle me-2"></i> Please correct the errors below:</div>
        <ul class="mb-0 ps-3 text-danger-emphasis">
            @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Main Visitor Registration Card -->
    <div class="visitor-card">
        <!-- Top Card Header Banner in Royal Blue & White Theme -->
        <div class="visitor-card-header">
            <div class="header-title">
                <div class="header-icon-circle">
                    <i class="fas fa-plus"></i>
                </div>
                <span>Visitor Registration</span>
            </div>
            <div class="stat-pill">
                <i class="fas fa-id-card"></i> Gate Entry Form
            </div>
        </div>

        <!-- Form Body with Horizontal Side-by-Side Sections -->
        <div class="visitor-card-body">
            <form id="visitorRegistrationForm" method="POST" action="{{ route('school.front-desk.visitor-registration.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row g-4">
                    <!-- LEFT COLUMN: Profile & Location -->
                    <div class="col-lg-6 col-md-12">
                        <div class="form-column-panel">
                            <!-- Visitor Type Selection -->
                            <div class="mb-3">
                                <label class="custom-label" for="visitor_type">
                                    Select Visitor Type <span class="required-star">*</span>
                                </label>
                                <select name="visitor_type" id="visitor_type" class="custom-select" required>
                                    <option value="">-- Select Type --</option>
                                    @foreach($visitorTypes as $vType)
                                        <option value="{{ $vType }}" {{ old('visitor_type') == $vType ? 'selected' : '' }}>
                                            {{ $vType }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Section 1: Personal Profile Information -->
                            <div class="form-section-header">
                                <span class="section-icon-badge"><i class="fas fa-user"></i></span>
                                <span>Personal Profile Information</span>
                            </div>

                            <!-- Full Name & Gender -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-7 col-sm-12">
                                    <label class="custom-label" for="full_name">
                                        Full Name <span class="required-star">*</span>
                                    </label>
                                    <input type="text" name="full_name" id="full_name" class="custom-input" placeholder="Enter Full Name" value="{{ old('full_name') }}" required>
                                </div>
                                <div class="col-md-5 col-sm-12">
                                    <label class="custom-label" for="gender">Gender</label>
                                    <select name="gender" id="gender" class="custom-select">
                                        <option value="">-- Select --</option>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Date of Birth & Mobile Number -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label" for="dob">Date of Birth</label>
                                    <input type="date" name="dob" id="dob" class="custom-input" value="{{ old('dob') }}">
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label" for="mobile_number">
                                        Mobile Number <span class="required-star">*</span>
                                        <span id="lookupStatus" class="ms-1" style="font-size: 11px; font-weight: normal;"></span>
                                    </label>
                                    <input type="tel" name="mobile_number" id="mobile_number" class="custom-input" placeholder="10-digit Mobile No." maxlength="15" value="{{ old('mobile_number') }}" required>
                                </div>
                            </div>

                            <!-- Alternate Mobile & Email Address -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label" for="alternate_mobile">Alternate Mobile</label>
                                    <input type="tel" name="alternate_mobile" id="alternate_mobile" class="custom-input" placeholder="Optional Emergency No." maxlength="15" value="{{ old('alternate_mobile') }}">
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label" for="email">Email Address</label>
                                    <input type="email" name="email" id="email" class="custom-input" placeholder="name@gmail.com" value="{{ old('email') }}">
                                </div>
                            </div>

                            <!-- Section 2: Address & Location Details -->
                            <div class="form-section-header">
                                <span class="section-icon-badge"><i class="fas fa-map-marker-alt"></i></span>
                                <span>Address & Location Details</span>
                            </div>

                            <!-- Street Address -->
                            <div class="mb-3">
                                <label class="custom-label" for="street_address">Street Address</label>
                                <textarea name="street_address" id="street_address" class="custom-textarea" rows="2" placeholder="Complete residential/office coordinates">{{ old('street_address') }}</textarea>
                            </div>

                            <!-- State, City, Pincode -->
                            <div class="row g-3">
                                <div class="col-md-4 col-sm-12">
                                    <label class="custom-label" for="state">State</label>
                                    <input type="text" name="state" id="state" class="custom-input" placeholder="-Select State-" value="{{ old('state') }}">
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <label class="custom-label" for="city">City</label>
                                    <input type="text" name="city" id="city" class="custom-input" placeholder="-Select City-" value="{{ old('city') }}">
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <label class="custom-label" for="pincode">Pincode / Zip</label>
                                    <input type="text" name="pincode" id="pincode" class="custom-input" placeholder="6-Digit Code" maxlength="10" value="{{ old('pincode') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT COLUMN: Meeting Assignment & Credentials -->
                    <div class="col-lg-6 col-md-12">
                        <div class="form-column-panel">
                            <!-- Section 3: Meeting & Destination Assignment -->
                            <div class="form-section-header blue-accent">
                                <span class="section-icon-badge"><i class="fas fa-user-friends"></i></span>
                                <span>Meeting & Destination Assignment</span>
                            </div>

                            <!-- Whom to Meet & Host Name -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label" for="whom_to_meet_type">
                                        Whom to Meet (Category Type) <span class="required-star">*</span>
                                    </label>
                                    <select name="whom_to_meet_type" id="whom_to_meet_type" class="custom-select" required>
                                        <option value="">-- Select Type --</option>
                                        @foreach($whomToMeetTypes as $wType)
                                            <option value="{{ $wType }}" {{ old('whom_to_meet_type') == $wType ? 'selected' : '' }}>
                                                {{ $wType }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label" for="host_name">Specific Host / Official Name</label>
                                    <input type="text" name="host_name" id="host_name" list="staffHostList" class="custom-input" placeholder="Individual's name to meet" value="{{ old('host_name') }}">
                                    <datalist id="staffHostList">
                                        @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->first_name }} {{ $staff->last_name }} ({{ $staff->designation?->name ?? 'Staff' }})"></option>
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>

                            <!-- Security Gate & Entourage Count -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label" for="security_gate">
                                        Security Gate Entry Point <span class="required-star">*</span>
                                    </label>
                                    <input type="text" name="security_gate" id="security_gate" list="securityGateList" class="custom-input" placeholder="e.g. Main Gate 1" value="{{ old('security_gate', 'Main Gate 1') }}" required>
                                    <datalist id="securityGateList">
                                        @foreach($securityGates as $gate)
                                            <option value="{{ $gate }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label" for="entourage_count">
                                        Total Entourage / Head Count <span class="required-star">*</span>
                                    </label>
                                    <input type="number" name="entourage_count" id="entourage_count" class="custom-input" min="1" max="100" value="{{ old('entourage_count', 1) }}" required>
                                </div>
                            </div>

                            <!-- Visit Purpose & Detailed Purpose Remarks -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label" for="visit_purpose">
                                        Visit Purpose Designation <span class="required-star">*</span>
                                    </label>
                                    <select name="visit_purpose" id="visit_purpose" class="custom-select" required>
                                        <option value="">-- Select Purpose --</option>
                                        @foreach($visitPurposes as $purpose)
                                            <option value="{{ $purpose }}" {{ old('visit_purpose') == $purpose ? 'selected' : '' }}>
                                                {{ $purpose }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label" for="detailed_purpose_remarks">Detailed Purpose Remarks</label>
                                    <input type="text" name="detailed_purpose_remarks" id="detailed_purpose_remarks" class="custom-input" placeholder="Enter Purpose Remarks" value="{{ old('detailed_purpose_remarks') }}">
                                </div>
                            </div>

                            <!-- Section 4: Verification Credentials & Photo Storage -->
                            <div class="form-section-header">
                                <span class="section-icon-badge"><i class="fas fa-shield-alt"></i></span>
                                <span>Verification Credentials & Photo Storage</span>
                            </div>

                            <!-- ID Type, Number & Vehicle -->
                            <div class="row g-3 mb-3">
                                <div class="col-md-4 col-sm-12">
                                    <label class="custom-label" for="id_proof_type">ID Proof Type</label>
                                    <select name="id_proof_type" id="id_proof_type" class="custom-select">
                                        <option value="">-- Select --</option>
                                        @foreach($idProofTypes as $idType)
                                            <option value="{{ $idType }}" {{ old('id_proof_type') == $idType ? 'selected' : '' }}>
                                                {{ $idType }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <label class="custom-label" for="id_proof_number">ID Reference No.</label>
                                    <input type="text" name="id_proof_number" id="id_proof_number" class="custom-input" placeholder="ID Number" value="{{ old('id_proof_number') }}">
                                </div>
                                <div class="col-md-4 col-sm-12">
                                    <label class="custom-label" for="vehicle_number">Vehicle No.</label>
                                    <input type="text" name="vehicle_number" id="vehicle_number" class="custom-input" placeholder="e.g. DL-01-XX-0000" value="{{ old('vehicle_number') }}">
                                </div>
                            </div>

                            <!-- Photo & Security Notes -->
                            <div class="row g-3">
                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label" for="photo">Capture / Upload Photo</label>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <input type="file" name="photo" id="photoInput" class="form-control form-control-sm" accept="image/*" style="border-radius: 8px; font-size: 12px; border: 1.5px solid #cbd5e1;">
                                        <button type="button" class="btn-camera-toggle" id="openWebcamBtn" onclick="openWebcamModal()">
                                            <i class="fas fa-camera"></i> Live
                                        </button>
                                    </div>

                                    <input type="hidden" name="webcam_photo" id="webcamPhotoInput">

                                    <div class="photo-preview-wrapper">
                                        <div class="photo-preview-box" id="photoPreviewBox" onclick="document.getElementById('photoInput').click()" title="Click to upload / change photo">
                                            <i class="fas fa-image placeholder-icon" id="photoPlaceholderIcon"></i>
                                            <img src="" id="photoPreviewImg" alt="Preview" style="display: none;">
                                        </div>
                                        <div class="small text-muted" style="font-size: 11px; line-height: 1.4;">
                                            <div>JPG / PNG / Webcam</div>
                                            <button type="button" id="clearPhotoBtn" class="btn btn-link btn-sm text-danger p-0 mt-1 fw-bold" style="font-size: 11px; text-decoration: none; display: none;" onclick="clearVisitorPhoto()">
                                                <i class="fas fa-trash-alt me-1"></i> Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label" for="security_notes">Security Notes / Remarks</label>
                                    <textarea name="security_notes" id="security_notes" class="custom-textarea" rows="4" placeholder="Gatekeeper observations">{{ old('security_notes') }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hidden default form submit trigger -->
                <button type="submit" id="nativeSubmitBtn" style="display: none;"></button>
            </form>
        </div>
    </div>

    <!-- Floating Action Bar with Submit & Discard (Stays floating on screen) -->
    <div class="floating-action-bar">
        <button type="button" id="floatingSubmitBtn" class="btn-submit-custom" onclick="submitRegistrationForm()">
            <i class="fas fa-check-circle"></i> Submit Registration
        </button>
        <button type="button" id="floatingDiscardBtn" class="btn-discard-custom" onclick="resetVisitorForm()">
            <i class="fas fa-times"></i> Discard
        </button>
    </div>

</div>

<!-- Custom Lightbox Camera Modal (Pops up ONLY on Live button click) -->
<div class="custom-modal-overlay" id="cameraModalOverlay">
    <div class="custom-camera-dialog">
        <div class="custom-camera-header">
            <h5><i class="fas fa-camera"></i> Live Visitor Photo Snapshot</h5>
            <button type="button" class="custom-camera-close-btn" onclick="closeWebcamModal()">&times;</button>
        </div>
        <div class="custom-camera-body">
            <div class="camera-view-frame">
                <video id="webcamVideo" autoplay playsinline></video>
                <canvas id="webcamCanvas" style="display: none;"></canvas>
            </div>
            <div id="cameraErrorMsg" class="alert alert-warning py-2 small mb-3" style="display: none; border-radius: 8px;"></div>
            <div class="d-flex justify-content-center gap-3">
                <button type="button" class="btn btn-primary fw-bold px-4" onclick="captureWebcamSnapshot()" style="border-radius: 10px; background: var(--theme-blue); border: none; padding: 10px 24px;">
                    <i class="fas fa-camera me-1"></i> Capture Photo
                </button>
                <button type="button" class="btn btn-secondary fw-semibold px-3" onclick="closeWebcamModal()" style="border-radius: 10px; padding: 10px 20px;">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Automatic Generated Visitor Pass Scan Card Modal (Matching Screenshot) -->
<div class="custom-modal-overlay" id="scanCardModalOverlay">
    <div class="scan-card-container" id="printableScanCard">
        <!-- Top Header -->
        <div class="card-top-header">
            <div class="header-left-brand">
                <div class="header-logo-circle">
                    <i class="fas fa-graduation-cap" id="modalSchoolLogoIcon"></i>
                    <img src="" id="modalSchoolLogoImg" alt="Logo" style="display: none;">
                </div>
                <div class="header-school-name" id="modalSchoolName">{{ Auth::user()?->school?->name ?? 'EDUZEN DEMO PANEL' }}</div>
            </div>
            <div class="header-code-badge">
                <span class="code-label">CODE</span>
                <span class="code-val" id="modalSchoolCode">{{ Auth::user()?->school?->code ? strtoupper(Auth::user()->school->code) : 'EDUZEN' }}</span>
            </div>
        </div>

        <!-- Gold Stripe -->
        <div class="card-gold-stripe"></div>

        <!-- Sub-Banner -->
        <div class="card-sub-banner">
            VISITOR ENTRY PASS
        </div>

        <!-- Body Content -->
        <div class="card-body-content">
            <div class="visitor-profile-row">
                <div class="visitor-meta-left" style="display: flex; gap: 10px; align-items: flex-start;">
                    <!-- Visitor Photo thumbnail on card if uploaded -->
                    <div id="cardVisitorPhotoContainer" style="display: none; width: 56px; height: 56px; border-radius: 8px; overflow: hidden; border: 1.5px solid #0056b3; flex-shrink: 0; margin-top: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <img id="cardVisitorPhotoImg" src="" alt="Visitor Photo" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div>
                        <div class="v-label-small">VISITOR NAME</div>
                        <div class="v-name-bold" id="cardVisitorName">Souhardyadip Mondal</div>
                        <div><span class="v-type-pill" id="cardVisitorType">Visitor</span></div>
                        <div class="v-detail-line"><strong>Mobile:</strong> <span id="cardVisitorMobile">7471515451</span></div>
                        <div class="v-detail-line"><strong>Vehicle:</strong> <span id="cardVisitorVehicle">N/A</span></div>
                    </div>
                </div>
                
                <div class="qr-code-wrapper">
                    <img src="" alt="QR Code" id="cardQrCodeImg" class="qr-code-img">
                    <span class="qr-sub-text">SCAN TO OUT</span>
                </div>
            </div>

            <!-- Details Block -->
            <div class="card-details-box">
                <div class="detail-row-2col">
                    <div><span class="v-label-small">PASS REFERENCE</span></div>
                    <div><span class="v-label-small">MEETING HOST</span></div>
                </div>
                <div class="detail-row-2col" style="margin-bottom: 7px;">
                    <div><span class="ref-code-red" id="cardPassNumber">PASS-EDUZEN-0019</span></div>
                    <div style="font-weight: 700; color: #1e293b;" id="cardMeetingHost">N/A</div>
                </div>

                <div class="detail-row-2col">
                    <div>In: <strong id="cardInTime">{{ date('d-M-Y h:i A') }}</strong></div>
                    <div>Type: <strong id="cardWhomType">Employee</strong></div>
                </div>

                <div class="detail-row-2col" style="margin-top: 5px;">
                    <div>Gate: <span class="pill-black" id="cardGate">1</span></div>
                    <div>Count: <span class="pill-cyan" id="cardCount">1</span> Head(s)</div>
                </div>
            </div>

            <!-- Purpose Block -->
            <div class="card-purpose-box">
                <div><strong>Purpose:</strong> <span class="purpose-highlight" id="cardPurpose">Meet Principal</span></div>
                <div style="margin-top: 2px;"><strong>Remarks:</strong> <span id="cardRemarks">N/A</span></div>
            </div>

            <!-- Buttons -->
            <div class="card-actions-row">
                <button type="button" class="btn-card-print" id="modalPrintPassBtn" onclick="printGeneratedPass()">
                    <i class="fas fa-print"></i> PRINT PASS
                </button>
                <button type="button" class="btn-card-new" onclick="closeScanCardAndNew()">
                    NEW CHECK-IN
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPrintUrl = '#';

    // Submit form via AJAX to instantly show the Scan Card popup
    function submitRegistrationForm() {
        const form = document.getElementById('visitorRegistrationForm');
        if (!form.reportValidity()) return;

        const btn = document.getElementById('floatingSubmitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Generating Pass...';

        const formData = new FormData(form);

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Submit Registration';

            if (data.success && data.visitor) {
                showScanCardModal(data);
                form.reset();
                clearVisitorPhoto();
                document.getElementById('entourage_count').value = '1';
                document.getElementById('security_gate').value = 'Main Gate 1';
            } else {
                alert(data.message || 'Registration failed. Please check form errors.');
            }
        })
        .catch(err => {
            console.error(err);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check-circle"></i> Submit Registration';
            form.submit();
        });
    }

    function showScanCardModal(data) {
        const v = data.visitor;
        currentPrintUrl = data.print_url;

        document.getElementById('cardVisitorName').textContent = v.full_name;
        document.getElementById('cardVisitorType').textContent = v.visitor_type || 'Visitor';
        document.getElementById('cardVisitorMobile').textContent = v.mobile_number;
        document.getElementById('cardVisitorVehicle').textContent = v.vehicle_number || 'N/A';
        document.getElementById('cardPassNumber').textContent = v.pass_number;
        document.getElementById('cardMeetingHost').textContent = v.host_name || 'N/A';
        document.getElementById('cardInTime').textContent = data.in_time || 'Just now';
        document.getElementById('cardWhomType').textContent = v.whom_to_meet_type || 'Official';
        document.getElementById('cardGate').textContent = v.security_gate ? v.security_gate.replace(/[^0-9]/g, '') || v.security_gate : '1';
        document.getElementById('cardCount').textContent = v.entourage_count || '1';
        document.getElementById('cardPurpose').textContent = v.visit_purpose;
        document.getElementById('cardRemarks').textContent = v.detailed_purpose_remarks || 'N/A';

        if (data.school_name) {
            document.getElementById('modalSchoolName').textContent = data.school_name;
        }
        if (data.school_code) {
            document.getElementById('modalSchoolCode').textContent = data.school_code;
        }

        // Show School Logo if present
        const schoolLogoImg = document.getElementById('modalSchoolLogoImg');
        const schoolLogoIcon = document.getElementById('modalSchoolLogoIcon');
        if (data.school_logo) {
            schoolLogoImg.src = data.school_logo;
            schoolLogoImg.style.display = 'block';
            schoolLogoIcon.style.display = 'none';
        } else {
            schoolLogoImg.style.display = 'none';
            schoolLogoIcon.style.display = 'block';
        }

        // Show Visitor Photo if uploaded
        const photoContainer = document.getElementById('cardVisitorPhotoContainer');
        const photoImg = document.getElementById('cardVisitorPhotoImg');
        if (data.photo_url) {
            photoImg.src = data.photo_url;
            photoContainer.style.display = 'block';
        } else {
            photoContainer.style.display = 'none';
        }

        // Generate QR code for Scanner Gun / Camera scanner
        const qrData = encodeURIComponent(v.pass_number);
        document.getElementById('cardQrCodeImg').src = `https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=${qrData}`;

        document.getElementById('scanCardModalOverlay').classList.add('active');
    }

    function printGeneratedPass() {
        if (currentPrintUrl && currentPrintUrl !== '#') {
            window.open(currentPrintUrl, '_blank');
        } else {
            window.print();
        }
    }

    function closeScanCardAndNew() {
        document.getElementById('scanCardModalOverlay').classList.remove('active');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Auto-open scan card if redirected with session
    @if(isset($registeredVisitor) && $registeredVisitor)
    document.addEventListener('DOMContentLoaded', function() {
        showScanCardModal({
            visitor: @json($registeredVisitor),
            photo_url: "{{ $registeredVisitor->photo_url }}",
            school_logo: "{{ $registeredVisitor->meta_data['school_logo'] ?? (Auth::user()?->school?->logo ? asset('storage/' . Auth::user()->school->logo) : '') }}",
            print_url: "{{ route('school.front-desk.visitor.print', $registeredVisitor->id) }}",
            school_name: "{{ $registeredVisitor->meta_data['school_name'] ?? 'EDUZEN DEMO PANEL' }}",
            school_code: "{{ $registeredVisitor->meta_data['school_code'] ?? 'EDUZEN' }}",
            in_time: "{{ $registeredVisitor->check_in_at ? $registeredVisitor->check_in_at->format('d-M-Y h:i A') : $registeredVisitor->created_at->format('d-M-Y h:i A') }}"
        });
    });
    @endif

    // File Input Photo Preview
    const photoInput = document.getElementById('photoInput');
    const photoPreviewBox = document.getElementById('photoPreviewBox');
    const photoPreviewImg = document.getElementById('photoPreviewImg');
    const photoPlaceholderIcon = document.getElementById('photoPlaceholderIcon');
    const clearPhotoBtn = document.getElementById('clearPhotoBtn');
    const webcamPhotoInput = document.getElementById('webcamPhotoInput');

    if (photoInput) {
        photoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    setPhotoPreview(event.target.result);
                    webcamPhotoInput.value = '';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function setPhotoPreview(src) {
        photoPreviewImg.src = src;
        photoPreviewImg.style.display = 'block';
        photoPlaceholderIcon.style.display = 'none';
        clearPhotoBtn.style.display = 'inline-block';
    }

    function clearVisitorPhoto() {
        photoInput.value = '';
        webcamPhotoInput.value = '';
        photoPreviewImg.src = '';
        photoPreviewImg.style.display = 'none';
        photoPlaceholderIcon.style.display = 'block';
        clearPhotoBtn.style.display = 'none';
    }

    // Live Webcam Lightbox Handling
    let webcamStream = null;
    const cameraOverlay = document.getElementById('cameraModalOverlay');
    const webcamVideo = document.getElementById('webcamVideo');
    const webcamCanvas = document.getElementById('webcamCanvas');
    const cameraErrorMsg = document.getElementById('cameraErrorMsg');

    function openWebcamModal() {
        cameraErrorMsg.style.display = 'none';
        cameraOverlay.classList.add('active');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480, facingMode: 'user' } })
                .then(function(stream) {
                    webcamStream = stream;
                    webcamVideo.srcObject = stream;
                    webcamVideo.play();
                })
                .catch(function(err) {
                    console.error("Camera access error:", err);
                    cameraErrorMsg.textContent = "Unable to access webcam. Please check browser permissions or upload an image file.";
                    cameraErrorMsg.style.display = 'block';
                });
        } else {
            cameraErrorMsg.textContent = "Webcam capture is not supported on this browser. Please upload an image file.";
            cameraErrorMsg.style.display = 'block';
        }
    }

    function captureWebcamSnapshot() {
        if (!webcamVideo || !webcamStream) return;

        webcamCanvas.width = 480;
        webcamCanvas.height = 360;
        const ctx = webcamCanvas.getContext('2d');
        ctx.drawImage(webcamVideo, 0, 0, webcamCanvas.width, webcamCanvas.height);

        const dataUrl = webcamCanvas.toDataURL('image/jpeg', 0.9);
        webcamPhotoInput.value = dataUrl;
        photoInput.value = '';
        setPhotoPreview(dataUrl);

        closeWebcamModal();
    }

    function closeWebcamModal() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
        cameraOverlay.classList.remove('active');
    }

    if (cameraOverlay) {
        cameraOverlay.addEventListener('click', function(e) {
            if (e.target === cameraOverlay) {
                closeWebcamModal();
            }
        });
    }

    // Reset Form Action
    function resetVisitorForm() {
        if (confirm("Are you sure you want to discard this form and reset all fields?")) {
            document.getElementById('visitorRegistrationForm').reset();
            clearVisitorPhoto();
            document.getElementById('lookupStatus').textContent = '';
            document.getElementById('entourage_count').value = '1';
            document.getElementById('security_gate').value = 'Main Gate 1';
        }
    }

    // Auto-Lookup Returning Visitor by Mobile Number
    const mobileInput = document.getElementById('mobile_number');
    let lookupTimeout = null;

    if (mobileInput) {
        mobileInput.addEventListener('input', function() {
            const val = this.value.trim();
            const statusBadge = document.getElementById('lookupStatus');
            clearTimeout(lookupTimeout);

            if (val.length >= 10) {
                statusBadge.innerHTML = '<span class="text-primary"><i class="fas fa-spinner fa-spin"></i> Checking...</span>';
                lookupTimeout = setTimeout(() => {
                    fetch(`{{ route('school.front-desk.visitor-registration.lookup') }}?phone=${encodeURIComponent(val)}`)
                        .then(res => res.json())
                        .then(data => {
                            if (data.found && data.visitor) {
                                statusBadge.innerHTML = '<span class="text-success fw-bold"><i class="fas fa-check-circle"></i> Returning Visitor Found</span>';
                                autofillReturningVisitor(data.visitor);
                            } else {
                                statusBadge.innerHTML = '<span class="text-muted">New Visitor</span>';
                            }
                        })
                        .catch(() => {
                            statusBadge.textContent = '';
                        });
                }, 400);
            } else {
                statusBadge.textContent = '';
            }
        });
    }

    function autofillReturningVisitor(v) {
        if (!document.getElementById('full_name').value && v.full_name) {
            document.getElementById('full_name').value = v.full_name;
        }
        if (v.visitor_type && !document.getElementById('visitor_type').value) {
            document.getElementById('visitor_type').value = v.visitor_type;
        }
        if (v.gender && !document.getElementById('gender').value) {
            document.getElementById('gender').value = v.gender;
        }
        if (v.dob && !document.getElementById('dob').value) {
            document.getElementById('dob').value = v.dob;
        }
        if (v.alternate_mobile && !document.getElementById('alternate_mobile').value) {
            document.getElementById('alternate_mobile').value = v.alternate_mobile;
        }
        if (v.email && !document.getElementById('email').value) {
            document.getElementById('email').value = v.email;
        }
        if (v.street_address && !document.getElementById('street_address').value) {
            document.getElementById('street_address').value = v.street_address;
        }
        if (v.state && !document.getElementById('state').value) {
            document.getElementById('state').value = v.state;
        }
        if (v.city && !document.getElementById('city').value) {
            document.getElementById('city').value = v.city;
        }
        if (v.pincode && !document.getElementById('pincode').value) {
            document.getElementById('pincode').value = v.pincode;
        }
        if (v.id_proof_type && !document.getElementById('id_proof_type').value) {
            document.getElementById('id_proof_type').value = v.id_proof_type;
        }
        if (v.id_proof_number && !document.getElementById('id_proof_number').value) {
            document.getElementById('id_proof_number').value = v.id_proof_number;
        }
        if (v.vehicle_number && !document.getElementById('vehicle_number').value) {
            document.getElementById('vehicle_number').value = v.vehicle_number;
        }
        if (v.photo_url && !photoPreviewImg.src) {
            setPhotoPreview(v.photo_url);
        }
    }
</script>
@endsection
