@extends('layouts.app')

@section('page-title', 'Offline Tests')

@section('content')
<style>
/* Sleek Blue and White Design System */
.ot-container {
    padding: 10px 5px;
}
.ot-filter-bar {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 14px 18px;
    margin-bottom: 24px;
    display: flex;
    gap: 14px;
    flex-wrap: wrap;
    align-items: center;
    box-shadow: 0 2px 4px rgba(37, 99, 235, 0.04);
}
.ot-filter-item {
    flex: 1;
    min-width: 170px;
}
.ot-filter-label {
    font-size: 11px;
    font-weight: 700;
    color: #1e3a8a;
    margin-bottom: 5px;
    display: block;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.ot-filter-select {
    width: 100%;
    height: 40px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
    padding: 0 12px;
    font-size: 13px;
    color: #0f172a;
    background-color: #f8fafc;
    outline: none;
    transition: all 0.2s ease;
}
.ot-filter-select:focus {
    border-color: #2563eb;
    background-color: #ffffff;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}

/* Empty State Styling */
.ot-empty-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 60px 20px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
}
.ot-illustration {
    width: 180px;
    height: auto;
    margin: 0 auto 20px auto;
    display: block;
}
.ot-empty-title {
    font-size: 14px;
    color: #475569;
    max-width: 440px;
    margin: 0 auto 24px auto;
    line-height: 1.6;
}
.btn-create-test-main {
    background: #2563eb;
    color: #ffffff;
    border: none;
    border-radius: 7px;
    padding: 12px 30px;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: 0.5px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 4px 14px rgba(37, 99, 235, 0.3);
    transition: all 0.2s ease;
}
.btn-create-test-main:hover {
    background: #1d4ed8;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(37, 99, 235, 0.4);
}

/* Test List Cards Grid */
.ot-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-top: 20px;
}
.ot-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
    position: relative;
    transition: all 0.2s ease;
}
.ot-card:hover {
    border-color: #3b82f6;
    box-shadow: 0 6px 16px rgba(59, 130, 246, 0.12);
}
.ot-card-hdr {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 14px;
}
.ot-card-title {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
}
.ot-badge {
    font-size: 10px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.ot-badge-published { background: #dbeafe; color: #1d4ed8; }
.ot-badge-draft { background: #f1f5f9; color: #64748b; }

.ot-card-info {
    font-size: 12.5px;
    color: #475569;
    display: flex;
    flex-direction: column;
    gap: 8px;
}
.ot-card-info-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* CREATE TEST MODAL STYLES (BLUE AND WHITE THEME) */
.ot-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(15, 23, 42, 0.65);
    backdrop-filter: blur(4px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.ot-modal-overlay.open {
    display: flex;
}
.ot-modal-content {
    background: #ffffff;
    width: 90%;
    max-width: 1000px;
    max-height: 90vh;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
    animation: modalSlideIn 0.25s ease-out;
}
@keyframes modalSlideIn {
    from { transform: translateY(-15px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}
.ot-modal-header {
    background: #2563eb;
    color: #ffffff;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #1d4ed8;
}
.ot-modal-header h2 {
    font-size: 17px;
    font-weight: 700;
    margin: 0;
    color: #ffffff;
    display: flex;
    align-items: center;
    gap: 8px;
}
.ot-modal-close {
    background: rgba(255, 255, 255, 0.15);
    border: none;
    color: #ffffff;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.ot-modal-close:hover {
    background: rgba(255, 255, 255, 0.3);
}
.ot-modal-body {
    padding: 24px;
    overflow-y: auto;
    flex: 1;
    background: #f8fafc;
}
.ot-modal-top-selectors {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 20px;
    background: #ffffff;
    padding: 16px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}
.ot-modal-grid {
    display: grid;
    grid-template-columns: 1.8fr 1fr;
    gap: 20px;
}
@media(max-width: 768px){
    .ot-modal-top-selectors { grid-template-columns: 1fr 1fr; }
    .ot-modal-grid { grid-template-columns: 1fr; }
}
.ot-section-box {
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 20px;
    background: #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
}
.ot-section-title {
    font-size: 15px;
    font-weight: 700;
    color: #1e3a8a;
    margin-bottom: 18px;
    padding-bottom: 8px;
    border-bottom: 2px solid #eff6ff;
}
.ot-form-group {
    margin-bottom: 16px;
}
.ot-form-group label {
    font-size: 11.5px;
    font-weight: 600;
    color: #334155;
    display: block;
    margin-bottom: 6px;
}
.ot-input, .ot-select, .ot-textarea {
    width: 100%;
    border: 1px solid #cbd5e1;
    border-radius: 7px;
    padding: 9px 13px;
    font-size: 13px;
    color: #0f172a;
    outline: none;
    background: #ffffff;
    transition: all 0.2s ease;
}
.ot-input:focus, .ot-select:focus, .ot-textarea:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
}
.ot-editor-toolbar {
    border: 1px solid #cbd5e1;
    border-bottom: none;
    border-top-left-radius: 7px;
    border-top-right-radius: 7px;
    background: #f1f5f9;
    padding: 8px 12px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
    font-size: 13px;
    color: #334155;
}
.ot-editor-toolbar button {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    cursor: pointer;
    padding: 3px 8px;
    border-radius: 4px;
    color: #1e293b;
    transition: all 0.15s;
}
.ot-editor-toolbar button:hover {
    background: #2563eb;
    color: #ffffff;
    border-color: #2563eb;
}
.ot-textarea-editor {
    border-top-left-radius: 0;
    border-top-right-radius: 0;
    min-height: 130px;
    resize: vertical;
}

.ot-modal-footer {
    padding: 16px 24px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: #ffffff;
}
.btn-save-draft {
    background: #ffffff;
    border: 1.5px solid #2563eb;
    color: #2563eb;
    padding: 9px 20px;
    border-radius: 7px;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.2s;
}
.btn-save-draft:hover {
    background: #eff6ff;
}

.btn-create-test-submit {
    background: #2563eb;
    border: none;
    color: #ffffff;
    padding: 9px 24px;
    border-radius: 7px;
    font-size: 12.5px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
    transition: all 0.2s;
}
.btn-create-test-submit:hover {
    background: #1d4ed8;
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.35);
}
</style>

<div class="ot-container">
    {{-- Top Filter Form --}}
    <form method="GET" action="{{ route('school.examination.offline-tests') }}" class="ot-filter-bar" id="filterForm">
        <div class="ot-filter-item">
            <label class="ot-filter-label">Academic Year *</label>
            <select name="academic_year" class="ot-filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="Apr 2025 - Mar 2026" selected>Apr 2025 - Mar 2026</option>
            </select>
        </div>
        <div class="ot-filter-item">
            <label class="ot-filter-label">Select Class *</label>
            <select name="class_id" class="ot-filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">All Classes</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="ot-filter-item">
            <label class="ot-filter-label">Select Section *</label>
            <select name="section_id" class="ot-filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">All Sections</option>
                @foreach($sections as $sec)
                    <option value="{{ $sec->id }}" {{ $selectedSectionId == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="ot-filter-item">
            <label class="ot-filter-label">Select Subject *</label>
            <select name="subject_id" class="ot-filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">All Subjects</option>
                @foreach($subjects as $sub)
                    <option value="{{ $sub->id }}" {{ $selectedSubjectId == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="ot-filter-item">
            <label class="ot-filter-label">Select Teacher *</label>
            <select name="teacher_id" class="ot-filter-select" onchange="document.getElementById('filterForm').submit()">
                <option value="">All Teachers</option>
                @foreach($teachers as $t)
                    <option value="{{ $t->id }}" {{ $selectedTeacherId == $t->id ? 'selected' : '' }}>{{ $t->first_name ?? $t->user?->name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Main Content Area --}}
    @if(isset($tests) && $tests->count() > 0)
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
            <h3 style="font-size:17px; font-weight:700; color:#1e3a8a;">Scheduled Offline Tests ({{ $tests->count() }})</h3>
            <button type="button" class="btn-create-test-main" onclick="openTestModal()" style="padding:9px 18px; font-size:12.5px;">
                <i class="fas fa-plus"></i> CREATE TEST
            </button>
        </div>
        <div class="ot-grid">
            @foreach($tests as $test)
            <div class="ot-card">
                <div class="ot-card-hdr">
                    <div class="ot-card-title">{{ $test->title }}</div>
                    <span class="ot-badge {{ $test->status === 'draft' ? 'ot-badge-draft' : 'ot-badge-published' }}">
                        {{ strtoupper($test->status) }}
                    </span>
                </div>
                <div class="ot-card-info">
                    <div class="ot-card-info-item"><i class="fas fa-book-open" style="color:#2563eb; width:16px;"></i> <strong>Subject:</strong> {{ $test->subject?->name ?? 'N/A' }}</div>
                    <div class="ot-card-info-item"><i class="fas fa-users" style="color:#0284c7; width:16px;"></i> <strong>Class/Sec:</strong> {{ $test->schoolClass?->name ?? 'All' }} {{ $test->section ? '('.$test->section->name.')' : '' }}</div>
                    <div class="ot-card-info-item"><i class="fas fa-user-tie" style="color:#0d9488; width:16px;"></i> <strong>Teacher:</strong> {{ $test->teacher?->first_name ?? 'N/A' }}</div>
                    <div class="ot-card-info-item"><i class="far fa-clock" style="color:#6366f1; width:16px;"></i> <strong>Timing:</strong> {{ $test->start_date_time ? date('d/m/Y h:i A', strtotime($test->start_date_time)) : 'N/A' }} ({{ $test->duration_minutes }} mins)</div>
                    <div class="ot-card-info-item"><i class="fas fa-star" style="color:#3b82f6; width:16px;"></i> <strong>Grading:</strong> {{ $test->grading_type }}</div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <div class="ot-empty-card">
            <svg class="ot-illustration" viewBox="0 0 200 160" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="95" cy="65" r="30" stroke="#2563eb" stroke-width="3"/>
                <path d="M95 45V65H110" stroke="#2563eb" stroke-width="3" stroke-linecap="round"/>
                <rect x="100" y="80" width="60" height="12" rx="2" fill="#60a5fa"/>
                <rect x="100" y="94" width="60" height="12" rx="2" fill="#2563eb"/>
                <rect x="100" y="108" width="60" height="12" rx="2" fill="#1d4ed8"/>
                <path d="M70 120C65 105 60 100 50 110" stroke="#3b82f6" stroke-width="3" stroke-linecap="round"/>
                <path d="M120 40L125 35M135 45L140 40" stroke="#2563eb" stroke-width="2" stroke-linecap="round"/>
                <path d="M80 120H150" stroke="#cbd5e1" stroke-width="4" stroke-linecap="round"/>
            </svg>
            <div class="ot-empty-title">
                There are no tests shared with this class yet. Go ahead and share your first test by clicking the button below.
            </div>
            <button type="button" class="btn-create-test-main" onclick="openTestModal()">
                CREATE TEST <i class="fas fa-arrow-right"></i>
            </button>
        </div>
    @endif
</div>

{{-- Create Test Modal (Blue & White Design) --}}
<div class="ot-modal-overlay" id="testModal">
    <div class="ot-modal-content">
        <div class="ot-modal-header">
            <h2><i class="fas fa-file-pen"></i> Create Test</h2>
            <button type="button" class="ot-modal-close" onclick="closeTestModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('school.examination.offline-tests') }}">
            @csrf
            <div class="ot-modal-body">
                {{-- Top Selectors --}}
                <div class="ot-modal-top-selectors">
                    <div>
                        <label class="ot-filter-label">Select Class *</label>
                        <select name="class_id" class="ot-select" required>
                            <option value="">Select Class</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="ot-filter-label">Select Section *</label>
                        <select name="section_id" class="ot-select">
                            <option value="">Select Section</option>
                            @foreach($sections as $sec)
                                <option value="{{ $sec->id }}" {{ $selectedSectionId == $sec->id ? 'selected' : '' }}>{{ $sec->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="ot-filter-label">Select Subject *</label>
                        <select name="subject_id" class="ot-select">
                            <option value="">Select Subject</option>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}" {{ $selectedSubjectId == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="ot-filter-label">Select Teacher *</label>
                        <select name="teacher_id" class="ot-select">
                            <option value="">Select Teacher</option>
                            @foreach($teachers as $t)
                                <option value="{{ $t->id }}" {{ $selectedTeacherId == $t->id ? 'selected' : '' }}>{{ $t->first_name ?? $t->user?->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Grid Details --}}
                <div class="ot-modal-grid">
                    {{-- Left: Test Details --}}
                    <div class="ot-section-box">
                        <div class="ot-section-title">Test Details</div>
                        <div class="ot-form-group">
                            <label>Test Title *</label>
                            <input type="text" name="title" class="ot-input" required placeholder="Bi-Weekly Test">
                        </div>
                        <div class="ot-form-group">
                            <label>Chapters</label>
                            <select name="chapters" class="ot-select">
                                <option value="">Select..</option>
                                <option value="Chapter 1: Basics">Chapter 1: Basics</option>
                                <option value="Chapter 2: Advanced Concepts">Chapter 2: Advanced Concepts</option>
                            </select>
                        </div>
                        <div class="ot-form-group">
                            <label>Sub-Chapters</label>
                            <select name="sub_chapters" class="ot-select">
                                <option value="">Select..</option>
                                <option value="Sub-chapter 1.1">Sub-chapter 1.1</option>
                                <option value="Sub-chapter 1.2">Sub-chapter 1.2</option>
                            </select>
                        </div>
                        <div class="ot-form-group">
                            <label>Test Instructions</label>
                            <div class="ot-editor-toolbar">
                                <button type="button" title="Bold"><i class="fas fa-bold"></i></button>
                                <button type="button" title="Italic"><i class="fas fa-italic"></i></button>
                                <button type="button" title="Underline"><i class="fas fa-underline"></i></button>
                                <button type="button" title="Strikethrough"><i class="fas fa-strikethrough"></i></button>
                                <button type="button" title="List"><i class="fas fa-list-ul"></i></button>
                                <button type="button" title="Numbers"><i class="fas fa-list-ol"></i></button>
                                <button type="button" title="Align"><i class="fas fa-align-left"></i></button>
                                <button type="button" title="Clear"><i class="fas fa-eraser"></i></button>
                            </div>
                            <textarea name="instructions" class="ot-textarea ot-textarea-editor" placeholder="Start typings..."></textarea>
                        </div>
                    </div>

                    {{-- Right: Timings --}}
                    <div class="ot-section-box">
                        <div class="ot-section-title">Timings</div>
                        <div class="ot-form-group">
                            <label>Test Start Date & Time *</label>
                            <input type="datetime-local" name="start_date_time" class="ot-input" required>
                        </div>
                        <div class="ot-form-group">
                            <label>Test Duration (in minutes) *</label>
                            <input type="number" name="duration_minutes" class="ot-input" required placeholder="Test duration in minutes" min="1" value="60">
                        </div>
                        <div class="ot-form-group">
                            <label>Grading Type *</label>
                            <select name="grading_type" class="ot-select" required>
                                <option value="Marks">⭐ Marks</option>
                                <option value="Grades">⭐ Grades (A-F)</option>
                                <option value="Percentage">⭐ Percentage (%)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="ot-modal-footer">
                <button type="submit" name="status" value="draft" class="btn-save-draft">
                    <i class="far fa-edit"></i> SAVE AS DRAFT
                </button>
                <button type="submit" name="status" value="published" class="btn-create-test-submit">
                    <i class="fas fa-check-circle"></i> CREATE TEST
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openTestModal() {
    document.getElementById('testModal').classList.add('open');
}
function closeTestModal() {
    document.getElementById('testModal').classList.remove('open');
}
</script>
@endsection
