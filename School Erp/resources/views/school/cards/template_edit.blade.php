@extends('layouts.app')

@section('page-title', 'Edit Card Template')

@section('content')
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
</style>

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-edit" style="color:var(--accent-blue);margin-right:8px;"></i>Edit Card Template</h1>
        <p>Modify styling, color theme, and design layouts for student ID, bus, or exam passes</p>
    </div>
</div>

<div class="grid-3">
    <!-- Edit Form -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr card-hdr-blue">
            <h3>Update Template: {{ $template->name }}</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.cards.template-edit', $template->id) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label class="form-label">Template Name</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $template->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Card Type</label>
                    <select name="type" class="form-control" required>
                        <option value="id_card" {{ $template->type === 'id_card' ? 'selected' : '' }}>Student ID Card</option>
                        <option value="bus_pass" {{ $template->type === 'bus_pass' ? 'selected' : '' }}>Bus Pass</option>
                        <option value="admit_card" {{ $template->type === 'admit_card' ? 'selected' : '' }}>Exam Admit Card</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Background Color Hex</label>
                    <input type="color" name="background_color" class="form-control" value="{{ old('background_color', $template->background_color) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Text Color Hex</label>
                    <input type="color" name="text_color" class="form-control" value="{{ old('text_color', $template->text_color) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Layout Style</label>
                    <select name="layout_style" class="form-control" required>
                        <option value="classic" {{ $template->layout_style === 'classic' ? 'selected' : '' }}>Classic Portrait</option>
                        <option value="minimal" {{ $template->layout_style === 'minimal' ? 'selected' : '' }}>Minimalist Landscape</option>
                        <option value="detailed" {{ $template->layout_style === 'detailed' ? 'selected' : '' }}>Detailed Double-sided</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Custom HTML Script / Layout (Optional)</label>
                    <textarea name="custom_html" class="form-control" style="height:140px; font-family:monospace; font-size:12px;" placeholder="Paste your custom Admit Card / ID Card HTML script here...">{{ old('custom_html', $template->custom_html) }}</textarea>
                    <small style="color:var(--t3); display:block; margin-top:4px; font-size:11px;">
                        Allowed Variables: 
                        <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; color:#1e293b;">[Student_Name]</code>,
                        <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; color:#1e293b;">[Admission_ID]</code>,
                        <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; color:#1e293b;">[Grade_Class]</code>,
                        <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; color:#1e293b;">[Card_No]</code>,
                        <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; color:#1e293b;">[Expiry_Date]</code>,
                        <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; color:#1e293b;">[School_Logo]</code>,
                        <code style="background:#e2e8f0; padding:1px 4px; border-radius:3px; color:#1e293b;">[School_Name]</code>
                    </small>
                </div>

                <div class="form-group">
                    <label class="form-label">Upload Custom Design / Background (Optional)</label>
                    <input type="file" name="background_image" class="form-control">
                    <small style="color:var(--t3); display:block; margin-top:4px;">If uploaded, this background image will replace the background color and layout defaults. Max: 2MB.</small>
                </div>

                <div style="margin-top:20px; display:flex; gap:10px;">
                    <button type="submit" class="btn-blue">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                    <a href="{{ route('school.cards.template-creator') }}" class="btn-outline-blue">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Right side Live Design Preview -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr card-hdr-blue" style="display:flex; justify-content:space-between; align-items:center;">
            <h3 style="margin:0;"><i class="fas fa-eye" style="margin-right:6px;"></i>Live Template Preview</h3>
            <span class="badge badge-blue" style="background:#dbeafe; color:#1e40af; font-size:10px;">Sample Data</span>
        </div>
        <div class="card-body" style="background:#f8fafc; padding:15px;">
            <div id="livePreviewWrapper" style="border:1px dashed var(--border-color); border-radius:8px; padding:10px; background:#ffffff; min-height:280px; overflow-x:auto;">
                <div id="livePreviewContent" style="width:100%; transition:all 0.2s ease;">
                    <!-- Realtime HTML content rendered here -->
                </div>
            </div>
            
            <div style="margin-top:15px; font-size:11.5px; color:var(--t2);">
                <strong style="color:var(--primary-blue); display:block; margin-bottom:4px;"><i class="fas fa-info-circle"></i> Live Preview Info:</strong>
                Text box mein HTML ya variables edit karte hi right side preview real-time update ho jata hai.
            </div>
        </div>
    </div>
</div>

<script>
function renderEditPreview() {
    const htmlTextarea = document.querySelector('textarea[name="custom_html"]');
    const previewContainer = document.getElementById('livePreviewContent');
    const rawHtml = htmlTextarea ? htmlTextarea.value : '';

    if (rawHtml && rawHtml.trim() !== '') {
        const sName = 'Aarav Sharma';
        const sClass = 'Class 10 - Section A';
        const sId = 'YIS/2026/00001';
        const cNo = 'CRD-782618';
        const expDate = '{{ date('Y-m-d', strtotime('+1 year')) }}';
        const logoUrl = '{{ asset("images/logo.png") }}';
        const schoolName = 'Yash International School';

        let parsed = rawHtml;
        parsed = parsed.replaceAll('[Student_Name]', sName)
                       .replaceAll('[Admission_ID]', sId)
                       .replaceAll('[Roll_No]', sId)
                       .replaceAll('[Grade_Class]', sClass)
                       .replaceAll('[Card_No]', cNo)
                       .replaceAll('[Expiry_Date]', expDate)
                       .replaceAll('[School_Logo]', logoUrl)
                       .replaceAll('[School_Name]', schoolName);

        parsed = parsed.replaceAll('$SchoolLogo', logoUrl)
                       .replaceAll('$StudentName', sName)
                       .replaceAll('$AdmissionID', sId)
                       .replaceAll('$GradeClass', sClass)
                       .replaceAll('$CardNo', cNo);

        previewContainer.innerHTML = parsed;
    } else {
        const bgColor = document.querySelector('input[name="background_color"]').value || '#1a1f3c';
        const textColor = document.querySelector('input[name="text_color"]').value || '#ffffff';
        const cardType = document.querySelector('select[name="type"]').value || 'Card Template';

        previewContainer.innerHTML = `
            <div style="width:100%; height:260px; border-radius:12px; background-color:${bgColor}; color:${textColor}; display:flex; flex-direction:column; padding:15px; box-shadow:var(--shadow-md); font-family:'Inter', sans-serif;">
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
    }
});
</script>
@endsection
