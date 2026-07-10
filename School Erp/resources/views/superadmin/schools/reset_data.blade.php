@extends('superadmin.layouts.master')

@section('styles')
<style>
    .reset-page-wrap {
        max-width: 720px;
        margin: 0 auto;
        padding: 32px 16px;
    }

    .reset-hero {
        background: linear-gradient(135deg, #1e1b4b 0%, #3730a3 100%);
        border-radius: 20px;
        padding: 32px;
        color: #fff;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        gap: 20px;
    }
    .reset-hero-icon {
        width: 64px; height: 64px;
        background: rgba(255,255,255,0.12);
        border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        font-size: 28px;
        flex-shrink: 0;
    }
    .reset-hero-text h1 { font-size: 1.35rem; font-weight: 800; margin: 0 0 6px; }
    .reset-hero-text p  { font-size: 0.88rem; opacity: 0.8; margin: 0; }

    .reset-info-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    }
    .reset-info-card h4 {
        font-size: 0.92rem;
        font-weight: 800;
        color: #1e1b4b;
        margin: 0 0 14px;
        display: flex; align-items: center; gap: 8px;
    }

    .reset-keep-list, .reset-wipe-list {
        list-style: none;
        padding: 0; margin: 0;
        display: flex; flex-wrap: wrap; gap: 8px;
    }
    .reset-keep-list li {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        display: flex; align-items: center; gap: 5px;
    }
    .reset-wipe-list li {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        display: flex; align-items: center; gap: 5px;
    }

    .danger-zone {
        background: #fff5f5;
        border: 2px solid #fca5a5;
        border-radius: 16px;
        padding: 24px;
        margin-top: 8px;
    }
    .danger-zone .dz-title {
        font-size: 0.95rem;
        font-weight: 800;
        color: #dc2626;
        margin: 0 0 6px;
        display: flex; align-items: center; gap: 8px;
    }
    .danger-zone .dz-desc {
        font-size: 0.82rem;
        color: #7f1d1d;
        margin: 0 0 18px;
    }
    .danger-zone label {
        font-size: 0.82rem;
        font-weight: 700;
        color: #1e1b4b;
        margin-bottom: 6px;
        display: block;
    }
    .danger-zone input[type=text] {
        width: 100%;
        border: 1.5px solid #fca5a5;
        border-radius: 10px;
        padding: 10px 14px;
        font-size: 0.88rem;
        font-weight: 700;
        outline: none;
        background: #fff;
        color: #1e1b4b;
        transition: border .2s;
        box-sizing: border-box;
        margin-bottom: 16px;
    }
    .danger-zone input[type=text]:focus {
        border-color: #dc2626;
        box-shadow: 0 0 0 3px rgba(220,38,38,.12);
    }

    .btn-reset-execute {
        background: linear-gradient(135deg, #dc2626, #991b1b);
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 10px;
        font-size: 0.9rem;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex; align-items: center; gap: 8px;
        transition: opacity .2s, transform .2s;
        letter-spacing: .3px;
    }
    .btn-reset-execute:hover { opacity: 0.88; transform: translateY(-1px); }
    .btn-reset-execute:disabled { opacity: 0.4; cursor: not-allowed; transform: none; }

    .btn-cancel-reset {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 12px 24px;
        border-radius: 10px;
        font-size: 0.88rem;
        font-weight: 700;
        text-decoration: none;
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #e2e8f0;
        margin-left: 10px;
        transition: background .2s;
    }
    .btn-cancel-reset:hover { background: #e2e8f0; color: #1e1b4b; }

    @media (max-width: 600px) {
        .reset-hero { flex-direction: column; }
    }
</style>
@endsection

@section('content')
<div class="reset-page-wrap">

    {{-- Hero --}}
    <div class="reset-hero">
        <div class="reset-hero-icon"><i class="fas fa-database"></i></div>
        <div class="reset-hero-text">
            <h1>Reset School Data</h1>
            <p>{{ $school->name }} &bull; Code: {{ $school->code }}</p>
        </div>
    </div>

    @if($errors->any())
        <div style="background:#fef2f2;border:1.5px solid #ef4444;border-radius:12px;padding:14px 18px;margin-bottom:20px;color:#991b1b;font-size:.85rem;font-weight:600;">
            <i class="fas fa-exclamation-circle" style="margin-right:6px;"></i>
            {{ $errors->first() }}
        </div>
    @endif

    {{-- What is preserved --}}
    <div class="reset-info-card">
        <h4 style="color:#166534;"><i class="fas fa-shield-halved" style="color:#22c55e;"></i> Preserved (NOT deleted)</h4>
        <ul class="reset-keep-list">
            <li><i class="fas fa-check-circle"></i> Students &amp; their profiles</li>
            <li><i class="fas fa-check-circle"></i> Teachers / Staff</li>
            <li><i class="fas fa-check-circle"></i> Classes &amp; Sections</li>
            <li><i class="fas fa-check-circle"></i> School profile &amp; settings</li>
            <li><i class="fas fa-check-circle"></i> Subjects</li>
            <li><i class="fas fa-check-circle"></i> Student Documents &amp; Cards</li>
        </ul>
    </div>

    {{-- What will be deleted --}}
    <div class="reset-info-card">
        <h4 style="color:#dc2626;"><i class="fas fa-trash-can" style="color:#ef4444;"></i> Will be deleted</h4>
        <ul class="reset-wipe-list">
            <li><i class="fas fa-x"></i> All Fee Data (schedules, components, payments, invoices)</li>
            <li><i class="fas fa-x"></i> Student Fee Records &amp; Receipts</li>
            <li><i class="fas fa-x"></i> Fee Refunds &amp; Pending Cheques</li>
            <li><i class="fas fa-x"></i> Academic Sessions</li>
            <li><i class="fas fa-x"></i> Student Attendance</li>
            <li><i class="fas fa-x"></i> Staff Attendance</li>
            <li><i class="fas fa-x"></i> Timetables</li>
            <li><i class="fas fa-x"></i> Exams, Marks &amp; Grade Scales</li>
            <li><i class="fas fa-x"></i> Expenses &amp; Income Records</li>
            <li><i class="fas fa-x"></i> Gallery &amp; Events</li>
            <li><i class="fas fa-x"></i> Teacher Assignments &amp; Study Materials</li>
            <li><i class="fas fa-x"></i> School Banks</li>
        </ul>
    </div>

    {{-- Danger Zone --}}
    <div class="danger-zone">
        <p class="dz-title"><i class="fas fa-triangle-exclamation"></i> Danger Zone — This action is irreversible</p>
        <p class="dz-desc">
            All the data listed above will be permanently deleted and <strong>cannot be recovered</strong>.
            To confirm, type the exact school name below:
            <strong style="color:#dc2626;">{{ $school->name }}</strong>
        </p>

        <form method="POST" action="{{ route('superadmin.schools.reset-data.execute', $school) }}" id="resetForm">
            @csrf
            <label>Type school name to confirm:</label>
            <input type="text"
                   name="confirm_name"
                   id="confirm_name"
                   placeholder="Type: {{ $school->name }}"
                   autocomplete="off"
                   oninput="checkConfirm(this.value)">

            <div style="display:flex; align-items:center; flex-wrap:wrap; gap:0;">
                <button type="submit" class="btn-reset-execute" id="executeBtn" disabled
                        onclick="return confirm('Are you absolutely sure? This will permanently delete all data except students, teachers and classes.')">
                    <i class="fas fa-trash-can"></i> Reset School Data Now
                </button>
                <a href="{{ route('superadmin.schools.edit', $school) }}" class="btn-cancel-reset">
                    <i class="fas fa-arrow-left"></i> Cancel
                </a>
            </div>
        </form>
    </div>

</div>

<script>
    const expectedName = @json(strtolower(trim($school->name)));
    function checkConfirm(val) {
        const btn = document.getElementById('executeBtn');
        btn.disabled = val.trim().toLowerCase() !== expectedName;
    }
</script>
@endsection
