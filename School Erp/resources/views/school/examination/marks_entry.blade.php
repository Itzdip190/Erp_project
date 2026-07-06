@extends('layouts.app')

@section('page-title', 'Marks Entry & Exam Management')

@section('content')
<style>
    :root {
        --blue-primary: #1e40af;
        --blue-secondary: #3b82f6;
        --blue-light: #eff6ff;
        --blue-dark: #1e3a8a;
        --border-color: #e2e8f0;
        --text-dark: #1e293b;
        --text-muted: #64748b;
    }

    .exam-hdr-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 20px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .exam-stat-card {
        background: linear-gradient(135deg, var(--blue-primary), var(--blue-secondary));
        color: #ffffff;
        border-radius: 12px;
        padding: 16px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 12px rgba(30, 64, 175, 0.15);
        min-width: 240px;
    }
    .exam-stat-card i {
        font-size: 28px;
        background: rgba(255, 255, 255, 0.2);
        padding: 12px;
        border-radius: 8px;
    }
    .exam-stat-card .num {
        font-size: 24px;
        font-weight: 700;
        line-height: 1;
    }
    .exam-stat-card .lbl {
        font-size: 13px;
        font-weight: 500;
        opacity: 0.9;
        margin-top: 4px;
    }

    .btn-blue-outline {
        border: 1.5px solid var(--blue-secondary);
        color: var(--blue-secondary);
        background: #ffffff;
        font-weight: 600;
        padding: 8px 16px;
        border-radius: 8px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-blue-outline:hover {
        background: var(--blue-light);
        color: var(--blue-primary);
    }

    .btn-blue-solid {
        background: linear-gradient(135deg, var(--blue-primary), var(--blue-secondary));
        color: #ffffff;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        box-shadow: 0 4px 10px rgba(59, 130, 246, 0.25);
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        cursor: pointer;
    }
    .btn-blue-solid:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(59, 130, 246, 0.35);
        color: #ffffff;
    }

    /* Stacked Filter Card Matching 2nd Image */
    .stacked-filter-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }
    .stacked-filter-group {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 20px;
    }
    .stacked-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .stacked-label {
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        text-transform: uppercase;
    }
    .stacked-input {
        width: 100%;
        height: 44px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0 14px;
        font-size: 14px;
        color: var(--text-dark);
        background: #ffffff;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .stacked-input:focus {
        border-color: var(--blue-secondary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }
    .stacked-input.active-exam {
        border-color: #f59e0b;
        box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.2);
    }

    .btn-apply-filter {
        width: 100%;
        height: 46px;
        background: #2563eb;
        color: #ffffff;
        font-weight: 700;
        font-size: 15px;
        border-radius: 10px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        transition: all 0.2s ease;
    }
    .btn-apply-filter:hover {
        background: #1d4ed8;
        transform: translateY(-1px);
    }

    /* Exam Cards List */
    .exam-card-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
        margin-bottom: 24px;
    }

    .exam-item-card {
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        transition: border-color 0.2s;
    }
    .exam-item-card:hover {
        border-color: var(--blue-secondary);
    }

    .exam-info-left {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .exam-info-meta {
        display: flex;
        align-items: center;
        gap: 24px;
        font-size: 13px;
        color: var(--text-muted);
        margin-top: 4px;
    }
    .exam-title {
        font-size: 18px;
        font-weight: 700;
        color: var(--blue-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .badge-pre-exam {
        background: #fef3c7;
        color: #92400e;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
    }
    .badge-ongoing {
        background: #dbeafe;
        color: #1e40af;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 6px;
        text-transform: uppercase;
    }

    .exam-actions-right {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .btn-action-sm {
        font-size: 12px;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 6px;
        border: 1px solid var(--border-color);
        background: #ffffff;
        color: var(--text-dark);
        transition: all 0.2s;
        cursor: pointer;
    }
    .btn-action-sm:hover {
        border-color: var(--blue-secondary);
        color: var(--blue-primary);
        background: var(--blue-light);
    }

    .icon-btn {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        border: 1px solid var(--border-color);
        background: #ffffff;
        color: var(--text-muted);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        cursor: pointer;
    }
    .icon-btn:hover {
        background: var(--blue-light);
        color: var(--blue-primary);
        border-color: var(--blue-secondary);
    }

    /* Side Slider Drawer Styles */
    .slider-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(3px);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    .slider-overlay.show {
        opacity: 1;
        visibility: visible;
    }

    .slider-drawer {
        position: fixed;
        top: 0; right: -720px; bottom: 0;
        width: 700px;
        max-width: 95vw;
        background: #ffffff;
        z-index: 1050;
        box-shadow: -10px 0 30px rgba(0, 0, 0, 0.15);
        display: flex;
        flex-direction: column;
        transition: right 0.35s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .slider-drawer.show {
        right: 0;
    }

    .slider-hdr {
        background: linear-gradient(135deg, var(--blue-dark), var(--blue-primary));
        color: #ffffff;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .slider-nav-tabs {
        display: flex;
        background: #f1f5f9;
        padding: 6px;
        gap: 6px;
        border-bottom: 1px solid var(--border-color);
    }
    .slider-tab-btn {
        flex: 1;
        padding: 10px;
        font-size: 13px;
        font-weight: 700;
        color: var(--text-muted);
        background: transparent;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .slider-tab-btn.active {
        background: #ffffff;
        color: var(--blue-primary);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
    }

    .slider-body {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
    }

    .slider-ftr {
        padding: 16px 24px;
        border-top: 1px solid var(--border-color);
        background: #f8fafc;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    /* Attendance Radio Toggle Buttons */
    .att-toggle {
        display: inline-flex;
        background: #e2e8f0;
        border-radius: 8px;
        padding: 3px;
        gap: 2px;
    }
    .att-label {
        padding: 5px 14px;
        font-size: 12px;
        font-weight: 700;
        border-radius: 6px;
        cursor: pointer;
        user-select: none;
        transition: all 0.2s;
    }
    .att-input:checked + .att-label.pres {
        background: #22c55e;
        color: #ffffff;
    }
    .att-input:checked + .att-label.abs {
        background: #ef4444;
        color: #ffffff;
    }
    .att-input { display: none; }

    /* Modal styles */
    .modal-backdrop-custom {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1060;
    }
    .modal-box-custom {
        background: #ffffff;
        border-radius: 16px;
        width: 100%;
        max-width: 900px;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }
    .modal-hdr-blue {
        background: linear-gradient(135deg, var(--blue-primary), var(--blue-secondary));
        color: #ffffff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
</style>

<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-edit" style="color:var(--blue-secondary);margin-right:8px;"></i>Marks Entry & Exam Management</h1>
        <p>Create subject-wise exams, match grade scales, set passing marks, and record student scores effortlessly</p>
    </div>
</div>

{{-- Stacked Filter Header matching 2nd Image --}}
<div class="stacked-filter-card">
    <form method="GET" action="{{ route('school.examination.marks-entry') }}" id="filterForm">
        <div class="stacked-filter-group">
            <div class="stacked-field">
                <label class="stacked-label">ACADEMIC YEAR</label>
                <select name="academic_year" class="stacked-input">
                    <option value="Apr 2025 - Mar 2026" {{ $academicYear === 'Apr 2025 - Mar 2026' ? 'selected' : '' }}>Apr 2025 - Mar 2026</option>
                    <option value="Apr 2024 - Mar 2025" {{ $academicYear === 'Apr 2024 - Mar 2025' ? 'selected' : '' }}>Apr 2024 - Mar 2025</option>
                </select>
            </div>
            <div class="stacked-field">
                <label class="stacked-label">EXAM STATUS</label>
                <select name="exam_type" class="stacked-input">
                    <option value="Ongoing & Completed" {{ $examType === 'Ongoing & Completed' ? 'selected' : '' }}>Ongoing & Completed</option>
                    <option value="PRE EXAM" {{ $examType === 'PRE EXAM' ? 'selected' : '' }}>Pre Exam</option>
                </select>
            </div>
            <div class="stacked-field">
                <label class="stacked-label">SELECT CLASS</label>
                <select name="class_id" id="mainClassSelect" class="stacked-input" onchange="loadMainSections(this.value)">
                    <option value="">-- Select Class --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="stacked-field">
                <label class="stacked-label">SELECT SECTION</label>
                <select name="section_id" id="mainSectionSelect" class="stacked-input">
                    <option value="">-- Select Section --</option>
                    @foreach($filteredSections as $s)
                        <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="stacked-field">
                <label class="stacked-label">SUBJECT</label>
                <select name="subject_id" id="mainSubjectSelect" class="stacked-input">
                    <option value="">-- Select Subject --</option>
                    @foreach($filteredSubjects as $sub)
                        <option value="{{ $sub->id }}" {{ $subjectId == $sub->id ? 'selected' : '' }}>{{ $sub->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="stacked-field">
                <label class="stacked-label">EXAM</label>
                <select name="exam_name" class="stacked-input active-exam">
                    <option value="">-- Select Exam --</option>
                    @foreach($exams as $ex)
                        <option value="{{ $ex->name }}" {{ $examName === $ex->name ? 'selected' : '' }}>{{ $ex->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <button type="submit" class="btn-apply-filter">
            <i class="fas fa-caret-down"></i> Apply Filter
        </button>
    </form>
</div>

{{-- Exam Stats & Create Exam Action Row --}}
<div class="exam-hdr-actions">
    <div class="exam-stat-card">
        <i class="fas fa-book-open"></i>
        <div>
            <div class="num">{{ $examsCount }}</div>
            <div class="lbl">Marks Entry / Exams Created</div>
        </div>
    </div>
    <div style="display:flex; align-items:center; gap:10px;">
        <button class="btn-blue-outline" onclick="alert('Viewing examination audit logs...')"><i class="fas fa-eye"></i> SHOW LOGS</button>
        <button class="btn-blue-solid" onclick="openCreateExamModal()"><i class="fas fa-plus"></i> CREATE NEW EXAM</button>
        <button class="icon-btn" title="Settings"><i class="fas fa-cog"></i></button>
    </div>
</div>

{{-- List of Exams --}}
<div class="exam-card-list">
    @forelse($exams as $index => $ex)
        <div class="exam-item-card">
            <div class="exam-info-left">
                <div style="display:flex; align-items:center; gap:10px;">
                    <span style="font-size:12px; font-weight:700; color:var(--text-muted);">0{{ $index + 1 }}.</span>
                    <h3 class="exam-title">{{ $ex->name }}</h3>
                    <span class="{{ $ex->status === 'PRE EXAM' ? 'badge-pre-exam' : 'badge-ongoing' }}">{{ $ex->status }}</span>
                </div>
                <div class="exam-info-meta">
                    <span><strong style="color:var(--text-dark);">Starts:</strong> {{ $ex->start_date ? date('d/m/Y', strtotime($ex->start_date)) : '—' }}</span>
                    <span><strong style="color:var(--text-dark);">Ends:</strong> {{ $ex->end_date ? date('d/m/Y', strtotime($ex->end_date)) : '—' }}</span>
                    <span><strong style="color:var(--text-dark);">Class:</strong> <span style="background:var(--blue-light); color:var(--blue-primary); font-weight:700; padding:2px 8px; border-radius:12px;">{{ $ex->schoolClass ? $ex->schoolClass->name : 'All Classes' }}</span></span>
                </div>
            </div>
            <div class="exam-actions-right">
                <button class="btn-action-sm" style="color:var(--blue-primary); border-color:var(--blue-secondary);" onclick="openExamSlider({{ $ex->id }}, 'marks')">UPDATE MARKS</button>
                <button class="btn-action-sm" onclick="openExamSlider({{ $ex->id }}, 'comments')">COMMENTS</button>
                <button class="btn-action-sm" onclick="openExamSlider({{ $ex->id }}, 'achievements')">ACHIEVEMENT</button>
                <button class="btn-action-sm" onclick="openExamSlider({{ $ex->id }}, 'attendance')">ATTENDANCE</button>
                
                <button class="icon-btn" title="Download Sheet"><i class="fas fa-download"></i></button>
                <button class="icon-btn" title="Edit Exam"><i class="fas fa-pencil-alt"></i></button>
                <form method="POST" action="{{ route('school.examination.exams.destroy', $ex->id) }}" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this exam?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="icon-btn" style="color:#ef4444;" title="Delete Exam"><i class="fas fa-trash"></i></button>
                </form>
                <button class="icon-btn" title="Exam Settings"><i class="fas fa-cog"></i></button>
            </div>
        </div>
    @empty
        <div style="text-align:center; padding:40px; background:#fff; border-radius:12px; border:1px solid var(--border-color); color:var(--text-muted);">
            <i class="fas fa-folder-open" style="font-size:36px; margin-bottom:12px; color:var(--blue-secondary);"></i>
            <p>No exams created yet. Click <strong>+ CREATE NEW EXAM</strong> to set up your first exam!</p>
        </div>
    @endforelse
</div>

{{-- Slide-Over Drawer Side Slider --}}
<div class="slider-overlay" id="sliderOverlay" onclick="closeExamSlider()"></div>
<div class="slider-drawer" id="sliderDrawer">
    <div class="slider-hdr">
        <div>
            <h3 style="margin:0; font-size:18px; font-weight:700;" id="sliderExamTitle">Exam Management</h3>
            <span style="font-size:12px; opacity:0.8;" id="sliderClassSubtitle">Class & Section Data</span>
        </div>
        <button type="button" style="background:none; border:none; color:#fff; font-size:24px; cursor:pointer;" onclick="closeExamSlider()">&times;</button>
    </div>

    {{-- Subject Selector inside Slider --}}
    <div style="padding:12px 24px; background:#f8fafc; border-bottom:1px solid var(--border-color); display:flex; align-items:center; gap:12px;">
        <label style="font-size:12px; font-weight:700; color:var(--text-dark); margin:0; white-space:nowrap;">Select Subject:</label>
        <select id="sliderSubjectSelect" class="form-control" style="height:38px; border-color:var(--border-color);" onchange="onSliderSubjectChange(this.value)">
            <option value="">Loading subjects...</option>
        </select>
    </div>

    {{-- Slider Tabs --}}
    <div class="slider-nav-tabs">
        <button class="slider-tab-btn active" id="tabBtn-marks" onclick="switchSliderTab('marks')">
            <i class="fas fa-edit"></i> Update Marks
        </button>
        <button class="slider-tab-btn" id="tabBtn-comments" onclick="switchSliderTab('comments')">
            <i class="fas fa-comment-dots"></i> Comments
        </button>
        <button class="slider-tab-btn" id="tabBtn-achievements" onclick="switchSliderTab('achievements')">
            <i class="fas fa-trophy"></i> Achievement
        </button>
        <button class="slider-tab-btn" id="tabBtn-attendance" onclick="switchSliderTab('attendance')">
            <i class="fas fa-user-check"></i> Attendance
        </button>
    </div>

    {{-- Slider Body Content --}}
    <div class="slider-body" id="sliderBody">
        <div style="text-align:center; padding:40px; color:var(--text-muted);">
            <i class="fas fa-spinner fa-spin" style="font-size:24px;"></i> Loading student records...
        </div>
    </div>

    {{-- Slider Footer --}}
    <div class="slider-ftr">
        <button type="button" class="btn-blue-outline" onclick="closeExamSlider()">Close</button>
        <button type="button" class="btn-blue-solid" onclick="saveSliderData()"><i class="fas fa-save"></i> Save All Changes</button>
    </div>
</div>

{{-- Create New Exam Modal --}}
<div class="modal-backdrop-custom" id="createExamModal">
    <div class="modal-box-custom">
        <div class="modal-hdr-blue">
            <h3 style="margin:0; font-size:18px; font-weight:700;"><i class="fas fa-calendar-plus" style="margin-right:8px;"></i>Create New Exam</h3>
            <button type="button" style="background:none; border:none; color:#fff; font-size:20px; cursor:pointer;" onclick="closeCreateExamModal()">&times;</button>
        </div>
        <form method="POST" action="{{ route('school.examination.exams.store') }}" style="padding:24px;">
            @csrf
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label" style="font-weight:600;">Academic Year *</label>
                    <select name="academic_year" class="form-control" required>
                        <option value="Apr 2025 - Mar 2026">Apr 2025 - Mar 2026</option>
                        <option value="Apr 2024 - Mar 2025">Apr 2024 - Mar 2025</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight:600;">Exam Name *</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Term 1, Unit Test 3" required>
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:16px;">
                <div class="form-group">
                    <label class="form-label" style="font-weight:600;">Start Date</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight:600;">End Date</label>
                    <input type="date" name="end_date" class="form-control">
                </div>
            </div>

            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-bottom:20px;">
                <div class="form-group">
                    <label class="form-label" style="font-weight:600;">Select Class *</label>
                    <select name="class_id" id="modalClassSelect" class="form-control" required onchange="onModalClassChange(this.value)">
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label" style="font-weight:600;">Select Section</label>
                    <select name="section_id" id="modalSectionSelect" class="form-control">
                        <option value="">-- All Sections --</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:20px; border-top:1px solid var(--border-color); padding-top:20px;">
                <h4 style="color:var(--blue-dark); margin-bottom:12px; font-weight:700; font-size:15px;">
                    <i class="fas fa-list-check" style="color:var(--blue-secondary); margin-right:6px;"></i> Subject Wise Exam & Passing Marks Setup
                </h4>
                <p style="font-size:12px; color:var(--text-muted); margin-bottom:14px;">
                    Subjects assigned to the selected class are loaded automatically below. Set passing marks and max marks for matching grade scale cleanly.
                </p>

                <div class="table-wrap">
                    <table class="tbl" style="width:100%; border-collapse:collapse;">
                        <thead>
                            <tr style="background:#f8fafc; text-align:left;">
                                <th style="padding:10px;">Subject Name</th>
                                <th style="padding:10px;">Exam Date</th>
                                <th style="padding:10px;">Max Marks *</th>
                                <th style="padding:10px;">Passing Marks *</th>
                                <th style="padding:10px;">Grade Scale Match</th>
                            </tr>
                        </thead>
                        <tbody id="modalSubjectsTable">
                            <tr>
                                <td colspan="5" style="text-align:center; padding:20px; color:var(--text-muted);">
                                    Please select a Class above to load class subjects.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:12px; margin-top:24px;">
                <button type="button" class="btn-blue-outline" onclick="closeCreateExamModal()">Cancel</button>
                <button type="submit" class="btn-blue-solid"><i class="fas fa-save"></i> Create Exam & Save Setup</button>
            </div>
        </form>
    </div>
</div>

<script>
    let currentExamData = null;
    let activeTab = 'marks';

    function openCreateExamModal() {
        document.getElementById('createExamModal').style.display = 'flex';
    }
    function closeCreateExamModal() {
        document.getElementById('createExamModal').style.display = 'none';
    }

    function openExamSlider(examId, defaultTab = 'marks') {
        activeTab = defaultTab;
        document.getElementById('sliderOverlay').classList.add('show');
        document.getElementById('sliderDrawer').classList.add('show');
        fetchSliderData(examId, null);
    }

    function closeExamSlider() {
        document.getElementById('sliderOverlay').classList.remove('show');
        document.getElementById('sliderDrawer').classList.remove('show');
    }

    function fetchSliderData(examId, subjectId) {
        document.getElementById('sliderBody').innerHTML = `
            <div style="text-align:center; padding:60px 20px; color:var(--text-muted);">
                <i class="fas fa-spinner fa-spin" style="font-size:32px; color:var(--blue-secondary); margin-bottom:12px;"></i>
                <p style="font-size:14px; font-weight:600;">Loading student examination details...</p>
            </div>`;

        let url = `{{ route('school.examination.get-slider-data') }}?exam_id=${examId}`;
        if (subjectId) url += `&subject_id=${subjectId}`;

        fetch(url)
            .then(res => res.json())
            .then(data => {
                currentExamData = data;
                document.getElementById('sliderExamTitle').innerText = data.exam.name;
                document.getElementById('sliderClassSubtitle').innerText = `Class: ${data.exam.school_class ? data.exam.school_class.name : 'All'} | Session: ${data.exam.academic_year}`;

                // Populate subjects dropdown
                const subSel = document.getElementById('sliderSubjectSelect');
                subSel.innerHTML = '';
                if (data.subjects.length === 0) {
                    subSel.innerHTML = '<option value="">No subjects found</option>';
                } else {
                    data.subjects.forEach(sub => {
                        const selected = sub.id == data.selected_subject_id ? 'selected' : '';
                        subSel.innerHTML += `<option value="${sub.id}" ${selected}>${sub.name} (${sub.type || 'Scholastic'})</option>`;
                    });
                }

                renderSliderTabContent();
            });
    }

    function onSliderSubjectChange(subjectId) {
        if (!currentExamData) return;
        fetchSliderData(currentExamData.exam.id, subjectId);
    }

    function switchSliderTab(tabName) {
        activeTab = tabName;
        document.querySelectorAll('.slider-tab-btn').forEach(btn => btn.classList.remove('active'));
        document.getElementById(`tabBtn-${tabName}`).classList.add('active');
        renderSliderTabContent();
    }

    function renderSliderTabContent() {
        if (!currentExamData) return;
        const students = currentExamData.students;
        const marks = currentExamData.marks;
        const body = document.getElementById('sliderBody');

        if (students.length === 0) {
            body.innerHTML = `
                <div style="text-align:center; padding:60px 20px; color:var(--text-muted);">
                    <i class="fas fa-user-slash" style="font-size:36px; color:#cbd5e1; margin-bottom:12px;"></i>
                    <p style="font-weight:600;">No active students found for this class and section.</p>
                </div>`;
            return;
        }

        let html = '';

        if (activeTab === 'marks') {
            html = `
                <table class="tbl" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc; text-align:left;">
                            <th style="padding:10px;">Roll</th>
                            <th style="padding:10px;">Student Name</th>
                            <th style="padding:10px;">Marks Obtained</th>
                            <th style="padding:10px;">Max</th>
                            <th style="padding:10px;">Grade</th>
                        </tr>
                    </thead>
                    <tbody>`;
            students.forEach(stu => {
                const m = marks[stu.id] || {};
                const obt = m.marks_obtained !== undefined && m.marks_obtained !== null ? m.marks_obtained : '';
                const max = m.max_marks || 100;
                const grd = m.grade || '—';
                html += `
                    <tr style="border-bottom:1px solid #f1f5f9;" data-student-id="${stu.id}">
                        <td style="padding:10px; color:var(--text-muted); font-weight:600;">${stu.roll_number || '—'}</td>
                        <td style="padding:10px; font-weight:700; color:var(--text-dark);">${stu.full_name}</td>
                        <td style="padding:10px;">
                            <input type="number" step="0.1" class="form-control slider-obt" value="${obt}" style="max-width:100px;" placeholder="0" oninput="liveUpdateSliderGrade(this)">
                        </td>
                        <td style="padding:10px;">
                            <input type="number" class="form-control slider-max" value="${max}" style="max-width:90px;" oninput="liveUpdateSliderGrade(this)">
                        </td>
                        <td style="padding:10px;">
                            <span class="slider-grade-badge" style="background:var(--blue-light); color:var(--blue-primary); font-weight:700; padding:4px 10px; border-radius:6px; font-size:12px;">${grd}</span>
                        </td>
                    </tr>`;
            });
            html += `</tbody></table>`;
        } else if (activeTab === 'comments') {
            html = `
                <table class="tbl" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc; text-align:left;">
                            <th style="padding:10px; width:30%;">Student Name</th>
                            <th style="padding:10px; width:70%;">Exam Teacher Remarks / Comments</th>
                        </tr>
                    </thead>
                    <tbody>`;
            students.forEach(stu => {
                const m = marks[stu.id] || {};
                const rem = m.remarks || '';
                html += `
                    <tr style="border-bottom:1px solid #f1f5f9;" data-student-id="${stu.id}">
                        <td style="padding:10px; font-weight:700; color:var(--text-dark);">${stu.full_name}</td>
                        <td style="padding:10px;">
                            <input type="text" class="form-control slider-remarks" value="${rem}" placeholder="Enter specific feedback for ${stu.first_name}...">
                        </td>
                    </tr>`;
            });
            html += `</tbody></table>`;
        } else if (activeTab === 'achievements') {
            html = `
                <table class="tbl" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc; text-align:left;">
                            <th style="padding:10px; width:30%;">Student Name</th>
                            <th style="padding:10px; width:70%;">Leadership Roles & Achievements</th>
                        </tr>
                    </thead>
                    <tbody>`;
            students.forEach(stu => {
                const m = marks[stu.id] || {};
                const ach = m.achievements || '';
                html += `
                    <tr style="border-bottom:1px solid #f1f5f9;" data-student-id="${stu.id}">
                        <td style="padding:10px; font-weight:700; color:var(--text-dark);">${stu.full_name}</td>
                        <td style="padding:10px;">
                            <input type="text" class="form-control slider-achievements" value="${ach}" placeholder="e.g. Class Monitor, Top Scorer in Quiz...">
                        </td>
                    </tr>`;
            });
            html += `</tbody></table>`;
        } else if (activeTab === 'attendance') {
            html = `
                <table class="tbl" style="width:100%; border-collapse:collapse;">
                    <thead>
                        <tr style="background:#f8fafc; text-align:left;">
                            <th style="padding:10px;">Roll</th>
                            <th style="padding:10px;">Student Name</th>
                            <th style="padding:10px; text-align:right;">Exam Attendance Status</th>
                        </tr>
                    </thead>
                    <tbody>`;
            students.forEach(stu => {
                const m = marks[stu.id] || {};
                const att = m.attendance_status || 'present';
                html += `
                    <tr style="border-bottom:1px solid #f1f5f9;" data-student-id="${stu.id}">
                        <td style="padding:10px; color:var(--text-muted); font-weight:600;">${stu.roll_number || '—'}</td>
                        <td style="padding:10px; font-weight:700; color:var(--text-dark);">${stu.full_name}</td>
                        <td style="padding:10px; text-align:right;">
                            <div class="att-toggle">
                                <input type="radio" name="att_${stu.id}" id="att_p_${stu.id}" value="present" class="att-input slider-att" ${att === 'present' ? 'checked' : ''}>
                                <label for="att_p_${stu.id}" class="att-label pres">PRESENT</label>
                                
                                <input type="radio" name="att_${stu.id}" id="att_a_${stu.id}" value="absent" class="att-input slider-att" ${att === 'absent' ? 'checked' : ''}>
                                <label for="att_a_${stu.id}" class="att-label abs">ABSENT</label>
                            </div>
                        </td>
                    </tr>`;
            });
            html += `</tbody></table>`;
        }

        body.innerHTML = html;
    }

    function liveUpdateSliderGrade(input) {
        const row = input.closest('tr');
        const obt = parseFloat(row.querySelector('.slider-obt').value);
        const max = parseFloat(row.querySelector('.slider-max').value);
        const badge = row.querySelector('.slider-grade-badge');

        if (isNaN(obt) || isNaN(max) || max <= 0) {
            badge.innerText = '—';
            return;
        }

        const pct = (obt / max) * 100;
        let grade = 'F';
        if (pct >= 90) grade = 'A+';
        else if (pct >= 80) grade = 'A';
        else if (pct >= 70) grade = 'B';
        else if (pct >= 60) grade = 'C';
        else if (pct >= 50) grade = 'D';

        badge.innerText = grade;
    }

    function saveSliderData() {
        if (!currentExamData) return;

        const subjectId = document.getElementById('sliderSubjectSelect').value;
        if (!subjectId) {
            alert('Please select a valid subject first.');
            return;
        }

        const rows = document.querySelectorAll('#sliderBody tr[data-student-id]');
        const records = [];

        rows.forEach(row => {
            const studentId = row.getAttribute('data-student-id');
            const obtEl = row.querySelector('.slider-obt');
            const maxEl = row.querySelector('.slider-max');
            const remEl = row.querySelector('.slider-remarks');
            const achEl = row.querySelector('.slider-achievements');
            const attEl = row.querySelector('.slider-att:checked');

            // Find existing record state from currentExamData or initialize
            const existing = currentExamData.marks[studentId] || {};

            records.push({
                student_id: studentId,
                marks_obtained: obtEl ? obtEl.value : (existing.marks_obtained !== undefined ? existing.marks_obtained : ''),
                max_marks: maxEl ? maxEl.value : (existing.max_marks || 100),
                remarks: remEl ? remEl.value : (existing.remarks || ''),
                achievements: achEl ? achEl.value : (existing.achievements || ''),
                attendance_status: attEl ? attEl.value : (existing.attendance_status || 'present')
            });
        });

        fetch(`{{ route('school.examination.save-slider-data') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                exam_name: currentExamData.exam.name,
                subject_id: subjectId,
                records: records
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                alert('Exam records, comments, achievements and attendance saved successfully!');
                fetchSliderData(currentExamData.exam.id, subjectId);
            } else {
                alert('Error saving exam data.');
            }
        })
        .catch(err => {
            alert('An unexpected error occurred while saving.');
        });
    }

    function loadMainSections(classId) {
        if (!classId) return;
        fetch(`{{ route('school.examination.get-class-data') }}?class_id=${classId}`)
            .then(res => res.json())
            .then(data => {
                const secSel = document.getElementById('mainSectionSelect');
                const subSel = document.getElementById('mainSubjectSelect');
                
                secSel.innerHTML = '<option value="">-- Select Section --</option>';
                data.sections.forEach(s => {
                    secSel.innerHTML += `<option value="${s.id}">${s.name}</option>`;
                });

                subSel.innerHTML = '<option value="">-- Select Subject --</option>';
                data.subjects.forEach(sub => {
                    subSel.innerHTML += `<option value="${sub.id}">${sub.name}</option>`;
                });
            });
    }

    function onModalClassChange(classId) {
        if (!classId) return;
        fetch(`{{ route('school.examination.get-class-data') }}?class_id=${classId}`)
            .then(res => res.json())
            .then(data => {
                const secSel = document.getElementById('modalSectionSelect');
                secSel.innerHTML = '<option value="">-- All Sections --</option>';
                data.sections.forEach(s => {
                    secSel.innerHTML += `<option value="${s.id}">${s.name}</option>`;
                });

                const tbody = document.getElementById('modalSubjectsTable');
                if (data.subjects.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" style="text-align:center; padding:15px; color:#ef4444;">No subjects found for this class!</td></tr>';
                    return;
                }

                tbody.innerHTML = '';
                data.subjects.forEach((sub, idx) => {
                    const maxM = sub.max_marks || 100;
                    const passM = sub.pass_marks || 33;
                    tbody.innerHTML += `
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px; font-weight:600;">
                                ${sub.name} <span style="font-size:11px; color:var(--text-muted);">(${sub.type || 'Scholastic'})</span>
                                <input type="hidden" name="subjects[${idx}][subject_id]" value="${sub.id}">
                            </td>
                            <td style="padding:10px;">
                                <input type="date" class="form-control" name="subjects[${idx}][exam_date]">
                            </td>
                            <td style="padding:10px;">
                                <input type="number" class="form-control" name="subjects[${idx}][max_marks]" value="${maxM}" required style="max-width:100px;">
                            </td>
                            <td style="padding:10px;">
                                <input type="number" class="form-control" name="subjects[${idx}][pass_marks]" value="${passM}" required style="max-width:100px;">
                            </td>
                            <td style="padding:10px;">
                                <span style="background:var(--blue-light); color:var(--blue-primary); padding:4px 8px; border-radius:6px; font-size:11px; font-weight:700;">
                                    Auto Matching Active
                                </span>
                            </td>
                        </tr>
                    `;
                });
            });
    }
</script>
@endsection
