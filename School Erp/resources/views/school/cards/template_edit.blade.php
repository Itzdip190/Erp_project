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

    <!-- Right side design info -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr card-hdr-blue">
            <h3>Template Information</h3>
        </div>
        <div class="card-body">
            @if($template->background_image)
                <div style="margin-bottom:20px;">
                    <label class="form-label" style="display:block; margin-bottom:8px;">Current Design Background:</label>
                    <div style="border:1px solid var(--border-color); padding:5px; border-radius:6px; background:#f8fafc;">
                        <img src="{{ asset('uploads/templates/' . $template->background_image) }}" style="width:100%; border-radius:4px; display:block;" alt="Card Design">
                    </div>
                </div>
            @else
                <div style="padding:30px; text-align:center; border:2px dashed var(--border-color); border-radius:8px; color:var(--t3); margin-bottom:20px; background:#f8fafc;">
                    <i class="fas fa-image" style="font-size:32px; opacity:0.3; margin-bottom:8px;"></i>
                    <p style="font-size:12px;">No custom background uploaded. Default theme color will be used.</p>
                </div>
            @endif

            <h4 style="font-size:13px; font-weight:700; color:var(--primary-blue); margin-bottom:10px;">Design Guidelines:</h4>
            <ul style="font-size:12px; line-height:1.6; color:var(--t2); padding-left:15px; display:flex; flex-direction:column; gap:6px;">
                <li>Uploading a design background allows full custom templates to be used.</li>
                <li>Verify your text color selection complements the uploaded background pattern.</li>
                <li>Standard dimension recommendations match CR-80 card sizes.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
