@extends('superadmin.layouts.master')

@section('styles')
<style>
    /* Card and Section Aesthetics */
    .qr-hub-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 18px;
        box-shadow: 0 4px 20px rgba(12, 16, 36, 0.04);
        transition: all 0.25s ease;
        overflow: hidden;
        margin-bottom: 24px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }
    .qr-hub-card:hover {
        box-shadow: 0 10px 30px rgba(12, 16, 36, 0.08);
        border-color: #cbd5e1;
    }
    .qr-hub-card-header {
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .qr-hub-card-body {
        padding: 24px;
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    .qr-badge-main {
        background: linear-gradient(135deg, rgba(229, 186, 115, 0.18), rgba(197, 155, 39, 0.22));
        color: #b45309;
        font-weight: 700;
        font-size: 0.72rem;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        padding: 5px 12px;
        border-radius: 999px;
        border: 1px solid rgba(229, 186, 115, 0.4);
    }
    .qr-badge-demo {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.12), rgba(79, 70, 229, 0.16));
        color: #4338ca;
        font-weight: 700;
        font-size: 0.72rem;
        letter-spacing: 0.6px;
        text-transform: uppercase;
        padding: 5px 12px;
        border-radius: 999px;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }

    /* Clean, 100% Unobstructed QR Frame */
    .qr-frame-wrapper {
        background: #f8fafc;
        border: 2px dashed #cbd5e1;
        border-radius: 16px;
        padding: 22px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
        margin-bottom: 20px;
    }
    .qr-display-box {
        background: #ffffff;
        padding: 14px;
        border-radius: 14px;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
        border: 2px solid #0c1024;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 230px;
        min-height: 230px;
    }
    .qr-display-box img, .qr-display-box canvas {
        width: 215px !important;
        height: 215px !important;
        max-width: 100%;
        display: block !important;
        image-rendering: pixelated;
    }

    /* URL Input & Quick Edit Box */
    .qr-url-group {
        position: relative;
        margin-bottom: 8px;
    }
    .qr-url-input {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.88rem;
        font-family: monospace;
        color: #0f172a;
        font-weight: 600;
        width: 100%;
        transition: all 0.2s ease;
    }
    .qr-url-input:focus {
        background: #fff;
        border-color: #e5ba73;
        box-shadow: 0 0 0 3px rgba(229, 186, 115, 0.25);
        outline: none;
    }

    /* Action Buttons */
    .btn-qr-action {
        font-weight: 600;
        font-size: 0.84rem;
        padding: 9px 16px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .btn-gold-action {
        background: linear-gradient(135deg, #e5ba73, #c59b27);
        color: #0c1024 !important;
        border: none;
        box-shadow: 0 4px 12px rgba(229, 186, 115, 0.25);
    }
    .btn-gold-action:hover {
        background: linear-gradient(135deg, #dbb065, #b88d1d);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(229, 186, 115, 0.35);
    }
    .btn-indigo-action {
        background: linear-gradient(135deg, #6366f1, #4f46e5);
        color: #ffffff !important;
        border: none;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    }
    .btn-indigo-action:hover {
        background: linear-gradient(135deg, #4f46e5, #4338ca);
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
    }

    /* Marketing Info Box */
    .marketing-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 0.76rem;
        font-weight: 600;
        background: #f1f5f9;
        color: #475569;
        cursor: pointer;
        transition: all 0.15s ease;
        border: 1px solid transparent;
    }
    .marketing-pill:hover, .marketing-pill.active {
        background: #e2e8f0;
        color: #0c1024;
        border-color: #cbd5e1;
    }

    /* Dark Mode Overrides */
    body.dark-mode .qr-hub-card {
        background: #111827;
        border-color: #1e293b;
    }
    body.dark-mode .qr-hub-card-header {
        border-bottom-color: #1e293b;
    }
    body.dark-mode .qr-frame-wrapper {
        background: #0b0f19;
        border-color: #334155;
    }
    body.dark-mode .qr-display-box {
        background: #ffffff; /* Must remain crisp white for camera scanner contrast */
    }
    body.dark-mode .qr-url-input {
        background: #1e293b;
        border-color: #334155;
        color: #e2e8f0;
    }
    body.dark-mode .marketing-pill {
        background: #1e293b;
        color: #94a3b8;
    }
    body.dark-mode .marketing-pill:hover, body.dark-mode .marketing-pill.active {
        background: #334155;
        color: #f8fafc;
    }

    /* Print Specific Styling for High Quality Standee / Poster */
    @media print {
        body * {
            visibility: hidden;
        }
        .main-sidebar, .main-header, .btn, .breadcrumb, .content-header, .no-print, .modal-backdrop {
            display: none !important;
        }
        #printableStandeeArea, #printableStandeeArea * {
            visibility: visible;
        }
        #printableStandeeArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: white !important;
            padding: 40px;
            display: block !important;
        }
    }
</style>
@endsection

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-3 align-items-center">
            <div class="col-sm-7">
                <div class="d-flex align-items-center gap-3">
                    <div class="brand-logo-icon-gold" style="width: 44px; height: 44px; font-size: 1.3rem;">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <div>
                        <h1 class="m-0 font-heading text-dark fw-bold" style="font-size: 1.55rem;">Website & Demo QR Generator</h1>
                        <p class="text-muted small mb-0">High-contrast, 100% scannable QR codes for Educore's Main Website and Live Demo Booking page.</p>
                    </div>
                </div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ $mainWebsiteUrl }}" target="_blank" class="btn btn-outline-secondary btn-sm rounded-pill mr-1" id="topBtnMainWebsite">
                    <i class="fas fa-globe mr-1"></i> Main Website
                </a>
                <a href="{{ $demoPageUrl }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill mr-1" id="topBtnBookDemo">
                    <i class="fas fa-calendar-check mr-1"></i> Book Demo
                </a>
                <button type="button" class="btn btn-dark btn-sm rounded-pill" onclick="printSingleStandee('demo')">
                    <i class="fas fa-print mr-1"></i> Print Standee
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <!-- Quick Info Banner -->
        <div class="card border-0 mb-4" style="background: linear-gradient(135deg, #0c1024, #171b30); border-radius: 16px; color: #fff;">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge" style="background: rgba(229,186,115,0.2); color: #e5ba73; border: 1px solid rgba(229,186,115,0.4); font-size: 0.72rem; letter-spacing: 0.5px;">
                                <i class="fas fa-shield-alt mr-1"></i> 100% CAMERA SCAN GUARANTEED
                            </span>
                            <span class="text-muted" style="font-size: 0.8rem;">Ultra-High Error Correction Level (ECC High 30%)</span>
                        </div>
                        <h4 class="fw-bold mb-2 text-white font-heading">Scan with Any Mobile Camera &ndash; Opens the Exact Destination Page</h4>
                        <p class="mb-0 text-white-50 small" style="line-height: 1.6;">
                            Clean, unblocked QR matrix ensures instant recognition on all Android and iOS smartphones without login redirects.
                            You can also edit or paste any custom page URL below &ndash; the QR code will update live in real-time.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-right mt-3 mt-lg-0">
                        <div class="d-inline-flex flex-column align-items-lg-end">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge badge-success px-2 py-1"><i class="fas fa-check-double mr-1"></i> Unobstructed Matrix</span>
                                <span class="badge badge-light px-2 py-1"><i class="fas fa-mobile-alt mr-1"></i> Mobile Browser Ready</span>
                            </div>
                            <small class="text-muted">Direct Navigation &bull; No Redirect Loops</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2 Main QR Cards Row -->
        <div class="row">
            
            <!-- Card 1: Educore Main Website QR -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="qr-hub-card">
                    <div class="qr-hub-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="brand-logo-icon-gold" style="width: 36px; height: 36px; font-size: 1rem;">
                                <i class="fas fa-globe"></i>
                            </div>
                            <div>
                                <h5 class="m-0 font-heading fw-bold text-dark">Educore Main Website</h5>
                                <small class="text-muted">Marketing Homepage & Product Overview</small>
                            </div>
                        </div>
                        <span class="qr-badge-main"><i class="fas fa-star mr-1"></i> Main Portal</span>
                    </div>

                    <div class="qr-hub-card-body">
                        <!-- Clean, 100% Scannable QR Frame -->
                        <div class="qr-frame-wrapper">
                            <div class="qr-display-box" id="mainQrBox">
                                <img src="{{ $mainQrImage }}" alt="Educore Main Website QR" id="mainQrImg" crossorigin="anonymous">
                            </div>
                            <div class="text-center mt-3">
                                <span class="badge badge-light text-dark border px-3 py-1 font-weight-bold" style="font-size: 0.82rem;">
                                    <i class="fas fa-camera mr-1 text-primary"></i> Point any phone camera to scan
                                </span>
                            </div>
                        </div>

                        <!-- Target URL Section (Editable by SuperAdmin) -->
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label text-muted small fw-bold text-uppercase mb-0">
                                Destination URL (Editable)
                            </label>
                            <small class="text-muted"><i class="fas fa-sync-alt mr-1"></i> Live updates QR</small>
                        </div>
                        <div class="input-group qr-url-group">
                            <input type="text" class="form-control qr-url-input" id="mainWebsiteUrlInput" value="{{ $mainWebsiteUrl }}" oninput="handleMainUrlInput(this.value)" placeholder="https://educorerp.com">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-sm px-3" type="button" onclick="copyToClipboard('mainWebsiteUrlInput', this)" title="Copy URL">
                                    <i class="fas fa-copy mr-1"></i> Copy
                                </button>
                                <a href="{{ $mainWebsiteUrl }}" target="_blank" id="mainOpenLinkBtn" class="btn btn-outline-secondary btn-sm px-3" title="Open URL in new tab">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                        <small class="text-muted d-block mb-3" style="font-size: 0.76rem;">You can type or paste any URL above. The QR code regenerates instantly for that exact page.</small>

                        <!-- Campaign Tag Selector -->
                        <div class="mb-3">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Quick Campaign Tracking:</label>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="marketing-pill active" onclick="applyMainCampaign('', this)">Standard Link</span>
                                <span class="marketing-pill" onclick="applyMainCampaign('?source=qr_standee', this)">Table Standee</span>
                                <span class="marketing-pill" onclick="applyMainCampaign('?source=qr_flyer', this)">Paper Flyer</span>
                                <span class="marketing-pill" onclick="applyMainCampaign('?source=qr_visiting_card', this)">Visiting Card</span>
                                <span class="marketing-pill" onclick="applyMainCampaign('?source=qr_expo', this)">Education Expo</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-auto pt-3 border-top">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-qr-action btn-gold-action w-100" onclick="downloadQr('mainQrImg', 'educore-main-website-qr.png', 'mainWebsiteUrlInput')">
                                        <i class="fas fa-download mr-1"></i> Download PNG
                                    </button>
                                </div>
                                <div class="col-sm-6 mt-2 mt-sm-0">
                                    <button type="button" class="btn btn-qr-action btn-outline-dark w-100" onclick="printSingleStandee('main')">
                                        <i class="fas fa-print mr-1"></i> Print Standee
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Educore Demo Booking Page QR -->
            <div class="col-xl-6 col-lg-6 mb-4">
                <div class="qr-hub-card">
                    <div class="qr-hub-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <div class="brand-logo-icon-gold" style="width: 36px; height: 36px; font-size: 1rem; background: linear-gradient(135deg, #6366f1, #4f46e5); color: #fff;">
                                <i class="fas fa-calendar-alt text-white"></i>
                            </div>
                            <div>
                                <h5 class="m-0 font-heading fw-bold text-dark">Educore Demo Booking Page</h5>
                                <small class="text-muted">Direct 1-on-1 Personalized Walkthrough</small>
                            </div>
                        </div>
                        <span class="qr-badge-demo"><i class="fas fa-bolt mr-1"></i> Lead Booster</span>
                    </div>

                    <div class="qr-hub-card-body">
                        <!-- Clean, 100% Scannable QR Frame -->
                        <div class="qr-frame-wrapper">
                            <div class="qr-display-box" id="demoQrBox">
                                <img src="{{ $demoQrImage }}" alt="Educore Book Demo QR" id="demoQrImg" crossorigin="anonymous">
                            </div>
                            <div class="text-center mt-3">
                                <span class="badge badge-light text-dark border px-3 py-1 font-weight-bold" style="font-size: 0.82rem;">
                                    <i class="fas fa-camera mr-1 text-indigo"></i> Point any phone camera to scan
                                </span>
                            </div>
                        </div>

                        <!-- Target URL Section (Editable by SuperAdmin) -->
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <label class="form-label text-muted small fw-bold text-uppercase mb-0">
                                Destination URL (Editable)
                            </label>
                            <small class="text-muted"><i class="fas fa-sync-alt mr-1"></i> Live updates QR</small>
                        </div>
                        <div class="input-group qr-url-group">
                            <input type="text" class="form-control qr-url-input" id="demoPageUrlInput" value="{{ $demoPageUrl }}" oninput="handleDemoUrlInput(this.value)" placeholder="https://educorerp.com/book-demo">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-sm px-3" type="button" onclick="copyToClipboard('demoPageUrlInput', this)" title="Copy URL">
                                    <i class="fas fa-copy mr-1"></i> Copy
                                </button>
                                <a href="{{ $demoPageUrl }}" target="_blank" id="demoOpenLinkBtn" class="btn btn-outline-secondary btn-sm px-3" title="Open URL in new tab">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        </div>
                        <small class="text-muted d-block mb-3" style="font-size: 0.76rem;">You can type or paste any URL above. The QR code regenerates instantly for that exact page.</small>

                        <!-- Campaign Tag Selector -->
                        <div class="mb-3">
                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Quick Campaign Tracking:</label>
                            <div class="d-flex flex-wrap gap-1">
                                <span class="marketing-pill active" onclick="applyDemoCampaign('', this)">Standard Link</span>
                                <span class="marketing-pill" onclick="applyDemoCampaign('?source=qr_sales_pitch', this)">Sales Pitch</span>
                                <span class="marketing-pill" onclick="applyDemoCampaign('?source=qr_conference', this)">Conference</span>
                                <span class="marketing-pill" onclick="applyDemoCampaign('?source=qr_brochure', this)">School Brochure</span>
                                <span class="marketing-pill" onclick="applyDemoCampaign('?source=qr_principal_meet', this)">Principal Meet</span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-auto pt-3 border-top">
                            <div class="row g-2">
                                <div class="col-sm-6">
                                    <button type="button" class="btn btn-qr-action btn-indigo-action w-100" onclick="downloadQr('demoQrImg', 'educore-book-demo-qr.png', 'demoPageUrlInput')">
                                        <i class="fas fa-download mr-1"></i> Download PNG
                                    </button>
                                </div>
                                <div class="col-sm-6 mt-2 mt-sm-0">
                                    <button type="button" class="btn btn-qr-action btn-outline-dark w-100" onclick="printSingleStandee('demo')">
                                        <i class="fas fa-print mr-1"></i> Print Standee
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Marketing Best Practices Guide -->
        <div class="card border-0" style="border-radius: 16px; background: #ffffff; box-shadow: 0 4px 20px rgba(0,0,0,0.03);">
            <div class="card-body p-4">
                <h5 class="fw-bold font-heading text-dark mb-3"><i class="fas fa-lightbulb text-warning mr-2"></i> How to Use These QR Codes for Maximum School Conversions</h5>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-light p-3 text-primary d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                                <i class="fas fa-print"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">A4 / Table-Top Acrylic Standees</h6>
                                <p class="text-muted small mb-0">Use the "Print Standee" button to print high-resolution acrylic tent inserts for your reception desk, events, or school conferences.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-light p-3 text-success d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">Live Campaign Attribution</h6>
                                <p class="text-muted small mb-0">Click any campaign pill (Sales Pitch, Expo, Standee) or type your own tracking parameter before downloading.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex gap-3">
                            <div class="rounded-circle bg-light p-3 text-info d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                                <i class="fas fa-camera"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold text-dark mb-1">100% Unobstructed Scanning</h6>
                                <p class="text-muted small mb-0">No center overlays or damaged data codewords &ndash; fully compliant with ISO/IEC 18004 QR standards for instant decoding.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ════════════════════════════════════════════════════════════════════════
     STANDALONE PRINT CONTAINER (Visible ONLY during window.print())
     ════════════════════════════════════════════════════════════════════════ -->
<div id="printableStandeeArea" style="display: none;">
    <div style="max-width: 680px; margin: 0 auto; text-align: center; border: 4px solid #0c1024; border-radius: 24px; padding: 40px 30px; font-family: 'Lato', sans-serif;">
        
        <!-- Header Branding -->
        <div style="display: inline-block; background: #0c1024; color: #fff; padding: 10px 28px; border-radius: 999px; margin-bottom: 20px;">
            <span style="font-size: 1.4rem; font-weight: 800; letter-spacing: 1px; color: #e5ba73;">EDUCORERP</span>
            <span style="font-size: 0.9rem; color: #cbd5e1; margin-left: 8px;">Next-Gen Cloud School ERP</span>
        </div>

        <h1 id="printStandeeTitle" style="font-size: 2.2rem; font-weight: 800; color: #0c1024; margin: 10px 0;">Scan to Explore Our Platform</h1>
        <p id="printStandeeSubtitle" style="font-size: 1.1rem; color: #475569; max-width: 500px; margin: 0 auto 30px;">
            Transform your institution with India's most advanced cloud-based school management software.
        </p>

        <!-- QR Display in Print -->
        <div style="display: inline-block; padding: 16px; background: #fff; border: 3px solid #0c1024; border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); margin-bottom: 25px;">
            <img id="printStandeeQrImg" src="" alt="Standee QR" style="width: 280px; height: 280px; display: block; margin: 0 auto;">
        </div>

        <!-- Scan Prompt -->
        <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 24px; display: inline-block; margin-bottom: 30px;">
            <span style="font-size: 1.1rem; font-weight: 700; color: #0c1024;">
                <i class="fas fa-camera" style="margin-right: 8px; color: #c59b27;"></i> Point Your Phone Camera to Scan
            </span>
            <div id="printStandeeUrl" style="font-family: monospace; font-size: 0.95rem; color: #64748b; margin-top: 4px;"></div>
        </div>

        <!-- Key Features List -->
        <div style="display: flex; justify-content: space-around; border-top: 2px dashed #cbd5e1; padding-top: 20px; margin-top: 10px; text-align: left;">
            <div>
                <strong style="color: #0c1024; font-size: 0.9rem;">&#10003; 50+ Smart Modules</strong><br>
                <span style="font-size: 0.8rem; color: #64748b;">Admissions, Fees, Exams</span>
            </div>
            <div>
                <strong style="color: #0c1024; font-size: 0.9rem;">&#10003; Android & iOS Mobile Apps</strong><br>
                <span style="font-size: 0.8rem; color: #64748b;">Parents, Teachers & Students</span>
            </div>
            <div>
                <strong style="color: #0c1024; font-size: 0.9rem;">&#10003; 24/7 Priority Support</strong><br>
                <span style="font-size: 0.8rem; color: #64748b;">Cloud Backups & Security</span>
            </div>
        </div>

        <!-- Footer -->
        <div style="margin-top: 30px; font-size: 0.85rem; color: #94a3b8;">
            Powered by Educorerp SaaS Hub &bull; Visit educorerp.com for more details
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Optional High-Performance Client QR Generator -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    const baseMainUrl = @json($mainWebsiteUrl);
    const baseDemoUrl = @json($demoPageUrl);

    // Build API QR URL with HIGH Error Correction (ecc=H) & 12 margin
    function generateQrApiUrl(url) {
        return `https://api.qrserver.com/v1/create-qr-code/?size=350x350&ecc=H&margin=12&data=${encodeURIComponent(url)}`;
    }

    // Live update Main QR code when input text is edited
    function handleMainUrlInput(newUrl) {
        if (!newUrl || !newUrl.trim()) return;
        const trimmed = newUrl.trim();
        document.getElementById('mainQrImg').src = generateQrApiUrl(trimmed);
        document.getElementById('mainOpenLinkBtn').href = trimmed;
    }

    // Live update Demo QR code when input text is edited
    function handleDemoUrlInput(newUrl) {
        if (!newUrl || !newUrl.trim()) return;
        const trimmed = newUrl.trim();
        document.getElementById('demoQrImg').src = generateQrApiUrl(trimmed);
        document.getElementById('demoOpenLinkBtn').href = trimmed;
    }

    // Apply Campaign to Main Website QR
    function applyMainCampaign(campaignParam, el) {
        document.querySelectorAll('#mainWebsiteUrlInput ~ .marketing-pill, .marketing-pill').forEach(p => {
            if (p.parentElement === el.parentElement) p.classList.remove('active');
        });
        el.classList.add('active');

        const finalUrl = baseMainUrl + campaignParam;
        document.getElementById('mainWebsiteUrlInput').value = finalUrl;
        handleMainUrlInput(finalUrl);
    }

    // Apply Campaign to Demo Page QR
    function applyDemoCampaign(campaignParam, el) {
        document.querySelectorAll('#demoPageUrlInput ~ .marketing-pill, .marketing-pill').forEach(p => {
            if (p.parentElement === el.parentElement) p.classList.remove('active');
        });
        el.classList.add('active');

        const finalUrl = baseDemoUrl + campaignParam;
        document.getElementById('demoPageUrlInput').value = finalUrl;
        handleDemoUrlInput(finalUrl);
    }

    // Copy to Clipboard Helper
    function copyToClipboard(inputId, buttonEl) {
        const input = document.getElementById(inputId);
        input.select();
        input.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(input.value).then(() => {
            const originalHtml = buttonEl.innerHTML;
            buttonEl.innerHTML = '<i class="fas fa-check text-success mr-1"></i> Copied!';
            buttonEl.classList.add('btn-light');
            setTimeout(() => {
                buttonEl.innerHTML = originalHtml;
                buttonEl.classList.remove('btn-light');
            }, 2000);
        }).catch(() => {
            alert('URL Copied: ' + input.value);
        });
    }

    // Download QR Code as PNG directly from image
    function downloadQr(imgId, filename, inputId) {
        const img = document.getElementById(imgId);
        
        fetch(img.src)
            .then(res => res.blob())
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.style.display = 'none';
                a.href = url;
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            })
            .catch(() => {
                const a = document.createElement('a');
                a.href = img.src;
                a.target = '_blank';
                a.download = filename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            });
    }

    // Print Single Standee (Main or Demo)
    function printSingleStandee(type) {
        const printArea = document.getElementById('printableStandeeArea');
        const printTitle = document.getElementById('printStandeeTitle');
        const printSubtitle = document.getElementById('printStandeeSubtitle');
        const printQrImg = document.getElementById('printStandeeQrImg');
        const printUrl = document.getElementById('printStandeeUrl');

        if (type === 'main') {
            const currentUrl = document.getElementById('mainWebsiteUrlInput').value;
            printTitle.innerText = "Visit Educorerp Main Portal";
            printSubtitle.innerText = "Explore our cloud school management system, features, modules, and platform updates.";
            printQrImg.src = document.getElementById('mainQrImg').src;
            printUrl.innerText = currentUrl;
        } else {
            const currentUrl = document.getElementById('demoPageUrlInput').value;
            printTitle.innerText = "Book a Live 1-on-1 Walkthrough";
            printSubtitle.innerText = "Schedule a complimentary personalized demo tailored to your school's unique academic workflows.";
            printQrImg.src = document.getElementById('demoQrImg').src;
            printUrl.innerText = currentUrl;
        }

        printArea.style.display = 'block';
        window.print();
        printArea.style.display = 'none';
    }
</script>
@endsection
