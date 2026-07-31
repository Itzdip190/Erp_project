@extends('layouts.app')

@section('page-title', 'AI Notice Templates')

@section('content')
<style>
    .tmpl-page-hdr {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 24px;
        background: #ffffff;
        padding: 20px 24px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        flex-wrap: wrap;
    }
    .tmpl-hdr-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
        color: #7e22ce;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        flex-shrink: 0;
    }
    .tmpl-hdr-title h1 {
        font-size: 20px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 4px 0;
        line-height: 1.2;
    }
    .tmpl-hdr-title p {
        font-size: 13px;
        color: #64748b;
        margin: 0;
    }

    .ai-hero-banner {
        background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #9333ea 100%);
        border-radius: 16px;
        padding: 24px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(124, 58, 237, 0.3);
        position: relative;
        overflow: hidden;
    }
    .ai-hero-banner::after {
        content: '';
        position: absolute;
        top: -40px;
        right: -40px;
        width: 180px;
        height: 180px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        pointer-events: none;
    }
    .ai-hero-title {
        font-size: 18px;
        font-weight: 700;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .ai-hero-desc {
        font-size: 13px;
        opacity: 0.9;
        margin-bottom: 16px;
        max-width: 700px;
    }
    .ai-preset-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .ai-chip {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: #ffffff;
        padding: 8px 16px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .ai-chip:hover {
        background: #ffffff;
        color: #6d28d9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .tmpl-grid-2 {
        display: grid;
        grid-template-columns: 380px 1fr;
        gap: 24px;
        align-items: start;
    }

    .tmpl-card {
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.03);
        overflow: hidden;
    }
    .tmpl-card-hdr {
        padding: 18px 20px;
        border-bottom: 1px solid #f1f5f9;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .tmpl-card-hdr h3 {
        font-size: 16px;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .tmpl-card-body {
        padding: 20px;
    }

    .form-group-tmpl {
        margin-bottom: 16px;
    }
    .form-group-tmpl label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }
    .form-control-tmpl {
        width: 100%;
        height: 42px;
        padding: 8px 12px;
        font-size: 14px;
        border-radius: 10px;
        border: 1px solid #cbd5e1;
        background-color: #f8fafc;
        color: #0f172a;
        outline: none;
        transition: all 0.2s ease;
    }
    .form-control-tmpl:focus {
        border-color: #7c3aed;
        background-color: #ffffff;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.15);
    }
    textarea.form-control-tmpl {
        height: 140px;
        resize: vertical;
    }

    .btn-purple-primary {
        background: linear-gradient(135deg, #7c3aed 0%, #6d28d9 100%);
        color: #ffffff;
        border: none;
        border-radius: 10px;
        height: 44px;
        padding: 0 20px;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        width: 100%;
        box-shadow: 0 4px 14px rgba(124, 58, 237, 0.25);
        transition: all 0.2s ease;
    }
    .btn-purple-primary:hover {
        background: linear-gradient(135deg, #6d28d9 0%, #5b21b6 100%);
        transform: translateY(-1px);
        color: #ffffff;
    }

    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
        border-radius: 10px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .btn-back:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .template-item-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 18px;
        margin-bottom: 16px;
        transition: all 0.2s ease;
        position: relative;
    }
    .template-item-card:hover {
        border-color: #c084fc;
        box-shadow: 0 6px 18px rgba(124, 58, 237, 0.08);
        transform: translateY(-2px);
    }
    .category-tag {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-block;
    }
    .cat-Holiday { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .cat-Exam { background: #eff6ff; color: #2563eb; border: 1px solid #bfdbfe; }
    .cat-Fee { background: #fefce8; color: #ca8a04; border: 1px solid #fef08a; }
    .cat-Meeting { background: #f3e8ff; color: #7e22ce; border: 1px solid #e9d5ff; }
    .cat-Emergency { background: #fff1f2; color: #e11d48; border: 1px solid #ffe4e6; }
    .cat-Event { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .cat-General { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

    .tmpl-actions {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .tmpl-act-btn {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .tmpl-act-btn:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
    .tmpl-act-use {
        background: #7c3aed;
        color: #ffffff;
        border-color: #7c3aed;
    }
    .tmpl-act-use:hover {
        background: #6d28d9;
        color: #ffffff;
    }
    .tmpl-act-del:hover {
        background: #fee2e2;
        color: #dc2626;
        border-color: #fecaca;
    }

    @media (max-width: 992px) {
        .tmpl-grid-2 {
            grid-template-columns: 1fr;
        }
    }

    /* ── DARK MODE COMPATIBILITY STYLES ── */
    body.dark-mode .tmpl-page-hdr,
    body.dark-theme .tmpl-page-hdr,
    .dark-mode .tmpl-page-hdr,
    [data-theme="dark"] .tmpl-page-hdr,
    [data-bs-theme="dark"] .tmpl-page-hdr {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25) !important;
    }

    body.dark-mode .tmpl-hdr-icon,
    body.dark-theme .tmpl-hdr-icon,
    .dark-mode .tmpl-hdr-icon,
    [data-theme="dark"] .tmpl-hdr-icon,
    [data-bs-theme="dark"] .tmpl-hdr-icon {
        background: rgba(168, 85, 247, 0.2) !important;
        color: #c084fc !important;
    }

    body.dark-mode .tmpl-hdr-title h1,
    body.dark-theme .tmpl-hdr-title h1,
    .dark-mode .tmpl-hdr-title h1,
    [data-theme="dark"] .tmpl-hdr-title h1,
    [data-bs-theme="dark"] .tmpl-hdr-title h1 {
        color: #f8fafc !important;
    }

    body.dark-mode .tmpl-hdr-title p,
    body.dark-theme .tmpl-hdr-title p,
    .dark-mode .tmpl-hdr-title p,
    [data-theme="dark"] .tmpl-hdr-title p,
    [data-bs-theme="dark"] .tmpl-hdr-title p {
        color: #94a3b8 !important;
    }

    body.dark-mode .tmpl-card,
    body.dark-theme .tmpl-card,
    .dark-mode .tmpl-card,
    [data-theme="dark"] .tmpl-card,
    [data-bs-theme="dark"] .tmpl-card {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.25) !important;
    }

    body.dark-mode .tmpl-card-hdr,
    body.dark-theme .tmpl-card-hdr,
    .dark-mode .tmpl-card-hdr,
    [data-theme="dark"] .tmpl-card-hdr,
    [data-bs-theme="dark"] .tmpl-card-hdr {
        background: #1e293b !important;
        border-bottom-color: #334155 !important;
    }

    body.dark-mode .tmpl-card-hdr h3,
    body.dark-theme .tmpl-card-hdr h3,
    .dark-mode .tmpl-card-hdr h3,
    [data-theme="dark"] .tmpl-card-hdr h3,
    [data-bs-theme="dark"] .tmpl-card-hdr h3 {
        color: #f8fafc !important;
    }

    body.dark-mode .form-group-tmpl label,
    body.dark-theme .form-group-tmpl label,
    .dark-mode .form-group-tmpl label,
    [data-theme="dark"] .form-group-tmpl label,
    [data-bs-theme="dark"] .form-group-tmpl label {
        color: #cbd5e1 !important;
    }

    body.dark-mode .form-control-tmpl,
    body.dark-theme .form-control-tmpl,
    .dark-mode .form-control-tmpl,
    [data-theme="dark"] .form-control-tmpl,
    [data-bs-theme="dark"] .form-control-tmpl {
        background-color: #0f172a !important;
        border-color: #334155 !important;
        color: #f8fafc !important;
        color-scheme: dark !important;
    }

    body.dark-mode .form-control-tmpl::placeholder,
    body.dark-theme .form-control-tmpl::placeholder,
    .dark-mode .form-control-tmpl::placeholder,
    [data-theme="dark"] .form-control-tmpl::placeholder,
    [data-bs-theme="dark"] .form-control-tmpl::placeholder {
        color: #64748b !important;
    }

    body.dark-mode .form-control-tmpl option,
    body.dark-theme .form-control-tmpl option,
    .dark-mode .form-control-tmpl option,
    [data-theme="dark"] .form-control-tmpl option,
    [data-bs-theme="dark"] .form-control-tmpl option {
        background-color: #0f172a !important;
        color: #f8fafc !important;
    }

    body.dark-mode .form-control-tmpl:focus,
    body.dark-theme .form-control-tmpl:focus,
    .dark-mode .form-control-tmpl:focus,
    [data-theme="dark"] .form-control-tmpl:focus,
    [data-bs-theme="dark"] .form-control-tmpl:focus {
        border-color: #a855f7 !important;
        background-color: #0f172a !important;
        box-shadow: 0 0 0 3px rgba(168, 85, 247, 0.25) !important;
    }

    body.dark-mode .btn-back,
    body.dark-theme .btn-back,
    .dark-mode .btn-back,
    [data-theme="dark"] .btn-back,
    [data-bs-theme="dark"] .btn-back {
        background: #334155 !important;
        color: #e2e8f0 !important;
        border-color: #475569 !important;
    }

    body.dark-mode .btn-back:hover,
    body.dark-theme .btn-back:hover,
    .dark-mode .btn-back:hover,
    [data-theme="dark"] .btn-back:hover,
    [data-bs-theme="dark"] .btn-back:hover {
        background: #475569 !important;
        color: #ffffff !important;
    }

    body.dark-mode .template-item-card,
    body.dark-theme .template-item-card,
    .dark-mode .template-item-card,
    [data-theme="dark"] .template-item-card,
    [data-bs-theme="dark"] .template-item-card {
        background: #0f172a !important;
        border-color: #334155 !important;
    }

    body.dark-mode .template-item-card:hover,
    body.dark-theme .template-item-card:hover,
    .dark-mode .template-item-card:hover,
    [data-theme="dark"] .template-item-card:hover,
    [data-bs-theme="dark"] .template-item-card:hover {
        border-color: #a855f7 !important;
    }

    body.dark-mode .template-item-card h4,
    body.dark-theme .template-item-card h4,
    .dark-mode .template-item-card h4,
    [data-theme="dark"] .template-item-card h4,
    [data-bs-theme="dark"] .template-item-card h4 {
        color: #f8fafc !important;
    }

    body.dark-mode .template-item-card div[style*="background:#f8fafc"],
    body.dark-theme .template-item-card div[style*="background:#f8fafc"],
    .dark-mode .template-item-card div[style*="background:#f8fafc"],
    [data-theme="dark"] .template-item-card div[style*="background:#f8fafc"],
    [data-bs-theme="dark"] .template-item-card div[style*="background:#f8fafc"] {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }

    body.dark-mode .tmpl-act-btn,
    body.dark-theme .tmpl-act-btn,
    .dark-mode .tmpl-act-btn,
    [data-theme="dark"] .tmpl-act-btn,
    [data-bs-theme="dark"] .tmpl-act-btn {
        background: #1e293b !important;
        border-color: #334155 !important;
        color: #cbd5e1 !important;
    }

    body.dark-mode .tmpl-act-btn:hover,
    body.dark-theme .tmpl-act-btn:hover,
    .dark-mode .tmpl-act-btn:hover,
    [data-theme="dark"] .tmpl-act-btn:hover,
    [data-bs-theme="dark"] .tmpl-act-btn:hover {
        background: #334155 !important;
        color: #ffffff !important;
    }
</style>

<div class="tmpl-page-hdr">
    <div style="display:flex; align-items:center; gap:14px;">
        <div class="tmpl-hdr-icon">
            <i class="fas fa-magic"></i>
        </div>
        <div class="tmpl-hdr-title">
            <h1>AI Notice Templates Library</h1>
            <p>Save reusable message templates and generate smart notice announcements instantly</p>
        </div>
    </div>
    <div>
        <a href="{{ route('school.communication.notice') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i> Back to Notice Board
        </a>
    </div>
</div>

@if(session('success'))
<div style="background:#dcfce7; border:1px solid #86efac; color:#166534; padding:12px 16px; border-radius:12px; margin-bottom:20px; font-size:14px; display:flex; align-items:center; gap:10px;">
    <i class="fas fa-check-circle" style="font-size:18px;"></i>
    <span>{{ session('success') }}</span>
</div>
@endif

<!-- AI Preset Generator Banner -->
<div class="ai-hero-banner">
    <div class="ai-hero-title">
        <i class="fas fa-robot" style="font-size:22px;"></i> AI Notice Assistant Generator
    </div>
    <div class="ai-hero-desc">
        Click any preset topic below to generate a pre-formatted professional notice template automatically. You can edit and save it for future notice broadcasts.
    </div>
    <div class="ai-preset-chips">
        <div class="ai-chip" onclick="generateAiPreset('holiday')">
            <i class="fas fa-umbrella-beach"></i> Holiday Announcement
        </div>
        <div class="ai-chip" onclick="generateAiPreset('exam')">
            <i class="fas fa-file-pen"></i> Examination Advisory
        </div>
        <div class="ai-chip" onclick="generateAiPreset('fee')">
            <i class="fas fa-receipt"></i> Fee Due Reminder
        </div>
        <div class="ai-chip" onclick="generateAiPreset('meeting')">
            <i class="fas fa-users-rectangle"></i> PTM Meeting Invite
        </div>
        <div class="ai-chip" onclick="generateAiPreset('emergency')">
            <i class="fas fa-triangle-exclamation"></i> Emergency Closure
        </div>
        <div class="ai-chip" onclick="generateAiPreset('sports')">
            <i class="fas fa-trophy"></i> Sports Day Circular
        </div>
    </div>
</div>

<div class="tmpl-grid-2">
    <!-- Save/Edit Form Card -->
    <div class="tmpl-card">
        <div class="tmpl-card-hdr">
            <h3 id="form_header_title"><i class="fas fa-plus-circle" style="color:#7c3aed;"></i> Create Notice Template</h3>
        </div>
        <div class="tmpl-card-body">
            <form method="POST" id="template_form" action="{{ route('school.communication.notice-templates.store') }}">
                @csrf
                <input type="hidden" name="_method" id="form_method" value="POST">

                <div class="form-group-tmpl">
                    <label>Template Category</label>
                    <select class="form-control-tmpl" name="category" id="tmpl_category">
                        <option value="General">General</option>
                        <option value="Holiday">Holiday</option>
                        <option value="Exam">Examination</option>
                        <option value="Fee">Fee Notice</option>
                        <option value="Meeting">Meeting / PTM</option>
                        <option value="Event">Sports & Events</option>
                        <option value="Emergency">Emergency Alert</option>
                    </select>
                </div>

                <div class="form-group-tmpl">
                    <label>Template Title</label>
                    <input type="text" class="form-control-tmpl" name="title" id="tmpl_title" required placeholder="e.g. Summer Break Notice Template">
                </div>

                <div class="form-group-tmpl">
                    <label>Target Audience</label>
                    <select class="form-control-tmpl" name="target_audience" id="tmpl_audience">
                        <option value="all">Everyone (Students, Parents & Staff)</option>
                        <option value="students">Students & Parents Only</option>
                        <option value="staff">School Staff Only</option>
                    </select>
                </div>

                <div class="form-group-tmpl">
                    <label>Template Content Message</label>
                    <textarea class="form-control-tmpl" name="content" id="tmpl_content" required placeholder="Write reusable template body content here..."></textarea>
                </div>

                <button type="submit" class="btn-purple-primary" id="form_submit_btn">
                    <i class="fas fa-save"></i> Save Notice Template
                </button>
                
                <button type="button" class="btn-back" id="cancel_edit_btn" style="width:100%; margin-top:10px; justify-content:center; display:none;" onclick="resetForm()">
                    Cancel Editing
                </button>
            </form>
        </div>
    </div>

    <!-- Templates List Card -->
    <div class="tmpl-card">
        <div class="tmpl-card-hdr">
            <h3><i class="fas fa-layer-group" style="color:#7c3aed;"></i> Saved Notice Templates ({{ count($templates) }})</h3>
        </div>
        <div class="tmpl-card-body" style="max-height: 600px; overflow-y:auto;">
            @forelse($templates as $tmpl)
            <div class="template-item-card" id="template_card_{{ $tmpl->id }}">
                <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px; gap:10px;">
                    <div>
                        <span class="category-tag cat-{{ $tmpl->category ?? 'General' }}">
                            {{ $tmpl->category ?? 'General' }}
                        </span>
                        <h4 style="font-size:15px; font-weight:700; color:#0f172a; margin:8px 0 2px 0;">
                            {{ $tmpl->title }}
                        </h4>
                        <span style="font-size:12px; color:#64748b;">Audience: <strong>{{ ucfirst($tmpl->target_audience) }}</strong></span>
                    </div>
                    
                    <div class="tmpl-actions">
                        <a href="{{ route('school.communication.notice', ['template_id' => $tmpl->id]) }}" class="tmpl-act-btn tmpl-act-use" title="Use this template to publish notice">
                            <i class="fas fa-paper-plane"></i> Use Notice
                        </a>
                        <button type="button" class="tmpl-act-btn" onclick="editTemplate({{ $tmpl->id }}, '{{ addslashes($tmpl->title) }}', '{{ $tmpl->category }}', '{{ $tmpl->target_audience }}', '{{ addslashes($tmpl->content) }}')" title="Edit Template">
                            <i class="fas fa-edit"></i>
                        </button>
                        <form method="POST" action="{{ route('school.communication.notice-templates.delete', $tmpl->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this notice template?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="tmpl-act-btn tmpl-act-del" title="Delete Template">
                                <i class="fas fa-trash-can"></i>
                            </button>
                        </form>
                    </div>
                </div>

                <div style="background:#f8fafc; padding:12px; border-radius:10px; font-size:13px; color:#334155; line-height:1.5; white-space:pre-line; margin-top:8px; border:1px solid #f1f5f9;">{{ $tmpl->content }}</div>
            </div>
            @empty
            <div style="text-align:center; padding:40px 20px; color:#94a3b8;">
                <i class="fas fa-wand-magic-sparkles" style="font-size:40px; color:#cbd5e1; margin-bottom:12px; display:block;"></i>
                <span style="font-size:14px; font-weight:600; color:#475569; display:block; margin-bottom:6px;">No Saved Templates Yet</span>
                <p style="font-size:13px; color:#94a3b8; max-width:400px; margin:0 auto 16px auto;">Click any of the AI Assistant Preset chips above to generate your first notice template!</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function generateAiPreset(category) {
    fetch("{{ route('school.communication.notice-templates.generate-ai') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ category: category })
    })
    .then(res => res.json())
    .then(res => {
        if(res.status === 'success') {
            resetForm();
            document.getElementById('tmpl_title').value = res.data.title;
            document.getElementById('tmpl_category').value = res.data.category;
            document.getElementById('tmpl_audience').value = res.data.target_audience;
            document.getElementById('tmpl_content').value = res.data.content;

            // Highlight form
            const formCard = document.querySelector('.tmpl-card');
            formCard.style.boxShadow = '0 0 0 3px rgba(124, 58, 237, 0.4)';
            setTimeout(() => formCard.style.boxShadow = '0 4px 14px rgba(0, 0, 0, 0.03)', 1500);
        }
    })
    .catch(err => console.error(err));
}

function editTemplate(id, title, category, audience, content) {
    document.getElementById('form_header_title').innerHTML = '<i class="fas fa-edit" style="color:#7c3aed;"></i> Edit Notice Template';
    document.getElementById('template_form').action = "{{ url('school/communication/notice-templates/update') }}/" + id;
    document.getElementById('form_method').value = "POST";
    document.getElementById('tmpl_title').value = title;
    document.getElementById('tmpl_category').value = category || 'General';
    document.getElementById('tmpl_audience').value = audience || 'all';
    document.getElementById('tmpl_content').value = content;
    document.getElementById('form_submit_btn').innerHTML = '<i class="fas fa-check"></i> Update Notice Template';
    document.getElementById('cancel_edit_btn').style.display = 'flex';
}

function resetForm() {
    document.getElementById('form_header_title').innerHTML = '<i class="fas fa-plus-circle" style="color:#7c3aed;"></i> Create Notice Template';
    document.getElementById('template_form').action = "{{ route('school.communication.notice-templates.store') }}";
    document.getElementById('form_method').value = "POST";
    document.getElementById('tmpl_title').value = '';
    document.getElementById('tmpl_category').value = 'General';
    document.getElementById('tmpl_audience').value = 'all';
    document.getElementById('tmpl_content').value = '';
    document.getElementById('form_submit_btn').innerHTML = '<i class="fas fa-save"></i> Save Notice Template';
    document.getElementById('cancel_edit_btn').style.display = 'none';
}
</script>
@endsection
