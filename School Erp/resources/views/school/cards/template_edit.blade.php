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
        --ctc-input-bg: #f8fafc;
        --ctc-input-text: #0f172a;
        --ctc-input-border: #cbd5e1;
        --ctc-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
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
        --ctc-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        --ctc-code-bg: rgba(59, 130, 246, 0.15);
        --ctc-code-text: #93c5fd;
        --ctc-code-border: rgba(59, 130, 246, 0.3);
    }

    /* ─── PAGE HEADER ─── */
    .ctc-page-hdr {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
        background: var(--ctc-bg-card);
        padding: 22px 28px;
        border-radius: 16px;
        border: 1px solid var(--ctc-border);
        box-shadow: var(--ctc-shadow);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .ctc-page-hdr::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, #2563eb 0%, #3b82f6 100%);
    }

    .ctc-hdr-icon {
        width: 52px;
        height: 52px;
        border-radius: 14px;
        background: var(--ctc-primary-bg);
        color: var(--ctc-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
        border: 1px solid var(--ctc-primary-border);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.1);
    }

    .ctc-hdr-title h1 {
        font-size: 22px;
        font-weight: 800;
        color: var(--ctc-text-main) !important;
        margin: 0 0 4px 0;
        line-height: 1.2;
        letter-spacing: -0.02em;
    }

    .ctc-hdr-title p {
        font-size: 13.5px;
        color: var(--ctc-text-muted) !important;
        margin: 0;
        font-weight: 500;
    }

    /* ─── GRID LAYOUT & CARDS ─── */
    .ctc-grid-edit {
        display: grid;
        grid-template-columns: 1.8fr 1fr;
        gap: 24px;
        align-items: start;
    }

    .ctc-card {
        background: var(--ctc-bg-card);
        border-radius: 16px;
        border: 1px solid var(--ctc-border);
        box-shadow: var(--ctc-shadow);
        overflow: hidden;
        transition: all 0.2s ease;
    }

    .ctc-card-hdr {
        padding: 20px 24px;
        border-bottom: 1px solid var(--ctc-border);
        background: var(--ctc-bg-card);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .ctc-card-hdr h3 {
        font-size: 16px;
        font-weight: 700;
        color: var(--ctc-text-main) !important;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .ctc-card-hdr h3 i {
        color: var(--ctc-primary);
        font-size: 18px;
    }

    .ctc-card-body {
        padding: 24px;
    }

    /* ─── FORM ELEMENTS ─── */
    .ctc-form-group {
        margin-bottom: 20px;
    }

    .ctc-form-group label {
        display: block;
        font-size: 12.5px;
        font-weight: 700;
        color: var(--ctc-text-main);
        margin-bottom: 8px;
        letter-spacing: 0.2px;
    }

    .ctc-form-control {
        width: 100%;
        height: 44px;
        padding: 10px 14px;
        font-size: 14px;
        font-weight: 500;
        border-radius: 10px;
        border: 1px solid var(--ctc-input-border);
        background-color: var(--ctc-input-bg);
        color: var(--ctc-input-text);
        transition: all 0.2s ease;
        outline: none;
    }

    .ctc-form-control::placeholder {
        color: var(--ctc-text-subtle);
        opacity: 1;
    }

    .ctc-form-control:focus {
        border-color: var(--ctc-border-focus);
        background-color: var(--ctc-input-bg);
        box-shadow: 0 0 0 3.5px var(--ctc-primary-bg);
    }

    textarea.ctc-form-control {
        height: 140px;
        resize: vertical;
        line-height: 1.5;
        font-family: 'Consolas', 'Fira Code', monospace;
        font-size: 12.5px;
    }

    /* Color picker row styling */
    .color-picker-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }

    .color-input-wrapper {
        display: flex;
        align-items: center;
        gap: 8px;
        background: var(--ctc-input-bg);
        border: 1px solid var(--ctc-input-border);
        border-radius: 10px;
        padding: 4px 10px;
    }

    .color-input-wrapper input[type="color"] {
        -webkit-appearance: none;
        border: none;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        cursor: pointer;
        background: none;
    }
    .color-input-wrapper input[type="color"]::-webkit-color-swatch-wrapper {
        padding: 0;
    }
    .color-input-wrapper input[type="color"]::-webkit-color-swatch {
        border: 1px solid var(--ctc-border);
        border-radius: 6px;
    }

    .color-hex-val {
        font-size: 13px;
        font-weight: 600;
        font-family: monospace;
        color: var(--ctc-text-main);
    }

    /* Custom File Input */
    input[type="file"].ctc-form-control {
        padding: 8px 12px;
        font-size: 13px;
        cursor: pointer;
    }
    input[type="file"].ctc-form-control::file-selector-button {
        background: var(--ctc-primary-bg);
        color: var(--ctc-primary);
        border: 1px solid var(--ctc-primary-border);
        border-radius: 6px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        margin-right: 10px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    input[type="file"].ctc-form-control::file-selector-button:hover {
        background: var(--ctc-primary);
        color: #ffffff;
    }

    /* Variable Tag Chips */
    .variable-tags-box {
        margin-top: 8px;
        padding: 10px 12px;
        background: var(--ctc-bg-subtle);
        border: 1px solid var(--ctc-border);
        border-radius: 10px;
    }
    .variable-tags-title {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        color: var(--ctc-text-muted);
        letter-spacing: 0.5px;
        margin-bottom: 8px;
        display: block;
    }
    .variable-tags-flex {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .variable-chip {
        font-family: 'Consolas', monospace;
        font-size: 11.5px;
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
    .variable-chip:hover {
        background: var(--ctc-primary);
        color: #ffffff;
        border-color: var(--ctc-primary);
        transform: translateY(-1px);
    }

    .btn-submit-gradient {
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 10px;
        height: 44px;
        padding: 0 22px;
        font-size: 14px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .btn-submit-gradient:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        box-shadow: 0 6px 18px rgba(37, 99, 235, 0.45);
        transform: translateY(-1px);
        color: #ffffff !important;
    }

    .btn-cancel-outline {
        background: var(--ctc-bg-subtle);
        color: var(--ctc-text-main) !important;
        border: 1px solid var(--ctc-border);
        border-radius: 10px;
        height: 44px;
        padding: 0 20px;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-cancel-outline:hover {
        background: var(--ctc-bg-hover);
        border-color: var(--ctc-input-border);
    }

    @media (max-width: 992px) {
        .ctc-grid-edit {
            grid-template-columns: 1fr;
            gap: 20px;
        }
        .ctc-page-hdr {
            padding: 18px 20px;
        }
    }

    @media (max-width: 576px) {
        .ctc-page-hdr {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        .color-picker-row {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="ctc-page-hdr">
    <div class="ctc-hdr-icon">
        <i class="fas fa-edit"></i>
    </div>
    <div class="ctc-hdr-title">
        <h1>Edit Card Template</h1>
        <p>Modify styling, color theme, and design layouts for student ID, bus, or exam passes</p>
    </div>
</div>

<div class="ctc-grid-edit">
    <!-- Edit Form Card -->
    <div class="ctc-card">
        <div class="ctc-card-hdr">
            <h3><i class="fas fa-pen-nib"></i> Update Template: {{ $template->name }}</h3>
        </div>
        <div class="ctc-card-body">
            <form method="POST" action="{{ route('school.cards.template-edit', $template->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="ctc-form-group">
                    <label>Template Name *</label>
                    <input type="text" name="name" class="ctc-form-control" value="{{ old('name', $template->name) }}" required>
                </div>
                <div class="ctc-form-group">
                    <label>Card Type *</label>
                    <select name="type" class="ctc-form-control" required>
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
                                <input type="color" name="background_color" id="bgColorPicker" value="{{ old('background_color', $template->background_color) }}" oninput="document.getElementById('bgColorHex').innerText = this.value">
                                <span class="color-hex-val" id="bgColorHex">{{ old('background_color', $template->background_color) }}</span>
                            </div>
                        </div>
                        <div>
                            <label>Text Color *</label>
                            <div class="color-input-wrapper">
                                <input type="color" name="text_color" id="textColorPicker" value="{{ old('text_color', $template->text_color) }}" oninput="document.getElementById('textColorHex').innerText = this.value">
                                <span class="color-hex-val" id="textColorHex">{{ old('text_color', $template->text_color) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ctc-form-group">
                    <label>Layout Style *</label>
                    <select name="layout_style" class="ctc-form-control" required>
                        <option value="classic" {{ $template->layout_style === 'classic' ? 'selected' : '' }}>Classic Portrait</option>
                        <option value="minimal" {{ $template->layout_style === 'minimal' ? 'selected' : '' }}>Minimalist Landscape</option>
                        <option value="detailed" {{ $template->layout_style === 'detailed' ? 'selected' : '' }}>Detailed Double-sided</option>
                    </select>
                </div>

                <div class="ctc-form-group">
                    <label>Custom HTML Script / Layout (Optional)</label>
                    <textarea name="custom_html" id="customHtmlInput" class="ctc-form-control" placeholder="Paste your custom Admit Card / ID Card HTML script here...">{{ old('custom_html', $template->custom_html) }}</textarea>
                    
                    <div class="variable-tags-box">
                        <span class="variable-tags-title"><i class="fas fa-code" style="margin-right: 4px;"></i> Click variable to insert into script:</span>
                        <div class="variable-tags-flex">
                            <span class="variable-chip" onclick="insertVariable('[Student_Name]')">[Student_Name]</span>
                            <span class="variable-chip" onclick="insertVariable('[Father_Name]')">[Father_Name]</span>
                            <span class="variable-chip" onclick="insertVariable('[Phone]')">[Phone]</span>
                            <span class="variable-chip" onclick="insertVariable('[Address]')">[Address]</span>
                            <span class="variable-chip" onclick="insertVariable('[Blood_Group]')">[Blood_Group]</span>
                            <span class="variable-chip" onclick="insertVariable('[Admission_ID]')">[Admission_ID]</span>
                            <span class="variable-chip" onclick="insertVariable('[Grade_Class]')">[Grade_Class]</span>
                            <span class="variable-chip" onclick="insertVariable('[Card_No]')">[Card_No]</span>
                            <span class="variable-chip" onclick="insertVariable('[Expiry_Date]')">[Expiry_Date]</span>
                            <span class="variable-chip" onclick="insertVariable('[School_Logo]')">[School_Logo]</span>
                            <span class="variable-chip" onclick="insertVariable('[School_Name]')">[School_Name]</span>
                        </div>
                    </div>
                </div>

                <div class="ctc-form-group">
                    <label>Upload Custom Design / Background (Optional)</label>
                    <input type="file" name="background_image" class="ctc-form-control" accept=".jpg,.jpeg,.png,.svg">
                    <small style="color: var(--ctc-text-muted); font-size: 11.5px; display: block; margin-top: 6px;">If uploaded, this background image will replace the background color and layout defaults. (Max: 2MB)</small>
                </div>

                <div style="margin-top:24px; display:flex; gap:12px;">
                    <button type="submit" class="btn-submit-gradient">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="{{ route('school.cards.template-creator') }}" class="btn-cancel-outline">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Right side Live Design Preview -->
    <div class="ctc-card">
        <div class="ctc-card-hdr">
            <h3><i class="fas fa-eye"></i> Live Template Preview</h3>
            <span style="background: var(--ctc-primary-bg); color: var(--ctc-primary); font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; border: 1px solid var(--ctc-primary-border);">
                Sample Data
            </span>
        </div>
        <div class="ctc-card-body" style="background: var(--ctc-bg-subtle);">
            <div id="livePreviewWrapper" style="border: 1px dashed var(--ctc-border); border-radius: 12px; padding: 14px; background: var(--ctc-bg-card); min-height: 280px; overflow-x: auto;">
                <div id="livePreviewContent" style="width:100%; transition:all 0.2s ease;">
                    <!-- Realtime HTML content rendered here -->
                </div>
            </div>
            
            <div style="margin-top: 16px; font-size: 12px; color: var(--ctc-text-muted); background: var(--ctc-bg-card); padding: 12px 14px; border-radius: 10px; border: 1px solid var(--ctc-border);">
                <strong style="color: var(--ctc-primary); display: block; margin-bottom: 4px; font-size: 12.5px;"><i class="fas fa-info-circle"></i> Live Preview Info:</strong>
                HTML text or variables edit karte hi preview real-time update ho jata hai.
            </div>
        </div>
    </div>
</div>

<script>
function insertVariable(tag) {
    const textarea = document.getElementById('customHtmlInput');
    if (!textarea) return;
    
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;

    textarea.value = text.substring(0, start) + tag + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + tag.length;
    textarea.focus();
    if (typeof renderEditPreview === 'function') renderEditPreview();
}

function renderCustomHtmlToIframe(containerElement, htmlContent) {
    containerElement.innerHTML = '';
    const iframe = document.createElement('iframe');
    iframe.className = 'card-preview-iframe';
    iframe.style.width = '100%';
    iframe.style.minHeight = '350px';
    iframe.style.border = 'none';
    iframe.style.borderRadius = '8px';
    iframe.style.background = '#ffffff';
    iframe.style.boxShadow = '0 2px 8px rgba(0,0,0,0.06)';
    iframe.style.overflow = 'hidden';
    containerElement.appendChild(iframe);

    const doc = iframe.contentWindow || iframe.contentDocument;
    const documentObj = doc.document || doc;
    documentObj.open();
    documentObj.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            margin: 0;
            padding: 10px;
            background: #ffffff;
            font-family: 'Inter', Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }
        * {
            box-sizing: border-box;
        }
    </style>
</head>
<body>
    <div id="cardContentWrapper" style="width:100%; display:flex; justify-content:center;">
        ${htmlContent}
    </div>
    <script>
        function resizeIframe() {
            try {
                const wrapper = document.getElementById('cardContentWrapper');
                if (wrapper) {
                    const h = Math.max(wrapper.scrollHeight + 20, 350);
                    window.frameElement.style.height = h + 'px';
                }
            } catch(e) {}
        }
        window.addEventListener('load', resizeIframe);
        setTimeout(resizeIframe, 100);
        setTimeout(resizeIframe, 400);
    <\/script>
</body>
</html>`);
    documentObj.close();
}

function renderEditPreview() {
    const htmlTextarea = document.querySelector('textarea[name="custom_html"]');
    const previewContainer = document.getElementById('livePreviewContent');
    const rawHtml = htmlTextarea ? htmlTextarea.value : '';

    if (rawHtml && rawHtml.trim() !== '') {
        const sName = 'Aarav Sharma';
        const fName = 'Rajesh Sharma';
        const sPhone = '+91 98765 43210';
        const sAddress = '123 Park Avenue, Sector 15, New Delhi - 110001';
        const sBloodGroup = 'B+';
        const sClass = 'Class 10 - Section A';
        const sId = 'YIS/2026/00001';
        const cNo = 'CRD-782618';
        const expDate = '{{ date('Y-m-d', strtotime('+1 year')) }}';
        const logoUrl = '{{ asset("images/logo.png") }}';
        const schoolName = 'Yash International School';

        let parsed = rawHtml;
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
                       .replaceAll('[SchoolName]', schoolName);

        parsed = parsed.replaceAll('$SchoolLogo', logoUrl)
                       .replaceAll('$StudentName', sName)
                       .replaceAll('$FatherName', fName)
                       .replaceAll('$Phone', sPhone)
                       .replaceAll('$Address', sAddress)
                       .replaceAll('$BloodGroup', sBloodGroup)
                       .replaceAll('$AdmissionID', sId)
                       .replaceAll('$GradeClass', sClass)
                       .replaceAll('$CardNo', cNo);

        renderCustomHtmlToIframe(previewContainer, parsed);
    } else {
        const bgColor = document.querySelector('input[name="background_color"]').value || '#1a1f3c';
        const textColor = document.querySelector('input[name="text_color"]').value || '#ffffff';
        const cardType = document.querySelector('select[name="type"]').value || 'Card Template';

        previewContainer.innerHTML = `
            <div style="width:100%; height:260px; border-radius:12px; background-color:${bgColor}; color:${textColor}; display:flex; flex-direction:column; padding:15px; box-shadow:0 4px 14px rgba(0,0,0,0.1); font-family:'Inter', sans-serif;">
                <div style="text-align:center; border-bottom:1px solid rgba(255,255,255,0.2); padding-bottom:8px; margin-bottom:12px;">
                    <h4 style="font-size:12px; font-weight:800; text-transform:uppercase; margin:0;">Yash International School</h4>
                    <span style="font-size:9px; opacity:0.8; text-transform:uppercase;">${cardType.replace('_', ' ')}</span>
                </div>
                <div style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center;">
                    <h3 style="font-size:14px; font-weight:800; margin-bottom:4px;">Aarav Sharma</h3>
                    <span style="font-size:10px; opacity:0.8;">Grade: Class 10 - Section A</span>
                </div>
            </div>
        `;
    }
}

document.addEventListener('DOMContentLoaded', function() {
    renderEditPreview();

    const htmlTextarea = document.querySelector('textarea[name="custom_html"]');
    if (htmlTextarea) {
        htmlTextarea.addEventListener('input', renderEditPreview);
        htmlTextarea.addEventListener('keyup', renderEditPreview);
    }

    const colorInput = document.querySelector('input[name="background_color"]');
    if (colorInput) {
        colorInput.addEventListener('change', renderEditPreview);
        colorInput.addEventListener('input', renderEditPreview);
    }
});
</script>
@endsection

