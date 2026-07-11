@extends('layouts.app')

@section('page-title', 'Edit Issued Certificate')

@section('content')
@php
    $school = app()->bound('currentSchool') ? app('currentSchool') : auth()->user()->school;
    $logoUrl = ($school->logo && Storage::disk('public')->exists($school->logo)) ? Storage::disk('public')->url($school->logo) : '';
    $schoolName = $school->name;
    $directorName = $school->director_name ?? 'Principal';
    
    $udise = is_array($school->udise_data) ? $school->udise_data : json_decode($school->udise_data ?? '[]', true);
    $signatureUrl = (!empty($udise['signature']) && Storage::disk('public')->exists($udise['signature'])) ? Storage::disk('public')->url($udise['signature']) : '';
    
    $settings = $certificate->design_settings ?? optional($certificate->template)->design_settings ?? [];
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
    .draggable-cert-element.selected-element {
        outline: 2px solid var(--accent-blue) !important;
        outline-offset: 4px;
        background: rgba(59, 130, 246, 0.08);
    }

    /* Font Toolbar */
    .font-toolbar {
        background: #1e293b;
        border-radius: 8px;
        padding: 10px 14px;
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }
    .font-toolbar label {
        color: #94a3b8;
        font-size: 10px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }
    .font-toolbar select,
    .font-toolbar input[type="number"] {
        background: #334155;
        color: #e2e8f0;
        border: 1px solid #475569;
        border-radius: 4px;
        padding: 3px 6px;
        font-size: 11px;
        height: 26px;
    }
    .font-toolbar input[type="number"] { width: 52px; }
    .font-toolbar select { max-width: 130px; }
    .font-toolbar input[type="color"] {
        width: 26px;
        height: 26px;
        border: none;
        border-radius: 4px;
        padding: 0;
        cursor: pointer;
        background: transparent;
    }
    .font-btn {
        background: #334155;
        border: 1px solid #475569;
        color: #e2e8f0;
        border-radius: 4px;
        width: 26px;
        height: 26px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 12px;
        font-weight: 700;
        transition: all 0.15s;
        flex-shrink: 0;
    }
    .font-btn:hover, .font-btn.active {
        background: var(--accent-blue);
        border-color: var(--accent-blue);
        color: #fff;
    }
    .toolbar-sep {
        width: 1px;
        height: 20px;
        background: #475569;
        margin: 0 2px;
    }
    .element-tab {
        padding: 6px 12px;
        border-radius: 6px 6px 0 0;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        border: none;
        background: #e2e8f0;
        color: #475569;
        transition: all 0.15s;
    }
    .element-tab.active {
        background: #1e293b;
        color: #fff;
    }
    .tab-panel { display: none; }
    .tab-panel.active { display: block; }

    .section-badge {
        display: inline-block;
        background: linear-gradient(135deg, #1e3a8a, #3b82f6);
        color: #fff;
        font-size: 9px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 10px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-right: 6px;
    }
</style>

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-paint-brush" style="color:var(--accent-blue);margin-right:8px;"></i>Certificate Designer</h1>
        <p>Full Canva-style editor — change fonts, colors, sizes, drag elements, edit all text.</p>
    </div>
    <div class="page-hdr-right">
        <a href="{{ route('school.certificates.manage') }}" class="btn-outline-blue">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>

<div style="display:grid; grid-template-columns: 380px 1fr; gap:20px; align-items:start;">
    <!-- ══ LEFT: Edit Form ══ -->
    <div style="display:flex; flex-direction:column; gap:16px;">

        <!-- Basic Info Card -->
        <div class="card">
            <div class="card-hdr card-hdr-blue">
                <h3><i class="fas fa-info-circle" style="margin-right:6px;"></i>Certificate Details</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('school.certificates.issued-edit', $certificate->id) }}" id="issuedCertEditForm">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Student Recipient</label>
                        <select name="student_id" id="inputStudentSel" class="form-control" onchange="syncStudentName(this.value)" required>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}" {{ $certificate->student_id == $st->id ? 'selected' : '' }}>{{ $st->full_name }} ({{ $st->admission_id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Certificate Template</label>
                        <select name="certificate_template_id" id="inputTemplateSel" class="form-control" onchange="syncTemplate(this.value)" required>
                            @foreach($templates as $tpl)
                                <option value="{{ $tpl->id }}" {{ $certificate->certificate_template_id == $tpl->id ? 'selected' : '' }}>{{ $tpl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Certificate Title</label>
                        <input type="text" name="custom_title" id="inputTitleText" class="form-control" value="{{ old('custom_title', $certificate->custom_title ?? optional($certificate->template)->title_text) }}" oninput="drawLivePreview()">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Body Text</label>
                        <textarea name="custom_body" id="inputBodyText" class="form-control" style="height:90px;" oninput="drawLivePreview()">{{ old('custom_body', $certificate->custom_body ?? optional($certificate->template)->body_text) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Issue Date</label>
                        <input type="date" name="issue_date" id="inputIssueDate" class="form-control" value="{{ $certificate->issue_date }}" required oninput="syncDate(this.value)">
                    </div>

                    <!-- Hidden design settings fields (populated by JS) -->
                    <input type="hidden" name="hide_header" id="fHideHeader" value="{{ !empty($settings['hide_header']) ? '1' : '0' }}">
                    <input type="hidden" name="hide_title" id="fHideTitle" value="{{ !empty($settings['hide_title']) ? '1' : '0' }}">
                    <input type="hidden" name="hide_body" id="fHideBody" value="{{ !empty($settings['hide_body']) ? '1' : '0' }}">
                    <input type="hidden" name="hide_signatures" id="fHideSignatures" value="{{ !empty($settings['hide_signatures']) ? '1' : '0' }}">
                    <input type="hidden" name="show_logo" id="fShowLogo" value="{{ !empty($settings['show_logo']) ? '1' : '0' }}">
                    <input type="hidden" name="logo_y_offset" id="fLogoY" value="{{ $settings['logo_y_offset'] ?? '' }}">
                    <input type="hidden" name="logo_x_offset" id="fLogoX" value="{{ $settings['logo_x_offset'] ?? '' }}">
                    <input type="hidden" name="show_school_name" id="fShowSchoolName" value="{{ !empty($settings['show_school_name']) ? '1' : '0' }}">
                    <input type="hidden" name="school_name_y_offset" id="fSchoolNameY" value="{{ $settings['school_name_y_offset'] ?? '' }}">
                    <input type="hidden" name="show_signature" id="fShowSignature" value="{{ !empty($settings['show_signature']) ? '1' : '0' }}">
                    <input type="hidden" name="sig_y_offset" id="fSigY" value="{{ $settings['sig_y_offset'] ?? '' }}">
                    <input type="hidden" name="sig_x_offset" id="fSigX" value="{{ $settings['sig_x_offset'] ?? '' }}">
                    <input type="hidden" name="name_font_size" id="fNameFontSize" value="{{ $settings['name_font_size'] ?? '32px' }}">
                    <input type="hidden" name="name_color" id="fNameColor" value="{{ $settings['name_color'] ?? '#1d4ed8' }}">
                    <input type="hidden" name="name_font_family" id="fNameFontFamily" value="{{ $settings['name_font_family'] ?? 'Great Vibes' }}">
                    <input type="hidden" name="name_bold" id="fNameBold" value="{{ $settings['name_bold'] ?? '0' }}">
                    <input type="hidden" name="name_italic" id="fNameItalic" value="{{ $settings['name_italic'] ?? '0' }}">
                    <input type="hidden" name="name_underline" id="fNameUnderline" value="{{ $settings['name_underline'] ?? '0' }}">
                    <input type="hidden" name="name_y_offset" id="fNameY" value="{{ $settings['name_y_offset'] ?? '' }}">
                    <input type="hidden" name="title_font_size" id="fTitleFontSize" value="{{ $settings['title_font_size'] ?? '22px' }}">
                    <input type="hidden" name="title_color" id="fTitleColor" value="{{ $settings['title_color'] ?? '#1e3a8a' }}">
                    <input type="hidden" name="title_font_family" id="fTitleFontFamily" value="{{ $settings['title_font_family'] ?? 'Cinzel' }}">
                    <input type="hidden" name="title_bold" id="fTitleBold" value="{{ $settings['title_bold'] ?? '1' }}">
                    <input type="hidden" name="title_italic" id="fTitleItalic" value="{{ $settings['title_italic'] ?? '0' }}">
                    <input type="hidden" name="body_font_size" id="fBodyFontSize" value="{{ $settings['body_font_size'] ?? '13px' }}">
                    <input type="hidden" name="body_color" id="fBodyColor" value="{{ $settings['body_color'] ?? '#334155' }}">
                    <input type="hidden" name="body_font_family" id="fBodyFontFamily" value="{{ $settings['body_font_family'] ?? 'Georgia' }}">
                    <input type="hidden" name="body_bold" id="fBodyBold" value="{{ $settings['body_bold'] ?? '0' }}">
                    <input type="hidden" name="body_italic" id="fBodyItalic" value="{{ $settings['body_italic'] ?? '0' }}">
                    <input type="hidden" name="body_y_offset" id="fBodyY" value="{{ $settings['body_y_offset'] ?? '' }}">
                    <input type="hidden" name="school_color" id="fSchoolColor" value="{{ $settings['school_color'] ?? '#1e3a8a' }}">
                    <input type="hidden" name="school_font_size" id="fSchoolFontSize" value="{{ $settings['school_font_size'] ?? '16px' }}">
                    <input type="hidden" name="school_font_family" id="fSchoolFontFamily" value="{{ $settings['school_font_family'] ?? 'Cinzel' }}">
                    <input type="hidden" name="date_y_offset" id="fDateY" value="{{ $settings['date_y_offset'] ?? '' }}">
                    <input type="hidden" name="date_x_offset" id="fDateX" value="{{ $settings['date_x_offset'] ?? '' }}">
                    <input type="hidden" name="ref_y_offset" id="fRefY" value="{{ $settings['ref_y_offset'] ?? '' }}">
                    <input type="hidden" name="ref_x_offset" id="fRefX" value="{{ $settings['ref_x_offset'] ?? '' }}">

                    <div style="display:flex; gap:10px; margin-top:12px;">
                        <button type="submit" class="btn-blue" style="flex:1; justify-content:center;">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                        <a href="{{ route('school.certificates.manage') }}" class="btn-outline-blue" style="flex:1; text-align:center; padding:9px 0;">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Visibility Toggles -->
        <div class="card">
            <div class="card-hdr card-hdr-blue">
                <h3><i class="fas fa-eye" style="margin-right:6px;"></i>Visibility Controls</h3>
            </div>
            <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px;">
                    <input type="checkbox" id="ckHideHeader" onchange="toggleVisibility('header',!this.checked)"> Hide School Name Header
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px;">
                    <input type="checkbox" id="ckHideTitle" onchange="toggleVisibility('title',!this.checked)"> Hide Certificate Title
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px;">
                    <input type="checkbox" id="ckHideBody" onchange="toggleVisibility('body',!this.checked)"> Hide Body Text
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px;">
                    <input type="checkbox" id="ckHideSig" onchange="toggleVisibility('sig',!this.checked)"> Hide Signature Block
                </label>
                <label style="display:flex; align-items:center; gap:8px; cursor:pointer; font-size:13px;">
                    <input type="checkbox" id="ckShowLogo" checked onchange="toggleVisibility('logo',this.checked)"> Show Logo
                </label>
            </div>
        </div>
    </div>

    <!-- ══ RIGHT: Live Preview + Canva Toolbar ══ -->
    <div style="display:flex; flex-direction:column; gap:0;">

        <!-- Canva Font Toolbar -->
        <div class="card" style="border-radius: 8px 8px 0 0; margin-bottom:0; border-bottom:none;">
            <div style="padding:12px 16px; background:#0f172a; border-radius:8px 8px 0 0;">
                <div style="display:flex; gap:6px; flex-wrap:wrap; margin-bottom:10px;">
                    <button class="element-tab active" onclick="switchTab('title', this)" id="tab-title">🏷 Title</button>
                    <button class="element-tab" onclick="switchTab('school', this)" id="tab-school">🏫 School</button>
                    <button class="element-tab" onclick="switchTab('name', this)" id="tab-name">✍ Student Name</button>
                    <button class="element-tab" onclick="switchTab('body', this)" id="tab-body">📄 Body</button>
                </div>

                <!-- Title Tab -->
                <div id="panel-title" class="tab-panel active">
                    <div class="font-toolbar">
                        <label>Font</label>
                        <select id="titleFontFamily" onchange="applyFont()">
                            <option value="Cinzel">Cinzel (Serif)</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Inter">Inter (Modern)</option>
                            <option value="Playfair Display">Playfair Display</option>
                            <option value="Montserrat">Montserrat</option>
                            <option value="Oswald">Oswald</option>
                            <option value="Lato">Lato</option>
                            <option value="Roboto">Roboto</option>
                            <option value="Open Sans">Open Sans</option>
                        </select>
                        <div class="toolbar-sep"></div>
                        <label>Size</label>
                        <input type="number" id="titleFontSize" value="22" min="8" max="72" onchange="applyFont()">
                        <div class="toolbar-sep"></div>
                        <button class="font-btn" id="btn-title-bold" onclick="toggleStyle('title','bold')" title="Bold"><b>B</b></button>
                        <button class="font-btn" id="btn-title-italic" onclick="toggleStyle('title','italic')" title="Italic"><i>I</i></button>
                        <button class="font-btn" id="btn-title-underline" onclick="toggleStyle('title','underline')" title="Underline"><u>U</u></button>
                        <div class="toolbar-sep"></div>
                        <label>Color</label>
                        <input type="color" id="titleColor" value="#1e3a8a" onchange="applyFont()">
                    </div>
                </div>

                <!-- School Tab -->
                <div id="panel-school" class="tab-panel">
                    <div class="font-toolbar">
                        <label>Font</label>
                        <select id="schoolFontFamily" onchange="applyFont()">
                            <option value="Cinzel">Cinzel (Serif)</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Inter">Inter (Modern)</option>
                            <option value="Playfair Display">Playfair Display</option>
                            <option value="Montserrat">Montserrat</option>
                            <option value="Oswald">Oswald</option>
                            <option value="Lato">Lato</option>
                        </select>
                        <div class="toolbar-sep"></div>
                        <label>Size</label>
                        <input type="number" id="schoolFontSize" value="16" min="8" max="48" onchange="applyFont()">
                        <div class="toolbar-sep"></div>
                        <label>Color</label>
                        <input type="color" id="schoolColor" value="#1e3a8a" onchange="applyFont()">
                    </div>
                </div>

                <!-- Student Name Tab -->
                <div id="panel-name" class="tab-panel">
                    <div class="font-toolbar">
                        <label>Font</label>
                        <select id="nameFontFamily" onchange="applyFont()">
                            <option value="Great Vibes">Great Vibes (Cursive)</option>
                            <option value="Dancing Script">Dancing Script</option>
                            <option value="Pacifico">Pacifico</option>
                            <option value="Sacramento">Sacramento</option>
                            <option value="Satisfy">Satisfy</option>
                            <option value="Cinzel">Cinzel (Serif)</option>
                            <option value="Georgia">Georgia</option>
                            <option value="Montserrat">Montserrat</option>
                            <option value="Oswald">Oswald</option>
                            <option value="Inter">Inter (Modern)</option>
                        </select>
                        <div class="toolbar-sep"></div>
                        <label>Size</label>
                        <input type="number" id="nameFontSize" value="32" min="12" max="80" onchange="applyFont()">
                        <div class="toolbar-sep"></div>
                        <button class="font-btn" id="btn-name-bold" onclick="toggleStyle('name','bold')" title="Bold"><b>B</b></button>
                        <button class="font-btn" id="btn-name-italic" onclick="toggleStyle('name','italic')" title="Italic"><i>I</i></button>
                        <button class="font-btn" id="btn-name-underline" onclick="toggleStyle('name','underline')" title="Underline"><u>U</u></button>
                        <div class="toolbar-sep"></div>
                        <label>Color</label>
                        <input type="color" id="nameColor" value="#1d4ed8" onchange="applyFont()">
                    </div>
                </div>

                <!-- Body Tab -->
                <div id="panel-body" class="tab-panel">
                    <div class="font-toolbar">
                        <label>Font</label>
                        <select id="bodyFontFamily" onchange="applyFont()">
                            <option value="Georgia">Georgia (Classic)</option>
                            <option value="Times New Roman">Times New Roman</option>
                            <option value="Inter">Inter (Modern)</option>
                            <option value="Lato">Lato</option>
                            <option value="Roboto">Roboto</option>
                            <option value="Open Sans">Open Sans</option>
                            <option value="Montserrat">Montserrat</option>
                        </select>
                        <div class="toolbar-sep"></div>
                        <label>Size</label>
                        <input type="number" id="bodyFontSize" value="13" min="8" max="28" onchange="applyFont()">
                        <div class="toolbar-sep"></div>
                        <button class="font-btn" id="btn-body-bold" onclick="toggleStyle('body','bold')" title="Bold"><b>B</b></button>
                        <button class="font-btn" id="btn-body-italic" onclick="toggleStyle('body','italic')" title="Italic"><i>I</i></button>
                        <button class="font-btn" id="btn-body-underline" onclick="toggleStyle('body','underline')" title="Underline"><u>U</u></button>
                        <div class="toolbar-sep"></div>
                        <label>Color</label>
                        <input type="color" id="bodyColor" value="#334155" onchange="applyFont()">
                    </div>
                </div>

                <div style="color:#64748b; font-size:10px; margin-top:4px;">
                    <i class="fas fa-arrows-alt"></i> Click & drag elements in the preview to reposition them
                </div>
            </div>
        </div>

        <!-- Live Preview -->
        <div class="card" style="border-radius: 0 0 8px 8px;">
            <div class="card-body" style="display:flex; justify-content:center; align-items:center; background:#94a3b8; padding:30px;">
                <!-- Certificate Preview -->
                <div id="certPreviewContainer" style="width:100%; max-width:680px; min-height:470px; border:1px solid #cbd5e1; background:#fff; color:#0f172a; padding:45px 35px; box-shadow:0 20px 60px rgba(0,0,0,0.3); font-family:'Georgia', serif; text-align:center; position:relative; overflow:hidden; box-sizing:border-box;">
                    
                    <!-- SVG Corner decorations -->
                    <div id="previewCertSvgDecorations">
                        <div style="position: absolute; top: 0; left: 0; width: 140px; height: 140px; pointer-events: none; overflow: hidden;">
                            <svg viewBox="0 0 200 200" style="width: 100%; height: 100%;">
                                <path d="M 0,0 L 200,0 C 130,70 70,130 0,180 Z" fill="#1e3a8a" />
                                <path d="M 200,0 C 145,85 85,145 0,195 L 0,180 C 70,130 130,70 200,0 Z" fill="#d97706" />
                            </svg>
                        </div>
                        <div style="position: absolute; bottom: 0; right: 0; width: 140px; height: 140px; pointer-events: none; overflow: hidden; transform: rotate(180deg);">
                            <svg viewBox="0 0 200 200" style="width: 100%; height: 100%;">
                                <path d="M 0,0 L 200,0 C 130,70 70,130 0,180 Z" fill="#1e3a8a" />
                                <path d="M 200,0 C 145,85 85,145 0,195 L 0,180 C 70,130 130,70 200,0 Z" fill="#d97706" />
                            </svg>
                        </div>
                        <div style="position: absolute; bottom: 35px; left: 35px; text-align: center;">
                            <svg viewBox="0 0 100 100" style="width: 48px; height: 48px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.12));">
                                <path d="M 35,70 L 50,90 L 65,70" fill="#b45309" />
                                <path d="M 40,70 L 50,95 L 60,70" fill="#d97706" />
                                <circle cx="50" cy="50" r="30" fill="#f59e0b" stroke="#d97706" stroke-width="2" />
                                <polygon points="50,32 55,43 67,45 58,54 60,66 50,60 40,66 42,54 33,45 45,43" fill="#fff" />
                            </svg>
                        </div>
                    </div>

                    <!-- Inner border -->
                    <div class="border-inner-decoration" style="border:2px solid #e2e8f0; height:100%; padding:35px 25px; min-height:380px; display:flex; flex-direction:column; justify-content:space-between; position:relative; box-sizing:border-box; z-index: 2;">
                        <div>
                            <!-- Logo -->
                            <div id="previewCertLogoWrap" class="draggable-cert-element" style="margin-bottom: 14px; text-align: center; z-index: 10;">
                                @if(!empty($logoUrl))
                                    <img id="previewCertLogo" src="{{ $logoUrl }}"
                                        style="max-height: 65px; max-width: 140px; object-fit: contain; display:inline-block;"
                                        onerror="this.style.display='none'; document.getElementById('previewCertLogoSvg').style.display='inline-block';">
                                    <svg id="previewCertLogoSvg" viewBox="0 0 120 120" style="width: 60px; height: 60px; display: none;">
                                        <defs><linearGradient id="lgFallback" x1="0" y1="0" x2="1" y2="1"><stop offset="0%" stop-color="#1e3a8a"/><stop offset="100%" stop-color="#3b82f6"/></linearGradient></defs>
                                        <circle cx="60" cy="60" r="55" fill="url(#lgFallback)" stroke="#d97706" stroke-width="4"/>
                                        <path d="M60 20 L80 50 L95 48 L75 75 L80 95 L60 82 L40 95 L45 75 L25 48 L40 50 Z" fill="#f59e0b" stroke="#fff" stroke-width="1.5"/>
                                        <circle cx="60" cy="60" r="12" fill="#fff" opacity="0.9"/>
                                    </svg>
                                @else
                                    <!-- Prominent school emblem when no logo -->
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
                            <h2 id="previewCertSchool" class="draggable-cert-element" style="font-size:16px; font-family:'Cinzel','Times New Roman',serif; font-weight:800; text-transform:uppercase; letter-spacing:2px; margin:0 auto 12px; color:#1e3a8a; z-index:10;">
                                {{ $schoolName }}
                            </h2>
                            
                            <div id="previewCertSchoolDivider" style="width:80px; height:2px; background:#d97706; margin:0 auto 15px;"></div>

                            <!-- Title -->
                            <h1 id="previewCertTitle" class="draggable-cert-element" style="font-size:22px; font-weight:800; text-transform:uppercase; color:#1e3a8a; margin:0 auto 15px; letter-spacing:1px; z-index:10; font-family:'Cinzel','Times New Roman',serif;">
                                {{ $certificate->custom_title ?? optional($certificate->template)->title_text }}
                            </h1>
                            
                            <p id="previewCertPresentedText" style="font-size:11px; color:#64748b; text-transform:uppercase; letter-spacing:1.5px; margin:0 auto 10px;">This is proudly presented to</p>

                            <!-- Student Name -->
                            <div id="previewCertStudentName" class="draggable-cert-element" style="font-size:32px; font-family:'Great Vibes','Brush Script MT',cursive; color:#1d4ed8; margin:5px auto 15px; z-index:10;">
                                {{ optional($certificate->student)->full_name ?? 'Student Name' }}
                            </div>

                            <!-- Body -->
                            <p id="previewCertBody" class="draggable-cert-element" style="font-size:13px; line-height:1.8; color:#334155; margin:0 auto 20px; max-width:90%; text-align:justify; text-justify:inter-word; z-index:10; font-family:'Georgia',serif;">
                                {{ $certificate->custom_body ?? optional($certificate->template)->body_text ?? 'This is to certify that [Student_Name] is a bonafide student of this institution.' }}
                            </p>
                        </div>

                        <!-- Bottom Row: Date + Signature -->
                        <div id="previewCertSigContainer" style="display:flex; justify-content:space-between; align-items:flex-end; font-size:11px; margin-top:20px; font-family:'Inter',sans-serif;">
                            <div id="previewCertDateWrap" class="draggable-cert-element" style="text-align:left; line-height:1.6; z-index:10;">
                                <div>Date of Issue: <strong id="previewCertDate">{{ $certificate->issue_date }}</strong></div>
                                <div id="previewCertNoWrap">Ref ID: <strong id="previewCertNo">{{ $certificate->certificate_number }}</strong></div>
                            </div>
                            
                            <!-- Principal Signature — only shown when signatureUrl is available -->
                            <div id="previewCertSignatureWrap" class="draggable-cert-element" style="text-align:center; margin-right:20px; z-index:10; {{ empty($signatureUrl) ? 'display:none;' : '' }}">
                                @if(!empty($signatureUrl))
                                    <img src="{{ $signatureUrl }}" style="max-height:40px; max-width:120px; object-fit:contain; display:block; margin:0 auto 4px;"
                                        onerror="this.parentElement.style.display='none';">
                                    <span style="font-size:10px; text-transform:uppercase; color:#64748b; font-weight:600;">{{ $directorName }}</span>
                                    <div style="font-size:9px; color:#94a3b8; text-transform:uppercase;">Authorized Signatory</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Print Button -->
        <div style="margin-top:12px; display:flex; gap:10px; justify-content:flex-end;">
            <button onclick="printCertificate()" class="btn-blue">
                <i class="fas fa-print"></i> Print Certificate
            </button>
        </div>
    </div>
</div>

<script>
// Google Fonts
(function() {
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cinzel:wght@400;700;900&family=Dancing+Script:wght@400;700&family=Pacifico&family=Sacramento&family=Satisfy&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@400;600;700&family=Oswald:wght@400;600&family=Lato:wght@400;700&family=Roboto:wght@400;700&family=Open+Sans:wght@400;600&family=Inter:wght@400;600;700&display=swap';
    document.head.appendChild(link);
})();

const schoolLogoUrl = "{{ $logoUrl }}";
const schoolNameText = "{{ $schoolName }}";
const directorNameText = "{{ $directorName }}";
const schoolSignatureUrl = "{{ $signatureUrl }}";

const studentDetails = {
    @foreach($students as $st)
    "{{ $st->id }}": { name: "{{ $st->full_name }}", id: "{{ $st->admission_id }}" },
    @endforeach
};

const templateDetails = {
    @foreach($templates as $tpl)
    "{{ $tpl->id }}": {
        title: "{{ $tpl->title_text }}",
        body: "{{ str_replace(["\r", "\n"], ' ', $tpl->body_text) }}",
        bg: "{{ $tpl->background_image ? asset('uploads/templates/' . $tpl->background_image) : '' }}",
        settings: {!! json_encode($tpl->design_settings ?? []) !!}
    },
    @endforeach
};

let currentBg = "";
const styleState = { title: { bold: false, italic: false, underline: false }, name: { bold: false, italic: false, underline: false }, body: { bold: false, italic: false, underline: false } };

// ─── Tab Switcher ───────────────────────────────────────────────
function switchTab(tab, btn) {
    document.querySelectorAll('.element-tab').forEach(t => t.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('panel-' + tab).classList.add('active');
}

// ─── Font Toggle ────────────────────────────────────────────────
function toggleStyle(el, prop) {
    styleState[el][prop] = !styleState[el][prop];
    const btn = document.getElementById(`btn-${el}-${prop}`);
    if (btn) btn.classList.toggle('active', styleState[el][prop]);
    applyFont();
}

// ─── Apply all font settings to preview ─────────────────────────
function applyFont() {
    const titleEl = document.getElementById('previewCertTitle');
    const nameEl  = document.getElementById('previewCertStudentName');
    const bodyEl  = document.getElementById('previewCertBody');
    const schoolEl= document.getElementById('previewCertSchool');

    // Title
    const tf = document.getElementById('titleFontFamily').value;
    const ts = document.getElementById('titleFontSize').value;
    const tc = document.getElementById('titleColor').value;
    titleEl.style.fontFamily = `'${tf}', serif`;
    titleEl.style.fontSize = ts + 'px';
    titleEl.style.color = tc;
    titleEl.style.fontWeight = styleState.title.bold ? '800' : '700';
    titleEl.style.fontStyle = styleState.title.italic ? 'italic' : 'normal';
    titleEl.style.textDecoration = styleState.title.underline ? 'underline' : 'none';

    // School Name
    const sf = document.getElementById('schoolFontFamily').value;
    const ss = document.getElementById('schoolFontSize').value;
    const sc = document.getElementById('schoolColor').value;
    schoolEl.style.fontFamily = `'${sf}', serif`;
    schoolEl.style.fontSize = ss + 'px';
    schoolEl.style.color = sc;

    // Student Name
    const nf = document.getElementById('nameFontFamily').value;
    const ns = document.getElementById('nameFontSize').value;
    const nc = document.getElementById('nameColor').value;
    nameEl.style.fontFamily = `'${nf}', cursive`;
    nameEl.style.fontSize = ns + 'px';
    nameEl.style.color = nc;
    nameEl.style.fontWeight = styleState.name.bold ? '700' : 'normal';
    nameEl.style.fontStyle = styleState.name.italic ? 'italic' : 'normal';
    nameEl.style.textDecoration = styleState.name.underline ? 'underline' : 'none';

    // Body
    const bf = document.getElementById('bodyFontFamily').value;
    const bs = document.getElementById('bodyFontSize').value;
    const bc = document.getElementById('bodyColor').value;
    bodyEl.style.fontFamily = `'${bf}', serif`;
    bodyEl.style.fontSize = bs + 'px';
    bodyEl.style.color = bc;
    bodyEl.style.fontWeight = styleState.body.bold ? '700' : 'normal';
    bodyEl.style.fontStyle = styleState.body.italic ? 'italic' : 'normal';
    bodyEl.style.textDecoration = styleState.body.underline ? 'underline' : 'none';

    // Persist to hidden fields
    document.getElementById('fTitleFontFamily').value = tf;
    document.getElementById('fTitleFontSize').value = ts + 'px';
    document.getElementById('fTitleColor').value = tc;
    document.getElementById('fTitleBold').value = styleState.title.bold ? '1' : '0';
    document.getElementById('fTitleItalic').value = styleState.title.italic ? '1' : '0';
    document.getElementById('fSchoolFontFamily').value = sf;
    document.getElementById('fSchoolFontSize').value = ss + 'px';
    document.getElementById('fSchoolColor').value = sc;
    document.getElementById('fNameFontFamily').value = nf;
    document.getElementById('fNameFontSize').value = ns + 'px';
    document.getElementById('fNameColor').value = nc;
    document.getElementById('fNameBold').value = styleState.name.bold ? '1' : '0';
    document.getElementById('fNameItalic').value = styleState.name.italic ? '1' : '0';
    document.getElementById('fNameUnderline').value = styleState.name.underline ? '1' : '0';
    document.getElementById('fBodyFontFamily').value = bf;
    document.getElementById('fBodyFontSize').value = bs + 'px';
    document.getElementById('fBodyColor').value = bc;
    document.getElementById('fBodyBold').value = styleState.body.bold ? '1' : '0';
    document.getElementById('fBodyItalic').value = styleState.body.italic ? '1' : '0';
    document.getElementById('fBodyUnderline').value = styleState.body.underline ? '1' : '0';
}

// ─── Visibility toggles ─────────────────────────────────────────
function toggleVisibility(el, show) {
    const map = { header: 'previewCertSchool', title: 'previewCertTitle', body: 'previewCertBody', sig: 'previewCertSigContainer', logo: 'previewCertLogoWrap' };
    const target = document.getElementById(map[el]);
    if (target) target.style.display = show ? '' : 'none';
    // Update hidden fields
    const hmap = { header: 'fHideHeader', title: 'fHideTitle', body: 'fHideBody', sig: 'fHideSignatures' };
    if (hmap[el]) document.getElementById(hmap[el]).value = show ? '0' : '1';
    if (el === 'logo') document.getElementById('fShowLogo').value = show ? '1' : '0';
}

// ─── Student name sync ──────────────────────────────────────────
function syncStudentName(studentId) {
    const s = studentDetails[studentId];
    if (s) document.getElementById('previewCertStudentName').textContent = s.name;
    syncBody();
}

// ─── Body text token replace ────────────────────────────────────
function syncBody() {
    const studentId = document.getElementById('inputStudentSel').value;
    const s = studentDetails[studentId] || { name: 'Student Name', id: 'ADMS-001' };
    let body = document.getElementById('inputBodyText').value;
    body = body.replace(/\[Student_Name\]/g, `<strong>${s.name}</strong>`);
    body = body.replace(/\[Admission_ID\]/g, `<strong>${s.id}</strong>`);
    body = body.replace(/\[Parent_Name\]/g, 'Guardian');
    body = body.replace(/\[Grade_Class\]/g, 'Grade');
    body = body.replace(/\[Admission_Date\]/g, '2024-04-01');
    body = body.replace(/\[Session_Name\]/g, '2024-2025');
    document.getElementById('previewCertBody').innerHTML = body;
}

function syncDate(val) {
    const d = document.getElementById('previewCertDate');
    if (d) d.textContent = val;
}

// ─── Template sync ──────────────────────────────────────────────
function syncTemplate(templateId) {
    const t = templateDetails[templateId];
    if (!t) return;
    document.getElementById('inputTitleText').value = t.title;
    document.getElementById('inputBodyText').value = t.body;
    document.getElementById('previewCertTitle').textContent = t.title;
    currentBg = t.bg;
    applyBg();
    syncBody();
    const s = t.settings || {};
    // Load font settings from template
    if (s.title_font_family) document.getElementById('titleFontFamily').value = s.title_font_family;
    if (s.title_font_size) document.getElementById('titleFontSize').value = parseInt(s.title_font_size);
    if (s.title_color) document.getElementById('titleColor').value = s.title_color;
    if (s.name_font_family) document.getElementById('nameFontFamily').value = s.name_font_family;
    if (s.name_font_size) document.getElementById('nameFontSize').value = parseInt(s.name_font_size);
    if (s.name_color) document.getElementById('nameColor').value = s.name_color;
    if (s.body_font_family) document.getElementById('bodyFontFamily').value = s.body_font_family;
    if (s.body_font_size) document.getElementById('bodyFontSize').value = parseInt(s.body_font_size);
    if (s.body_color) document.getElementById('bodyColor').value = s.body_color;
    if (s.school_font_family) document.getElementById('schoolFontFamily').value = s.school_font_family;
    if (s.school_font_size) document.getElementById('schoolFontSize').value = parseInt(s.school_font_size);
    if (s.school_color) document.getElementById('schoolColor').value = s.school_color;
    applyFont();
}

function applyBg() {
    const container = document.getElementById('certPreviewContainer');
    const decors = document.getElementById('previewCertSvgDecorations');
    const border = container.querySelector('.border-inner-decoration');
    if (currentBg) {
        decors.style.display = 'none';
        border.style.border = 'none';
        container.style.backgroundImage = `url('${currentBg}')`;
        container.style.backgroundSize = 'cover';
        container.style.backgroundPosition = 'center';
        container.style.border = 'none';
    } else {
        decors.style.display = 'block';
        border.style.border = '2px solid #e2e8f0';
        container.style.backgroundImage = 'none';
        container.style.border = '1px solid #cbd5e1';
    }
}

// ─── Live input sync ────────────────────────────────────────────
document.getElementById('inputTitleText').addEventListener('input', function() {
    document.getElementById('previewCertTitle').textContent = this.value;
});
document.getElementById('inputBodyText').addEventListener('input', syncBody);

// ─── Print ──────────────────────────────────────────────────────
function printCertificate() {
    const printArea = document.getElementById('certPreviewContainer').outerHTML;
    const win = window.open('', '_blank', 'width=900,height=700');
    win.document.write(`<!DOCTYPE html><html><head><title>Certificate</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Cinzel:wght@400;700;900&family=Dancing+Script:wght@400;700&family=Pacifico&family=Sacramento&family=Satisfy&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Montserrat:wght@400;600;700&family=Inter:wght@400;600;700&display=swap">
    <style>
        body { margin:0; display:flex; justify-content:center; align-items:center; min-height:100vh; background:#f1f5f9; font-family:Georgia,serif; }
        #certPreviewContainer { max-width:700px; width:100%; -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
        #certPreviewContainer * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; color-adjust: exact !important; }
        .draggable-cert-element { cursor:default !important; outline:none !important; }
        @media print { body { background:#fff; } }
    </style></head><body>${printArea}</body></html>`);
    win.document.close();
    setTimeout(() => win.print(), 800);
}

// ─── Draggable ──────────────────────────────────────────────────
let activeDrag = null, dragStartX = 0, dragStartY = 0, elemStartX = 0, elemStartY = 0;

function initDraggable() {
    document.querySelectorAll('.draggable-cert-element').forEach(el => {
        el.addEventListener('mousedown', startDrag);
    });
}

function startDrag(e) {
    if (window.getComputedStyle(e.currentTarget).position !== 'absolute') return;
    activeDrag = e.currentTarget;
    dragStartX = e.clientX; dragStartY = e.clientY;
    const rect = activeDrag.getBoundingClientRect();
    const parentRect = activeDrag.offsetParent.getBoundingClientRect();
    elemStartX = rect.left - parentRect.left;
    elemStartY = rect.top - parentRect.top;
    document.addEventListener('mousemove', onDragMove);
    document.addEventListener('mouseup', onDragEnd);
}

function onDragMove(e) {
    if (!activeDrag) return;
    e.preventDefault();
    const dx = e.clientX - dragStartX;
    const dy = e.clientY - dragStartY;
    const newX = elemStartX + dx;
    const newY = elemStartY + dy;
    activeDrag.style.top = newY + 'px';
    activeDrag.style.left = newX + 'px';
    activeDrag.style.transform = 'none';
    activeDrag.style.margin = '0';
    const pw = activeDrag.offsetParent.clientWidth;
    const xPct = Math.round((newX / pw) * 100) + '%';
    const yPx = Math.round(newY) + 'px';
    const id = activeDrag.id;
    if (id === 'previewCertLogoWrap') { document.getElementById('fLogoY').value = yPx; document.getElementById('fLogoX').value = xPct; }
    else if (id === 'previewCertSchool') { document.getElementById('fSchoolNameY').value = yPx; }
    else if (id === 'previewCertStudentName') { document.getElementById('fNameY').value = yPx; }
    else if (id === 'previewCertBody') { document.getElementById('fBodyY').value = yPx; }
    else if (id === 'previewCertDateWrap') { document.getElementById('fDateY').value = yPx; document.getElementById('fDateX').value = xPct; }
    else if (id === 'previewCertSignatureWrap') { document.getElementById('fSigY').value = yPx; document.getElementById('fSigX').value = xPct; }
}

function onDragEnd() {
    activeDrag = null;
    document.removeEventListener('mousemove', onDragMove);
    document.removeEventListener('mouseup', onDragEnd);
}

// ─── Init ────────────────────────────────────────────────────────
window.addEventListener('DOMContentLoaded', () => {
    const selTpl = document.getElementById('inputTemplateSel').value;
    if (templateDetails[selTpl]) { currentBg = templateDetails[selTpl].bg; applyBg(); }

    // Load saved settings into toolbar
    const s = {!! json_encode($settings) !!};
    if (s.title_font_family) document.getElementById('titleFontFamily').value = s.title_font_family;
    if (s.title_font_size) document.getElementById('titleFontSize').value = parseInt(s.title_font_size);
    if (s.title_color) document.getElementById('titleColor').value = s.title_color;
    if (s.title_bold === '1' || s.title_bold === true) { styleState.title.bold = true; document.getElementById('btn-title-bold')?.classList.add('active'); }
    if (s.title_italic === '1') { styleState.title.italic = true; document.getElementById('btn-title-italic')?.classList.add('active'); }
    if (s.name_font_family) document.getElementById('nameFontFamily').value = s.name_font_family;
    if (s.name_font_size) document.getElementById('nameFontSize').value = parseInt(s.name_font_size);
    if (s.name_color) document.getElementById('nameColor').value = s.name_color;
    if (s.name_bold === '1') { styleState.name.bold = true; document.getElementById('btn-name-bold')?.classList.add('active'); }
    if (s.name_italic === '1') { styleState.name.italic = true; document.getElementById('btn-name-italic')?.classList.add('active'); }
    if (s.name_underline === '1') { styleState.name.underline = true; document.getElementById('btn-name-underline')?.classList.add('active'); }
    if (s.body_font_family) document.getElementById('bodyFontFamily').value = s.body_font_family;
    if (s.body_font_size) document.getElementById('bodyFontSize').value = parseInt(s.body_font_size);
    if (s.body_color) document.getElementById('bodyColor').value = s.body_color;
    if (s.body_bold === '1') { styleState.body.bold = true; document.getElementById('btn-body-bold')?.classList.add('active'); }
    if (s.body_italic === '1') { styleState.body.italic = true; document.getElementById('btn-body-italic')?.classList.add('active'); }
    if (s.school_font_family) document.getElementById('schoolFontFamily').value = s.school_font_family;
    if (s.school_font_size) document.getElementById('schoolFontSize').value = parseInt(s.school_font_size);
    if (s.school_color) document.getElementById('schoolColor').value = s.school_color;

    // Visibility toggles sync
    document.getElementById('ckHideHeader').checked = s.hide_header == '1';
    document.getElementById('ckHideTitle').checked = s.hide_title == '1';
    document.getElementById('ckHideBody').checked = s.hide_body == '1';
    document.getElementById('ckHideSig').checked = s.hide_signatures == '1';
    document.getElementById('ckShowLogo').checked = s.show_logo != '0';

    applyFont();
    syncBody();
    initDraggable();

    // Sync student
    const selSt = document.getElementById('inputStudentSel').value;
    if (studentDetails[selSt]) document.getElementById('previewCertStudentName').textContent = studentDetails[selSt].name;
});
</script>
@endsection
