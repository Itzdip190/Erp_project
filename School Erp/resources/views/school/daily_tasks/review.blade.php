@extends('layouts.app')

@section('page-title', 'Daily Task Teacher Review')

@section('content')
<style>
    /* =========================================================
       PURE BLUE & WHITE SYSTEM - ENTERPRISE GRADE
       100% Mobile & Touch Device Optimized
       ========================================================= */
    :root {
        --theme-royal-blue: #0038b8;
        --theme-primary-blue: #2563eb;
        --theme-hover-blue: #1d4ed8;
        --theme-light-blue: #eff6ff;
        --theme-subtle-blue: #f0f7ff;
        --theme-blue-border: #bfdbfe;
        --theme-card-border: #e2e8f0;
        --theme-card-bg: #ffffff;
        --theme-text-dark: #0f172a;
        --theme-text-muted: #64748b;
        --theme-shadow-sm: 0 2px 8px rgba(37, 99, 235, 0.05);
        --theme-shadow-md: 0 8px 24px -4px rgba(37, 99, 235, 0.1), 0 4px 10px -2px rgba(0, 0, 0, 0.03);
        --theme-shadow-lg: 0 18px 38px -6px rgba(37, 99, 235, 0.16), 0 8px 16px -4px rgba(37, 99, 235, 0.08);
    }

    body.dark-mode {
        --theme-card-bg: #131c2e;
        --theme-subtle-blue: #19253d;
        --theme-card-border: rgba(255, 255, 255, 0.1);
        --theme-text-dark: #f8fafc;
        --theme-text-muted: #94a3b8;
        --theme-light-blue: rgba(37, 99, 235, 0.15);
        --theme-blue-border: rgba(59, 130, 246, 0.35);
    }

    @keyframes dtSlideUp {
        from {
            opacity: 0;
            transform: translateY(14px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes dtPulseGlow {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(37, 99, 235, 0.35);
        }
        50% {
            box-shadow: 0 0 0 8px rgba(37, 99, 235, 0);
        }
    }

    .anim-slide-up {
        animation: dtSlideUp 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    .dt-wrapper {
        width: 100%;
        padding: 12px 16px;
        box-sizing: border-box;
    }

    @media (max-width: 576px) {
        .dt-wrapper {
            padding: 8px 10px;
        }
    }

    /* ─── PAGE HEADER BANNER ─── */
    .dt-header-banner {
        background: linear-gradient(135deg, #ffffff 0%, #f0f7ff 100%);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 18px;
        padding: 20px 24px;
        margin-bottom: 20px;
        box-shadow: var(--theme-shadow-md);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        position: relative;
        overflow: hidden;
    }

    body.dark-mode .dt-header-banner {
        background: linear-gradient(135deg, #131c2e 0%, #19253d 100%);
    }

    .dt-header-banner::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        background: linear-gradient(180deg, #2563eb 0%, #1d4ed8 100%);
    }

    .dt-header-left {
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .dt-header-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: var(--theme-primary-blue);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        border: 1.5px solid var(--theme-blue-border);
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.12);
        animation: dtPulseGlow 3s infinite;
        flex-shrink: 0;
    }

    .dt-header-title {
        font-size: 19px;
        font-weight: 800;
        color: var(--theme-text-dark);
        margin: 0;
        letter-spacing: -0.3px;
    }

    .dt-header-subtitle {
        font-size: 12.5px;
        color: var(--theme-text-muted);
        margin: 2px 0 0 0;
        font-weight: 500;
    }

    .dt-nav-tab-group {
        display: flex;
        gap: 6px;
        background: var(--theme-subtle-blue);
        padding: 5px;
        border-radius: 14px;
        border: 1.5px solid var(--theme-blue-border);
    }

    .dt-nav-tab-item {
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 13px;
        color: var(--theme-text-muted);
        text-decoration: none;
        transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .dt-nav-tab-item.active {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
    }

    .dt-nav-tab-item:not(.active):hover {
        background: #e0edff;
        color: var(--theme-primary-blue);
    }

    @media (max-width: 768px) {
        .dt-header-banner {
            flex-direction: column;
            align-items: flex-start;
            padding: 16px 18px;
            gap: 14px;
        }
        .dt-nav-tab-group {
            width: 100%;
        }
        .dt-nav-tab-item {
            flex: 1;
            justify-content: center;
        }
    }

    /* ─── FILTER CONTROL PANEL ─── */
    .dt-filter-panel {
        background: var(--theme-card-bg);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 18px;
        padding: 20px 24px;
        box-shadow: var(--theme-shadow-md);
        margin-bottom: 22px;
        width: 100%;
        box-sizing: border-box;
    }

    .dt-filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 14px;
        align-items: flex-end;
        width: 100%;
    }

    @media (min-width: 1200px) {
        .dt-filter-grid {
            grid-template-columns: 180px 240px 180px 180px minmax(180px, 1fr) auto;
        }
    }

    @media (max-width: 576px) {
        .dt-filter-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
    }

    .dt-field-group {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .dt-field-label {
        font-size: 11.5px;
        font-weight: 800;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0;
    }

    body.dark-mode .dt-field-label {
        color: #cbd5e1;
    }

    .dt-field-input-wrap {
        position: relative;
        width: 100%;
    }

    .dt-field-input {
        width: 100%;
        height: 44px;
        padding: 8px 14px;
        border-radius: 12px;
        border: 1.5px solid var(--theme-blue-border);
        background: #ffffff;
        color: var(--theme-text-dark);
        font-size: 13.5px;
        font-weight: 600;
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    body.dark-mode .dt-field-input {
        background: #131c2e;
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.15);
    }

    .dt-field-input:focus {
        border-color: var(--theme-primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.16);
    }

    /* Segmented Mode Switch Box */
    .dt-mode-switch-box {
        display: flex;
        height: 44px;
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 12px;
        overflow: hidden;
        background: var(--theme-subtle-blue);
        padding: 3px;
        box-sizing: border-box;
    }

    .dt-mode-btn {
        flex: 1;
        text-align: center;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 800;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        background: transparent;
        color: var(--theme-text-muted);
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    .dt-mode-btn.active {
        background: #2563eb;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.28);
    }

    /* Primary Royal Blue Button */
    .dt-btn-royal {
        height: 44px;
        background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
        color: #ffffff !important;
        border: none;
        padding: 0 22px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 13.5px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(37, 99, 235, 0.28);
        transition: all 0.22s cubic-bezier(0.16, 1, 0.3, 1);
        text-decoration: none;
        white-space: nowrap;
    }

    .dt-btn-royal:hover {
        background: linear-gradient(135deg, #1d4ed8 0%, #1e40af 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(37, 99, 235, 0.38);
    }

    .dt-btn-royal:active {
        transform: scale(0.98);
    }

    /* ─── QUICK PRESET BAR (SWIPEABLE ON MOBILE) ─── */
    .dt-presets-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: var(--theme-card-bg);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 16px;
        padding: 12px 18px;
        margin-bottom: 18px;
        box-shadow: var(--theme-shadow-sm);
        flex-wrap: wrap;
    }

    .dt-presets-chips {
        display: flex;
        align-items: center;
        gap: 8px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 2px;
    }

    .dt-preset-btn {
        padding: 7px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        border: 1.5px solid var(--theme-blue-border);
        background: var(--theme-light-blue);
        color: var(--theme-primary-blue);
        cursor: pointer;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.18s;
    }

    .dt-preset-btn:hover {
        background: var(--theme-primary-blue);
        color: #ffffff;
        border-color: var(--theme-primary-blue);
        transform: translateY(-1px);
    }

    @media (max-width: 640px) {
        .dt-presets-bar {
            flex-direction: column;
            align-items: stretch;
            padding: 12px 14px;
        }
        .dt-presets-chips {
            width: 100%;
        }
    }

    /* ─── STUDENT REVIEW CARD ─── */
    .dt-student-card {
        background: var(--theme-card-bg);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 18px;
        padding: 18px 22px;
        margin-bottom: 18px;
        box-shadow: var(--theme-shadow-sm);
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .dt-student-card:hover {
        border-color: var(--theme-primary-blue);
        box-shadow: var(--theme-shadow-md);
    }

    .dt-student-avatar {
        width: 48px;
        height: 48px;
        border-radius: 14px;
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        color: #2563eb;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 16px;
        flex-shrink: 0;
        border: 1.5px solid var(--theme-blue-border);
        overflow: hidden;
        position: relative;
    }

    .dt-student-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    /* ─── QUESTION EVALUATION ITEM ─── */
    .dt-question-item {
        background: var(--theme-subtle-blue);
        border: 1.5px solid var(--theme-blue-border);
        border-radius: 14px;
        padding: 14px 16px;
        margin-bottom: 12px;
        transition: background-color 0.15s;
    }

    .dt-question-item:last-child {
        margin-bottom: 0;
    }

    .dt-question-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 10px;
    }

    /* Touch-Friendly Star Rating Widget */
    .star-rating {
        display: inline-flex;
        flex-direction: row-reverse;
        gap: 8px;
        background: #ffffff;
        padding: 6px 12px;
        border-radius: 12px;
        border: 1.5px solid var(--theme-blue-border);
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.02);
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        font-size: 26px;
        color: #cbd5e1;
        cursor: pointer;
        padding: 2px 4px;
        transition: color 0.15s, transform 0.15s;
        touch-action: manipulation;
    }

    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: #f59e0b;
        transform: scale(1.18);
    }

    /* Touch Option Buttons */
    .status-opt-btn {
        padding: 8px 14px;
        border-radius: 10px;
        font-size: 12px;
        font-weight: 700;
        border: 1.5px solid var(--theme-blue-border);
        background: var(--theme-card-bg);
        color: var(--theme-text-muted);
        cursor: pointer;
        transition: all 0.15s ease;
        touch-action: manipulation;
    }

    .status-opt-btn:hover {
        background: #eff6ff;
        color: #2563eb;
    }

    .status-opt-btn.active-opt {
        background: #2563eb;
        color: #ffffff;
        border-color: #2563eb;
        box-shadow: 0 2px 8px rgba(37, 99, 235, 0.28);
    }

    /* Question Specific Remarks Input */
    .dt-remarks-input-wrap {
        position: relative;
        width: 100%;
        margin-top: 8px;
    }

    .dt-remarks-input-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 13px;
    }

    .dt-remarks-input {
        width: 100%;
        padding: 8px 12px 8px 34px;
        border-radius: 10px;
        border: 1.5px solid var(--theme-blue-border);
        background: #ffffff;
        font-size: 12.5px;
        font-weight: 500;
        color: var(--theme-text-dark);
        outline: none;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
    }

    body.dark-mode .dt-remarks-input {
        background: #131c2e;
        color: #f8fafc;
        border-color: rgba(255, 255, 255, 0.15);
    }

    .dt-remarks-input:focus {
        border-color: var(--theme-primary-blue);
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.14);
    }

    /* ─── STICKY & FLOATING SAVE BAR ─── */
    .sticky-save-bar {
        position: sticky;
        bottom: 20px;
        z-index: 990;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
        border: 1.5px solid var(--theme-blue-border);
        box-shadow: 0 12px 36px -4px rgba(37, 99, 235, 0.25), 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 14px 20px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
    }

    body.dark-mode .sticky-save-bar {
        background: rgba(19, 28, 46, 0.96);
    }

    @media (max-width: 768px) {
        .sticky-save-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            margin: 0 !important;
            border-radius: 20px 20px 0 0;
            padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 -8px 28px rgba(37, 99, 235, 0.2);
            z-index: 1050;
            flex-direction: column;
            align-items: stretch;
            border-left: none;
            border-right: none;
            border-bottom: none;
        }
        body.dark-mode .sticky-save-bar {
            background: rgba(19, 28, 46, 0.98);
        }
        .save-info-text {
            display: none;
        }
        .sticky-save-bar button {
            width: 100%;
            height: 48px;
            font-size: 15px;
            border-radius: 14px;
        }
        #evaluationContainer {
            padding-bottom: 85px;
        }
    }
</style>

<div class="dt-wrapper">
    <!-- Header Banner -->
    <div class="dt-header-banner anim-slide-up">
        <div class="dt-header-left">
            <div class="dt-header-icon">
                <i class="fas fa-clipboard-user"></i>
            </div>
            <div>
                <h4 class="dt-header-title">Daily Task Teacher Review</h4>
                <p class="dt-header-subtitle">Evaluate class students on daily rubrics, star ratings & specific teacher remarks</p>
            </div>
        </div>

        <div class="dt-nav-tab-group">
            <a href="{{ route('school.daily-tasks.review') }}" class="dt-nav-tab-item active">
                <i class="fas fa-pen-to-square"></i> Review Entry
            </a>
            <a href="{{ route('school.daily-tasks.reports') }}" class="dt-nav-tab-item">
                <i class="fas fa-chart-pie"></i> Student Reports
            </a>
        </div>
    </div>

    <!-- Filter Card: Class / Section / Mode Selector -->
    <div class="dt-filter-panel anim-slide-up">
        <div class="dt-filter-grid">
            
            <!-- 1. Evaluation Date -->
            <div class="dt-field-group">
                <label class="dt-field-label"><i class="fas fa-calendar-day text-primary me-1"></i> Evaluation Date <span class="text-danger">*</span></label>
                <div class="dt-field-input-wrap">
                    <input type="date" id="evalDate" class="dt-field-input" value="{{ $selectedDate }}">
                </div>
            </div>

            <!-- 2. Review Mode Toggle -->
            <div class="dt-field-group">
                <label class="dt-field-label"><i class="fas fa-users-gear text-primary me-1"></i> Review Mode <span class="text-danger">*</span></label>
                <div class="dt-mode-switch-box">
                    <button type="button" class="dt-mode-btn {{ $selectedReviewType === 'class_teacher' ? 'active' : '' }}" id="modeClassTeacher" onclick="setReviewMode('class_teacher')">
                        <i class="fas fa-chalkboard-user me-1"></i> Class Teacher
                    </button>
                    <button type="button" class="dt-mode-btn {{ $selectedReviewType === 'subject_teacher' ? 'active' : '' }}" id="modeSubjectTeacher" onclick="setReviewMode('subject_teacher')">
                        <i class="fas fa-book-bookmark me-1"></i> Subject Teacher
                    </button>
                </div>
            </div>

            <!-- 3. Class Select -->
            <div class="dt-field-group">
                <label class="dt-field-label"><i class="fas fa-school text-primary me-1"></i> Class <span class="text-danger">*</span></label>
                <div class="dt-field-input-wrap">
                    <select id="evalClass" class="dt-field-input" onchange="onClassChange(this.value)">
                        <option value="">-- Select Class --</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ (string)$selectedClassId === (string)$c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 4. Section Select -->
            <div class="dt-field-group">
                <label class="dt-field-label"><i class="fas fa-layer-group text-primary me-1"></i> Section <span class="text-danger">*</span></label>
                <div class="dt-field-input-wrap">
                    <select id="evalSection" class="dt-field-input" onchange="onSectionChange(this.value)">
                        <option value="">-- Select Section --</option>
                    </select>
                </div>
            </div>

            <!-- 5. Subject Select (Conditional) -->
            <div class="dt-field-group" id="subjectCol" style="display: {{ $selectedReviewType === 'subject_teacher' ? 'block' : 'none' }};">
                <label class="dt-field-label"><i class="fas fa-book-open text-primary me-1"></i> Subject <span class="text-danger">*</span></label>
                <div class="dt-field-input-wrap">
                    <select id="evalSubject" class="dt-field-input">
                        <option value="">-- Select Subject --</option>
                        @foreach($allSubjects as $sub)
                            <option value="{{ $sub->id }}" data-class-id="{{ $sub->class_id }}" {{ (string)$selectedSubjectId === (string)$sub->id ? 'selected' : '' }}>
                                {{ $sub->name }} ({{ $sub->schoolClass->name ?? 'All' }})
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 6. Load Action Button -->
            <div class="dt-field-group">
                <label class="dt-field-label d-none d-md-block">&nbsp;</label>
                <button type="button" class="dt-btn-royal w-100" onclick="loadStudentsAndQuestions()">
                    <i class="fas fa-magnifying-glass"></i> Load Sheet
                </button>
            </div>

        </div>
    </div>

    <!-- Alert placeholder -->
    <div id="ajaxAlertPlaceholder"></div>

    <!-- Loading Spinner -->
    <div id="loadingIndicator" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"></div>
        <div class="mt-3 fw-bold fs-6" style="color: var(--theme-primary-blue);">Loading class students and evaluation criteria...</div>
    </div>

    <!-- Evaluation Form Container -->
    <div id="evaluationContainer" style="display: none;" class="anim-slide-up">
        
        <!-- Quick Preset Toolbar (Swipeable on Mobile) -->
        <div class="dt-presets-bar">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="text-muted fw-bold small me-1"><i class="fas fa-wand-magic-sparkles text-primary me-1"></i> Quick Presets:</span>
                <div class="dt-presets-chips">
                    <button type="button" class="dt-preset-btn" onclick="presetAllRatings(5)">
                        <i class="fas fa-star text-warning"></i> Rate 5★ All
                    </button>
                    <button type="button" class="dt-preset-btn" onclick="presetAllRatings(4)">
                        <i class="fas fa-star text-warning"></i> Rate 4★ All
                    </button>
                    <button type="button" class="dt-preset-btn" onclick="presetAllStatus('Excellent')">
                        <i class="fas fa-thumbs-up"></i> Mark All Excellent
                    </button>
                    <button type="button" class="dt-preset-btn" onclick="presetAllStatus('Good')">
                        <i class="fas fa-check"></i> Mark All Good
                    </button>
                </div>
            </div>
            <div>
                <span id="studentCountBadge" class="badge rounded-pill px-3 py-2 fs-6 fw-bold" style="background: #eff6ff; color: #2563eb; border: 1.5px solid var(--theme-blue-border);">0 Students</span>
            </div>
        </div>

        <!-- Student Cards List -->
        <div id="studentsList"></div>

        <!-- Overall Remarks Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--theme-card-bg); border: 1.5px solid var(--theme-blue-border) !important;">
            <div class="card-body p-4">
                <label class="form-label fw-bold" style="color: var(--theme-text-dark);"><i class="fas fa-message text-primary me-2"></i> Overall Class Daily Review Note / General Teacher Feedback</label>
                <textarea id="overallRemarks" class="form-control" rows="3" placeholder="Add overall class observations, performance highlights or circular notes for parents..." style="border-radius: 12px; border: 1.5px solid var(--theme-blue-border); font-size: 13.5px;"></textarea>
            </div>
        </div>

        <!-- Sticky Floating Save Button Footer -->
        <div class="sticky-save-bar mb-5">
            <div class="save-info-text">
                <span class="text-muted small fw-bold"><i class="fas fa-info-circle text-primary me-1"></i> Reviews will be saved and reflected on student report cards & analytics.</span>
            </div>
            <button type="button" class="dt-btn-royal px-4 py-2.5 fs-6" id="saveReviewBtn" onclick="submitDailyReview()">
                <i class="fas fa-floppy-disk me-1"></i> Save Daily Task Reviews
            </button>
        </div>
    </div>
</div>

<script>
    const classesData = @json($classes);
    const teacherContext = @json($teacherContext);
    let currentReviewMode = '{{ $selectedReviewType }}';
    let loadedQuestions = [];
    let loadedStudents = [];

    function setReviewMode(mode) {
        currentReviewMode = mode;
        document.getElementById('modeClassTeacher').classList.toggle('active', mode === 'class_teacher');
        document.getElementById('modeSubjectTeacher').classList.toggle('active', mode === 'subject_teacher');
        document.getElementById('subjectCol').style.display = mode === 'subject_teacher' ? 'block' : 'none';
    }

    function onClassChange(classId) {
        const secSelect = document.getElementById('evalSection');
        secSelect.innerHTML = '<option value="">-- Select Section --</option>';

        if (!classId) return;

        const cls = classesData.find(c => String(c.id) === String(classId));
        if (cls && cls.sections && cls.sections.length > 0) {
            cls.sections.forEach(sec => {
                const opt = document.createElement('option');
                opt.value = sec.id;
                opt.textContent = sec.name;
                secSelect.appendChild(opt);
            });
            secSelect.selectedIndex = 1;
        }

        const subSelect = document.getElementById('evalSubject');
        let firstSubVal = '';
        Array.from(subSelect.options).forEach(opt => {
            if (!opt.value) return;
            const subClassId = opt.getAttribute('data-class-id');
            if (!subClassId || String(subClassId) === String(classId)) {
                opt.style.display = 'block';
                if (!firstSubVal) firstSubVal = opt.value;
            } else {
                opt.style.display = 'none';
            }
        });

        if (firstSubVal && !subSelect.value) {
            subSelect.value = firstSubVal;
        }
    }

    function onSectionChange(sectionId) {}

    function showAlert(type, message) {
        const container = document.getElementById('ajaxAlertPlaceholder');
        container.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show border-0 shadow-sm anim-slide-up" role="alert" style="border-radius: 12px; border: 1.5px solid var(--theme-blue-border);">
                <i class="fas fa-${type === 'success' ? 'check-circle text-primary' : 'exclamation-circle text-danger'} me-2"></i> ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
    }

    async function loadStudentsAndQuestions() {
        const date = document.getElementById('evalDate').value;
        const classId = document.getElementById('evalClass').value;
        let sectionId = document.getElementById('evalSection').value;
        let subjectId = document.getElementById('evalSubject').value;

        if (!classId) {
            showAlert('danger', 'Please select a Class.');
            return;
        }

        if (!sectionId) {
            const secSelect = document.getElementById('evalSection');
            if (secSelect.options.length > 1) {
                secSelect.selectedIndex = 1;
                sectionId = secSelect.value;
            }
        }

        if (currentReviewMode === 'subject_teacher' && !subjectId) {
            const subSelect = document.getElementById('evalSubject');
            const visibleOpt = Array.from(subSelect.options).find(o => o.value && o.style.display !== 'none');
            if (visibleOpt) {
                subSelect.value = visibleOpt.value;
                subjectId = visibleOpt.value;
            }
        }

        document.getElementById('loadingIndicator').style.display = 'block';
        document.getElementById('evaluationContainer').style.display = 'none';
        document.getElementById('ajaxAlertPlaceholder').innerHTML = '';

        try {
            const url = new URL("{{ route('school.daily-tasks.review.load-students') }}", window.location.origin);
            url.searchParams.append('date', date);
            url.searchParams.append('class_id', classId);
            if (sectionId) url.searchParams.append('section_id', sectionId);
            url.searchParams.append('review_type', currentReviewMode);
            if (subjectId) url.searchParams.append('subject_id', subjectId);

            const res = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await res.json();

            document.getElementById('loadingIndicator').style.display = 'none';

            if (!data.success) {
                showAlert('danger', data.message || 'Error loading evaluation sheet.');
                return;
            }

            loadedQuestions = data.questions || [];
            loadedStudents = data.students || [];

            renderEvaluationSheet(data.students, data.questions, data.evaluations || {}, data.existing_review);
            document.getElementById('evaluationContainer').style.display = 'block';

        } catch (e) {
            document.getElementById('loadingIndicator').style.display = 'none';
            showAlert('danger', 'An error occurred while loading data: ' + e.message);
        }
    }

    function renderEvaluationSheet(students, questions, evaluations, existingReview) {
        document.getElementById('studentCountBadge').textContent = `${students.length} Students`;
        document.getElementById('overallRemarks').value = existingReview ? (existingReview.overall_remarks || '') : '';

        const container = document.getElementById('studentsList');
        container.innerHTML = '';

        students.forEach((stu) => {
            const initials = ((stu.first_name ? stu.first_name[0] : '') + (stu.last_name ? stu.last_name[0] : '')).toUpperCase() || 'ST';
            const studentEvals = evaluations[stu.id] || {};

            let questionsHtml = '';

            questions.forEach(q => {
                const qEval = studentEvals[q.id] || {};
                const currentRating = qEval.rating || 0;
                const currentScore = qEval.score !== undefined ? qEval.score : '';
                const currentOption = qEval.status_option || '';
                const currentRemarks = qEval.remarks || '';

                let controlHtml = '';

                if (q.evaluation_type === 'rating') {
                    controlHtml = `
                        <div class="star-rating" data-student="${stu.id}" data-question="${q.id}">
                            ${[5, 4, 3, 2, 1].map(star => `
                                <input type="radio" id="star_${stu.id}_${q.id}_${star}" name="eval_${stu.id}_${q.id}_rating" value="${star}" ${currentRating == star ? 'checked' : ''}>
                                <label for="star_${stu.id}_${q.id}_${star}"><i class="fas fa-star"></i></label>
                            `).join('')}
                        </div>
                    `;
                } else if (q.evaluation_type === 'score') {
                    controlHtml = `
                        <div class="d-flex align-items-center gap-2" style="max-width: 140px;">
                            <input type="number" class="form-control form-control-sm text-center fw-bold eval-score-input" data-student="${stu.id}" data-question="${q.id}" value="${currentScore}" min="0" max="${q.max_score || 10}" placeholder="0 - ${q.max_score || 10}" style="border-radius: 10px; border: 1.5px solid var(--theme-blue-border); height: 38px;">
                            <span class="text-muted small fw-bold">/ ${q.max_score || 10}</span>
                        </div>
                    `;
                } else if (q.evaluation_type === 'options') {
                    const opts = ['Excellent', 'Good', 'Average', 'Needs Improvement'];
                    controlHtml = `
                        <div class="d-flex align-items-center gap-1.5 flex-wrap opt-group" data-student="${stu.id}" data-question="${q.id}">
                            ${opts.map(opt => `
                                <button type="button" class="status-opt-btn ${currentOption === opt ? 'active-opt' : ''}" onclick="selectStatusOption(this, ${stu.id}, ${q.id}, '${opt}')">${opt}</button>
                            `).join('')}
                            <input type="hidden" class="eval-option-val" data-student="${stu.id}" data-question="${q.id}" value="${currentOption}">
                        </div>
                    `;
                } else if (q.evaluation_type === 'boolean') {
                    controlHtml = `
                        <div class="d-flex align-items-center gap-2 opt-group" data-student="${stu.id}" data-question="${q.id}">
                            <button type="button" class="status-opt-btn ${currentOption === 'Yes' ? 'active-opt' : ''}" onclick="selectStatusOption(this, ${stu.id}, ${q.id}, 'Yes')"><i class="fas fa-check text-primary me-1"></i> Yes</button>
                            <button type="button" class="status-opt-btn ${currentOption === 'No' ? 'active-opt' : ''}" onclick="selectStatusOption(this, ${stu.id}, ${q.id}, 'No')"><i class="fas fa-times text-danger me-1"></i> No</button>
                            <input type="hidden" class="eval-option-val" data-student="${stu.id}" data-question="${q.id}" value="${currentOption}">
                        </div>
                    `;
                }

                questionsHtml += `
                    <div class="dt-question-item">
                        <div class="dt-question-header">
                            <div class="d-flex align-items-center gap-2">
                                ${q.head ? `<span class="badge bg-light text-primary border border-primary-subtle px-2 py-0.5" style="font-size: 11px;"><i class="fas ${q.head.icon || 'fa-tasks'} me-1"></i> ${q.head.name}</span>` : ''}
                                <span class="fw-bold" style="font-size: 13.5px; color: var(--theme-text-dark);">${q.question}</span>
                            </div>
                            <div>
                                ${controlHtml}
                            </div>
                        </div>
                        <div class="dt-remarks-input-wrap">
                            <i class="fas fa-comment-dots"></i>
                            <input type="text" class="dt-remarks-input eval-remarks-input" data-student="${stu.id}" data-question="${q.id}" value="${currentRemarks}" placeholder="Teacher specific observation / remarks for this task (e.g. Completed page 12 neatly, Needs guidance)...">
                        </div>
                    </div>
                `;
            });

            const avatarHtml = stu.photo
                ? `<img src="${stu.photo}" alt="${stu.first_name}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 14px;" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                   <span style="display:none; width: 100%; height: 100%; align-items: center; justify-content: center; font-weight: 800; font-size: 16px;">${initials}</span>`
                : `<span style="display:flex; width: 100%; height: 100%; align-items: center; justify-content: center; font-weight: 800; font-size: 16px;">${initials}</span>`;

            const cardHtml = `
                <div class="dt-student-card" id="student_card_${stu.id}">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 mb-3 border-bottom" style="border-color: var(--theme-blue-border) !important;">
                        <div class="d-flex align-items-center gap-3">
                            <div class="dt-student-avatar">
                                ${avatarHtml}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold" style="font-size: 15px; color: var(--theme-text-dark);">${stu.first_name} ${stu.last_name || ''}</h6>
                                <div class="text-muted small mt-1">
                                    <span class="badge bg-light text-primary border border-primary-subtle me-1.5">Roll: ${stu.roll_no || 'N/A'}</span>
                                    <span class="badge bg-light text-secondary border">Adm: ${stu.admission_no || 'N/A'}</span>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button type="button" class="dt-preset-btn" onclick="rateStudentAllStars(${stu.id}, 5)">
                                <i class="fas fa-star text-warning"></i> 5★ All
                            </button>
                        </div>
                    </div>
                    <div>
                        ${questionsHtml}
                    </div>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', cardHtml);
        });
    }

    function selectStatusOption(btn, studentId, questionId, optVal) {
        const group = btn.closest('.opt-group');
        group.querySelectorAll('.status-opt-btn').forEach(b => b.classList.remove('active-opt'));
        btn.classList.add('active-opt');
        group.querySelector('.eval-option-val').value = optVal;
    }

    function rateStudentAllStars(studentId, stars) {
        loadedQuestions.forEach(q => {
            if (q.evaluation_type === 'rating') {
                const radio = document.getElementById(`star_${studentId}_${q.id}_${stars}`);
                if (radio) radio.checked = true;
            }
        });
    }

    function presetAllRatings(stars) {
        loadedStudents.forEach(stu => {
            rateStudentAllStars(stu.id, stars);
        });
    }

    function presetAllStatus(optVal) {
        loadedStudents.forEach(stu => {
            loadedQuestions.forEach(q => {
                if (q.evaluation_type === 'options') {
                    const group = document.querySelector(`.opt-group[data-student="${stu.id}"][data-question="${q.id}"]`);
                    if (group) {
                        group.querySelectorAll('.status-opt-btn').forEach(b => {
                            if (b.textContent.trim() === optVal) {
                                b.classList.add('active-opt');
                            } else {
                                b.classList.remove('active-opt');
                            }
                        });
                        const hidden = group.querySelector('.eval-option-val');
                        if (hidden) hidden.value = optVal;
                    }
                }
            });
        });
    }

    async function submitDailyReview() {
        const date = document.getElementById('evalDate').value;
        const classId = document.getElementById('evalClass').value;
        const sectionId = document.getElementById('evalSection').value;
        const subjectId = document.getElementById('evalSubject').value;
        const overallRemarks = document.getElementById('overallRemarks').value;

        const saveBtn = document.getElementById('saveReviewBtn');
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Saving Reviews...';

        const evaluations = {};

        loadedStudents.forEach(stu => {
            evaluations[stu.id] = {};

            loadedQuestions.forEach(q => {
                let rating = null;
                let score = null;
                let statusOption = null;

                if (q.evaluation_type === 'rating') {
                    const checkedRadio = document.querySelector(`input[name="eval_${stu.id}_${q.id}_rating"]:checked`);
                    if (checkedRadio) rating = checkedRadio.value;
                } else if (q.evaluation_type === 'score') {
                    const scoreInput = document.querySelector(`.eval-score-input[data-student="${stu.id}"][data-question="${q.id}"]`);
                    if (scoreInput && scoreInput.value !== '') score = scoreInput.value;
                } else if (q.evaluation_type === 'options' || q.evaluation_type === 'boolean') {
                    const optInput = document.querySelector(`.eval-option-val[data-student="${stu.id}"][data-question="${q.id}"]`);
                    if (optInput && optInput.value !== '') statusOption = optInput.value;
                }

                const remarksInput = document.querySelector(`.eval-remarks-input[data-student="${stu.id}"][data-question="${q.id}"]`);
                const remarks = remarksInput ? remarksInput.value : '';

                evaluations[stu.id][q.id] = {
                    rating: rating,
                    score: score,
                    status_option: statusOption,
                    remarks: remarks,
                };
            });
        });

        try {
            const res = await fetch("{{ route('school.daily-tasks.review.save') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    date: date,
                    class_id: classId,
                    section_id: sectionId,
                    review_type: currentReviewMode,
                    subject_id: subjectId || null,
                    overall_remarks: overallRemarks,
                    evaluations: evaluations,
                }),
            });

            const data = await res.json();
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-floppy-disk me-1"></i> Save Daily Task Reviews';

            if (data.success) {
                showAlert('success', data.message || 'Daily Task Reviews saved successfully!');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                showAlert('danger', data.message || 'Failed to save reviews.');
            }
        } catch (e) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = '<i class="fas fa-floppy-disk me-1"></i> Save Daily Task Reviews';
            showAlert('danger', 'Error saving reviews: ' + e.message);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        const classSelect = document.getElementById('evalClass');
        if (classSelect && classSelect.options.length > 1 && !classSelect.value) {
            classSelect.selectedIndex = 1;
            onClassChange(classSelect.value);
        } else if (classSelect && classSelect.value) {
            onClassChange(classSelect.value);
        }
    });
</script>
@endsection
