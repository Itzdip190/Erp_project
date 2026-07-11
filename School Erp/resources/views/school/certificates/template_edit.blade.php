@extends('layouts.app')

@section('page-title', 'Edit Certificate Template')

@section('content')
@php
    $school = app()->bound('currentSchool') ? app('currentSchool') : auth()->user()->school;
    $logoUrl = ($school->logo && Storage::disk('public')->exists($school->logo)) ? Storage::disk('public')->url($school->logo) : '';
    $schoolName = $school->name;
    $directorName = $school->director_name ?? 'Principal';
    
    $udise = is_array($school->udise_data) ? $school->udise_data : json_decode($school->udise_data ?? '[]', true);
    $signatureUrl = (!empty($udise['signature']) && Storage::disk('public')->exists($udise['signature'])) ? Storage::disk('public')->url($udise['signature']) : '';
    
    $settings = $template->design_settings ?? [];
@endphp
<style>
    :root {
        --primary-blue: #1e3a8a;
        --accent-blue: #3b82f6;
        --light-blue: #eff6ff;
        --border-color: #cbd5e1;
    }
    .btn-blue {
        background-color: var(--accent-blue);
        color: #ffffff !important;
        font-weight: 600;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-blue:hover {
        background-color: var(--primary-blue);
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
    }
    .btn-outline-blue {
        background-color: transparent;
        color: var(--accent-blue) !important;
        border: 1.5px solid var(--accent-blue);
        font-weight: 600;
        padding: 9px 18px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-outline-blue:hover {
        background-color: var(--light-blue);
        color: var(--primary-blue) !important;
        border-color: var(--primary-blue);
    }
    .card-hdr-blue {
        background-color: var(--light-blue);
        border-bottom: 1px solid var(--border-color);
        padding: 15px 20px;
        color: var(--primary-blue);
        font-weight: 700;
    }

    /* Canva-like Drag and Drop Styles */
    .draggable-cert-element {
        cursor: move !important;
        user-select: none;
        position: relative;
        transition: outline 0.15s ease, background 0.15s ease;
    }
    .draggable-cert-element:hover {
        outline: 1.5px dashed var(--accent-blue) !important;
        outline-offset: 4px;
        background: rgba(59, 130, 246, 0.05);
    }
    .draggable-cert-element::after {
        content: "Drag";
        position: absolute;
        top: -18px;
        right: 0;
        background: var(--accent-blue);
        color: #fff;
        font-size: 8px;
        font-family: sans-serif;
        padding: 2px 4px;
        border-radius: 3px;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.15s ease;
    }
    .draggable-cert-element:hover::after {
        opacity: 0.8;
    }
</style>

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-edit" style="color:var(--accent-blue);margin-right:8px;"></i>Edit Certificate Template</h1>
        <p>Design your layout settings by dragging elements directly in the preview, or enter offsets manually below.</p>
    </div>
</div>

<div class="grid-3">
    <!-- Edit Form Settings (Left 1 Span) -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr card-hdr-blue">
            <h3>Layout Options</h3>
        </div>
        <div class="card-body" style="max-height: 750px; overflow-y: auto;">
            <form method="POST" action="{{ route('school.certificates.template-edit', $template->id) }}" id="templateEditForm" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Template Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Certificate Type</label>
                    <select name="type" class="form-control" required>
                        <option value="transfer" {{ $template->type === 'transfer' ? 'selected' : '' }}>School Leaving / Transfer Certificate</option>
                        <option value="character" {{ $template->type === 'character' ? 'selected' : '' }}>Character Certificate</option>
                        <option value="custom" {{ $template->type === 'custom' ? 'selected' : '' }}>Custom Merit / Sports Award</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Header Title Text</label>
                    <input type="text" name="title_text" id="inputTitleText" class="form-control" value="{{ old('title_text', $template->title_text) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Body Text Template</label>
                    <textarea name="body_text" id="inputBodyText" class="form-control" style="height:110px;" required>{{ old('body_text', $template->body_text) }}</textarea>
                </div>

                <div class="form-group" style="margin-bottom:12px;">
                    <label class="form-label">Upload Custom Background Image</label>
                    <input type="file" name="background_image" class="form-control">
                    <small style="color:var(--t3); display:block; margin-top:4px;">Upload background design border. Leave blank to keep current.</small>
                </div>

                <!-- Custom Layout Alignment Panel -->
                <div style="margin-bottom:20px; border:1px solid var(--border-color); border-radius:6px; padding:12px; background:#f8fafc;">
                    <h4 style="font-weight:700; color:var(--primary-blue); font-size:13px; margin:0 0 10px;"><i class="fas fa-sliders-h"></i> Layout & Text Alignment Settings</h4>
                    
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <!-- Hide options -->
                        <div style="display:grid; grid-template-columns:1fr; gap:6px; border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:8px;">
                            <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; cursor:pointer;">
                                <input type="checkbox" class="sync-check" name="hide_header" value="1" {{ !empty($settings['hide_header']) ? 'checked' : '' }}> Hide School Header
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; cursor:pointer;">
                                <input type="checkbox" class="sync-check" name="hide_title" value="1" {{ !empty($settings['hide_title']) ? 'checked' : '' }}> Hide Certificate Title
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; cursor:pointer;">
                                <input type="checkbox" class="sync-check" name="hide_body" value="1" {{ !empty($settings['hide_body']) ? 'checked' : '' }}> Hide Default Body Text
                            </label>
                            <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; cursor:pointer;">
                                <input type="checkbox" class="sync-check" name="hide_signatures" value="1" {{ !empty($settings['hide_signatures']) ? 'checked' : '' }}> Hide Signatures block
                            </label>
                        </div>
                        
                        <!-- Overlay elements -->
                        <div style="border-bottom:1px solid var(--border-color); padding-bottom:8px; margin-bottom:8px; display:flex; flex-direction:column; gap:6px;">
                            <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; cursor:pointer;">
                                <input type="checkbox" class="sync-check" name="show_logo" value="1" {{ !empty($settings['show_logo']) ? 'checked' : '' }}> Show School Logo Overlay
                            </label>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px;">
                                <div class="form-group" style="margin:0;">
                                    <label style="font-size:10px; margin-bottom:2px; display:block;">Logo Vertical</label>
                                    <input type="text" class="sync-input" name="logo_y_offset" value="{{ $settings['logo_y_offset'] ?? '' }}" placeholder="e.g. 35px" style="padding:4px 8px; font-size:11px; height:28px;">
                                </div>
                                <div class="form-group" style="margin:0;">
                                    <label style="font-size:10px; margin-bottom:2px; display:block;">Logo Horizontal</label>
                                    <input type="text" class="sync-input" name="logo_x_offset" value="{{ $settings['logo_x_offset'] ?? '' }}" placeholder="e.g. 50%" style="padding:4px 8px; font-size:11px; height:28px;">
                                </div>
                            </div>

                            <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; cursor:pointer; margin-top:6px;">
                                <input type="checkbox" class="sync-check" name="show_school_name" value="1" {{ !empty($settings['show_school_name']) ? 'checked' : '' }}> Show School Name Overlay
                            </label>
                            <div class="form-group" style="margin:0;">
                                <label style="font-size:10px; margin-bottom:2px; display:block;">School Name Vertical</label>
                                <input type="text" class="sync-input" name="school_name_y_offset" value="{{ $settings['school_name_y_offset'] ?? '' }}" placeholder="e.g. 90px" style="padding:4px 8px; font-size:11px; height:28px;">
                            </div>

                            <label style="display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; cursor:pointer; margin-top:6px;">
                                <input type="checkbox" class="sync-check" name="show_signature" value="1" {{ !empty($settings['show_signature']) ? 'checked' : '' }}> Show Principal Signature
                            </label>
                            <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px;">
                                <div class="form-group" style="margin:0;">
                                    <label style="font-size:10px; margin-bottom:2px; display:block;">Sig Vertical</label>
                                    <input type="text" class="sync-input" name="sig_y_offset" value="{{ $settings['sig_y_offset'] ?? '' }}" placeholder="e.g. 305px" style="padding:4px 8px; font-size:11px; height:28px;">
                                </div>
                                <div class="form-group" style="margin:0;">
                                    <label style="font-size:10px; margin-bottom:2px; display:block;">Sig Horizontal</label>
                                    <input type="text" class="sync-input" name="sig_x_offset" value="{{ $settings['sig_x_offset'] ?? '' }}" placeholder="e.g. 380px" style="padding:4px 8px; font-size:11px; height:28px;">
                                </div>
                            </div>
                        </div>

                        <!-- Name and Body styles -->
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                            <div class="form-group" style="margin:0;">
                                <label style="font-size:10px; display:block;">Name Font Size</label>
                                <input type="text" class="sync-input" name="name_font_size" value="{{ $settings['name_font_size'] ?? '' }}" placeholder="e.g. 28px" style="padding:4px 8px; font-size:11px; height:28px;">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label style="font-size:10px; display:block;">Name Color Hex</label>
                                <input type="color" class="sync-input" name="name_color" value="{{ $settings['name_color'] ?? '#1d4ed8' }}" style="height:28px; padding:2px; width:100%;">
                            </div>
                        </div>
                        <div class="form-group" style="margin:0;">
                            <label style="font-size:10px; display:block;">Name Vertical</label>
                            <input type="text" class="sync-input" name="name_y_offset" value="{{ $settings['name_y_offset'] ?? '' }}" placeholder="e.g. 185px" style="padding:4px 8px; font-size:11px; height:28px;">
                        </div>

                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                            <div class="form-group" style="margin:0;">
                                <label style="font-size:10px; display:block;">Body Font Size</label>
                                <input type="text" class="sync-input" name="body_font_size" value="{{ $settings['body_font_size'] ?? '' }}" placeholder="e.g. 13px" style="padding:4px 8px; font-size:11px; height:28px;">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label style="font-size:10px; display:block;">Body Vertical</label>
                                <input type="text" class="sync-input" name="body_y_offset" value="{{ $settings['body_y_offset'] ?? '' }}" placeholder="e.g. 220px" style="padding:4px 8px; font-size:11px; height:28px;">
                            </div>
                        </div>

                        <!-- Date and Ref coordinates -->
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px;">
                            <div class="form-group" style="margin:0;">
                                <label style="font-size:10px; display:block;">Date Vertical</label>
                                <input type="text" class="sync-input" name="date_y_offset" value="{{ $settings['date_y_offset'] ?? '' }}" placeholder="e.g. 305px" style="padding:4px 8px; font-size:11px; height:28px;">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label style="font-size:10px; display:block;">Date Horizontal</label>
                                <input type="text" class="sync-input" name="date_x_offset" value="{{ $settings['date_x_offset'] ?? '' }}" placeholder="e.g. 60px" style="padding:4px 8px; font-size:11px; height:28px;">
                            </div>
                        </div>
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:6px;">
                            <div class="form-group" style="margin:0;">
                                <label style="font-size:10px; display:block;">Ref ID Vertical</label>
                                <input type="text" class="sync-input" name="ref_y_offset" value="{{ $settings['ref_y_offset'] ?? '' }}" placeholder="e.g. 325px" style="padding:4px 8px; font-size:11px; height:28px;">
                            </div>
                            <div class="form-group" style="margin:0;">
                                <label style="font-size:10px; display:block;">Ref ID Horizontal</label>
                                <input type="text" class="sync-input" name="ref_x_offset" value="{{ $settings['ref_x_offset'] ?? '' }}" placeholder="e.g. 60px" style="padding:4px 8px; font-size:11px; height:28px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div style="display:flex; gap:10px;">
                    <button type="submit" class="btn-blue" style="flex:1; justify-content:center;">
                        <i class="fas fa-save"></i> Save Layout
                    </button>
                    <a href="{{ route('school.certificates.template-creator') }}" class="btn-outline-blue" style="flex:1; text-align:center; padding:9px 0;">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Live Preview Interactive Card (Right 2 Spans) -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr card-hdr-blue" style="display:flex; justify-content:space-between; align-items:center;">
            <h3>Live Canvas Layout Designer</h3>
            <span style="font-size:12px; color:var(--t2); font-weight:normal;"><i class="fas fa-arrows-alt"></i> Click & drag elements directly on the certificate to auto-save coordinates!</span>
        </div>
        <div class="card-body" style="display:flex; justify-content:center; align-items:center; background:#cbd5e1; padding:30px;">
            <!-- Live CSS Preview Card -->
            <div id="certPreviewContainer" style="width:100%; max-width:650px; min-height:450px; border:1px solid #cbd5e1; background:#fff; color:#0f172a; padding:45px 35px; box-shadow:var(--shadow-lg); font-family:'Georgia', serif; text-align:center; position:relative; overflow:hidden; box-sizing:border-box;">
                
                <!-- SVG Corner decorations (Visible when no custom background image) -->
                <div id="previewCertSvgDecorations">
                    <!-- Top-Left Wave -->
                    <div style="position: absolute; top: 0; left: 0; width: 140px; height: 140px; pointer-events: none; overflow: hidden;">
                        <svg viewBox="0 0 200 200" style="width: 100%; height: 100%;">
                            <path d="M 0,0 L 200,0 C 130,70 70,130 0,180 Z" fill="#1e3a8a" />
                            <path d="M 200,0 C 145,85 85,145 0,195 L 0,180 C 70,130 130,70 200,0 Z" fill="#d97706" />
                        </svg>
                    </div>
                    <!-- Bottom-Right Wave -->
                    <div style="position: absolute; bottom: 0; right: 0; width: 140px; height: 140px; pointer-events: none; overflow: hidden; transform: rotate(180deg);">
                        <svg viewBox="0 0 200 200" style="width: 100%; height: 100%;">
                            <path d="M 0,0 L 200,0 C 130,70 70,130 0,180 Z" fill="#1e3a8a" />
                            <path d="M 200,0 C 145,85 85,145 0,195 L 0,180 C 70,130 130,70 200,0 Z" fill="#d97706" />
                        </svg>
                    </div>
                    <!-- Gold Seal Emblem -->
                    <div style="position: absolute; bottom: 35px; left: 35px; text-align: center;">
                        <svg viewBox="0 0 100 100" style="width: 48px; height: 48px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.12));">
                            <path d="M 35,70 L 50,90 L 65,70" fill="#b45309" />
                            <path d="M 40,70 L 50,95 L 60,70" fill="#d97706" />
                            <circle cx="50" cy="50" r="30" fill="#f59e0b" stroke="#d97706" stroke-width="2" />
                            <polygon points="50,32 55,43 67,45 58,54 60,66 50,60 40,66 42,54 33,45 45,43" fill="#fff" />
                        </svg>
                    </div>
                </div>

                <!-- Inner border (Only visible when no background image) -->
                <div class="border-inner-decoration" style="border:2px solid #e2e8f0; height:100%; padding:35px 25px; min-height:356px; display:flex; flex-direction:column; justify-content:space-between; position:relative; box-sizing:border-box; z-index: 2;">
                    <div>
                        <!-- Dynamic Logo Overlay -->
                        <div id="previewCertLogoWrap" class="draggable-cert-element" style="margin-bottom: 14px; text-align: center; z-index: 10;">
                            @if(!empty($logoUrl))
                                <img id="previewCertLogo" src="{{ $logoUrl }}"
                                    style="max-height: 65px; max-width: 140px; object-fit: contain; display:inline-block;"
                                    onerror="this.style.display='none'; document.getElementById('previewCertLogoSvg').style.display='inline-block';">
                                <svg id="previewCertLogoSvg" viewBox="0 0 120 120" style="width: 60px; height: 60px; display: none;">
                                    <defs><linearGradient id="lgFb" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#1e3a8a"/><stop offset="100%" stop-color="#3b82f6"/></linearGradient></defs>
                                    <circle cx="60" cy="60" r="55" fill="url(#lgFb)" stroke="#d97706" stroke-width="4"/>
                                    <path d="M60 20 L80 50 L95 48 L75 75 L80 95 L60 82 L40 95 L45 75 L25 48 L40 50 Z" fill="#f59e0b" stroke="#fff" stroke-width="1.5"/>
                                    <circle cx="60" cy="60" r="12" fill="#fff" opacity="0.9"/>
                                </svg>
                            @else
                                <!-- Premium School Emblem SVG -->
                                <svg id="previewCertLogoPlaceholder" viewBox="0 0 120 120" style="width: 65px; height: 65px; display: inline-block; filter: drop-shadow(0 2px 6px rgba(30,58,138,0.25));">
                                    <defs><linearGradient id="lg1" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#1e3a8a"/><stop offset="100%" stop-color="#3b82f6"/></linearGradient></defs>
                                    <circle cx="60" cy="60" r="55" fill="url(#lg1)" stroke="#d97706" stroke-width="4"/>
                                    <path d="M60 20 L80 50 L95 48 L75 75 L80 95 L60 82 L40 95 L45 75 L25 48 L40 50 Z" fill="#f59e0b" stroke="#fff" stroke-width="1.5"/>
                                    <circle cx="60" cy="60" r="13" fill="#fff" opacity="0.95"/>
                                    <text x="60" y="65" text-anchor="middle" font-size="10" fill="#1e3a8a" font-family="sans-serif" font-weight="bold">★</text>
                                </svg>
                            @endif
                        </div>

                        <!-- School Name -->
                        <h2 style="font-size:16px; font-family:'Cinzel', 'Times New Roman', serif; font-weight: 800; text-transform:uppercase; letter-spacing:2px; margin:0 auto 12px; color:#1e3a8a; z-index: 10;" id="previewCertSchool" class="draggable-cert-element">
                            {{ $schoolName }}
                        </h2>
                        
                        <div id="previewCertSchoolDivider" style="width: 80px; height: 2px; background: #d97706; margin: 0 auto 15px;"></div>

                        <!-- Title -->
                        <h1 style="font-size:22px; font-weight:800; text-transform:uppercase; color:#1e3a8a; margin:0 auto 15px; letter-spacing: 1px; z-index: 10;" id="previewCertTitle" class="draggable-cert-element">
                            {{ $template->title_text }}
                        </h1>
                        
                        <p id="previewCertPresentedText" style="font-size: 11px; color: #64748b; text-transform: uppercase; letter-spacing: 1.5px; margin: 0 auto 10px;">This is proudly presented to</p>

                        <!-- Student Name (Elegant Cursive font) -->
                        <div id="previewCertStudentName" class="draggable-cert-element" style="font-size:32px; font-family:'Great Vibes', 'Brush Script MT', cursive; color:#1d4ed8; margin:5px auto 15px; z-index: 10;">Priya Patel</div>

                        <!-- Body text -->
                        <p style="font-size:13px; line-height:1.8; color:#334155; margin:0 auto 20px; max-width: 90%; text-align:justify; text-justify:inter-word; z-index: 10;" id="previewCertBody" class="draggable-cert-element">
                            This is to certify that Priya Patel, bearing Admission ID YIS/2026/00002, has successfully cleared academic grades at this institution.
                        </p>
                    </div>

                    <!-- Bottom Signatures Container -->
                    <div id="previewCertSigContainer" style="display:flex; justify-content:space-between; align-items: flex-end; font-size:11px; margin-top:20px; font-family:'Inter', sans-serif;">
                        <div style="text-align:left; line-height: 1.6; z-index: 10;" id="previewCertDateWrap" class="draggable-cert-element">
                            <div>Date of Issue: <strong id="previewCertDate">{{ date('Y-m-d') }}</strong></div>
                            <div id="previewCertNoWrap">Ref ID: <strong id="previewCertNo">CERT-89718</strong></div>
                        </div>
                        
                        <!-- Principal Signature — only shown when uploaded -->
                        <div id="previewCertSignatureWrap" class="draggable-cert-element" style="text-align: center; margin-right: 20px; z-index: 10; {{ empty($signatureUrl) ? 'display:none;' : '' }}">
                            @if(!empty($signatureUrl))
                                <img src="{{ $signatureUrl }}" style="max-height: 40px; max-width: 120px; object-fit: contain; display: block; margin: 0 auto 4px;"
                                    onerror="this.parentElement.style.display='none';">
                                <span style="font-size: 10px; text-transform: uppercase; color: #64748b; font-weight: 600;">{{ $directorName }}</span>
                                <div style="font-size: 9px; color: #94a3b8; text-transform: uppercase;">Authorized Signatory</div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const schoolLogoUrl = "{{ $logoUrl }}";
const schoolNameText = "{{ $schoolName }}";
const directorNameText = "{{ $directorName }}";
const schoolSignatureUrl = "{{ $signatureUrl }}";

// Template initial configuration
const templateBg = "{{ $template->background_image ? asset('uploads/templates/' . $template->background_image) : '' }}";

function drawLivePreview() {
    const container = document.getElementById('certPreviewContainer');
    const borderEl = container.querySelector('.border-inner-decoration');
    const decorsEl = document.getElementById('previewCertSvgDecorations');
    
    const headerEl = document.getElementById('previewCertSchool');
    const titleEl = document.getElementById('previewCertTitle');
    const bodyEl = document.getElementById('previewCertBody');
    const studentNameEl = document.getElementById('previewCertStudentName');
    const sigEl = document.getElementById('previewCertSigContainer');
    const dateWrap = document.getElementById('previewCertDateWrap');
    const noWrap = document.getElementById('previewCertNoWrap');
    
    const logoWrap = document.getElementById('previewCertLogoWrap');
    const signatureWrap = document.getElementById('previewCertSignatureWrap');

    // Read form values
    const hideHeader = document.querySelector('input[name="hide_header"]').checked;
    const hideTitle = document.querySelector('input[name="hide_title"]').checked;
    const hideBody = document.querySelector('input[name="hide_body"]').checked;
    const hideSignatures = document.querySelector('input[name="hide_signatures"]').checked;
    
    const showLogo = document.querySelector('input[name="show_logo"]').checked;
    const logoY = document.querySelector('input[name="logo_y_offset"]').value;
    const logoX = document.querySelector('input[name="logo_x_offset"]').value;
    
    const showSchoolName = document.querySelector('input[name="show_school_name"]').checked;
    const schoolNameY = document.querySelector('input[name="school_name_y_offset"]').value;
    
    const showSignature = document.querySelector('input[name="show_signature"]').checked;
    const sigY = document.querySelector('input[name="sig_y_offset"]').value;
    const sigX = document.querySelector('input[name="sig_x_offset"]').value;
    
    const nameSize = document.querySelector('input[name="name_font_size"]').value;
    const nameColor = document.querySelector('input[name="name_color"]').value;
    const nameY = document.querySelector('input[name="name_y_offset"]').value;
    
    const bodySize = document.querySelector('input[name="body_font_size"]').value;
    const bodyY = document.querySelector('input[name="body_y_offset"]').value;
    
    const dateY = document.querySelector('input[name="date_y_offset"]').value;
    const dateX = document.querySelector('input[name="date_x_offset"]').value;
    const refY = document.querySelector('input[name="ref_y_offset"]').value;
    const refX = document.querySelector('input[name="ref_x_offset"]').value;

    // Apply title and body content
    titleEl.textContent = document.getElementById('inputTitleText').value;
    bodyEl.textContent = document.getElementById('inputBodyText').value.replace('[Student_Name]', 'Priya Patel').replace('[Admission_ID]', 'YIS/2026/00002');

    if (templateBg) {
        decorsEl.style.display = 'none';
        borderEl.style.border = 'none';
        container.style.backgroundImage = `url('${templateBg}')`;
        container.style.backgroundSize = 'cover';
        container.style.backgroundPosition = 'center';
        container.style.border = 'none';

        headerEl.style.display = hideHeader ? 'none' : 'block';
        titleEl.style.display = hideTitle ? 'none' : 'block';
        bodyEl.style.display = hideBody ? 'none' : 'block';
        sigEl.style.display = hideSignatures ? 'none' : 'flex';

        // Logo
        logoWrap.style.display = showLogo ? 'block' : 'none';
        if (showLogo && logoY && logoX) {
            logoWrap.style.position = 'absolute';
            logoWrap.style.top = logoY;
            logoWrap.style.left = logoX;
            logoWrap.style.transform = 'translateX(-50%)';
            logoWrap.style.margin = '0';
        } else {
            logoWrap.style.position = 'static';
            logoWrap.style.transform = 'none';
            logoWrap.style.marginBottom = '12px';
        }

        // School Name Overlay
        if (showSchoolName) {
            headerEl.style.display = 'block';
            if (schoolNameY) {
                headerEl.style.position = 'absolute';
                headerEl.style.top = schoolNameY;
                headerEl.style.left = '50%';
                headerEl.style.transform = 'translateX(-50%)';
                headerEl.style.margin = '0';
                headerEl.style.width = '100%';
            } else {
                headerEl.style.position = 'static';
                headerEl.style.transform = 'none';
            }
        } else if (hideHeader) {
            headerEl.style.display = 'none';
        }

        // Signature Overlay
        signatureWrap.style.display = showSignature ? 'block' : 'none';
        if (showSignature && sigY && sigX) {
            signatureWrap.style.position = 'absolute';
            signatureWrap.style.top = sigY;
            signatureWrap.style.left = sigX;
            signatureWrap.style.margin = '0';
        } else {
            signatureWrap.style.position = 'static';
        }

        // Styles
        if (nameSize) studentNameEl.style.fontSize = nameSize;
        if (nameColor) studentNameEl.style.color = nameColor;
        if (bodySize) bodyEl.style.fontSize = bodySize;

        // Offsets
        if (nameY) {
            studentNameEl.style.position = 'absolute';
            studentNameEl.style.top = nameY;
            studentNameEl.style.left = '50%';
            studentNameEl.style.transform = 'translateX(-50%)';
            studentNameEl.style.width = '100%';
            studentNameEl.style.margin = '0';
        } else {
            studentNameEl.style.position = 'static';
            studentNameEl.style.transform = 'none';
            studentNameEl.style.width = 'auto';
            studentNameEl.style.margin = '5px 0 15px';
        }

        if (bodyY) {
            bodyEl.style.position = 'absolute';
            bodyEl.style.top = bodyY;
            bodyEl.style.left = '50%';
            bodyEl.style.transform = 'translateX(-50%)';
            bodyEl.style.width = '80%';
        } else {
            bodyEl.style.position = 'static';
            bodyEl.style.transform = 'none';
            bodyEl.style.width = 'auto';
        }

        if (dateY && dateX) {
            dateWrap.style.position = 'absolute';
            dateWrap.style.top = dateY;
            dateWrap.style.left = dateX;
            dateWrap.style.margin = '0';
        } else {
            dateWrap.style.position = 'static';
        }

        if (refY && refX) {
            noWrap.style.position = 'absolute';
            noWrap.style.top = refY;
            noWrap.style.left = refX;
            noWrap.style.margin = '0';
        } else {
            noWrap.style.position = 'static';
        }

    } else {
        // Reset to Premium vector default template
        decorsEl.style.display = 'block';
        borderEl.style.border = '2px solid #e2e8f0';
        container.style.backgroundImage = 'none';
        container.style.border = '1px solid #cbd5e1';

        headerEl.style.display = 'block';
        headerEl.style.position = 'static';
        headerEl.style.transform = 'none';
        headerEl.style.margin = '0 0 12px';

        titleEl.style.display = 'block';
        titleEl.style.position = 'static';
        titleEl.style.transform = 'none';
        
        bodyEl.style.display = 'block';
        bodyEl.style.position = 'static';
        bodyEl.style.transform = 'none';
        bodyEl.style.width = 'auto';
        bodyEl.style.fontSize = '13px';

        studentNameEl.style.display = 'block';
        studentNameEl.style.position = 'static';
        studentNameEl.style.transform = 'none';
        studentNameEl.style.width = 'auto';
        studentNameEl.style.margin = '5px 0 15px';
        studentNameEl.style.fontSize = '32px';
        studentNameEl.style.color = nameColor || '#1d4ed8';

        sigEl.style.display = 'flex';
        
        logoWrap.style.display = 'block';
        logoWrap.style.position = 'static';
        logoWrap.style.transform = 'none';
        logoWrap.style.marginBottom = '12px';

        signatureWrap.style.display = 'block';
        signatureWrap.style.position = 'static';
        signatureWrap.style.transform = 'none';

        dateWrap.style.position = 'static';
        noWrap.style.position = 'static';
    }
}

// Bind live changes to redraw preview instantly
document.querySelectorAll('.sync-check, .sync-input').forEach(input => {
    input.addEventListener('change', drawLivePreview);
    input.addEventListener('input', drawLivePreview);
});
document.getElementById('inputTitleText').addEventListener('input', drawLivePreview);
document.getElementById('inputBodyText').addEventListener('input', drawLivePreview);

// Canva-style Drag and Drop implementation
let activeDragElement = null;
let dragStartX = 0;
let dragStartY = 0;
let elementStartX = 0;
let elementStartY = 0;

function initDraggable() {
    const draggables = document.querySelectorAll('.draggable-cert-element');
    draggables.forEach(el => {
        el.addEventListener('mousedown', startDrag);
        el.addEventListener('touchstart', startDrag, { passive: true });
    });
}

function startDrag(e) {
    // Only drag absolute positioned elements
    const isAbsolute = window.getComputedStyle(e.currentTarget).position === 'absolute';
    if (!isAbsolute) return;

    activeDragElement = e.currentTarget;
    const clientX = e.type === 'touchstart' ? e.touches[0].clientX : e.clientX;
    const clientY = e.type === 'touchstart' ? e.touches[0].clientY : e.clientY;
    
    dragStartX = clientX;
    dragStartY = clientY;
    
    const rect = activeDragElement.getBoundingClientRect();
    const parentRect = activeDragElement.offsetParent.getBoundingClientRect();
    
    elementStartX = rect.left - parentRect.left;
    elementStartY = rect.top - parentRect.top;
    
    document.addEventListener('mousemove', dragMove);
    document.addEventListener('mouseup', dragEnd);
    document.addEventListener('touchmove', dragMove, { passive: false });
    document.addEventListener('touchend', dragEnd);
}

function dragMove(e) {
    if (!activeDragElement) return;
    e.preventDefault();
    
    const clientX = e.type === 'touchmove' ? e.touches[0].clientX : e.clientX;
    const clientY = e.type === 'touchmove' ? e.touches[0].clientY : e.clientY;
    
    const dx = clientX - dragStartX;
    const dy = clientY - dragStartY;
    
    let newX = elementStartX + dx;
    let newY = elementStartY + dy;
    
    activeDragElement.style.top = newY + 'px';
    activeDragElement.style.left = newX + 'px';
    activeDragElement.style.transform = 'none';
    activeDragElement.style.margin = '0';
    
    // Auto-update offset inputs in the form
    const parent = activeDragElement.offsetParent;
    const parentWidth = parent.clientWidth;
    const parentHeight = parent.clientHeight;
    
    const xPct = Math.round((newX / parentWidth) * 100) + '%';
    const yPx = Math.round(newY) + 'px';
    const elementId = activeDragElement.id;

    if (elementId === 'previewCertLogoWrap') {
        document.querySelector('input[name="logo_y_offset"]').value = yPx;
        document.querySelector('input[name="logo_x_offset"]').value = xPct;
    } else if (elementId === 'previewCertSchool') {
        document.querySelector('input[name="school_name_y_offset"]').value = yPx;
    } else if (elementId === 'previewCertStudentName') {
        document.querySelector('input[name="name_y_offset"]').value = yPx;
    } else if (elementId === 'previewCertBody') {
        document.querySelector('input[name="body_y_offset"]').value = yPx;
    } else if (elementId === 'previewCertDateWrap') {
        document.querySelector('input[name="date_y_offset"]').value = yPx;
        document.querySelector('input[name="date_x_offset"]').value = xPct;
    } else if (elementId === 'previewCertNoWrap') {
        document.querySelector('input[name="ref_y_offset"]').value = yPx;
        document.querySelector('input[name="ref_x_offset"]').value = xPct;
    } else if (elementId === 'previewCertSignatureWrap') {
        document.querySelector('input[name="sig_y_offset"]').value = yPx;
        document.querySelector('input[name="sig_x_offset"]').value = xPct;
    }
}

function dragEnd(e) {
    activeDragElement = null;
    document.removeEventListener('mousemove', dragMove);
    document.removeEventListener('mouseup', dragEnd);
    document.removeEventListener('touchmove', dragMove);
    document.removeEventListener('touchend', dragEnd);
}

// Initial draw
window.addEventListener('DOMContentLoaded', () => {
    drawLivePreview();
    initDraggable();
});
</script>
@endsection
