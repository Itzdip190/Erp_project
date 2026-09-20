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

    .btn-qr-scanner-trigger {
        background: #ffffff;
        color: var(--theme-blue);
        border: 1.5px solid #ffffff;
        border-radius: 24px;
        padding: 6px 15px;
        font-size: 12.5px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        transition: all 0.22s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.12);
        text-decoration: none;
    }

    .btn-qr-scanner-trigger:hover {
        background: #f8fafc;
        transform: translateY(-2px);
        box-shadow: 0 5px 14px rgba(0, 0, 0, 0.18);
        color: #1e40af;
    }

    /* QR Generator Modal - Ultra Premium Theme */
    .qr-modal-dialog {
        background: #ffffff;
        border-radius: 22px;
        width: 100%;
        max-width: 580px;
        overflow: hidden;
        box-shadow: 0 25px 60px -10px rgba(15, 23, 42, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.1);
        animation: fadeInScale 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
        border: none;
    }

    .qr-modal-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #2563eb 100%);
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
    }

    .qr-modal-header .hdr-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .qr-modal-icon-badge {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(8px);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        color: #60a5fa;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }

    .qr-modal-title {
        font-size: 16.5px;
        font-weight: 800;
        margin: 0;
        letter-spacing: -0.2px;
        color: #ffffff;
    }

    .qr-modal-sub {
        font-size: 11.5px;
        color: #93c5fd;
        margin: 0;
    }

    .qr-modal-close-btn {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.12);
        border: none;
        color: #ffffff;
        font-size: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .qr-modal-close-btn:hover {
        background: rgba(239, 68, 68, 0.85);
        transform: scale(1.05);
    }

    .qr-gold-stripe {
        background: linear-gradient(90deg, #f59e0b 0%, #fbbf24 50%, #f59e0b 100%);
        height: 4px;
        width: 100%;
    }

    /* Premium Standee Showcase Card */
    .qr-showcase-box {
        background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1.5px solid #e2e8f0;
        border-radius: 18px;
        padding: 22px 20px;
        text-align: center;
        position: relative;
        box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.8), 0 8px 20px rgba(0, 0, 0, 0.03);
    }

    .qr-smart-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #0f172a;
        color: #38bdf8;
        font-size: 10.5px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        padding: 4px 12px;
        border-radius: 20px;
        margin-bottom: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }

    .qr-school-heading {
        font-size: 18px;
        font-weight: 900;
        color: #0f172a;
        margin-bottom: 4px;
        letter-spacing: -0.3px;
    }

    .qr-code-pill-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #ffffff;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        padding: 2px 8px;
        font-size: 11px;
        font-weight: 800;
        color: #1e40af;
        margin-bottom: 16px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
    }

    .qr-code-pill-badge .code-highlight {
        color: #dc2626;
    }

    /* Target Scanner Bracket Box */
    .qr-target-container {
        position: relative;
        display: inline-block;
        padding: 14px;
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 12px 30px -8px rgba(37, 99, 235, 0.22), 0 4px 10px rgba(0,0,0,0.04);
        margin-bottom: 14px;
        border: 2px solid #e0e7ff;
    }

    .scanner-corner {
        position: absolute;
        width: 20px;
        height: 20px;
        border-color: #2563eb;
        border-style: solid;
        pointer-events: none;
    }

    .corner-tl { top: 6px; left: 6px; border-width: 3px 0 0 3px; border-radius: 6px 0 0 0; }
    .corner-tr { top: 6px; right: 6px; border-width: 3px 3px 0 0; border-radius: 0 6px 0 0; }
    .corner-bl { bottom: 6px; left: 6px; border-width: 0 0 3px 3px; border-radius: 0 0 0 6px; }
    .corner-br { bottom: 6px; right: 6px; border-width: 0 3px 3px 0; border-radius: 0 0 6px 0; }

    .qr-target-img {
        width: 175px;
        height: 175px;
        display: block;
        border-radius: 8px;
    }

    .qr-scan-badge-prompt {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        color: #1d4ed8;
        padding: 5px 14px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 14px;
    }

    /* 3 Step Visual Journey */
    .qr-step-journey {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: 4px;
    }

    .step-pill-item {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 700;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 5px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }

    .step-pill-item i {
        color: #2563eb;
    }

    .step-arrow-divider {
        color: #94a3b8;
        font-size: 10px;
    }

    /* Integrated URL Copy Box */
    .url-copy-wrapper {
        background: #f8fafc;
        border: 1.5px solid #cbd5e1;
        border-radius: 12px;
        padding: 4px 5px 4px 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }

    .url-copy-wrapper:focus-within {
        border-color: #2563eb;
        background: #ffffff;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
    }

    .url-copy-input {
        flex: 1;
        border: none;
        background: transparent;
        font-size: 12.5px;
        font-weight: 600;
        color: #1e293b;
        outline: none;
        padding: 4px 0;
    }

    .btn-copy-gradient {
        background: linear-gradient(135deg, #1e40af 0%, #2563eb 100%);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 7px 16px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }

    .btn-copy-gradient:hover {
        background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(37, 99, 235, 0.35);
        color: #ffffff;
    }

    /* Action Buttons in Modal */
    .btn-modal-print {
        flex: 1.2;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 12.5px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        transition: all 0.2s;
        box-shadow: 0 3px 8px rgba(15, 23, 42, 0.2);
    }

    .btn-modal-print:hover {
        background: #020617;
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(15, 23, 42, 0.3);
        color: #ffffff;
    }

    .btn-modal-download {
        flex: 1;
        background: #ffffff;
        color: #334155;
        border: 1.5px solid #cbd5e1;
        border-radius: 10px;
        padding: 9px 14px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-modal-download:hover {
        background: #f1f5f9;
        border-color: #94a3b8;
        color: #0f172a;
    }

    .btn-modal-open {
        flex: 1;
        background: var(--theme-blue-light);
        color: var(--theme-blue);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 10px;
        padding: 9px 14px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.2s;
    }

    .btn-modal-open:hover {
        background: var(--theme-blue);
        color: #ffffff;
        border-color: var(--theme-blue);
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
            <div class="d-flex align-items-center gap-2">
                <button type="button" id="btnOpenQrModal" class="btn-qr-scanner-trigger" onclick="openQrGeneratorModal()" title="Generate Gate QR Code for Visitor Self-Registration">
                    <i class="fas fa-qrcode"></i> Generate Scanner / QR
                </button>
                <div class="stat-pill">
                    <i class="fas fa-id-card"></i> Gate Entry Form
                </div>
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
                                    <select name="visit_purpose" id="visit_purpose" class="custom-select" required onchange="handleVisitPurposeChange(this.value)">
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

                            <!-- Candidate CV Upload (PDF Only) when Interview is selected -->
                            <div class="row g-3 mb-3" id="cvUploadRow" style="display: {{ stripos(old('visit_purpose', ''), 'interview') !== false ? 'flex' : 'none' }};">
                                <div class="col-12">
                                    <div class="p-3" style="background: #eff6ff; border: 1.5px dashed #2563eb; border-radius: 10px;">
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <label class="custom-label mb-0" for="cvInput" style="color: #1e40af;">
                                                <i class="fas fa-file-pdf text-danger me-1"></i> Candidate CV / Resume (PDF Only) <span class="required-star">*</span>
                                            </label>
                                            <span class="badge bg-danger text-white px-2 py-1" style="font-size: 10px;">Interview Candidate</span>
                                        </div>
                                        <div class="input-group input-group-sm">
                                            <input type="file" name="cv" id="cvInput" class="form-control" accept=".pdf,application/pdf" onchange="validateCvFile(this)" style="border-radius: 8px; font-size: 12.5px; background: #fff;">
                                        </div>
                                        <div class="small text-muted mt-1" style="font-size: 11px;">
                                            <i class="fas fa-info-circle me-1"></i> Strictly PDF format (Max 10MB). Automatically accessible by Teachers & HR.
                                        </div>
                                    </div>
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

                            <!-- Photo (Live Camera Only) & Security Notes -->
                            <div class="row g-3">
                                <div class="col-md-6 col-sm-12">
                                    <label class="custom-label">Capture Visitor Photo (Live Camera Only)</label>
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <button type="button" class="btn-camera-toggle" id="openWebcamBtn" onclick="openWebcamModal()" style="flex: 1; padding: 9px 16px; border-radius: 8px; font-weight: 700; background: var(--theme-blue); color: #fff; border: none; display: flex; align-items: center; justify-content: center; gap: 8px; box-shadow: 0 4px 12px rgba(29, 78, 216, 0.25);">
                                            <i class="fas fa-camera"></i> Take Live Camera Photo
                                        </button>
                                    </div>

                                    <input type="hidden" name="webcam_photo" id="webcamPhotoInput">

                                    <div class="photo-preview-wrapper">
                                        <div class="photo-preview-box" id="photoPreviewBox" onclick="openWebcamModal()" title="Click to take live photo with webcam" style="cursor: pointer;">
                                            <i class="fas fa-camera placeholder-icon" id="photoPlaceholderIcon"></i>
                                            <img src="" id="photoPreviewImg" alt="Preview" style="display: none;">
                                        </div>
                                        <div class="small text-muted" style="font-size: 11px; line-height: 1.4;">
                                            <div class="fw-bold text-dark"><i class="fas fa-shield-alt text-primary me-1"></i> Live Camera Only</div>
                                            <div>Direct gate verification</div>
                                            <button type="button" id="clearPhotoBtn" class="btn btn-link btn-sm text-danger p-0 mt-1 fw-bold" style="font-size: 11px; text-decoration: none; display: none;" onclick="clearVisitorPhoto()">
                                                <i class="fas fa-trash-alt me-1"></i> Retake / Remove
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

<!-- QR Code Generator Lightbox Modal - Ultra Premium Theme -->
<div class="custom-modal-overlay" id="qrGeneratorModalOverlay" style="display: none;">
    <div class="qr-modal-dialog">
        <!-- Header -->
        <div class="qr-modal-header">
            <div class="hdr-left">
                <div class="qr-modal-icon-badge">
                    <i class="fas fa-qrcode"></i>
                </div>
                <div>
                    <h5 class="qr-modal-title">Visitor Self-Registration Portal</h5>
                    <p class="qr-modal-sub">Smart Front Gate QR Scanner & Standee Poster</p>
                </div>
            </div>
            <button type="button" class="qr-modal-close-btn" onclick="closeQrGeneratorModal()">&times;</button>
        </div>
        <div class="qr-gold-stripe"></div>

        <!-- Body -->
        <div class="p-4">
            <!-- Showcase Card -->
            <div class="qr-showcase-box">
                <span class="qr-smart-pill">
                    <i class="fas fa-bolt"></i> Smart Gate Self-Service QR
                </span>
                
                <h4 class="qr-school-heading">{{ $school->name ?? 'School Campus' }}</h4>
                
                <div>
                    <div class="qr-code-pill-badge">
                        <span>SCHOOL CODE:</span>
                        <span class="code-highlight">{{ $schoolCode ?? 'EDUZEN' }}</span>
                    </div>
                </div>

                <!-- Futuristic Scanner Target Frame -->
                <div>
                    <div class="qr-target-container">
                        <div class="scanner-corner corner-tl"></div>
                        <div class="scanner-corner corner-tr"></div>
                        <div class="scanner-corner corner-bl"></div>
                        <div class="scanner-corner corner-br"></div>
                        <img src="{{ $qrCodeImageUrl ?? '' }}" alt="Visitor Form QR" class="qr-target-img" id="modalQrCodeImg">
                    </div>
                </div>

                <div>
                    <div class="qr-scan-badge-prompt">
                        <i class="fas fa-camera"></i> Point Phone Camera to Scan & Register
                    </div>
                </div>

                <!-- 3-Step Journey -->
                <div class="qr-step-journey">
                    <div class="step-pill-item"><i class="fas fa-qrcode"></i> 1. Scan QR</div>
                    <span class="step-arrow-divider"><i class="fas fa-chevron-right"></i></span>
                    <div class="step-pill-item"><i class="fas fa-user-edit"></i> 2. Fill Form</div>
                    <span class="step-arrow-divider"><i class="fas fa-chevron-right"></i></span>
                    <div class="step-pill-item"><i class="fas fa-envelope-open-text"></i> 3. Pass to Email</div>
                </div>
            </div>

            <!-- Direct URL Integrated Box -->
            <div class="mb-3 mt-3">
                <label class="custom-label mb-1 fw-bold text-dark" style="font-size: 12px;">Direct Portal Registration URL</label>
                <div class="url-copy-wrapper">
                    <i class="fas fa-globe text-primary ms-1" style="font-size: 13px;"></i>
                    <input type="text" id="qrPublicFormUrlInput" class="url-copy-input" readonly value="{{ $publicFormUrl ?? '' }}">
                    <button type="button" id="btnCopyQrLink" class="btn-copy-gradient" onclick="copyQrLink()">
                        <i class="fas fa-copy"></i> Copy Link
                    </button>
                </div>
            </div>

            <!-- Modal Action Buttons -->
            <div class="d-flex flex-wrap gap-2 mt-3">
                <button type="button" class="btn-modal-print" onclick="printQrPoster()">
                    <i class="fas fa-print"></i> Print Gate Poster
                </button>
                <button type="button" class="btn-modal-download" onclick="downloadQrImage()">
                    <i class="fas fa-download"></i> Download QR
                </button>
                <a href="{{ $publicFormUrl ?? '#' }}" target="_blank" class="btn-modal-open">
                    <i class="fas fa-external-link-alt"></i> Open Form
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    let currentPrintUrl = '#';

    // Purpose & Interview CV Handling
    function handleVisitPurposeChange(val) {
        const row = document.getElementById('cvUploadRow');
        if (!row) return;
        if (val && val.toLowerCase().includes('interview')) {
            row.style.setProperty('display', 'flex', 'important');
        } else {
            row.style.setProperty('display', 'none', 'important');
        }
    }

    function validateCvFile(input) {
        const file = input.files[0];
        if (!file) return;
        const fname = file.name.toLowerCase();
        if (!fname.endsWith('.pdf') && file.type !== 'application/pdf') {
            alert("Only PDF files (.pdf) are allowed for candidate CV / Resume upload.");
            input.value = '';
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            alert("Candidate CV file size must not exceed 10 MB.");
            input.value = '';
            return;
        }
    }

    // Submit form via AJAX to instantly show the Scan Card popup
    function submitRegistrationForm() {
        const form = document.getElementById('visitorRegistrationForm');
        if (!form.reportValidity()) return;

        // Check Interview CV requirement
        const purposeVal = document.getElementById('visit_purpose')?.value || '';
        if (purposeVal.toLowerCase().includes('interview')) {
            const cvInput = document.getElementById('cvInput');
            if (!cvInput || !cvInput.files || cvInput.files.length === 0) {
                alert("Candidate CV / Resume (PDF Only) is mandatory for Interview visitors. Please attach the CV.");
                if (cvInput) cvInput.focus();
                return;
            }
        }

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
                const cvRow = document.getElementById('cvUploadRow');
                if (cvRow) cvRow.style.display = 'none';
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

        const scanModal = document.getElementById('scanCardModalOverlay');
        if (scanModal) {
            scanModal.classList.add('active');
            scanModal.style.setProperty('display', 'flex', 'important');
        }
    }

    function printGeneratedPass() {
        if (currentPrintUrl && currentPrintUrl !== '#') {
            window.open(currentPrintUrl, '_blank');
        } else {
            window.print();
        }
    }

    function closeScanCardAndNew() {
        const scanModal = document.getElementById('scanCardModalOverlay');
        if (scanModal) {
            scanModal.classList.remove('active');
            scanModal.style.setProperty('display', 'none', 'important');
        }
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

    // Live Camera Photo Handling ONLY (No Gallery Upload)
    const photoPreviewBox = document.getElementById('photoPreviewBox');
    const photoPreviewImg = document.getElementById('photoPreviewImg');
    const photoPlaceholderIcon = document.getElementById('photoPlaceholderIcon');
    const clearPhotoBtn = document.getElementById('clearPhotoBtn');
    const webcamPhotoInput = document.getElementById('webcamPhotoInput');

    function setPhotoPreview(src) {
        photoPreviewImg.src = src;
        photoPreviewImg.style.display = 'block';
        photoPlaceholderIcon.style.display = 'none';
        clearPhotoBtn.style.display = 'inline-block';
    }

    function clearVisitorPhoto() {
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
        if (cameraOverlay) {
            cameraOverlay.classList.add('active');
            cameraOverlay.style.setProperty('display', 'flex', 'important');
        }

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: { width: 640, height: 480, facingMode: 'user' } })
                .then(function(stream) {
                    webcamStream = stream;
                    webcamVideo.srcObject = stream;
                    webcamVideo.play();
                })
                .catch(function(err) {
                    console.error("Camera access error:", err);
                    cameraErrorMsg.textContent = "Unable to access webcam. Please allow camera permissions in your browser.";
                    cameraErrorMsg.style.display = 'block';
                });
        } else {
            cameraErrorMsg.textContent = "Live webcam capture is not supported on this browser or connection is not secure (HTTPS required).";
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
        setPhotoPreview(dataUrl);

        closeWebcamModal();
    }

    function closeWebcamModal() {
        if (webcamStream) {
            webcamStream.getTracks().forEach(track => track.stop());
            webcamStream = null;
        }
        if (cameraOverlay) {
            cameraOverlay.classList.remove('active');
            cameraOverlay.style.setProperty('display', 'none', 'important');
        }
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

    // QR Code Modal Functions
    function openQrGeneratorModal() {
        const modal = document.getElementById('qrGeneratorModalOverlay');
        if (modal) {
            modal.classList.add('active');
            modal.style.setProperty('display', 'flex', 'important');
        }
    }

    function closeQrGeneratorModal() {
        const modal = document.getElementById('qrGeneratorModalOverlay');
        if (modal) {
            modal.classList.remove('active');
            modal.style.setProperty('display', 'none', 'important');
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('qrGeneratorModalOverlay');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeQrGeneratorModal();
                }
            });
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeQrGeneratorModal();
            }
        });

        const btn = document.getElementById('btnOpenQrModal');
        if (btn) {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                openQrGeneratorModal();
            });
        }
    });

    function copyQrLink() {
        const input = document.getElementById('qrPublicFormUrlInput');
        if (!input) return;
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            const btn = document.getElementById('btnCopyQrLink');
            const orig = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check text-success"></i> Copied!';
            setTimeout(() => { btn.innerHTML = orig; }, 2000);
        }).catch(() => {
            document.execCommand('copy');
            alert('Link copied to clipboard!');
        });
    }

    function printQrPoster() {
        const schoolName = @json($school->name ?? 'School Campus');
        const schoolCode = @json($schoolCode ?? 'EDUZEN');
        const qrImgUrl = @json($qrCodeImageUrl ?? '');

        const posterHtml = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Visitor Gate Pass QR Poster - ${schoolName}</title>
                <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800;900&display=swap" rel="stylesheet">
                <style>
                    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
                    body { background: #f8fafc; padding: 40px; display: flex; align-items: center; justify-content: center; min-height: 100vh; }
                    .standee-poster {
                        width: 520px;
                        background: #ffffff;
                        border: 4px solid #1d4ed8;
                        border-radius: 24px;
                        padding: 36px 30px;
                        text-align: center;
                        box-shadow: 0 20px 50px rgba(0,0,0,0.12);
                    }
                    .poster-header-badge {
                        background: #1d4ed8;
                        color: #ffffff;
                        font-size: 13px;
                        font-weight: 800;
                        text-transform: uppercase;
                        letter-spacing: 1px;
                        padding: 6px 18px;
                        border-radius: 20px;
                        display: inline-block;
                        margin-bottom: 16px;
                    }
                    .school-name {
                        font-size: 24px;
                        font-weight: 900;
                        color: #0f172a;
                        margin-bottom: 6px;
                        line-height: 1.2;
                    }
                    .school-sub {
                        font-size: 13px;
                        color: #64748b;
                        margin-bottom: 24px;
                    }
                    .qr-display-box {
                        background: #f1f5f9;
                        border: 3px dashed #2563eb;
                        border-radius: 20px;
                        padding: 24px;
                        display: inline-block;
                        margin-bottom: 24px;
                    }
                    .qr-img {
                        width: 220px;
                        height: 220px;
                        display: block;
                        margin: 0 auto;
                    }
                    .scan-instructions {
                        font-size: 18px;
                        font-weight: 800;
                        color: #1d4ed8;
                        margin-bottom: 8px;
                    }
                    .scan-subtext {
                        font-size: 13px;
                        color: #475569;
                        line-height: 1.5;
                    }
                    .poster-footer {
                        margin-top: 26px;
                        padding-top: 18px;
                        border-top: 1px solid #e2e8f0;
                        font-size: 11px;
                        color: #94a3b8;
                    }
                    @media print {
                        body { background: transparent; padding: 0; }
                        .standee-poster { border: 3px solid #1d4ed8; box-shadow: none; width: 100%; max-width: 600px; margin: 0 auto; }
                    }
                </style>
            </head>
            <body>
                <div class="standee-poster">
                    <div class="poster-header-badge">Front Gate Visitor Portal</div>
                    <h1 class="school-name">${schoolName}</h1>
                    <p class="school-sub">School Code: <strong>${schoolCode}</strong></p>
                    
                    <div class="qr-display-box">
                        <img src="${qrImgUrl}" alt="Scan QR" class="qr-img">
                    </div>

                    <div class="scan-instructions">📲 Scan with Phone Camera to Register</div>
                    <p class="scan-subtext">Fill in your visit details on your mobile. Once approved, your Digital Visitor Pass will be delivered directly to your email!</p>

                    <div class="poster-footer">
                        Powered by Educorerp • Front Desk Visitor Security System
                    </div>
                </div>
                <script>
                    window.onload = function() { window.print(); }
                <\/script>
            </body>
            </html>
        `;
        const printWin = window.open('', '_blank');
        printWin.document.write(posterHtml);
        printWin.document.close();
    }

    function downloadQrImage() {
        const qrUrl = @json($qrCodeImageUrl ?? '');
        if (!qrUrl) return;
        const link = document.createElement('a');
        link.href = qrUrl;
        link.download = `visitor_qr_${@json($schoolCode ?? 'school')}.png`;
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>
@endsection
