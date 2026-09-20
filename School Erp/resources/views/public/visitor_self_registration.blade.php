<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Visitor Registration — {{ $school->name }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-cyan: #0096e6;
            --brand-cyan-hover: #0284c7;
            --brand-blue-dark: #0f172a;
            --brand-bg: #eef2f6;
            --card-border: #e2e8f0;
            --input-border: #d1d5db;
            --input-focus: #0096e6;
            --accent-soft-blue: #e0f2fe;
            --text-dark: #1e293b;
            --text-muted: #64748b;
        }

        * {
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: #f1f5f9;
            background-image: 
                radial-gradient(at 15% 15%, rgba(0, 150, 230, 0.08) 0px, transparent 50%),
                radial-gradient(at 85% 85%, rgba(14, 165, 233, 0.06) 0px, transparent 50%);
            min-height: 100vh;
            margin: 0;
            padding: 24px 16px 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            -webkit-tap-highlight-color: transparent;
        }

        .kiosk-wrapper {
            width: 100%;
            max-width: 980px;
            margin: 0 auto;
        }

        /* Outer Floating Kiosk Card Matching User Screenshots */
        .kiosk-card {
            background: #ffffff;
            background-image: linear-gradient(180deg, #e6f6ff 0%, #ffffff 75px, #ffffff 100%);
            border-radius: 24px;
            box-shadow: 0 20px 60px -15px rgba(15, 23, 42, 0.12), 0 0 0 1px rgba(226, 232, 240, 0.9);
            padding: 24px 32px 36px;
            position: relative;
            transition: all 0.3s ease;
        }

        @media (max-width: 768px) {
            .kiosk-card {
                padding: 18px 16px 28px;
                border-radius: 20px;
            }
        }

        /* Top Navigation Header */
        .kiosk-top-nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            position: relative;
        }

        .nav-circle-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #ffffff;
            border: 1.5px solid #0096e6;
            color: #0096e6;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            text-decoration: none;
            transition: all 0.2s ease;
            box-shadow: 0 2px 8px rgba(0, 150, 230, 0.15);
            cursor: pointer;
        }

        .nav-circle-btn:hover {
            background: #0096e6;
            color: #ffffff;
            transform: scale(1.05);
        }

        /* Interactive Category Switcher Tabs */
        .category-tabs-wrap {
            display: flex;
            align-items: center;
            background: rgba(241, 245, 249, 0.85);
            padding: 4px;
            border-radius: 30px;
            border: 1px solid #cbd5e1;
            gap: 4px;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.03);
        }

        .category-tab-btn {
            background: transparent;
            border: none;
            border-radius: 24px;
            padding: 7px 18px;
            font-size: 13px;
            font-weight: 700;
            color: #64748b;
            cursor: pointer;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            display: inline-flex;
            align-items: center;
            gap: 7px;
            white-space: nowrap;
        }

        .category-tab-btn:hover {
            color: #0284c7;
            background: rgba(255, 255, 255, 0.7);
        }

        .category-tab-btn.active {
            background: #0096e6;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 150, 230, 0.35);
        }

        @media (max-width: 640px) {
            .category-tabs-wrap {
                width: 100%;
                overflow-x: auto;
                justify-content: space-between;
            }
            .category-tab-btn {
                padding: 6px 12px;
                font-size: 12px;
                flex: 1;
                justify-content: center;
            }
        }

        /* Main Grid: Left Photo Box & Right Form Grid */
        .kiosk-grid {
            display: grid;
            grid-template-columns: 310px 1fr;
            gap: 32px;
            align-items: start;
        }

        @media (max-width: 860px) {
            .kiosk-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }
        }

        /* Left Side Photo Box Matching Image 1, 2, 3 */
        .photo-side-box {
            border: 2px dashed #0096e6;
            border-radius: 16px;
            padding: 30px 18px 24px;
            text-align: center;
            background: #ffffff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 260px;
        }

        .avatar-circle-frame {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            border: 2px dashed #7dd3fc;
            background: #f0f9ff;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 24px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 150, 230, 0.08);
        }

        .avatar-placeholder-icon {
            font-size: 58px;
            color: #bae6fd;
        }

        .avatar-real-preview {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            display: none;
        }

        .photo-btn-group {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
        }

        .btn-photo-pill {
            background: #ffffff;
            border: 1.5px solid #0096e6;
            color: #0096e6;
            border-radius: 10px;
            padding: 7px 12px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s ease;
            text-decoration: none;
            flex: 1;
            justify-content: center;
            white-space: nowrap;
        }

        .btn-photo-pill:hover {
            background: #f0f9ff;
            color: #0284c7;
            border-color: #0284c7;
        }

        .btn-photo-pill.take-selfie {
            background: #ffffff;
            border-color: #0096e6;
            color: #0096e6;
        }

        .btn-photo-pill.take-selfie:hover {
            background: #0096e6;
            color: #ffffff;
        }

        .btn-clear-photo {
            font-size: 11px;
            font-weight: 700;
            color: #ef4444;
            background: none;
            border: none;
            cursor: pointer;
            margin-top: 10px;
            display: none;
        }

        .btn-clear-photo:hover {
            text-decoration: underline;
        }

        /* Right Side Form Inputs Grid */
        .fields-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px 18px;
        }

        @media (max-width: 600px) {
            .fields-grid {
                grid-template-columns: 1fr;
                gap: 14px;
            }
        }

        .field-full {
            grid-column: 1 / -1;
        }

        /* Form Controls Styled to match the screenshot */
        .form-floating-custom {
            position: relative;
        }

        .input-kiosk {
            width: 100%;
            height: 44px;
            border: 1.5px solid var(--input-border);
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 13.5px;
            color: var(--text-dark);
            background: #ffffff;
            transition: all 0.2s ease;
            outline: none;
        }

        .input-kiosk::placeholder {
            color: #94a3b8;
            font-size: 13px;
        }

        .input-kiosk:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px rgba(0, 150, 230, 0.12);
        }

        select.input-kiosk {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
            padding-right: 36px;
        }

        .textarea-kiosk {
            width: 100%;
            min-height: 44px;
            height: 44px;
            border: 1.5px solid var(--input-border);
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13.5px;
            color: var(--text-dark);
            background: #ffffff;
            transition: all 0.2s ease;
            outline: none;
            resize: vertical;
        }

        .textarea-kiosk:focus {
            border-color: var(--input-focus);
            box-shadow: 0 0 0 3px rgba(0, 150, 230, 0.12);
        }

        /* Labeled Fieldset/Box style as shown in screenshot */
        .labeled-field {
            position: relative;
        }

        .floating-field-legend {
            position: absolute;
            top: -9px;
            left: 12px;
            background: #ffffff;
            padding: 0 6px;
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            z-index: 2;
            letter-spacing: 0.2px;
        }

        .req-star {
            color: #ef4444;
            font-weight: 800;
            margin-left: 2px;
        }

        /* Upload ID Proof Box Exactly as in Screenshot */
        .upload-id-box {
            border: 1.5px dashed #0096e6;
            border-radius: 8px;
            padding: 9px 14px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #ffffff;
            cursor: pointer;
            transition: all 0.2s ease;
            user-select: none;
            height: 44px;
        }

        .upload-id-box:hover {
            background: #f0f9ff;
            border-color: #0284c7;
        }

        .upload-id-text {
            font-size: 13px;
            font-weight: 600;
            color: #0284c7;
            display: flex;
            align-items: center;
            gap: 8px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .upload-id-icon-badge {
            width: 26px;
            height: 26px;
            border-radius: 6px;
            background: #0096e6;
            color: #ffffff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            flex-shrink: 0;
            transition: transform 0.2s;
        }

        .upload-id-box:hover .upload-id-icon-badge {
            transform: translateY(-1px);
        }

        /* Bottom Action Buttons: BACK & SUBMIT */
        .kiosk-actions-bar {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 18px;
            margin-top: 36px;
        }

        .btn-kiosk-back {
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #1e3a8a;
            border-radius: 10px;
            padding: 10px 32px;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            min-width: 130px;
            text-align: center;
        }

        .btn-kiosk-back:hover {
            background: #f8fafc;
            border-color: #94a3b8;
            color: #0f172a;
        }

        .btn-kiosk-submit {
            background: #0096e6;
            background-image: linear-gradient(90deg, #0096e6 0%, #00a8f3 100%);
            border: none;
            color: #ffffff;
            border-radius: 10px;
            padding: 11px 36px;
            font-size: 14px;
            font-weight: 800;
            letter-spacing: 0.5px;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(0, 150, 230, 0.35);
            min-width: 140px;
            text-align: center;
        }

        .btn-kiosk-submit:hover {
            background: #0284c7;
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 150, 230, 0.45);
            color: #ffffff;
        }

        .btn-kiosk-submit:disabled {
            opacity: 0.65;
            cursor: not-allowed;
            transform: none;
        }

        /* Forms Dynamic Visibility */
        .category-form-section {
            display: none;
        }

        .category-form-section.active {
            display: contents;
        }

        /* Selfie Camera Modal */
        .camera-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(15, 23, 42, 0.8);
            backdrop-filter: blur(6px);
            z-index: 99999;
            align-items: center;
            justify-content: center;
            padding: 16px;
        }

        .camera-overlay.active {
            display: flex;
        }

        .camera-modal-box {
            background: #ffffff;
            border-radius: 20px;
            max-width: 460px;
            width: 100%;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
        }

        .camera-modal-hdr {
            background: #0096e6;
            color: #ffffff;
            padding: 14px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-weight: 700;
            font-size: 15px;
        }

        .camera-feed-container {
            position: relative;
            background: #000000;
            width: 100%;
            height: 300px;
            overflow: hidden;
        }

        .camera-feed-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Success Card Screen */
        .success-pass-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 40px 24px;
            text-align: center;
            max-width: 580px;
            margin: 0 auto;
            box-shadow: 0 20px 50px -10px rgba(16, 185, 129, 0.15), 0 0 0 1.5px #86efac;
            animation: fadeIn 0.4s ease;
        }

        .success-icon-badge {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: #dcfce7;
            color: #16a34a;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 38px;
            margin-bottom: 20px;
            box-shadow: 0 10px 25px rgba(22, 163, 74, 0.2);
        }

        .pass-code-pill {
            background: #f8fafc;
            border: 2px dashed #0096e6;
            border-radius: 12px;
            padding: 12px 20px;
            font-size: 20px;
            font-weight: 800;
            color: #0096e6;
            letter-spacing: 1px;
            display: inline-block;
            margin: 16px 0 24px;
        }

        .school-info-snippet {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            color: #64748b;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 12px;
        }

        /* ════════════════════════════════════════════════════════════
           STEP 1: CATEGORY SELECTION LANDING SCREEN
           ════════════════════════════════════════════════════════════ */
        .selection-screen {
            animation: fadeInStep 0.35s ease forwards;
        }

        .selection-hero {
            text-align: center;
            margin-bottom: 26px;
        }

        .selection-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #e0f2fe;
            color: #0284c7;
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 12px;
            border: 1px solid #bae6fd;
        }

        .selection-hero h2 {
            font-size: 24px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .selection-hero p {
            font-size: 13.5px;
            color: #64748b;
            max-width: 540px;
            margin: 0 auto;
        }

        .selection-cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 24px;
        }

        @media (max-width: 860px) {
            .selection-cards-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        .selection-card {
            background: #ffffff;
            border: 2px solid #e2e8f0;
            border-radius: 18px;
            padding: 24px 20px 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            user-select: none;
        }

        .selection-card:hover {
            border-color: #0096e6;
            transform: translateY(-4px);
            box-shadow: 0 16px 32px -8px rgba(0, 150, 230, 0.18);
        }

        .card-icon-wrap {
            width: 70px;
            height: 70px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin-bottom: 16px;
            transition: transform 0.25s ease;
        }

        .selection-card:hover .card-icon-wrap {
            transform: scale(1.08);
        }

        .card-icon-wrap.vendor-icon {
            background: linear-gradient(135deg, #e0f2fe, #bae6fd);
            color: #0284c7;
        }

        .card-icon-wrap.admission-icon {
            background: linear-gradient(135deg, #dcfce7, #bbf7d0);
            color: #16a34a;
        }

        .card-icon-wrap.general-icon {
            background: linear-gradient(135deg, #f3e8ff, #e9d5ff);
            color: #7c3aed;
        }

        .selection-card h4 {
            font-size: 17px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .selection-card p {
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
            margin-bottom: 14px;
            flex-grow: 1;
        }

        .card-tags {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            justify-content: center;
            margin-bottom: 16px;
        }

        .card-tag-pill {
            background: #f1f5f9;
            color: #475569;
            font-size: 11px;
            font-weight: 600;
            padding: 3px 9px;
            border-radius: 12px;
        }

        .card-select-btn {
            width: 100%;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 700;
            border: none;
            background: #f1f5f9;
            color: #0f172a;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
        }

        .selection-card:hover .card-select-btn {
            background: #0096e6;
            color: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 150, 230, 0.3);
        }

        .interview-callout-badge {
            background: #fef3c7;
            color: #92400e;
            border: 1px solid #fde68a;
            font-size: 11px;
            font-weight: 700;
            padding: 4px 10px;
            border-radius: 20px;
            margin-bottom: 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-back-to-categories {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            background: #ffffff;
            border: 1.5px solid #cbd5e1;
            color: #475569;
            font-size: 12.5px;
            font-weight: 700;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-back-to-categories:hover {
            border-color: #0096e6;
            color: #0096e6;
            background: #f0f9ff;
        }

        @keyframes fadeInStep {
            from { opacity: 0; transform: translateY(8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.96); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
</head>
<body>

<div class="kiosk-wrapper">

    @if(session('request_submitted'))
    <!-- Success Confirmation Screen -->
    <div class="success-pass-card">
        <div class="success-icon-badge">
            <i class="fas fa-check"></i>
        </div>
        <h3 class="fw-bold text-dark mb-1">Registration Request Submitted!</h3>
        <p class="text-muted mb-2" style="font-size: 14px;">Welcome to <strong>{{ session('school_name', $school->name) }}</strong></p>

        <div class="pass-code-pill">
            <span style="font-size: 11px; text-transform: uppercase; color: #64748b; display: block; font-weight: 700; letter-spacing: 0.5px;">Gate Pass Request No.</span>
            {{ session('submitted_pass_number') }}
        </div>

        <div class="alert alert-info text-start small mb-4" style="border-radius: 12px; background: #f0f9ff; border: 1px solid #bae6fd; color: #0369a1;">
            <div class="fw-bold mb-1"><i class="fas fa-info-circle me-1"></i> What happens next?</div>
            <div>• Please inform security / front desk at the gate that your details are submitted.</div>
            <div>• Once approved, your official digital gate pass will be issued.</div>
        </div>

        <a href="{{ route('public.visitor.register', ['schoolCode' => $school->code ?: $school->id]) }}" class="btn-kiosk-submit d-inline-block text-decoration-none">
            <i class="fas fa-redo me-1"></i> Register Another Visitor
        </a>
    </div>

    @else

    @php
        $showFormInitially = $errors->any() || request()->has('category') || old('category_mode');
    @endphp

    <!-- =============================================================
         STEP 1: INTERACTIVE CATEGORY SELECTION LANDING SCREEN
         ============================================================= -->
    <div class="kiosk-card selection-screen" id="kioskStepSelection" style="{{ $showFormInitially ? 'display: none;' : 'display: block;' }}">
        <!-- School Header Snippet -->
        <div class="school-info-snippet mb-3">
            @if($school->logo_url)
                <img src="{{ $school->logo_url }}" alt="Logo" style="height: 28px; width: 28px; object-fit: contain;">
            @else
                <i class="fas fa-school text-primary" style="font-size: 22px;"></i>
            @endif
            <span class="fw-bold fs-6 text-dark">{{ $school->name }}</span>
            <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px;">Gate Scan</span>
        </div>

        <div class="selection-hero">
            <div class="selection-badge">
                <i class="fas fa-id-badge"></i> Smart Gate Visitor Pass
            </div>
            <h2>Please Select Your Visit Category</h2>
            <p>Tap your visitor category below to proceed with quick self-registration at the front gate.</p>
        </div>

        <!-- 3 Interactive Category Selection Cards -->
        <div class="selection-cards-grid">
            <!-- 1. GENERAL -->
            <div class="selection-card" onclick="selectCategoryAndProceed('general')">
                <div class="interview-callout-badge">
                    <i class="fas fa-file-pdf"></i> Includes Job Interview & CV
                </div>
                <div class="card-icon-wrap general-icon">
                    <i class="fas fa-user-tag"></i>
                </div>
                <h4>General</h4>
                <p>For management meetings, candidate job interviews, parent-teacher visits, alumni, and guest visits.</p>
                <div class="card-tags">
                    <span class="card-tag-pill">Job Interview</span>
                    <span class="card-tag-pill">Staff Interview</span>
                    <span class="card-tag-pill">Official Meeting</span>
                </div>
                <button type="button" class="card-select-btn">
                    Select General <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <!-- 2. ADMISSION -->
            <div class="selection-card" onclick="selectCategoryAndProceed('admission')">
                <div class="card-icon-wrap admission-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h4>Admission</h4>
                <p>For parents and guardians inquiring about new admissions, campus tour, counseling, and prospectus.</p>
                <div class="card-tags">
                    <span class="card-tag-pill">New Admission</span>
                    <span class="card-tag-pill">Campus Tour</span>
                    <span class="card-tag-pill">Counseling</span>
                </div>
                <button type="button" class="card-select-btn">
                    Select Admission <i class="fas fa-arrow-right"></i>
                </button>
            </div>

            <!-- 3. VENDOR -->
            <div class="selection-card" onclick="selectCategoryAndProceed('vendor')">
                <div class="card-icon-wrap vendor-icon">
                    <i class="fas fa-truck-ramp-box"></i>
                </div>
                <h4>Vendor</h4>
                <p>For suppliers, official couriers, maintenance contractors, and cafeteria/goods service providers.</p>
                <div class="card-tags">
                    <span class="card-tag-pill">Suppliers</span>
                    <span class="card-tag-pill">Deliveries</span>
                    <span class="card-tag-pill">Contractors</span>
                </div>
                <button type="button" class="card-select-btn">
                    Select Vendor <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </div>

        <div class="text-center text-muted small mt-3" style="font-size: 12px;">
            <i class="fas fa-shield-alt text-primary me-1"></i> Data entered is directly synced to {{ $school->name }} Front Gate Security.
        </div>
    </div>

    <!-- =============================================================
         STEP 2: REGISTRATION FORM SCREEN
         ============================================================= -->
    <div class="kiosk-card" id="kioskStepForm" style="{{ $showFormInitially ? 'display: block;' : 'display: none;' }}">

        <!-- Top Navigation Bar matching reference with Back, Category Pills, and Home -->
        <div class="kiosk-top-nav">
            <button type="button" class="btn-back-to-categories" onclick="goToCategorySelection()" title="Back to Category Selection">
                <i class="fas fa-arrow-left"></i> Categories
            </button>

            <!-- Category Switcher Tabs -->
            <div class="category-tabs-wrap" role="tablist">
                <button type="button" class="category-tab-btn {{ $activeCategory === 'general' ? 'active' : '' }}" data-cat="general" onclick="switchCategory('general')">
                    <i class="fas fa-user-tag"></i> General
                </button>
                <button type="button" class="category-tab-btn {{ $activeCategory === 'admission' ? 'active' : '' }}" data-cat="admission" onclick="switchCategory('admission')">
                    <i class="fas fa-graduation-cap"></i> Admission
                </button>
                <button type="button" class="category-tab-btn {{ $activeCategory === 'vendor' ? 'active' : '' }}" data-cat="vendor" onclick="switchCategory('vendor')">
                    <i class="fas fa-truck-ramp-box"></i> Vendor
                </button>
            </div>

            <a href="{{ route('public.visitor.register', ['schoolCode' => $school->code ?: $school->id]) }}" class="nav-circle-btn" title="Refresh / Home">
                <i class="fas fa-home"></i>
            </a>
        </div>

        <!-- School Title Snippet -->
        <div class="school-info-snippet">
            @if($school->logo_url)
                <img src="{{ $school->logo_url }}" alt="Logo" style="height: 22px; width: 22px; object-fit: contain;">
            @else
                <i class="fas fa-school text-primary"></i>
            @endif
            <span>{{ $school->name }}</span>
            <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 11px;">Gate Scan</span>
        </div>

        @if($errors->any())
        <div class="alert alert-danger py-2 px-3 mb-3 small" style="border-radius: 10px;">
            <div class="fw-bold mb-1"><i class="fas fa-exclamation-triangle me-1"></i> Please check required fields:</div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form id="visitorKioskForm" method="POST" action="{{ route('public.visitor.register.store', ['schoolCode' => $school->code ?: $school->id]) }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="category_mode" id="categoryModeInput" value="{{ $activeCategory }}">

            <div class="kiosk-grid">

                <!-- Left Column: Live Camera Picture ONLY (No Gallery Upload) -->
                <div class="photo-side-box">
                    <div class="avatar-circle-frame">
                        <i class="fas fa-user avatar-placeholder-icon" id="avatarPlaceholderIcon"></i>
                        <img src="" id="visitorPhotoPreview" class="avatar-real-preview" alt="Preview">
                    </div>

                    <input type="hidden" name="webcam_photo" id="webcamPhotoDataInput">

                    <div class="photo-btn-group" style="width: 100%;">
                        <button type="button" class="btn-photo-pill take-selfie" onclick="openSelfieCameraModal()" style="width: 100%; justify-content: center; background: #0096e6; color: #fff; border-color: #0096e6; font-weight: 700; box-shadow: 0 4px 12px rgba(0, 150, 230, 0.25);">
                            <i class="fas fa-camera"></i> Take Live Photo
                        </button>
                    </div>

                    <div class="small text-muted mt-2" style="font-size: 11px;">
                        <i class="fas fa-info-circle me-1"></i> Live camera snapshot only
                    </div>

                    <button type="button" id="btnClearPhoto" class="btn-clear-photo" onclick="clearPhotoSelection()">
                        <i class="fas fa-trash me-1"></i> Retake / Remove
                    </button>
                </div>

                <!-- Right Column: Fields Grid -->
                <div class="fields-grid">

                    <!-- ==========================================
                         IMAGE 1: VENDOR FORM FIELDS
                         ========================================== -->
                    <div id="vendorFieldsSection" class="category-form-section {{ $activeCategory === 'vendor' ? 'active' : '' }}">
                        <!-- Visitor's Name -->
                        <div class="labeled-field">
                            <input type="text" name="vendor_visitor_name" id="vendor_visitor_name" class="input-kiosk" placeholder="Visitor's Name *" value="{{ old('vendor_visitor_name') }}">
                        </div>

                        <!-- Select Meeting Purpose -->
                        <div class="labeled-field">
                            <select name="vendor_meeting_purpose" id="vendor_meeting_purpose" class="input-kiosk">
                                <option value="">Select Meeting Purpose *</option>
                                @foreach($vendorPurposes as $vPurpose)
                                    <option value="{{ $vPurpose }}" {{ old('vendor_meeting_purpose') == $vPurpose ? 'selected' : '' }}>
                                        {{ $vPurpose }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Email id -->
                        <div class="labeled-field">
                            <input type="email" name="vendor_email" id="vendor_email" class="input-kiosk" placeholder="Email id" value="{{ old('vendor_email') }}">
                        </div>

                        <!-- Organisation Name -->
                        <div class="labeled-field">
                            <input type="text" name="vendor_organisation_name" id="vendor_organisation_name" class="input-kiosk" placeholder="Organisation Name *" value="{{ old('vendor_organisation_name') }}">
                        </div>

                        <!-- Description -->
                        <div class="labeled-field">
                            <input type="text" name="vendor_description" id="vendor_description" class="input-kiosk" placeholder="Description" value="{{ old('vendor_description') }}">
                        </div>

                        <!-- No Of Visitors -->
                        <div class="labeled-field">
                            <span class="floating-field-legend">No Of Visitors</span>
                            <select name="vendor_no_of_visitors" id="vendor_no_of_visitors" class="input-kiosk">
                                <option value="0" {{ old('vendor_no_of_visitors', '0') == '0' ? 'selected' : '' }}>0</option>
                                <option value="1" {{ old('vendor_no_of_visitors') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ old('vendor_no_of_visitors') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ old('vendor_no_of_visitors') == '3' ? 'selected' : '' }}>3</option>
                                <option value="4" {{ old('vendor_no_of_visitors') == '4' ? 'selected' : '' }}>4</option>
                                <option value="5" {{ old('vendor_no_of_visitors') == '5' ? 'selected' : '' }}>5+</option>
                            </select>
                        </div>

                        <!-- Upload ID Proof -->
                        <div class="field-full">
                            <div class="upload-id-box" onclick="triggerIdUpload('vendor')">
                                <span class="upload-id-text" id="vendorIdUploadText">
                                    <i class="fas fa-file-invoice text-muted"></i> Upload ID Proof
                                </span>
                                <span class="upload-id-icon-badge">
                                    <i class="fas fa-arrow-up-from-bracket"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ==========================================
                         IMAGE 2: ADMISSION FORM FIELDS
                         ========================================== -->
                    <div id="admissionFieldsSection" class="category-form-section {{ $activeCategory === 'admission' ? 'active' : '' }}">
                        <!-- Academic Year -->
                        <div class="labeled-field">
                            <span class="floating-field-legend">Academic Year</span>
                            <select name="admission_academic_year" id="admission_academic_year" class="input-kiosk">
                                @foreach($academicSessions as $session)
                                    <option value="{{ $session->name }}" {{ old('admission_academic_year') == $session->name ? 'selected' : '' }}>
                                        {{ $session->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Parent's Name -->
                        <div class="labeled-field">
                            <input type="text" name="admission_parent_name" id="admission_parent_name" class="input-kiosk" placeholder="Parent's Name *" value="{{ old('admission_parent_name') }}">
                        </div>

                        <!-- Child's Name -->
                        <div class="labeled-field">
                            <input type="text" name="admission_child_name" id="admission_child_name" class="input-kiosk" placeholder="Child's Name *" value="{{ old('admission_child_name') }}">
                        </div>

                        <!-- Select Date of Birth -->
                        <div class="labeled-field">
                            <input type="date" name="admission_dob" id="admission_dob" class="input-kiosk" placeholder="Select Date of Birth *" value="{{ old('admission_dob') }}" title="Select Date of Birth">
                        </div>

                        <!-- Gender -->
                        <div class="labeled-field">
                            <select name="admission_gender" id="admission_gender" class="input-kiosk">
                                <option value="">Gender *</option>
                                <option value="Male" {{ old('admission_gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                <option value="Female" {{ old('admission_gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                <option value="Other" {{ old('admission_gender') == 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>

                        <!-- Source of Information -->
                        <div class="labeled-field">
                            <select name="admission_source_of_information" id="admission_source_of_information" class="input-kiosk">
                                <option value="">Source of Information *</option>
                                @foreach($sourceOfInformation as $src)
                                    <option value="{{ $src }}" {{ old('admission_source_of_information') == $src ? 'selected' : '' }}>
                                        {{ $src }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Address (Single Box as requested) -->
                        <div class="labeled-field">
                            <input type="text" name="admission_address" id="admission_address" class="input-kiosk" placeholder="Address *" value="{{ old('admission_address') }}">
                        </div>

                        <!-- Admission Sought in Class -->
                        <div class="labeled-field">
                            <select name="admission_sought_class" id="admission_sought_class" class="input-kiosk">
                                <option value="">Admission Sought in Class *</option>
                                @foreach($schoolClasses as $cls)
                                    <option value="{{ $cls->name }}" {{ old('admission_sought_class') == $cls->name ? 'selected' : '' }}>
                                        {{ $cls->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Email id -->
                        <div class="labeled-field">
                            <input type="email" name="admission_email" id="admission_email" class="input-kiosk" placeholder="Email id" value="{{ old('admission_email') }}">
                        </div>

                        <!-- No Of Visitors -->
                        <div class="labeled-field">
                            <span class="floating-field-legend">No Of Visitors</span>
                            <select name="admission_no_of_visitors" id="admission_no_of_visitors" class="input-kiosk">
                                <option value="0" {{ old('admission_no_of_visitors', '0') == '0' ? 'selected' : '' }}>0</option>
                                <option value="1" {{ old('admission_no_of_visitors') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ old('admission_no_of_visitors') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ old('admission_no_of_visitors') == '3' ? 'selected' : '' }}>3</option>
                                <option value="4" {{ old('admission_no_of_visitors') == '4' ? 'selected' : '' }}>4</option>
                                <option value="5" {{ old('admission_no_of_visitors') == '5' ? 'selected' : '' }}>5+</option>
                            </select>
                        </div>

                        <!-- Upload ID Proof -->
                        <div class="field-full">
                            <div class="upload-id-box" onclick="triggerIdUpload('admission')">
                                <span class="upload-id-text" id="admissionIdUploadText">
                                    <i class="fas fa-file-invoice text-muted"></i> Upload ID Proof
                                </span>
                                <span class="upload-id-icon-badge">
                                    <i class="fas fa-arrow-up-from-bracket"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- ==========================================
                         IMAGE 3: GENERAL PURPOSE FORM FIELDS
                         ========================================== -->
                    <div id="generalFieldsSection" class="category-form-section {{ $activeCategory === 'general' ? 'active' : '' }}">
                        <!-- Visitor's Name -->
                        <div class="labeled-field">
                            <input type="text" name="general_visitor_name" id="general_visitor_name" class="input-kiosk" placeholder="Visitor's Name *" value="{{ old('general_visitor_name') }}">
                        </div>

                        <!-- Select Meeting Purpose -->
                        <div class="labeled-field">
                            <select name="general_meeting_purpose" id="general_meeting_purpose" class="input-kiosk" onchange="handleGeneralPurposeChange(this.value)">
                                <option value="">Select Meeting Purpose *</option>
                                @foreach($generalPurposes as $gPurpose)
                                    <option value="{{ $gPurpose }}" {{ old('general_meeting_purpose') == $gPurpose ? 'selected' : '' }}>
                                        {{ $gPurpose }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Email id -->
                        <div class="labeled-field">
                            <input type="email" name="general_email" id="general_email" class="input-kiosk" placeholder="Email id" value="{{ old('general_email') }}">
                        </div>

                        <!-- Address (Single Box as requested) -->
                        <div class="labeled-field">
                            <input type="text" name="general_address" id="general_address" class="input-kiosk" placeholder="Address *" value="{{ old('general_address') }}">
                        </div>

                        <!-- Description -->
                        <div class="labeled-field">
                            <input type="text" name="general_description" id="general_description" class="input-kiosk" placeholder="Description" value="{{ old('general_description') }}">
                        </div>

                        <!-- No Of Visitors -->
                        <div class="labeled-field">
                            <span class="floating-field-legend">No Of Visitors</span>
                            <select name="general_no_of_visitors" id="general_no_of_visitors" class="input-kiosk">
                                <option value="0" {{ old('general_no_of_visitors', '0') == '0' ? 'selected' : '' }}>0</option>
                                <option value="1" {{ old('general_no_of_visitors') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ old('general_no_of_visitors') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ old('general_no_of_visitors') == '3' ? 'selected' : '' }}>3</option>
                                <option value="4" {{ old('general_no_of_visitors') == '4' ? 'selected' : '' }}>4</option>
                                <option value="5" {{ old('general_no_of_visitors') == '5' ? 'selected' : '' }}>5+</option>
                            </select>
                        </div>

                        <!-- CV Upload (PDF Only) for Interview Candidate -->
                        <div class="field-full" id="interviewCvSection" style="display: {{ stripos(old('general_meeting_purpose', ''), 'interview') !== false ? 'block' : 'none' }};">
                            <div class="p-3 mb-2" style="background: #f0f7ff; border: 1.5px dashed #0096e6; border-radius: 12px;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <span class="fw-bold" style="color: #0b3a75; font-size: 13px;">
                                        <i class="fas fa-file-pdf text-danger me-1"></i> Candidate CV / Resume (PDF Only) *
                                    </span>
                                    <span class="badge bg-danger text-white px-2 py-1" style="font-size: 10px;">Required for Interview</span>
                                </div>
                                <div class="upload-id-box" onclick="document.getElementById('interviewCvInput').click()" style="background: #fff; cursor: pointer;">
                                    <span class="upload-id-text" id="interviewCvUploadText">
                                        <i class="fas fa-cloud-arrow-up text-primary me-2"></i> Choose PDF file (Max 10MB)
                                    </span>
                                    <span class="upload-id-icon-badge" style="background: #e8f4fc; color: #0096e6;">
                                        <i class="fas fa-file-arrow-up"></i>
                                    </span>
                                </div>
                                <input type="file" name="cv" id="interviewCvInput" accept=".pdf,application/pdf" class="d-none" onchange="handleInterviewCvSelect(this)">
                                <div class="d-flex justify-content-between align-items-center mt-2" style="font-size: 11px; color: #64748b;">
                                    <span><i class="fas fa-lock me-1"></i> Sent directly to School HR & Teachers</span>
                                    <button type="button" id="btnRemoveCv" class="btn btn-link text-danger p-0 text-decoration-none small" style="display: none; font-size: 11px;" onclick="removeInterviewCv()">
                                        <i class="fas fa-trash me-1"></i> Remove CV
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Upload ID Proof -->
                        <div class="field-full">
                            <div class="upload-id-box" onclick="triggerIdUpload('general')">
                                <span class="upload-id-text" id="generalIdUploadText">
                                    <i class="fas fa-file-invoice text-muted"></i> Upload ID Proof
                                </span>
                                <span class="upload-id-icon-badge">
                                    <i class="fas fa-arrow-up-from-bracket"></i>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Shared Hidden ID Proof Input -->
                    <input type="file" name="id_proof" id="hiddenIdProofInput" accept=".pdf,.png,.jpg,.jpeg,.webp" class="d-none">

                    <!-- Unified Form Fields for Submission Mapping -->
                    <input type="hidden" name="visitor_name" id="sub_visitor_name">
                    <input type="hidden" name="parent_name" id="sub_parent_name">
                    <input type="hidden" name="child_name" id="sub_child_name">
                    <input type="hidden" name="dob" id="sub_dob">
                    <input type="hidden" name="gender" id="sub_gender">
                    <input type="hidden" name="source_of_information" id="sub_source_of_information">
                    <input type="hidden" name="admission_sought_class" id="sub_admission_sought_class">
                    <input type="hidden" name="academic_year" id="sub_academic_year">
                    <input type="hidden" name="organisation_name" id="sub_organisation_name">
                    <input type="hidden" name="meeting_purpose" id="sub_meeting_purpose">
                    <input type="hidden" name="email" id="sub_email">
                    <input type="hidden" name="address" id="sub_address">
                    <input type="hidden" name="description" id="sub_description">
                    <input type="hidden" name="no_of_visitors" id="sub_no_of_visitors">

                </div>
            </div>

            <!-- Bottom Action Buttons: BACK & SUBMIT as in Screenshots -->
            <div class="kiosk-actions-bar">
                <button type="button" class="btn-kiosk-back" onclick="resetOrGoBack()">
                    BACK
                </button>
                <button type="submit" id="btnSubmitKiosk" class="btn-kiosk-submit">
                    SUBMIT
                </button>
            </div>
        </form>

    </div>
    @endif

</div>

<!-- Camera Selfie Modal -->
<div class="camera-overlay" id="selfieCameraOverlay">
    <div class="camera-modal-box">
        <div class="camera-modal-hdr">
            <span><i class="fas fa-camera me-2"></i> Take Selfie</span>
            <button type="button" class="btn-close btn-close-white" onclick="closeSelfieCameraModal()"></button>
        </div>
        <div class="camera-feed-container">
            <video id="selfieVideoFeed" autoplay playsinline></video>
            <canvas id="selfieCanvas" style="display: none;"></canvas>
        </div>
        <div id="selfieCameraAlert" class="alert alert-warning py-1 small m-2" style="display: none;"></div>
        <div class="p-3 text-center d-flex justify-content-center gap-2 bg-light">
            <button type="button" class="btn btn-primary fw-bold px-4" onclick="captureSelfieSnapshot()" style="background: #0096e6; border-color: #0096e6; border-radius: 8px;">
                <i class="fas fa-camera me-1"></i> Capture Photo
            </button>
            <button type="button" class="btn btn-secondary fw-semibold px-3" onclick="closeSelfieCameraModal()" style="border-radius: 8px;">
                Cancel
            </button>
        </div>
    </div>
</div>

<script>
    // Current Active Category
    let currentCategory = '{{ $activeCategory }}';

    function switchCategory(cat) {
        currentCategory = cat;
        document.getElementById('categoryModeInput').value = cat;

        // Update tab buttons
        document.querySelectorAll('.category-tab-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        const activeTab = Array.from(document.querySelectorAll('.category-tab-btn')).find(b => b.textContent.toLowerCase().includes(cat));
        if (activeTab) activeTab.classList.add('active');

        // Toggle field sections
        document.querySelectorAll('.category-form-section').forEach(sec => {
            sec.classList.remove('active');
        });
        const activeSection = document.getElementById(cat + 'FieldsSection');
        if (activeSection) {
            activeSection.classList.add('active');
        }
    }

    // Step 1 -> Step 2 transition
    function selectCategoryAndProceed(cat) {
        switchCategory(cat);
        const stepSelection = document.getElementById('kioskStepSelection');
        const stepForm = document.getElementById('kioskStepForm');
        if (stepSelection) stepSelection.style.display = 'none';
        if (stepForm) {
            stepForm.style.display = 'block';
            stepForm.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    // Step 2 -> Step 1 back transition
    function goToCategorySelection() {
        const stepSelection = document.getElementById('kioskStepSelection');
        const stepForm = document.getElementById('kioskStepForm');
        if (stepSelection) {
            stepSelection.style.display = 'block';
            stepSelection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        if (stepForm) stepForm.style.display = 'none';
    }

    // ID Proof Upload Trigger
    function triggerIdUpload(category) {
        const fileInput = document.getElementById('hiddenIdProofInput');
        if (fileInput) fileInput.click();
    }

    const idFileInput = document.getElementById('hiddenIdProofInput');
    if (idFileInput) {
        idFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const fileName = file.name;
                const displayText = `<i class="fas fa-check-circle text-success me-1"></i> ${fileName}`;
                
                ['vendor', 'admission', 'general'].forEach(cat => {
                    const textSpan = document.getElementById(cat + 'IdUploadText');
                    if (textSpan) textSpan.innerHTML = displayText;
                });
            }
        });
    }

    // Interview Purpose & CV Upload Handling
    function handleGeneralPurposeChange(val) {
        const cvSec = document.getElementById('interviewCvSection');
        if (!cvSec) return;
        if (val && val.toLowerCase().includes('interview')) {
            cvSec.style.display = 'block';
        } else {
            cvSec.style.display = 'none';
        }
    }

    function handleInterviewCvSelect(input) {
        const file = input.files[0];
        const textSpan = document.getElementById('interviewCvUploadText');
        const removeBtn = document.getElementById('btnRemoveCv');
        if (!file) return;

        const fileName = file.name.toLowerCase();
        if (!fileName.endsWith('.pdf') && file.type !== 'application/pdf') {
            alert("Only PDF files (.pdf) are allowed for CV / Resume upload.");
            input.value = '';
            if (textSpan) textSpan.innerHTML = '<i class="fas fa-cloud-arrow-up text-primary me-2"></i> Choose PDF file (Max 10MB)';
            if (removeBtn) removeBtn.style.display = 'none';
            return;
        }

        if (file.size > 10 * 1024 * 1024) {
            alert("The CV file size must not exceed 10 MB.");
            input.value = '';
            return;
        }

        if (textSpan) {
            textSpan.innerHTML = `<i class="fas fa-file-pdf text-danger me-2"></i> <strong>${file.name}</strong> (${(file.size / (1024 * 1024)).toFixed(2)} MB)`;
        }
        if (removeBtn) removeBtn.style.display = 'inline-block';
    }

    function removeInterviewCv() {
        const input = document.getElementById('interviewCvInput');
        const textSpan = document.getElementById('interviewCvUploadText');
        const removeBtn = document.getElementById('btnRemoveCv');
        if (input) input.value = '';
        if (textSpan) textSpan.innerHTML = '<i class="fas fa-cloud-arrow-up text-primary me-2"></i> Choose PDF file (Max 10MB)';
        if (removeBtn) removeBtn.style.display = 'none';
    }

    // Photo Live Camera Handling ONLY (No Gallery Upload)
    const webcamInput = document.getElementById('webcamPhotoDataInput');
    const photoPreview = document.getElementById('visitorPhotoPreview');
    const avatarPlaceholder = document.getElementById('avatarPlaceholderIcon');
    const btnClearPhoto = document.getElementById('btnClearPhoto');

    function setPhotoPreview(dataUrl) {
        if (photoPreview && avatarPlaceholder) {
            photoPreview.src = dataUrl;
            photoPreview.style.display = 'block';
            avatarPlaceholder.style.display = 'none';
        }
        if (btnClearPhoto) btnClearPhoto.style.display = 'inline-block';
    }

    function clearPhotoSelection() {
        if (webcamInput) webcamInput.value = '';
        if (photoPreview) {
            photoPreview.src = '';
            photoPreview.style.display = 'none';
        }
        if (avatarPlaceholder) avatarPlaceholder.style.display = 'block';
        if (btnClearPhoto) btnClearPhoto.style.display = 'none';
    }

    // Selfie Camera
    let selfieStream = null;
    const cameraOverlay = document.getElementById('selfieCameraOverlay');
    const selfieVideo = document.getElementById('selfieVideoFeed');
    const selfieCanvas = document.getElementById('selfieCanvas');
    const selfieAlert = document.getElementById('selfieCameraAlert');

    function openSelfieCameraModal() {
        if (selfieAlert) selfieAlert.style.display = 'none';
        if (cameraOverlay) cameraOverlay.classList.add('active');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } } })
                .then(function(stream) {
                    selfieStream = stream;
                    selfieVideo.srcObject = stream;
                    selfieVideo.play();
                })
                .catch(function(err) {
                    console.error("Camera error:", err);
                    if (selfieAlert) {
                        selfieAlert.textContent = "Camera access denied or unavailable. Please enable camera permissions to take a live photo.";
                        selfieAlert.style.display = 'block';
                    }
                });
        } else {
            if (selfieAlert) {
                selfieAlert.textContent = "Live webcam is not supported on this browser or connection is not secure (HTTPS required).";
                selfieAlert.style.display = 'block';
            }
        }
    }

    function captureSelfieSnapshot() {
        if (!selfieVideo || !selfieStream) return;
        selfieCanvas.width = 480;
        selfieCanvas.height = 360;
        const ctx = selfieCanvas.getContext('2d');
        ctx.drawImage(selfieVideo, 0, 0, selfieCanvas.width, selfieCanvas.height);

        const dataUrl = selfieCanvas.toDataURL('image/jpeg', 0.9);
        webcamInput.value = dataUrl;

        setPhotoPreview(dataUrl);
        closeSelfieCameraModal();
    }

    function closeSelfieCameraModal() {
        if (selfieStream) {
            selfieStream.getTracks().forEach(track => track.stop());
            selfieStream = null;
        }
        if (cameraOverlay) cameraOverlay.classList.remove('active');
    }

    function resetOrGoBack() {
        const stepSelection = document.getElementById('kioskStepSelection');
        if (stepSelection) {
            goToCategorySelection();
        } else if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.reload();
        }
    }

    // Initialize state on load
    document.addEventListener('DOMContentLoaded', function() {
        const generalPurposeEl = document.getElementById('general_meeting_purpose');
        if (generalPurposeEl && generalPurposeEl.value) {
            handleGeneralPurposeChange(generalPurposeEl.value);
        }
    });

    // Form Submission & Mapping
    const kioskForm = document.getElementById('visitorKioskForm');
    if (kioskForm) {
        kioskForm.addEventListener('submit', function(e) {
            const cat = currentCategory;

            if (cat === 'vendor') {
                const name = document.getElementById('vendor_visitor_name').value.trim();
                const purpose = document.getElementById('vendor_meeting_purpose').value;
                const org = document.getElementById('vendor_organisation_name').value.trim();

                if (!name) {
                    alert("Please enter Visitor's Name");
                    document.getElementById('vendor_visitor_name').focus();
                    e.preventDefault();
                    return false;
                }
                if (!purpose) {
                    alert("Please select Meeting Purpose");
                    document.getElementById('vendor_meeting_purpose').focus();
                    e.preventDefault();
                    return false;
                }
                if (!org) {
                    alert("Please enter Organisation Name");
                    document.getElementById('vendor_organisation_name').focus();
                    e.preventDefault();
                    return false;
                }

                document.getElementById('sub_visitor_name').value = name;
                document.getElementById('sub_meeting_purpose').value = purpose;
                document.getElementById('sub_email').value = document.getElementById('vendor_email').value.trim();
                document.getElementById('sub_organisation_name').value = org;
                document.getElementById('sub_description').value = document.getElementById('vendor_description').value.trim();
                document.getElementById('sub_no_of_visitors').value = document.getElementById('vendor_no_of_visitors').value;

            } else if (cat === 'admission') {
                const parent = document.getElementById('admission_parent_name').value.trim();
                const child = document.getElementById('admission_child_name').value.trim();
                const dob = document.getElementById('admission_dob').value;
                const gender = document.getElementById('admission_gender').value;
                const src = document.getElementById('admission_source_of_information').value;
                const addr = document.getElementById('admission_address').value.trim();
                const cls = document.getElementById('admission_sought_class').value;

                if (!parent) {
                    alert("Please enter Parent's Name");
                    document.getElementById('admission_parent_name').focus();
                    e.preventDefault();
                    return false;
                }
                if (!child) {
                    alert("Please enter Child's Name");
                    document.getElementById('admission_child_name').focus();
                    e.preventDefault();
                    return false;
                }
                if (!dob) {
                    alert("Please select Date of Birth");
                    document.getElementById('admission_dob').focus();
                    e.preventDefault();
                    return false;
                }
                if (!gender) {
                    alert("Please select Gender");
                    document.getElementById('admission_gender').focus();
                    e.preventDefault();
                    return false;
                }
                if (!src) {
                    alert("Please select Source of Information");
                    document.getElementById('admission_source_of_information').focus();
                    e.preventDefault();
                    return false;
                }
                if (!addr) {
                    alert("Please enter Address");
                    document.getElementById('admission_address').focus();
                    e.preventDefault();
                    return false;
                }
                if (!cls) {
                    alert("Please select Admission Sought in Class");
                    document.getElementById('admission_sought_class').focus();
                    e.preventDefault();
                    return false;
                }

                document.getElementById('sub_parent_name').value = parent;
                document.getElementById('sub_child_name').value = child;
                document.getElementById('sub_dob').value = dob;
                document.getElementById('sub_gender').value = gender;
                document.getElementById('sub_source_of_information').value = src;
                document.getElementById('sub_address').value = addr;
                document.getElementById('sub_admission_sought_class').value = cls;
                document.getElementById('sub_academic_year').value = document.getElementById('admission_academic_year').value;
                document.getElementById('sub_email').value = document.getElementById('admission_email').value.trim();
                document.getElementById('sub_no_of_visitors').value = document.getElementById('admission_no_of_visitors').value;

            } else if (cat === 'general') {
                const name = document.getElementById('general_visitor_name').value.trim();
                const purpose = document.getElementById('general_meeting_purpose').value;
                const addr = document.getElementById('general_address').value.trim();

                if (!name) {
                    alert("Please enter Visitor's Name");
                    document.getElementById('general_visitor_name').focus();
                    e.preventDefault();
                    return false;
                }
                if (!purpose) {
                    alert("Please select Meeting Purpose");
                    document.getElementById('general_meeting_purpose').focus();
                    e.preventDefault();
                    return false;
                }
                if (!addr) {
                    alert("Please enter Address");
                    document.getElementById('general_address').focus();
                    e.preventDefault();
                    return false;
                }

                // If interview, validate CV upload
                if (purpose.toLowerCase().includes('interview')) {
                    const cvInput = document.getElementById('interviewCvInput');
                    if (!cvInput || !cvInput.files || cvInput.files.length === 0) {
                        alert("Candidate CV / Resume (PDF Only) is mandatory for Interview registration. Please upload CV.");
                        if (cvInput) {
                            cvInput.click();
                        }
                        e.preventDefault();
                        return false;
                    }
                }

                document.getElementById('sub_visitor_name').value = name;
                document.getElementById('sub_meeting_purpose').value = purpose;
                document.getElementById('sub_email').value = document.getElementById('general_email').value.trim();
                document.getElementById('sub_address').value = addr;
                document.getElementById('sub_description').value = document.getElementById('general_description').value.trim();
                document.getElementById('sub_no_of_visitors').value = document.getElementById('general_no_of_visitors').value;
            }

            const btn = document.getElementById('btnSubmitKiosk');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> SUBMITTING...';
            }
        });
    }
</script>
</body>
</html>
