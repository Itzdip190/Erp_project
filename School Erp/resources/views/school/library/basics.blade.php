@extends('layouts.app')

@section('title', 'Library Basics - Library Management')
@section('page-title', 'Library Basics')

@section('content')
<style>
    /* ==========================================================================
       ROYAL COBALT BLUE LUXURY ERP THEME & HEAVY ANIMATIONS (Image 1 & Image 3)
       ========================================================================== */
    :root {
        --lib-blue-primary: #0038b8;
        --lib-blue-deep: #002266;
        --lib-blue-midnight: #001233;
        --lib-blue-gradient: linear-gradient(135deg, #002266 0%, #0038b8 55%, #1d4ed8 100%);
        --lib-blue-light: #eff6ff;
        --lib-blue-border: #bfdbfe;
        --lib-blue-glow: rgba(0, 56, 184, 0.25);
        --lib-accent-gold: #d97706;
        --lib-accent-emerald: #10b981;
        --lib-border-card: #e2e8f0;
    }

    /* Page Entrance Animations */
    @keyframes pageSlideFade {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulseGlowRing {
        0%, 100% {
            box-shadow: 0 0 0 0 rgba(0, 56, 184, 0.4), 0 16px 36px rgba(0, 56, 184, 0.28);
        }
        50% {
            box-shadow: 0 0 0 10px rgba(0, 56, 184, 0), 0 20px 42px rgba(0, 56, 184, 0.4);
        }
    }

    @keyframes neonBadgePulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
        }
        50% {
            opacity: 0.7;
            transform: scale(1.15);
        }
    }

    @keyframes shimmerSweep {
        0% {
            background-position: -200% 0;
        }
        100% {
            background-position: 200% 0;
        }
    }

    @keyframes floatBar {
        0%, 100% {
            transform: translate(-50%, 0);
        }
        50% {
            transform: translate(-50%, -4px);
        }
    }

    .lib-page-wrapper {
        animation: pageSlideFade 0.45s cubic-bezier(0.16, 1, 0.3, 1) both;
        padding-bottom: 100px; /* Space for bottom floating bar */
    }

    /* =========================================================================
       1. ROYAL BLUE LIVE CALCULATOR / SIMULATOR (Positioned ABOVE with Heavy Animations)
       ========================================================================= */
    .simulator-hero-card {
        background: linear-gradient(135deg, #001233 0%, #002266 32%, #0038b8 72%, #1d4ed8 100%);
        border: 1.5px solid rgba(147, 197, 253, 0.35);
        border-radius: 18px;
        color: #ffffff;
        padding: 24px 28px;
        margin-bottom: 28px;
        box-shadow: 0 18px 45px -8px rgba(0, 56, 184, 0.35), inset 0 1px 0 rgba(255, 255, 255, 0.25);
        position: relative;
        overflow: hidden;
        animation: pulseGlowRing 4s infinite ease-in-out;
        transition: all 0.3s ease;
    }

    .simulator-hero-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.12) 0%, transparent 50%);
        pointer-events: none;
    }

    .sim-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 20px;
        position: relative;
        z-index: 2;
    }

    .sim-header-title {
        font-size: 17px;
        font-weight: 800;
        letter-spacing: -0.3px;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
        margin: 0;
    }

    .sim-live-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.3);
        backdrop-filter: blur(6px);
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 11.5px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        color: #ffffff;
    }

    .sim-live-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 8px #10b981;
        animation: neonBadgePulse 1.8s infinite ease-in-out;
    }

    /* 4-Column Simulator Control Deck */
    .sim-control-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 16px;
        position: relative;
        z-index: 2;
        margin-bottom: 20px;
    }

    @media (max-width: 992px) {
        .sim-control-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .sim-control-grid {
            grid-template-columns: 1fr;
        }
    }

    .sim-control-card {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.22);
        border-radius: 12px;
        padding: 12px 14px;
        backdrop-filter: blur(10px);
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .sim-control-card:hover {
        background: rgba(255, 255, 255, 0.16);
        border-color: rgba(255, 255, 255, 0.4);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    .sim-field-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: rgba(255, 255, 255, 0.75);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .sim-native-input {
        background: rgba(255, 255, 255, 0.14);
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 8px;
        color: #ffffff;
        font-size: 13.5px;
        font-weight: 700;
        padding: 7px 10px;
        width: 100%;
        outline: none;
        transition: all 0.2s ease;
    }

    .sim-native-input:focus {
        background: rgba(255, 255, 255, 0.24);
        border-color: #93c5fd;
        box-shadow: 0 0 0 3px rgba(147, 197, 253, 0.3);
    }

    .sim-native-input option {
        background: #0f172a;
        color: #ffffff;
    }

    /* Live Output Results Deck */
    .sim-results-banner {
        background: rgba(0, 18, 51, 0.65);
        border: 1.5px solid rgba(255, 255, 255, 0.25);
        border-radius: 14px;
        padding: 16px 22px;
        backdrop-filter: blur(12px);
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        position: relative;
        z-index: 2;
    }

    .sim-rule-pill-display {
        font-size: 13.5px;
        color: rgba(255, 255, 255, 0.95);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .sim-metric-group {
        display: flex;
        align-items: center;
        gap: 28px;
    }

    .sim-metric-item {
        text-align: right;
    }

    .sim-metric-label {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: rgba(255, 255, 255, 0.65);
        margin-bottom: 2px;
    }

    .sim-metric-val {
        font-size: 17px;
        font-weight: 800;
        color: #fde047;
        letter-spacing: -0.3px;
        transition: transform 0.2s ease;
    }

    .sim-metric-total {
        font-size: 24px;
        font-weight: 900;
        color: #86efac;
        letter-spacing: -0.5px;
        text-shadow: 0 0 12px rgba(134, 239, 172, 0.4);
    }

    /* =========================================================================
       2. LIBRARY RULES CARD (Exact Wireframe Reproduction from Image 1)
       ========================================================================= */
    .lib-rules-card {
        background: #ffffff;
        border: 1px solid var(--lib-border-card);
        border-radius: 16px;
        box-shadow: 0 6px 24px rgba(0, 56, 184, 0.05), 0 1px 3px rgba(0, 0, 0, 0.02);
        padding: 34px 40px;
        margin-bottom: 24px;
    }

    .lib-card-title {
        font-size: 21px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 28px;
        letter-spacing: -0.3px;
    }

    /* Role Headings */
    .lib-role-heading {
        font-size: 15.5px;
        font-weight: 800;
        color: #1e293b;
        margin-top: 10px;
        margin-bottom: 18px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .lib-role-heading::before {
        content: '';
        display: inline-block;
        width: 4px;
        height: 16px;
        background: var(--lib-blue-primary);
        border-radius: 2px;
    }

    .lib-role-heading.staff-heading::before {
        background: #059669;
    }

    /* Form Rows Alignment */
    .lib-rule-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        min-height: 52px;
        gap: 20px;
    }

    .lib-rule-label {
        font-size: 14px;
        font-weight: 600;
        color: #334155;
        min-width: 320px;
        flex: 1;
    }

    .lib-rule-control {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 16px;
        width: 380px;
    }

    /* Underline Inputs */
    .lib-underline-input-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
        width: 100%;
    }

    .lib-underline-input {
        border: none;
        border-bottom: 1.5px solid #94a3b8;
        background: transparent;
        font-size: 14.5px;
        font-weight: 700;
        color: #0f172a;
        width: 220px;
        padding: 4px 8px;
        outline: none;
        transition: all 0.2s ease;
    }

    .lib-underline-input:focus {
        border-bottom-color: var(--lib-blue-primary);
        box-shadow: 0 2px 0 var(--lib-blue-primary);
        background: rgba(0, 56, 184, 0.02);
    }

    .lib-unit-text {
        font-size: 13.5px;
        font-weight: 600;
        color: #475569;
        min-width: 50px;
    }

    /* Boxed Inputs (Image 1 Style) */
    .lib-box-input-group {
        position: relative;
        display: flex;
        flex-direction: column;
        width: 200px;
    }

    .lib-box-input-group label {
        position: absolute;
        top: -8px;
        left: 12px;
        background: #ffffff;
        padding: 0 6px;
        font-size: 11px;
        font-weight: 700;
        color: #475569;
        z-index: 2;
    }

    .lib-box-input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .lib-box-input-wrapper .currency-symbol {
        position: absolute;
        left: 12px;
        font-size: 13.5px;
        font-weight: 700;
        color: #64748b;
        pointer-events: none;
    }

    .lib-box-input {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 8px 12px 8px 28px;
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        width: 100%;
        background: #ffffff;
        outline: none;
        transition: all 0.2s ease;
    }

    .lib-box-input:focus {
        border-color: var(--lib-blue-primary);
        box-shadow: 0 0 0 3px rgba(0, 56, 184, 0.12);
    }

    /* Dropdowns */
    .lib-dropdown-select {
        border: 1px solid #fed7aa;
        border-radius: 8px;
        padding: 8px 30px 8px 12px;
        font-size: 13px;
        font-weight: 700;
        color: #c2410c;
        background: #fff7ed url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ea580c' stroke-linecap='round' stroke-linejoin='round' stroke-width='2.2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") no-repeat right 10px center/11px;
        appearance: none;
        outline: none;
        cursor: pointer;
        min-width: 140px;
        transition: all 0.2s ease;
    }

    .lib-dropdown-select:focus {
        border-color: #ea580c;
        box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.18);
    }

    .lib-divider {
        border-top: 1px solid #f1f5f9;
        margin: 26px 0 20px 0;
    }

    /* =========================================================================
       3. FLOATING STICKY BOTTOM SAVE ACTION BAR (With Heavy Animations)
       ========================================================================= */
    .lib-floating-bar {
        position: fixed;
        bottom: 24px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1050;
        background: rgba(15, 23, 42, 0.92);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 50px;
        padding: 8px 12px 8px 22px;
        box-shadow: 0 20px 45px rgba(0, 0, 0, 0.35), 0 0 25px rgba(0, 56, 184, 0.3);
        display: flex;
        align-items: center;
        gap: 20px;
        animation: floatBar 4s infinite ease-in-out;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .lib-floating-bar:hover {
        background: rgba(15, 23, 42, 0.98);
        box-shadow: 0 24px 50px rgba(0, 0, 0, 0.45), 0 0 35px rgba(0, 56, 184, 0.45);
    }

    .lib-floating-info {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff;
        font-size: 13.5px;
        font-weight: 600;
    }

    .lib-sync-icon {
        color: #10b981;
        font-size: 14px;
    }

    .lib-btn-float-save {
        background: var(--lib-blue-gradient);
        color: #ffffff;
        font-size: 14px;
        font-weight: 800;
        padding: 10px 26px;
        border-radius: 40px;
        border: none;
        box-shadow: 0 4px 16px rgba(0, 56, 184, 0.5);
        display: inline-flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .lib-btn-float-save:hover {
        transform: translateY(-2px) scale(1.03);
        box-shadow: 0 8px 24px rgba(0, 56, 184, 0.7);
        color: #ffffff;
    }

    .lib-btn-float-save:active {
        transform: translateY(0) scale(1);
    }

    /* Shortcut badge */
    .shortcut-badge {
        background: rgba(255, 255, 255, 0.18);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 6px;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 7px;
        color: rgba(255, 255, 255, 0.85);
    }

    /* Toast Notification */
    .lib-toast {
        position: fixed;
        bottom: 90px;
        right: 28px;
        z-index: 9999;
        background: #0f172a;
        color: #ffffff;
        border-left: 4px solid #10b981;
        padding: 14px 22px;
        border-radius: 10px;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13.5px;
        font-weight: 600;
        opacity: 0;
        transform: translateY(20px);
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        pointer-events: none;
    }

    .lib-toast.show {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }
</style>

<div class="container-fluid px-0 lib-page-wrapper">
    <!-- =========================================================================
         1. ROYAL COBALT BLUE SIMULATOR (POSITIONED ABOVE WITH HEAVY ANIMATIONS)
         ========================================================================= -->
    <div class="simulator-hero-card">
        <div class="sim-header-row">
            <h5 class="sim-header-title">
                <i class="fas fa-bolt text-warning"></i> Live Connected Borrow & Late Fine Simulator
            </h5>
            <div class="sim-live-badge">
                <span class="sim-live-dot"></span> Live Engine Synchronized
            </div>
        </div>

        <!-- 4 Columns Control Deck -->
        <div class="sim-control-grid">
            <div class="sim-control-card">
                <div class="sim-field-label"><i class="fas fa-user-tag text-info"></i> Member Type</div>
                <select id="simMemberType" class="sim-native-input" onchange="runLiveCalculation()">
                    <option value="student">👨‍🎓 Student Member</option>
                    <option value="staff">👨‍🏫 Staff Member</option>
                </select>
            </div>

            <div class="sim-control-card">
                <div class="sim-field-label"><i class="fas fa-calendar-days text-warning"></i> Days Overdue</div>
                <input type="number" id="simDaysLate" class="sim-native-input" value="4" min="0" max="365" oninput="runLiveCalculation()">
            </div>

            <div class="sim-control-card">
                <div class="sim-field-label"><i class="fas fa-shield-heart text-success"></i> Return Condition</div>
                <select id="simCondition" class="sim-native-input" onchange="runLiveCalculation()">
                    <option value="returned">✅ Returned (Good / Normal)</option>
                    <option value="damaged">⚠️ Damaged Book</option>
                    <option value="lost">❌ Lost Book</option>
                </select>
            </div>

            <div class="sim-control-card">
                <div class="sim-field-label"><i class="fas fa-indian-rupee-sign text-light"></i> Book Price (₹)</div>
                <input type="number" id="simBookPrice" class="sim-native-input" value="500" min="0" oninput="runLiveCalculation()">
            </div>
        </div>

        <!-- Real-Time Calculation Results Banner -->
        <div class="sim-results-banner">
            <div class="sim-rule-pill-display">
                <i class="fas fa-circle-info text-info me-1"></i>
                <span id="simPolicyText">Student Policy: 14 Days • Max 3 Books • ₹5.00/day</span>
            </div>
            <div class="sim-metric-group">
                <div class="sim-metric-item">
                    <div class="sim-metric-label">Late Fine</div>
                    <div class="sim-metric-val" id="simLateFineText">₹ 20.00</div>
                </div>
                <div class="sim-metric-item">
                    <div class="sim-metric-label">Condition Fine</div>
                    <div class="sim-metric-val" id="simConditionFineText">₹ 0.00</div>
                </div>
                <div class="sim-metric-item ps-3 border-start border-white-50">
                    <div class="sim-metric-label" style="color: #86efac;">Total Fine Due</div>
                    <div class="sim-metric-total" id="simTotalFineText">₹ 20.00</div>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================================
         2. LIBRARY RULES CARD (Exact Wireframe Reproduction from Image 1)
         ========================================================================= -->
    <div class="lib-rules-card">
        <h4 class="lib-card-title">Library Rules</h4>

        <form id="libraryRulesForm">
            @csrf

            <!-- 1. STUDENT RULES -->
            <div class="lib-role-heading">Student</div>

            <!-- Student: Borrow Period -->
            <div class="lib-rule-row">
                <div class="lib-rule-label">Borrow Period</div>
                <div class="lib-rule-control">
                    <div class="lib-underline-input-wrapper">
                        <input type="number" id="inp_student_period" name="student_borrow_period" class="lib-underline-input" value="{{ $studentRule->borrow_period_days ?? 14 }}" min="1" max="365" required oninput="runLiveCalculation()">
                        <span class="lib-unit-text">Days</span>
                    </div>
                </div>
            </div>

            <!-- Student: Maximum Books Allowed -->
            <div class="lib-rule-row">
                <div class="lib-rule-label">Maximum Books Allowed per student / staff</div>
                <div class="lib-rule-control">
                    <div class="lib-underline-input-wrapper">
                        <input type="number" id="inp_student_max_books" name="student_max_books" class="lib-underline-input" value="{{ $studentRule->max_books_allowed ?? 3 }}" min="1" max="50" required oninput="runLiveCalculation()">
                        <span class="lib-unit-text">Books</span>
                    </div>
                </div>
            </div>

            <!-- Student: Late Fine -->
            <div class="lib-rule-row">
                <div class="lib-rule-label">Late Fine</div>
                <div class="lib-rule-control">
                    <div class="lib-box-input-group">
                        <label>Late Fine</label>
                        <div class="lib-box-input-wrapper">
                            <span class="currency-symbol">₹</span>
                            <input type="number" step="0.5" id="inp_student_late_fine" name="student_late_fine" class="lib-box-input" value="{{ $studentRule->late_fine_amount ?? 5 }}" min="0" required oninput="runLiveCalculation()">
                        </div>
                    </div>
                    <select id="inp_student_late_fine_type" name="student_late_fine_type" class="lib-dropdown-select" onchange="runLiveCalculation()">
                        <option value="per_day" {{ ($studentRule->late_fine_type ?? 'per_day') == 'per_day' ? 'selected' : '' }}>Per Day</option>
                        <option value="fixed_amount" {{ ($studentRule->late_fine_type ?? '') == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="per_week" {{ ($studentRule->late_fine_type ?? '') == 'per_week' ? 'selected' : '' }}>Per Week</option>
                    </select>
                </div>
            </div>

            <!-- Student: Lost Book Fine -->
            <div class="lib-rule-row">
                <div class="lib-rule-label">Lost Book Fine</div>
                <div class="lib-rule-control">
                    <div class="lib-box-input-group">
                        <label>Lost Book Fine</label>
                        <div class="lib-box-input-wrapper">
                            <span class="currency-symbol">₹</span>
                            <input type="number" step="1" id="inp_student_lost_fine" name="student_lost_fine" class="lib-box-input" value="{{ $studentRule->lost_book_fine_amount ?? 200 }}" min="0" required oninput="runLiveCalculation()">
                        </div>
                    </div>
                    <select id="inp_student_lost_fine_type" name="student_lost_fine_type" class="lib-dropdown-select" onchange="runLiveCalculation()">
                        <option value="fixed_amount" {{ ($studentRule->lost_book_fine_type ?? 'fixed_amount') == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="percentage_price" {{ ($studentRule->lost_book_fine_type ?? '') == 'percentage_price' ? 'selected' : '' }}>% of Book Price</option>
                        <option value="full_price_plus_fee" {{ ($studentRule->lost_book_fine_type ?? '') == 'full_price_plus_fee' ? 'selected' : '' }}>Price + Fee</option>
                    </select>
                </div>
            </div>

            <!-- Student: Damaged Book Fine -->
            <div class="lib-rule-row">
                <div class="lib-rule-label">Damaged Book Fine</div>
                <div class="lib-rule-control">
                    <div class="lib-box-input-group">
                        <label>Damaged Book Fine</label>
                        <div class="lib-box-input-wrapper">
                            <span class="currency-symbol">₹</span>
                            <input type="number" step="1" id="inp_student_damaged_fine" name="student_damaged_fine" class="lib-box-input" value="{{ $studentRule->damaged_book_fine_amount ?? 100 }}" min="0" required oninput="runLiveCalculation()">
                        </div>
                    </div>
                    <select id="inp_student_damaged_fine_type" name="student_damaged_fine_type" class="lib-dropdown-select" onchange="runLiveCalculation()">
                        <option value="fixed_amount" {{ ($studentRule->damaged_book_fine_type ?? 'fixed_amount') == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="percentage_price" {{ ($studentRule->damaged_book_fine_type ?? '') == 'percentage_price' ? 'selected' : '' }}>% of Book Price</option>
                    </select>
                </div>
            </div>

            <div class="lib-divider"></div>

            <!-- 2. STAFF RULES -->
            <div class="lib-role-heading staff-heading">Staff</div>

            <!-- Staff: Borrow Period -->
            <div class="lib-rule-row">
                <div class="lib-rule-label">Borrow Period</div>
                <div class="lib-rule-control">
                    <div class="lib-underline-input-wrapper">
                        <input type="number" id="inp_staff_period" name="staff_borrow_period" class="lib-underline-input" value="{{ $staffRule->borrow_period_days ?? 30 }}" min="1" max="365" required oninput="runLiveCalculation()">
                        <span class="lib-unit-text">Days</span>
                    </div>
                </div>
            </div>

            <!-- Staff: Maximum Books Allowed -->
            <div class="lib-rule-row">
                <div class="lib-rule-label">Maximum Books Allowed per student / staff</div>
                <div class="lib-rule-control">
                    <div class="lib-underline-input-wrapper">
                        <input type="number" id="inp_staff_max_books" name="staff_max_books" class="lib-underline-input" value="{{ $staffRule->max_books_allowed ?? 5 }}" min="1" max="100" required oninput="runLiveCalculation()">
                        <span class="lib-unit-text">Books</span>
                    </div>
                </div>
            </div>

            <!-- Staff: Late Fine -->
            <div class="lib-rule-row">
                <div class="lib-rule-label">Late Fine</div>
                <div class="lib-rule-control">
                    <div class="lib-box-input-group">
                        <label>Late Fine</label>
                        <div class="lib-box-input-wrapper">
                            <span class="currency-symbol">₹</span>
                            <input type="number" step="0.5" id="inp_staff_late_fine" name="staff_late_fine" class="lib-box-input" value="{{ $staffRule->late_fine_amount ?? 2 }}" min="0" required oninput="runLiveCalculation()">
                        </div>
                    </div>
                    <select id="inp_staff_late_fine_type" name="staff_late_fine_type" class="lib-dropdown-select" onchange="runLiveCalculation()">
                        <option value="per_day" {{ ($staffRule->late_fine_type ?? 'per_day') == 'per_day' ? 'selected' : '' }}>Per Day</option>
                        <option value="fixed_amount" {{ ($staffRule->late_fine_type ?? '') == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="per_week" {{ ($staffRule->late_fine_type ?? '') == 'per_week' ? 'selected' : '' }}>Per Week</option>
                    </select>
                </div>
            </div>

            <!-- Staff: Lost Book Fine -->
            <div class="lib-rule-row">
                <div class="lib-rule-label">Lost Book Fine</div>
                <div class="lib-rule-control">
                    <div class="lib-box-input-group">
                        <label>Lost Book Fine</label>
                        <div class="lib-box-input-wrapper">
                            <span class="currency-symbol">₹</span>
                            <input type="number" step="1" id="inp_staff_lost_fine" name="staff_lost_fine" class="lib-box-input" value="{{ $staffRule->lost_book_fine_amount ?? 300 }}" min="0" required oninput="runLiveCalculation()">
                        </div>
                    </div>
                    <select id="inp_staff_lost_fine_type" name="staff_lost_fine_type" class="lib-dropdown-select" onchange="runLiveCalculation()">
                        <option value="fixed_amount" {{ ($staffRule->lost_book_fine_type ?? 'fixed_amount') == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="percentage_price" {{ ($staffRule->lost_book_fine_type ?? '') == 'percentage_price' ? 'selected' : '' }}>% of Book Price</option>
                        <option value="full_price_plus_fee" {{ ($staffRule->lost_book_fine_type ?? '') == 'full_price_plus_fee' ? 'selected' : '' }}>Price + Fee</option>
                    </select>
                </div>
            </div>

            <!-- Staff: Damaged Book Fine -->
            <div class="lib-rule-row">
                <div class="lib-rule-label">Damaged Book Fine</div>
                <div class="lib-rule-control">
                    <div class="lib-box-input-group">
                        <label>Damaged Book Fine</label>
                        <div class="lib-box-input-wrapper">
                            <span class="currency-symbol">₹</span>
                            <input type="number" step="1" id="inp_staff_damaged_fine" name="staff_damaged_fine" class="lib-box-input" value="{{ $staffRule->damaged_book_fine_amount ?? 150 }}" min="0" required oninput="runLiveCalculation()">
                        </div>
                    </div>
                    <select id="inp_staff_damaged_fine_type" name="staff_damaged_fine_type" class="lib-dropdown-select" onchange="runLiveCalculation()">
                        <option value="fixed_amount" {{ ($staffRule->damaged_book_fine_type ?? 'fixed_amount') == 'fixed_amount' ? 'selected' : '' }}>Fixed Amount</option>
                        <option value="percentage_price" {{ ($staffRule->damaged_book_fine_type ?? '') == 'percentage_price' ? 'selected' : '' }}>% of Book Price</option>
                    </select>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- =========================================================================
     3. FLOATING SAVE ACTION BAR (STICKY BOTTOM WITH HEAVY ANIMATIONS)
     ========================================================================= -->
<div class="lib-floating-bar" id="libFloatingBar">
    <div class="lib-floating-info">
        <i class="fas fa-circle-check lib-sync-icon"></i>
        <span>Ready to save rules</span>
        <span class="shortcut-badge d-none d-md-inline">Ctrl + S</span>
    </div>
    <button type="button" class="lib-btn-float-save" id="btnFloatingSave" onclick="submitRulesForm()">
        <i class="fas fa-floppy-disk"></i> Save Library Rules
    </button>
</div>

<!-- Dynamic Toast Notification -->
<div id="libToast" class="lib-toast">
    <i class="fas fa-circle-check text-success fa-lg"></i>
    <span id="libToastMsg">Changes saved successfully!</span>
</div>

<!-- =========================================================================
     JAVASCRIPT: REAL-TIME SIMULATOR & FLOATING SAVE ENGINE
     ========================================================================= -->
<script>
    function showToast(msg, isError = false) {
        const toast = document.getElementById('libToast');
        const toastMsg = document.getElementById('libToastMsg');
        toastMsg.innerText = msg;
        if (isError) {
            toast.style.borderLeftColor = '#ef4444';
            toast.querySelector('i').className = 'fas fa-circle-xmark text-danger fa-lg';
        } else {
            toast.style.borderLeftColor = '#10b981';
            toast.querySelector('i').className = 'fas fa-circle-check text-success fa-lg';
        }
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3500);
    }

    // Submit rules form via floating button
    function submitRulesForm() {
        const form = document.getElementById('libraryRulesForm');
        const btn = document.getElementById('btnFloatingSave');
        const origHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        btn.disabled = true;

        const formData = new FormData(form);

        fetch('{{ route("school.library.basics.save-rules") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = origHTML;
            btn.disabled = false;
            if (data.success) {
                showToast(data.message || 'Library rules saved successfully!');
                runLiveCalculation();
            } else {
                showToast(data.message || 'Failed to save rules.', true);
            }
        })
        .catch(err => {
            btn.innerHTML = origHTML;
            btn.disabled = false;
            showToast('Error communicating with the server.', true);
        });
    }

    // Also support native form submit
    document.getElementById('libraryRulesForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitRulesForm();
    });

    // Support Ctrl + S keyboard shortcut
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            submitRulesForm();
        }
    });

    // Real-Time Calculation Engine
    function runLiveCalculation() {
        const memberType = document.getElementById('simMemberType').value;
        const daysLate = parseFloat(document.getElementById('simDaysLate').value) || 0;
        const condition = document.getElementById('simCondition').value;
        const bookPrice = parseFloat(document.getElementById('simBookPrice').value) || 0;

        const isStudent = (memberType === 'student');
        const period = isStudent ? (document.getElementById('inp_student_period').value || 14) : (document.getElementById('inp_staff_period').value || 30);
        const maxBooks = isStudent ? (document.getElementById('inp_student_max_books').value || 3) : (document.getElementById('inp_staff_max_books').value || 5);
        const lateFineRate = parseFloat(isStudent ? document.getElementById('inp_student_late_fine').value : document.getElementById('inp_staff_late_fine').value) || 0;
        const lateFineType = isStudent ? document.getElementById('inp_student_late_fine_type').value : document.getElementById('inp_staff_late_fine_type').value;

        const lostFineRate = parseFloat(isStudent ? document.getElementById('inp_student_lost_fine').value : document.getElementById('inp_staff_lost_fine').value) || 0;
        const lostFineType = isStudent ? document.getElementById('inp_student_lost_fine_type').value : document.getElementById('inp_staff_lost_fine_type').value;

        const damagedFineRate = parseFloat(isStudent ? document.getElementById('inp_student_damaged_fine').value : document.getElementById('inp_staff_damaged_fine').value) || 0;
        const damagedFineType = isStudent ? document.getElementById('inp_student_damaged_fine_type').value : document.getElementById('inp_staff_damaged_fine_type').value;

        // Calculate Late Fine
        let lateFine = 0.00;
        if (daysLate > 0) {
            if (lateFineType === 'per_day') {
                lateFine = daysLate * lateFineRate;
            } else if (lateFineType === 'per_week') {
                lateFine = Math.ceil(daysLate / 7) * lateFineRate;
            } else {
                lateFine = lateFineRate;
            }
        }

        // Calculate Condition Fine
        let conditionFine = 0.00;
        if (condition === 'lost') {
            if (lostFineType === 'percentage_price') {
                conditionFine = (bookPrice * lostFineRate) / 100.0;
            } else if (lostFineType === 'full_price_plus_fee') {
                conditionFine = bookPrice + lostFineRate;
            } else {
                conditionFine = lostFineRate;
            }
        } else if (condition === 'damaged') {
            if (damagedFineType === 'percentage_price') {
                conditionFine = (bookPrice * damagedFineRate) / 100.0;
            } else {
                conditionFine = damagedFineRate;
            }
        }

        const totalFine = (lateFine + conditionFine).toFixed(2);

        // Update Simulator Display
        document.getElementById('simPolicyText').innerHTML = 
            `<strong>${memberType.toUpperCase()} Policy:</strong> ${period} Days Loan • Max ${maxBooks} Books • Rate: ₹${lateFineRate.toFixed(2)} (${lateFineType.replace('_', ' ')})`;
        document.getElementById('simLateFineText').innerText = `₹ ${lateFine.toFixed(2)}`;
        document.getElementById('simConditionFineText').innerText = `₹ ${conditionFine.toFixed(2)}`;
        document.getElementById('simTotalFineText').innerText = `₹ ${totalFine}`;
    }

    // Run initial live calculation
    document.addEventListener('DOMContentLoaded', function() {
        runLiveCalculation();
    });
</script>
@endsection
