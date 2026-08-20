@extends('layouts.app')

@section('page-title', 'Edit Card Template')

@section('content')
<style>
    /* ─── CARD TEMPLATE DESIGN SYSTEM & THEME TOKENS ─── */
    :root {
        --ctc-bg-card: #ffffff;
        --ctc-bg-subtle: #f8fafc;
        --ctc-bg-hover: #f1f5f9;
        --ctc-border: #e2e8f0;
        --ctc-border-focus: #3b82f6;
        --ctc-text-main: #0f172a;
        --ctc-text-muted: #64748b;
        --ctc-text-subtle: #94a3b8;
        --ctc-primary: #2563eb;
        --ctc-primary-hover: #1d4ed8;
        --ctc-primary-bg: #eff6ff;
        --ctc-primary-border: #dbeafe;
        --ctc-input-bg: #ffffff;
        --ctc-input-text: #0f172a;
        --ctc-input-border: #cbd5e1;
        --ctc-shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
        --ctc-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
        --ctc-shadow-lg: 0 20px 30px -10px rgba(0, 0, 0, 0.15);
        --ctc-shadow-xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        --ctc-code-bg: #f1f5f9;
        --ctc-code-text: #1e293b;
        --ctc-code-border: #cbd5e1;
    }

    body.dark-mode {
        --ctc-bg-card: #131c2e;
        --ctc-bg-subtle: #0b1120;
        --ctc-bg-hover: rgba(255, 255, 255, 0.05);
        --ctc-border: rgba(255, 255, 255, 0.1);
        --ctc-border-focus: #60a5fa;
        --ctc-text-main: #f8fafc;
        --ctc-text-muted: #94a3b8;
        --ctc-text-subtle: #64748b;
        --ctc-primary: #3b82f6;
        --ctc-primary-hover: #2563eb;
        --ctc-primary-bg: rgba(59, 130, 246, 0.15);
        --ctc-primary-border: rgba(59, 130, 246, 0.3);
        --ctc-input-bg: #0b1120;
        --ctc-input-text: #f8fafc;
        --ctc-input-border: rgba(255, 255, 255, 0.15);
        --ctc-shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.2);
        --ctc-shadow: 0 4px 20px rgba(0, 0, 0, 0.35);
        --ctc-shadow-lg: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        --ctc-shadow-xl: 0 30px 60px -15px rgba(0, 0, 0, 0.8);
        --ctc-code-bg: rgba(59, 130, 246, 0.15);
        --ctc-code-text: #93c5fd;
        --ctc-code-border: rgba(59, 130, 246, 0.3);
    }

    /* ─── PAGE HEADER HERO ─── */
    .ctc-hero {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
        background: var(--ctc-bg-card);
        padding: 22px 28px;
        border-radius: 18px;
        border: 1px solid var(--ctc-border);
        box-shadow: var(--ctc-shadow);
        position: relative;
        overflow: hidden;
        flex-wrap: wrap;
    }

    .ctc-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 5px;
        height: 100%;
        background: linear-gradient(180deg, #2563eb 0%, #3b82f6 50%, #8b5cf6 100%);
    }

    .ctc-hero-left {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .ctc-hero-icon {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, rgba(37, 99, 235, 0.15), rgba(139, 92, 246, 0.15));
        color: var(--ctc-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        flex-shrink: 0;
        border: 1px solid var(--ctc-primary-border);
        box-shadow: 0 6px 16px rgba(37, 99, 235, 0.12);
    }

    .ctc-hero-title h1 {
        font-size: 22px;
        font-weight: 800;
        color: var(--ctc-text-main) !important;
        margin: 0 0 4px 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .ctc-hero-title p {
        font-size: 13.5px;
        color: var(--ctc-text-muted) !important;
        margin: 0;
        font-weight: 500;
    }

    /* ─── 3-PANEL EDITOR LAYOUT ─── */
    .ctc-editor-layout {
        display: grid;
        grid-template-columns: 380px 1fr 280px;
        min-height: 720px;
        background: var(--ctc-bg-subtle);
        border: 1px solid var(--ctc-border);
        border-radius: 20px;
        box-shadow: var(--ctc-shadow-lg);
        overflow: hidden;
    }

    .editor-left-panel {
        background: var(--ctc-bg-card);
        border-right: 1px solid var(--ctc-border);
        padding: 22px;
        overflow-y: auto;
        max-height: 800px;
    }

    .editor-center-panel {
        padding: 24px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        overflow: auto;
        position: relative;
        background: radial-gradient(circle, var(--ctc-border) 1px, transparent 1px);
        background-size: 20px 20px;
    }

    .preview-canvas-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        max-width: 650px;
        background: var(--ctc-bg-card);
        padding: 8px 16px;
        border-radius: 12px;
        border: 1px solid var(--ctc-border);
        margin-bottom: 20px;
        box-shadow: var(--ctc-shadow-sm);
    }

    .preview-canvas-wrapper {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 520px;
    }

    #liveEditorCardPreview {
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .editor-right-panel {
        background: var(--ctc-bg-card);
        border-left: 1px solid var(--ctc-border);
        padding: 22px;
        display: flex;
        flex-direction: column;
        gap: 16px;
        overflow-y: auto;
        max-height: 800px;
    }

    /* Form Fields */
    .ctc-form-group {
        margin-bottom: 16px;
    }
    .ctc-form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: var(--ctc-text-main);
        margin-bottom: 6px;
        letter-spacing: 0.2px;
    }
    .ctc-form-control {
        width: 100%;
        height: 40px;
        padding: 8px 12px;
        font-size: 13.5px;
        font-weight: 500;
        border-radius: 10px;
        border: 1px solid var(--ctc-input-border);
        background-color: var(--ctc-input-bg);
        color: var(--ctc-input-text);
        transition: all 0.2s ease;
        outline: none;
    }
    .ctc-form-control:focus {
        border-color: var(--ctc-border-focus);
        box-shadow: 0 0 0 3px var(--ctc-primary-bg);
    }

    textarea.ctc-form-control {
        height: 140px;
        resize: vertical;
        line-height: 1.45;
        font-family: 'Consolas', 'Fira Code', monospace;
        font-size: 12px;
    }

    .color-picker-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }
    .color-input-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--ctc-input-bg);
        border: 1px solid var(--ctc-input-border);
        border-radius: 8px;
        padding: 4px 8px;
    }
    .color-input-wrapper input[type="color"] {
        -webkit-appearance: none;
        border: none;
        width: 26px;
        height: 26px;
        border-radius: 6px;
        cursor: pointer;
        background: none;
    }
    .color-input-wrapper input[type="color"]::-webkit-color-swatch-wrapper { padding: 0; }
    .color-input-wrapper input[type="color"]::-webkit-color-swatch {
        border: 1px solid var(--ctc-border);
        border-radius: 4px;
    }
    .color-hex-val {
        font-size: 12px;
        font-weight: 600;
        font-family: monospace;
        color: var(--ctc-text-main);
    }

    /* Variable Categorized Accordion */
    .var-category-box {
        background: var(--ctc-bg-subtle);
        border: 1px solid var(--ctc-border);
        border-radius: 12px;
        padding: 12px;
        margin-top: 10px;
    }
    .var-category-title {
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--ctc-text-muted);
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .var-chip-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 12px;
    }
    .var-chip {
        font-family: 'Consolas', monospace;
        font-size: 11px;
        font-weight: 600;
        background: var(--ctc-code-bg);
        color: var(--ctc-code-text);
        border: 1px solid var(--ctc-code-border);
        padding: 3px 8px;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s ease;
        user-select: none;
    }
    .var-chip:hover {
        background: var(--ctc-primary);
        color: #ffffff;
        border-color: var(--ctc-primary);
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.25);
    }

    /* Buttons */
    .btn-save-template {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 12px;
        height: 46px;
        width: 100%;
        font-size: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.35);
        transition: all 0.2s ease;
    }
    .btn-save-template:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-1px);
    }

    .btn-action-side {
        width: 100%;
        height: 42px;
        border-radius: 10px;
        border: 1px solid var(--ctc-border);
        background: var(--ctc-bg-subtle);
        color: var(--ctc-text-main);
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-action-side:hover {
        background: var(--ctc-bg-hover);
        border-color: var(--ctc-input-border);
    }

    .badge-card-type {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 11.5px;
        white-space: nowrap;
    }
    .badge-type-id {
        background: rgba(16, 185, 129, 0.15);
        color: #10b981;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }
    .badge-type-bus {
        background: rgba(59, 130, 246, 0.15);
        color: #3b82f6;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }
    .badge-type-admit {
        background: rgba(168, 85, 247, 0.15);
        color: #a855f7;
        border: 1px solid rgba(168, 85, 247, 0.3);
    }

    .btn-card-act {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 11.5px;
        font-weight: 700;
        cursor: pointer;
        border: 1px solid var(--ctc-border);
        background: var(--ctc-bg-subtle);
        color: var(--ctc-text-main);
        transition: all 0.2s ease;
    }
    .btn-card-act:hover {
        background: var(--ctc-bg-hover);
        border-color: var(--ctc-input-border);
    }

    @media (max-width: 1200px) {
        .ctc-editor-layout {
            grid-template-columns: 340px 1fr;
        }
        .editor-right-panel {
            grid-column: span 2;
            border-left: none;
            border-top: 1px solid var(--ctc-border);
            max-height: none;
        }
    }

    @media (max-width: 900px) {
        .ctc-editor-layout {
            grid-template-columns: 1fr;
        }
        .editor-left-panel, .editor-center-panel, .editor-right-panel {
            grid-column: span 1;
            border: none;
            border-bottom: 1px solid var(--ctc-border);
        }
    }
</style>

<!-- Hero Header -->
<div class="ctc-hero">
    <div class="ctc-hero-left">
        <div class="ctc-hero-icon">
            <i class="fas fa-edit"></i>
        </div>
        <div class="ctc-hero-title">
            <h1>Edit Card Template</h1>
            <p>Modify layout styles, colors, HTML code, and variables with realtime preview</p>
        </div>
    </div>
    <div style="display: flex; gap: 10px;">
        <a href="{{ route('school.cards.template-creator') }}" class="btn-action-side" style="height: 44px; padding: 0 18px; width: auto;">
            <i class="fas fa-arrow-left"></i> Back to Gallery
        </a>
    </div>
</div>

<!-- 3-Panel Visual Template Editor Form -->
<form method="POST" action="{{ route('school.cards.template-edit', $template->id) }}" enctype="multipart/form-data">
    @csrf
    <div class="ctc-editor-layout">
        <!-- Left Panel: Properties & Variables -->
        <div class="editor-left-panel">
            <div class="ctc-form-group">
                <label>Template Name *</label>
                <input type="text" name="name" id="tplNameInput" class="ctc-form-control" value="{{ old('name', $template->name) }}" required oninput="triggerLiveUpdate()">
            </div>

            <div class="ctc-form-group">
                <label>Card Type *</label>
                <select name="type" id="tplTypeSelect" class="ctc-form-control" required onchange="onCardTypeChange(this.value)">
                    <option value="id_card" {{ $template->type === 'id_card' ? 'selected' : '' }}>Student ID Card</option>
                    <option value="bus_pass" {{ $template->type === 'bus_pass' ? 'selected' : '' }}>Bus Pass</option>
                    <option value="admit_card" {{ $template->type === 'admit_card' ? 'selected' : '' }}>Exam Admit Card</option>
                </select>
            </div>

            <div class="ctc-form-group">
                <div class="color-picker-row">
                    <div>
                        <label>Background Color *</label>
                        <div class="color-input-wrapper">
                            <input type="color" name="background_color" id="editorBgColor" value="{{ old('background_color', $template->background_color) }}" oninput="syncEditorColor('bg', this.value)">
                            <span class="color-hex-val" id="editorBgHex">{{ old('background_color', $template->background_color) }}</span>
                        </div>
                    </div>
                    <div>
                        <label>Text Color *</label>
                        <div class="color-input-wrapper">
                            <input type="color" name="text_color" id="editorTextColor" value="{{ old('text_color', $template->text_color) }}" oninput="syncEditorColor('text', this.value)">
                            <span class="color-hex-val" id="editorTextHex">{{ old('text_color', $template->text_color) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ctc-form-group">
                <label>Layout Style *</label>
                <select name="layout_style" id="tplLayoutSelect" class="ctc-form-control" required onchange="triggerLiveUpdate()">
                    <option value="classic" {{ $template->layout_style === 'classic' ? 'selected' : '' }}>Classic Portrait</option>
                    <option value="minimal" {{ $template->layout_style === 'minimal' ? 'selected' : '' }}>Minimalist Landscape</option>
                    <option value="detailed" {{ $template->layout_style === 'detailed' ? 'selected' : '' }}>Detailed Double-sided</option>
                </select>
            </div>

            <div class="ctc-form-group">
                <label>Upload Custom Design / Background (Optional)</label>
                <input type="file" name="background_image" class="ctc-form-control" accept=".jpg,.jpeg,.png,.svg">
                <small style="color: var(--ctc-text-muted); font-size: 11px; display: block; margin-top: 4px;">Max: 2MB</small>
            </div>

            <div class="ctc-form-group" style="margin-bottom: 10px;">
                <label>Custom HTML Layout</label>
                <textarea name="custom_html" id="editorCustomHtml" class="ctc-form-control" placeholder="Paste custom ID / Admit card HTML here..." oninput="triggerLiveUpdate()">{{ old('custom_html', $template->custom_html) }}</textarea>
            </div>

            <!-- Categorized Variables Accordion -->
            <div class="var-category-box">
                <span class="var-category-title"><i class="fas fa-user-graduate" style="color: var(--ctc-primary);"></i> Student Information</span>
                <div class="var-chip-grid">
                    <span class="var-chip" onclick="insertEditorVar('[Student_Name]')">[Student_Name]</span>
                    <span class="var-chip" onclick="insertEditorVar('[Student_Image]')">[Student_Image]</span>
                    <span class="var-chip" onclick="insertEditorVar('[Admission_ID]')">[Admission_ID]</span>
                    <span class="var-chip" onclick="insertEditorVar('[Grade_Class]')">[Grade_Class]</span>
                    <span class="var-chip" onclick="insertEditorVar('[Blood_Group]')">[Blood_Group]</span>
                    <span class="var-chip" onclick="insertEditorVar('[Card_No]')">[Card_No]</span>
                    <span class="var-chip" onclick="insertEditorVar('[Expiry_Date]')">[Expiry_Date]</span>
                </div>

                <span class="var-category-title"><i class="fas fa-users" style="color: #10b981;"></i> Parent Information</span>
                <div class="var-chip-grid">
                    <span class="var-chip" onclick="insertEditorVar('[Father_Name]')">[Father_Name]</span>
                    <span class="var-chip" onclick="insertEditorVar('[Phone]')">[Phone]</span>
                    <span class="var-chip" onclick="insertEditorVar('[Address]')">[Address]</span>
                </div>

                <span class="var-category-title"><i class="fas fa-school" style="color: #f59e0b;"></i> School Information</span>
                <div class="var-chip-grid">
                    <span class="var-chip" onclick="insertEditorVar('[School_Name]')">[School_Name]</span>
                    <span class="var-chip" onclick="insertEditorVar('[School_Logo]')">[School_Logo]</span>
                    <span class="var-chip" onclick="insertEditorVar('[School_Address]')">[School_Address]</span>
                    <span class="var-chip" onclick="insertEditorVar('[School_Phone]')">[School_Phone]</span>
                    <span class="var-chip" onclick="insertEditorVar('[Principal_Signature]')">[Principal_Signature]</span>
                </div>

                <span class="var-category-title"><i class="fas fa-file-signature" style="color: #a855f7;"></i> Admit Card Variables</span>
                <div class="var-chip-grid" style="margin-bottom: 0;">
                    <span class="var-chip" onclick="insertEditorVar('[Exam_Name]')">[Exam_Name]</span>
                    <span class="var-chip" onclick="insertEditorVar('[Exam_Schedule_Table]')">[Exam_Schedule_Table]</span>
                </div>
            </div>
        </div>

        <!-- Center Panel: Large Live Preview -->
        <div class="editor-center-panel">
            <div class="preview-canvas-toolbar">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span style="font-size: 12px; font-weight: 700; color: var(--ctc-text-muted); text-transform: uppercase;">
                        <i class="fas fa-eye" style="color: var(--ctc-primary); margin-right: 4px;"></i> Live Preview
                    </span>
                    <span id="previewTypeBadge" class="badge-card-type badge-type-id">ID Card</span>
                </div>
                <div style="display: flex; align-items: center; gap: 6px;">
                    <button type="button" class="btn-card-act" onclick="zoomEditorPreview(0.1)" title="Zoom In">
                        <i class="fas fa-search-plus"></i>
                    </button>
                    <button type="button" class="btn-card-act" onclick="zoomEditorPreview(-0.1)" title="Zoom Out">
                        <i class="fas fa-search-minus"></i>
                    </button>
                    <button type="button" class="btn-card-act" onclick="resetEditorZoom()" title="Reset Scale">
                        100%
                    </button>
                </div>
            </div>

            <div class="preview-canvas-wrapper">
                <div id="liveEditorCardPreview">
                    <!-- Live rendered content -->
                </div>
            </div>
        </div>

        <!-- Right Panel: Quick Actions & Specs -->
        <div class="editor-right-panel">
            <div style="border-bottom: 1px solid var(--ctc-border); padding-bottom: 14px;">
                <h4 style="font-size: 13px; font-weight: 800; text-transform: uppercase; color: var(--ctc-text-muted); margin: 0 0 10px 0; letter-spacing: 0.5px;">
                    <i class="fas fa-bolt" style="color: #f59e0b; margin-right: 4px;"></i> Quick Actions
                </h4>
                <button type="submit" class="btn-save-template">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>

            <a href="{{ route('school.cards.template-creator') }}" class="btn-action-side">
                <i class="fas fa-arrow-left"></i> Back to Gallery
            </a>

            <!-- Spec Info Card -->
            <div style="background: var(--ctc-bg-subtle); border: 1px solid var(--ctc-border); border-radius: 12px; padding: 14px; margin-top: auto;">
                <strong style="color: var(--ctc-primary); font-size: 12px; display: block; margin-bottom: 6px;">
                    <i class="fas fa-info-circle"></i> Live Preview Info:
                </strong>
                <p style="font-size: 11.5px; color: var(--ctc-text-muted); margin: 0; line-height: 1.5;">
                    HTML text, variables, background color or typography changes update this preview in real time without reloading.
                </p>
            </div>
        </div>
    </div>
</form>

<script>
const defaultSchoolLogo = '{{ asset("images/logo.png") }}';
const defaultSchoolName = 'Yash International School';
const defaultSchoolAddress = 'Main Campus, Education Zone, City';
const defaultSchoolPhone = '+91 98765 43210';
const sampleStudentPhoto = "data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='120' height='140' viewBox='0 0 120 140' fill='%23e2e8f0'><rect width='120' height='140' rx='6'/><circle cx='60' cy='50' r='28' fill='%2394a3b8'/><path d='M20 130 C20 95, 100 95, 100 130 Z' fill='%2394a3b8'/></svg>";

let currentEditorZoom = 1;

function parseCardTemplateHtml(rawHtml, tplData) {
    const sName = 'Aarav Sharma';
    const fName = 'Rajesh Sharma';
    const sPhone = '+91 98765 43210';
    const sAddress = '123 Park Avenue, Sector 15, New Delhi - 110001';
    const sBloodGroup = 'B+';
    const sClass = 'Class 10 - Section A';
    const sId = 'YIS/2026/00001';
    const cNo = 'CRD-782618';
    const expDate = '{{ date('Y-m-d', strtotime('+1 year')) }}';
    const logoUrl = defaultSchoolLogo;
    const schoolName = defaultSchoolName;
    const photoTag = `<img src="${sampleStudentPhoto}" style="width:100%; height:100%; object-fit:cover; border-radius:inherit;" alt="Student" />`;

    const timetableHtml = `
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 12px; font-size: 11.5px; text-align: left;">
            <thead>
                <tr style="background-color: #eff6ff; color: #1e3a8a; border: 1px solid #cbd5e1;">
                    <th style="padding: 5px; border: 1px solid #cbd5e1;">Day</th>
                    <th style="padding: 5px; border: 1px solid #cbd5e1;">Subject</th>
                    <th style="padding: 5px; border: 1px solid #cbd5e1;">Timing</th>
                    <th style="padding: 5px; border: 1px solid #cbd5e1;">Sign</th>
                </tr>
            </thead>
            <tbody>
                <tr><td style="padding: 4px 6px; border: 1px solid #e2e8f0;">Day 1</td><td style="padding: 4px 6px; border: 1px solid #e2e8f0; font-weight: 600;">Mathematics</td><td style="padding: 4px 6px; border: 1px solid #e2e8f0;">09:00 AM - 12:00 PM</td><td style="padding: 4px 6px; border: 1px solid #e2e8f0;"></td></tr>
                <tr><td style="padding: 4px 6px; border: 1px solid #e2e8f0;">Day 2</td><td style="padding: 4px 6px; border: 1px solid #e2e8f0; font-weight: 600;">Science & Tech</td><td style="padding: 4px 6px; border: 1px solid #e2e8f0;">09:00 AM - 12:00 PM</td><td style="padding: 4px 6px; border: 1px solid #e2e8f0;"></td></tr>
                <tr><td style="padding: 4px 6px; border: 1px solid #e2e8f0;">Day 3</td><td style="padding: 4px 6px; border: 1px solid #e2e8f0; font-weight: 600;">English Language</td><td style="padding: 4px 6px; border: 1px solid #e2e8f0;">09:00 AM - 12:00 PM</td><td style="padding: 4px 6px; border: 1px solid #e2e8f0;"></td></tr>
            </tbody>
        </table>`;

    if (!rawHtml || rawHtml.trim() === '') {
        const bgColor = tplData ? (tplData.background_color || '#1a1f3c') : '#1a1f3c';
        const txtColor = tplData ? (tplData.text_color || '#ffffff') : '#ffffff';
        const typeLabel = tplData && tplData.type === 'bus_pass' ? 'Bus Transport Pass' : (tplData && tplData.type === 'admit_card' ? 'Exam Admit Card' : 'Student Identity Card');
        
        return `
            <div style="width: 320px; height: 460px; border-radius: 16px; background-color: ${bgColor}; color: ${txtColor}; display: flex; flex-direction: column; padding: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.18); font-family: 'Inter', Arial, sans-serif; box-sizing: border-box; position: relative;">
                <div style="text-align: center; border-bottom: 1px solid rgba(255,255,255,0.2); padding-bottom: 10px; margin-bottom: 14px;">
                    <h4 style="font-size: 13px; font-weight: 800; text-transform: uppercase; margin: 0; color:${txtColor};">${schoolName}</h4>
                    <span style="font-size: 9px; opacity: 0.85; text-transform: uppercase; letter-spacing: 0.5px; color:${txtColor};">${typeLabel}</span>
                </div>
                <div style="display: flex; justify-content: center; margin-bottom: 14px;">
                    <div style="width: 95px; height: 115px; border-radius: 10px; border: 2px solid rgba(255,255,255,0.3); overflow: hidden; background: #ffffff;">
                        <img src="${sampleStudentPhoto}" style="width: 100%; height: 100%; object-fit: cover;" alt="Student" />
                    </div>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; text-align: center;">
                    <h3 style="font-size: 16px; font-weight: 800; margin: 0 0 4px 0; color:${txtColor};">${sName}</h3>
                    <span style="font-size: 11px; opacity: 0.85; margin-bottom: 12px; color:${txtColor};">Grade: ${sClass}</span>
                    <div style="width: 100%; border-top: 1px solid rgba(255,255,255,0.15); padding-top: 10px; font-size: 11px; line-height: 1.6; text-align: left; color:${txtColor};">
                        <div style="display:flex; justify-content:space-between;"><span>Admission ID:</span><strong>${sId}</strong></div>
                        <div style="display:flex; justify-content:space-between;"><span>Card Number:</span><strong>${cNo}</strong></div>
                        <div style="display:flex; justify-content:space-between;"><span>Expiry Date:</span><strong>${expDate}</strong></div>
                    </div>
                </div>
                <div style="display: flex; justify-content: center; margin-top: auto; padding-top: 8px; border-top: 1px solid rgba(255,255,255,0.2);">
                    <div style="width: 36px; height: 36px; background: #ffffff; padding: 2px; border-radius: 4px; display: flex; align-items: center; justify-content: center; color: #000;">
                        <i class="fas fa-qrcode" style="font-size: 30px;"></i>
                    </div>
                </div>
            </div>`;
    }

    let parsed = rawHtml;
    parsed = parsed.replaceAll('src="[Student_Photo]"', `src="${sampleStudentPhoto}"`)
                   .replaceAll('src="[Student_Image]"', `src="${sampleStudentPhoto}"`)
                   .replaceAll('src="[StudentPhoto]"', `src="${sampleStudentPhoto}"`)
                   .replaceAll('src="[Photo]"', `src="${sampleStudentPhoto}"`)
                   .replaceAll('src="STUDENT PHOTO"', `src="${sampleStudentPhoto}"`);

    parsed = parsed.replaceAll('[Student_Name]', sName)
                   .replaceAll('[StudentName]', sName)
                   .replaceAll('[Father_Name]', fName)
                   .replaceAll('[FatherName]', fName)
                   .replaceAll('[Phone]', sPhone)
                   .replaceAll('[Phone_No]', sPhone)
                   .replaceAll('[Address]', sAddress)
                   .replaceAll('[Blood_Group]', sBloodGroup)
                   .replaceAll('[BloodGroup]', sBloodGroup)
                   .replaceAll('[Admission_ID]', sId)
                   .replaceAll('[AdmissionID]', sId)
                   .replaceAll('[Roll_No]', sId)
                   .replaceAll('[RollNo]', sId)
                   .replaceAll('[Grade_Class]', sClass)
                   .replaceAll('[GradeClass]', sClass)
                   .replaceAll('[Class]', sClass)
                   .replaceAll('[Card_No]', cNo)
                   .replaceAll('[CardNo]', cNo)
                   .replaceAll('[Expiry_Date]', expDate)
                   .replaceAll('[ExpiryDate]', expDate)
                   .replaceAll('[School_Logo]', logoUrl)
                   .replaceAll('[SchoolLogo]', logoUrl)
                   .replaceAll('[School_Name]', schoolName)
                   .replaceAll('[SchoolName]', schoolName)
                   .replaceAll('[School_Address]', defaultSchoolAddress)
                   .replaceAll('[School_Phone]', defaultSchoolPhone)
                   .replaceAll('[Exam_Name]', 'FINAL SEMESTER EXAMINATION 2026')
                   .replaceAll('[Exam_Schedule_Table]', timetableHtml)
                   .replaceAll('[Student_Image]', photoTag)
                   .replaceAll('[Student_Photo]', photoTag);

    return parsed;
}

function triggerLiveUpdate() {
    const rawHtml = document.getElementById('editorCustomHtml').value;
    const bgColor = document.getElementById('editorBgColor').value;
    const txtColor = document.getElementById('editorTextColor').value;
    const cardType = document.getElementById('tplTypeSelect').value;

    const tplData = {
        background_color: bgColor,
        text_color: txtColor,
        type: cardType
    };

    const rendered = parseCardTemplateHtml(rawHtml, tplData);
    const previewContainer = document.getElementById('liveEditorCardPreview');
    if (previewContainer) {
        previewContainer.innerHTML = rendered;
        previewContainer.style.transform = `scale(${currentEditorZoom})`;
    }
}

function syncEditorColor(type, val) {
    if (type === 'bg') {
        document.getElementById('editorBgHex').innerText = val;
    } else {
        document.getElementById('editorTextHex').innerText = val;
    }
    triggerLiveUpdate();
}

function onCardTypeChange(type) {
    const badge = document.getElementById('previewTypeBadge');
    if (badge) {
        if (type === 'id_card') {
            badge.className = 'badge-card-type badge-type-id';
            badge.innerText = 'ID Card';
        } else if (type === 'bus_pass') {
            badge.className = 'badge-card-type badge-type-bus';
            badge.innerText = 'Bus Pass';
        } else {
            badge.className = 'badge-card-type badge-type-admit';
            badge.innerText = 'Admit Card';
        }
    }
    triggerLiveUpdate();
}

function insertEditorVar(tag) {
    const textarea = document.getElementById('editorCustomHtml');
    if (!textarea) return;

    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;

    textarea.value = text.substring(0, start) + tag + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + tag.length;
    textarea.focus();
    triggerLiveUpdate();
}

function zoomEditorPreview(delta) {
    currentEditorZoom = Math.min(Math.max(currentEditorZoom + delta, 0.4), 1.8);
    const el = document.getElementById('liveEditorCardPreview');
    if (el) el.style.transform = `scale(${currentEditorZoom})`;
}

function resetEditorZoom() {
    currentEditorZoom = 1;
    const el = document.getElementById('liveEditorCardPreview');
    if (el) el.style.transform = `scale(1)`;
}

document.addEventListener('DOMContentLoaded', function() {
    onCardTypeChange(document.getElementById('tplTypeSelect').value);
    triggerLiveUpdate();
});
</script>
@endsection

