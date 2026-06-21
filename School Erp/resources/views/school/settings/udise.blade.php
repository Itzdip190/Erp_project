@extends('layouts.app')

@section('title', 'UDISE Info')
@section('page-title', 'UDISE Info')

@section('styles')
<style>
    /* ═══════════════════════════════════════════════════════════════
       UDISE DASHBOARD — Scoped Styling
       Theme: Royal Blue, Slate Grey, and Clean White
       ═══════════════════════════════════════════════════════════════ */
    :root {
        --udise-primary: #0B5394;
        --udise-dark: #0D3B5C;
        --udise-light: #E8F1FA;
        --udise-accent: #B5862B;
        --udise-white: #FFFFFF;
        --udise-border: #DCE3EA;
        --udise-bg: #F8FAF8;
        --udise-text: #1E293B;
        --udise-text-muted: #64748B;
    }

    .udise-container {
        max-width: 1200px;
        margin: 0 auto;
        padding-bottom: 40px;
    }

    .udise-header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        background: var(--udise-white);
        padding: 16px 24px;
        border-radius: 12px;
        border: 1px solid var(--udise-border);
        box-shadow: 0 4px 12px rgba(11, 83, 148, 0.03);
    }

    .udise-header-title {
        margin: 0;
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 20px;
        font-weight: 800;
        color: var(--udise-dark);
    }

    .udise-header-subtitle {
        font-size: 12px;
        color: var(--udise-text-muted);
        margin-top: 2px;
    }

    .udise-controls {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .udise-select-yr {
        padding: 8px 12px;
        border-radius: 8px;
        border: 1px solid var(--udise-border);
        font-size: 13.5px;
        font-weight: 700;
        color: var(--udise-dark);
        outline: none;
        background-color: var(--udise-white);
        cursor: pointer;
    }

    .udise-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 9px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .udise-btn-save {
        background: var(--udise-primary);
        color: var(--udise-white);
        box-shadow: 0 2px 6px rgba(11, 83, 148, 0.2);
    }
    .udise-btn-save:hover {
        background: var(--udise-dark);
        transform: translateY(-1px);
    }

    .udise-btn-dl {
        background: var(--udise-white);
        color: var(--udise-accent);
        border: 1.5px solid var(--udise-accent);
    }
    .udise-btn-dl:hover {
        background: rgba(181, 134, 43, 0.05);
        transform: translateY(-1px);
    }

    /* ── TABS NAVIGATION ────────────────────────────────────────── */
    .udise-wrapper {
        background: var(--udise-white);
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 4px 20px rgba(11, 83, 148, 0.05);
        border: 1px solid var(--udise-border);
    }

    .udise-tabs-list {
        display: flex;
        list-style: none;
        padding: 0;
        margin: 0 0 24px 0;
        border-bottom: 2px solid var(--udise-border);
        gap: 4px;
        overflow-x: auto;
    }

    .udise-tab-btn {
        background: none;
        border: none;
        color: var(--udise-text-muted);
        padding: 12px 20px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s ease;
        position: relative;
        text-transform: uppercase;
        white-space: nowrap;
        letter-spacing: 0.5px;
    }

    .udise-tab-btn::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 3px;
        background: var(--udise-primary);
        transform: scaleX(0);
        transition: transform 0.25s ease;
    }

    .udise-tab-btn.active {
        color: var(--udise-primary);
    }

    .udise-tab-btn.active::after {
        transform: scaleX(1);
    }

    .udise-tab-btn:hover:not(.active) {
        color: var(--udise-dark);
        background: rgba(11, 83, 148, 0.02);
    }

    /* Tab Pane Content */
    .udise-tab-content {
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .udise-tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(4px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* ── FORM LAYOUTS ───────────────────────────────────────────── */
    .udise-section-title {
        font-family: 'Plus Jakarta Sans', sans-serif;
        font-size: 16px;
        font-weight: 800;
        color: var(--udise-dark);
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .udise-section-desc {
        font-size: 12.5px;
        color: var(--udise-text-muted);
        margin: 0 0 20px 0;
    }

    .udise-grid-3 {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .udise-grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 24px;
    }

    .udise-form-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .udise-label {
        font-size: 12px;
        font-weight: 700;
        color: var(--udise-dark);
        text-transform: uppercase;
        letter-spacing: 0.2px;
    }

    .udise-input {
        padding: 10px 14px;
        border-radius: 8px;
        border: 1px solid var(--udise-border);
        font-size: 13.5px;
        color: var(--udise-text);
        outline: none;
        transition: all 0.2s ease;
        background: var(--udise-white);
        font-family: inherit;
    }

    .udise-input:focus {
        border-color: var(--udise-primary);
        box-shadow: 0 0 0 3px rgba(11, 83, 148, 0.08);
    }

    .udise-input:disabled {
        background: #F1F5F9;
        color: #94A3B8;
        cursor: not-allowed;
    }

    /* ── CHECKBOX GRIDS ─────────────────────────────────────────── */
    .udise-checkbox-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px 24px;
        margin: 20px 0;
        background: #F8FAFC;
        padding: 20px;
        border-radius: 12px;
        border: 1px solid var(--udise-border);
    }

    .udise-checkbox-card {
        display: flex;
        align-items: center;
        gap: 10px;
        cursor: pointer;
        padding: 6px;
        border-radius: 6px;
        transition: background-color 0.2s ease;
    }

    .udise-checkbox-card:hover {
        background: rgba(11, 83, 148, 0.04);
    }

    .udise-checkbox {
        width: 18px;
        height: 18px;
        border-radius: 4px;
        border: 1px solid var(--udise-border);
        cursor: pointer;
        accent-color: var(--udise-primary);
    }

    .udise-checkbox-label {
        font-size: 13px;
        font-weight: 500;
        color: var(--udise-text);
        user-select: none;
    }

    /* ── TABLES ─────────────────────────────────────────────────── */
    .udise-table-container {
        overflow-x: auto;
        border: 1px solid var(--udise-border);
        border-radius: 12px;
        margin-top: 10px;
    }

    .udise-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        text-align: left;
    }

    .udise-table th {
        background: var(--udise-dark);
        color: var(--udise-white);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 10.5px;
        letter-spacing: 0.5px;
        padding: 14px 12px;
    }

    .udise-table td {
        padding: 12px;
        border-bottom: 1px solid var(--udise-border);
        color: var(--udise-text);
        vertical-align: middle;
    }

    .udise-table tbody tr:nth-child(even) td {
        background: rgba(11, 83, 148, 0.02);
    }

    .udise-table tbody tr:hover td {
        background: rgba(11, 83, 148, 0.06);
    }

    .udise-table-input {
        width: 90px;
        padding: 6px 10px;
        border: 1px solid var(--udise-border);
        border-radius: 6px;
        text-align: center;
        outline: none;
        font-size: 13px;
        transition: all 0.2s ease;
    }

    .udise-table-input:focus {
        border-color: var(--udise-primary);
        box-shadow: 0 0 0 2px rgba(11, 83, 148, 0.1);
    }

    /* ── DECLARATION ────────────────────────────────────────────── */
    .udise-dec-box {
        background: #FEF3C7;
        border: 1px solid #FCD34D;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 24px;
        display: flex;
        gap: 12px;
    }

    .udise-dec-icon {
        color: #D97706;
        font-size: 20px;
        margin-top: 2px;
    }

    .udise-dec-text {
        font-size: 13.5px;
        line-height: 1.5;
        color: #92400E;
    }

    /* Toast Notification styles */
    #udiseToast {
        position: fixed;
        bottom: 20px;
        right: 20px;
        background: var(--udise-white);
        color: var(--udise-dark);
        padding: 14px 20px;
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-left: 4px solid var(--udise-primary);
        z-index: 10000;
        font-size: 13px;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.3s ease;
    }
    #udiseToast.show {
        transform: translateY(0);
        opacity: 1;
    }
</style>
@endsection

@section('content')
<div class="udise-container">
    <form method="POST" action="{{ route('school.settings.udise.update') }}" id="form-udise">
        @csrf
        @method('PUT')

        {{-- Dynamic Header Row --}}
        <div class="udise-header-row">
            <div>
                <h2 class="udise-header-title">UDISE Data Management</h2>
                <div class="udise-header-subtitle">Official UDISE+ Government Report Configuration</div>
            </div>
            <div class="udise-controls">
                <div style="display:flex; align-items:center; gap:6px;">
                    <span style="font-size:11.5px; font-weight:700; color:var(--udise-text-muted);">Academic Year*</span>
                    <select name="academic_year" class="udise-select-yr">
                        <option value="2025-2026" {{ ($udise['academic_year'] ?? '2025-2026') === '2025-2026' ? 'selected' : '' }}>Apr 2025 - Mar 2026</option>
                        <option value="2024-2025" {{ ($udise['academic_year'] ?? '') === '2024-2025' ? 'selected' : '' }}>Apr 2024 - Mar 2025</option>
                    </select>
                </div>
                <button type="button" class="udise-btn udise-btn-dl" onclick="window.print()">
                    <i class="fas fa-download"></i> Download/View
                </button>
                <button type="submit" class="udise-btn udise-btn-save">
                    <i class="fas fa-save"></i> Save UDISE
                </button>
            </div>
        </div>

        {{-- Tabs Container --}}
        <div class="udise-wrapper">
            <ul class="udise-tabs-list">
                <li><button type="button" class="udise-tab-btn active" data-tab="profile">School Profile</button></li>
                <li><button type="button" class="udise-tab-btn" data-tab="infra">Infrastructure</button></li>
                <li><button type="button" class="udise-tab-btn" data-tab="enrollment">Enrollment</button></li>
                <li><button type="button" class="udise-tab-btn" data-tab="teachers">Teachers</button></li>
                <li><button type="button" class="udise-tab-btn" data-tab="facilities">Facilities</button></li>
                <li><button type="button" class="udise-tab-btn" data-tab="declaration">Declaration</button></li>
            </ul>

            {{-- 1. SCHOOL PROFILE TAB --}}
            <div class="udise-tab-content active" id="tab-profile">
                <h3 class="udise-section-title"><i class="fas fa-school" style="color:var(--udise-primary);"></i> School Profile Details</h3>
                <div class="udise-section-desc">Manage core regulatory registration codes and affiliations.</div>
                
                <div class="udise-grid-3">
                    <div class="udise-form-group">
                        <label class="udise-label">UDISE School Code</label>
                        <input type="text" name="udise_code" class="udise-input" 
                               value="{{ $udise['udise_code'] ?? '' }}" placeholder="11-digit code" maxlength="11">
                    </div>
                    <div class="udise-form-group">
                        <label class="udise-label">School Category</label>
                        <select name="school_category" class="udise-input">
                            <option value="">Select Category</option>
                            <option value="primary" {{ ($udise['school_category'] ?? '') === 'primary' ? 'selected' : '' }}>Primary Only (1-5)</option>
                            <option value="upper_primary" {{ ($udise['school_category'] ?? '') === 'upper_primary' ? 'selected' : '' }}>Upper Primary (1-8)</option>
                            <option value="secondary" {{ ($udise['school_category'] ?? '') === 'secondary' ? 'selected' : '' }}>Secondary (1-10)</option>
                            <option value="higher_secondary" {{ ($udise['school_category'] ?? '') === 'higher_secondary' ? 'selected' : '' }}>Higher Secondary (1-12)</option>
                        </select>
                    </div>
                    <div class="udise-form-group">
                        <label class="udise-label">Management Type</label>
                        <select name="management_type" class="udise-input">
                            <option value="">Select Management</option>
                            <option value="govt" {{ ($udise['management_type'] ?? '') === 'govt' ? 'selected' : '' }}>Department of Education (Government)</option>
                            <option value="aided" {{ ($udise['management_type'] ?? '') === 'aided' ? 'selected' : '' }}>Govt. Aided</option>
                            <option value="private" {{ ($udise['management_type'] ?? '') === 'private' ? 'selected' : '' }}>Private Unaided</option>
                        </select>
                    </div>
                </div>

                <div class="udise-grid-2">
                    <div class="udise-form-group">
                        <label class="udise-label">Affiliation Board</label>
                        <input type="text" name="affiliation_board" class="udise-input" 
                               value="{{ $udise['affiliation_board'] ?? '' }}" placeholder="e.g. CBSE, ICSE, State Board">
                    </div>
                    <div class="udise-form-group">
                        <label class="udise-label">Affiliation Number</label>
                        <input type="text" name="affiliation_number" class="udise-input" 
                               value="{{ $udise['affiliation_number'] ?? '' }}" placeholder="Affiliation Number">
                    </div>
                </div>
            </div>

            {{-- 2. INFRASTRUCTURE TAB --}}
            <div class="udise-tab-content" id="tab-infra">
                <h3 class="udise-section-title"><i class="fas fa-building" style="color:var(--udise-primary);"></i> Physical Infrastructure</h3>
                <div class="udise-section-desc">Provide physical rooms, sanitation facilities, and basic utility information.</div>

                <div class="udise-grid-3">
                    <div class="udise-form-group">
                        <label class="udise-label">Total Classrooms*</label>
                        <input type="number" name="classrooms_count" class="udise-input" 
                               value="{{ $udise['classrooms_count'] ?? '' }}" min="0" required>
                    </div>
                    <div class="udise-form-group">
                        <label class="udise-label">Good Condition Classrooms*</label>
                        <input type="number" name="good_classrooms_count" class="udise-input" 
                               value="{{ $udise['good_classrooms_count'] ?? '' }}" min="0" required>
                    </div>
                </div>

                <div class="udise-grid-2">
                    <div class="udise-form-group">
                        <label class="udise-label">Boys Functional Toilets</label>
                        <input type="number" name="boys_toilets" class="udise-input" 
                               value="{{ $udise['boys_toilets'] ?? '' }}" min="0">
                    </div>
                    <div class="udise-form-group">
                        <label class="udise-label">Girls Functional Toilets</label>
                        <input type="number" name="girls_toilets" class="udise-input" 
                               value="{{ $udise['girls_toilets'] ?? '' }}" min="0">
                    </div>
                </div>

                <h4 class="udise-label" style="margin-top: 10px;">Utility & Learning Facilities</h4>
                <div class="udise-checkbox-grid">
                    <label class="udise-checkbox-card">
                        <input type="hidden" name="library_available" value="0">
                        <input type="checkbox" name="library_available" value="1" class="udise-checkbox" 
                               {{ ($udise['library_available'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label">Library Available</span>
                    </label>
                    <label class="udise-checkbox-card">
                        <input type="hidden" name="playground_available" value="0">
                        <input type="checkbox" name="playground_available" value="1" class="udise-checkbox" 
                               {{ ($udise['playground_available'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label">Playground Available</span>
                    </label>
                    <label class="udise-checkbox-card">
                        <input type="hidden" name="electricity_available" value="0">
                        <input type="checkbox" name="electricity_available" value="1" class="udise-checkbox" 
                               {{ ($udise['electricity_available'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label">Electricity Available</span>
                    </label>
                    <label class="udise-checkbox-card">
                        <input type="hidden" name="internet_available" value="0">
                        <input type="checkbox" name="internet_available" value="1" class="udise-checkbox" 
                               {{ ($udise['internet_available'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label">Internet Available</span>
                    </label>
                    <label class="udise-checkbox-card">
                        <input type="hidden" name="drinking_water_available" value="0">
                        <input type="checkbox" name="drinking_water_available" value="1" class="udise-checkbox" 
                               {{ ($udise['drinking_water_available'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label">Drinking Water Available</span>
                    </label>
                    <label class="udise-checkbox-card">
                        <input type="hidden" name="cwsn_toilet_available" value="0">
                        <input type="checkbox" name="cwsn_toilet_available" value="1" class="udise-checkbox" 
                               {{ ($udise['cwsn_toilet_available'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label">CWSN Friendly Toilet</span>
                    </label>
                    <label class="udise-checkbox-card">
                        <input type="hidden" name="ict_lab_available" value="0">
                        <input type="checkbox" name="ict_lab_available" value="1" class="udise-checkbox" 
                               {{ ($udise['ict_lab_available'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label">ICT Lab Available</span>
                    </label>
                    <label class="udise-checkbox-card">
                        <input type="hidden" name="smart_classroom_available" value="0">
                        <input type="checkbox" name="smart_classroom_available" value="1" class="udise-checkbox" 
                               {{ ($udise['smart_classroom_available'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label">Smart Classroom Available</span>
                    </label>
                </div>
            </div>

            {{-- 3. ENROLLMENT TAB --}}
            <div class="udise-tab-content" id="tab-enrollment">
                <h3 class="udise-section-title"><i class="fas fa-users" style="color:var(--udise-primary);"></i> Enrollment Data</h3>
                <div class="udise-section-desc">Category-wise details automatically calculated from dynamic student records. CWSN (Children with Special Needs) fields are editable.</div>

                <div style="font-size: 13.5px; font-weight:700; color:var(--udise-dark); margin-bottom: 12px;">
                    Total Dynamic Enrollment: <span style="color:var(--udise-primary); font-size:16px;">{{ $grandTotalStudents }}</span>
                </div>

                <div class="udise-table-container">
                    <table class="udise-table">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Class</th>
                                <th style="width: 12%;">Total Students</th>
                                <th style="width: 14%;">General</th>
                                <th style="width: 14%;">SC</th>
                                <th style="width: 14%;">ST</th>
                                <th style="width: 14%;">OBC</th>
                                <th style="width: 10%; text-align: center;">CWSN Boys (Editable)</th>
                                <th style="width: 10%; text-align: center;">CWSN Girls (Editable)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($enrollmentData as $row)
                                <tr>
                                    <td><strong>{{ $row['class_name'] }}</strong></td>
                                    <td style="font-weight: 700; color:var(--udise-dark);">{{ $row['total_students'] }}</td>
                                    <td><span style="font-size:11.5px; color:#475569;">B-{{ $row['general']['boys'] }} G-{{ $row['general']['girls'] }}</span></td>
                                    <td><span style="font-size:11.5px; color:#475569;">B-{{ $row['sc']['boys'] }} G-{{ $row['sc']['girls'] }}</span></td>
                                    <td><span style="font-size:11.5px; color:#475569;">B-{{ $row['st']['boys'] }} G-{{ $row['st']['girls'] }}</span></td>
                                    <td><span style="font-size:11.5px; color:#475569;">B-{{ $row['obc']['boys'] }} G-{{ $row['obc']['girls'] }}</span></td>
                                    <td style="text-align: center;">
                                        <input type="number" name="cwsn_boys[{{ $row['class_id'] }}]" class="udise-table-input" 
                                               value="{{ $udise['cwsn_boys'][$row['class_id']] ?? 0 }}" min="0">
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="number" name="cwsn_girls[{{ $row['class_id'] }}]" class="udise-table-input" 
                                               value="{{ $udise['cwsn_girls'][$row['class_id']] ?? 0 }}" min="0">
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" style="text-align:center; color:var(--udise-text-muted);">No classes found. Set up classes first.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 4. TEACHERS TAB --}}
            <div class="udise-tab-content" id="tab-teachers">
                <h3 class="udise-section-title"><i class="fas fa-chalkboard-teacher" style="color:var(--udise-primary);"></i> Teaching Staff Information</h3>
                <div class="udise-section-desc">Staff demographics read directly from personnel files. Input the number of professionally trained teachers.</div>

                <div class="udise-grid-3">
                    <div class="udise-form-group">
                        <label class="udise-label">Total Teaching Faculty</label>
                        <input type="text" class="udise-input" value="{{ $teacherCounts['total'] }}" disabled>
                    </div>
                    <div class="udise-form-group">
                        <label class="udise-label">Male Faculty</label>
                        <input type="text" class="udise-input" value="{{ $teacherCounts['male'] }}" disabled>
                    </div>
                    <div class="udise-form-group">
                        <label class="udise-label">Female Faculty</label>
                        <input type="text" class="udise-input" value="{{ $teacherCounts['female'] }}" disabled>
                    </div>
                </div>

                <div class="udise-grid-3">
                    <div class="udise-form-group">
                        <label class="udise-label">Regular Teachers</label>
                        <input type="text" class="udise-input" value="{{ $teacherCounts['regular'] }}" disabled>
                    </div>
                    <div class="udise-form-group">
                        <label class="udise-label">Contract/Temporary Teachers</label>
                        <input type="text" class="udise-input" value="{{ $teacherCounts['contract'] }}" disabled>
                    </div>
                    <div class="udise-form-group">
                        <label class="udise-label">Pupil-Teacher Ratio (PTR)</label>
                        <input type="text" class="udise-input" value="{{ $teacherCounts['ptr'] }} : 1" disabled>
                    </div>
                </div>

                <div class="udise-grid-2" style="margin-top: 10px; border-top: 1px solid var(--udise-border); padding-top: 20px;">
                    <div class="udise-form-group">
                        <label class="udise-label">Professionally Trained Teachers*</label>
                        <input type="number" name="trained_teachers" class="udise-input" 
                               value="{{ $udise['trained_teachers'] ?? '' }}" min="0" placeholder="Number of trained teachers">
                    </div>
                </div>
            </div>

            {{-- 5. FACILITIES TAB --}}
            <div class="udise-tab-content" id="tab-facilities">
                <h3 class="udise-section-title"><i class="fas fa-gifts" style="color:var(--udise-primary);"></i> School Schemes & Facilities</h3>
                <div class="udise-section-desc">Select various welfare and operational programs currently run by the institution.</div>

                <div class="udise-checkbox-grid" style="grid-template-columns: repeat(2, 1fr);">
                    <label class="udise-checkbox-card">
                        <input type="hidden" name="facility_pre_primary" value="0">
                        <input type="checkbox" name="facility_pre_primary" value="1" class="udise-checkbox" 
                               {{ ($udise['facility_pre_primary'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label">Pre-Primary Section Operates Here</span>
                    </label>
                    <label class="udise-checkbox-card">
                        <input type="hidden" name="facility_mid_day_meal" value="0">
                        <input type="checkbox" name="facility_mid_day_meal" value="1" class="udise-checkbox" 
                               {{ ($udise['facility_mid_day_meal'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label">Mid-Day Meal Applicable</span>
                    </label>
                    <label class="udise-checkbox-card">
                        <input type="hidden" name="facility_inclusive_education" value="0">
                        <input type="checkbox" name="facility_inclusive_education" value="1" class="udise-checkbox" 
                               {{ ($udise['facility_inclusive_education'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label">Inclusive Education (CWSN Support) Provided</span>
                    </label>
                    <label class="udise-checkbox-card">
                        <input type="hidden" name="facility_transport" value="0">
                        <input type="checkbox" name="facility_transport" value="1" class="udise-checkbox" 
                               {{ ($udise['facility_transport'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label">Official Transport Facility Available</span>
                    </label>
                </div>
            </div>

            {{-- 6. DECLARATION TAB --}}
            <div class="udise-tab-content" id="tab-declaration">
                <h3 class="udise-section-title"><i class="fas fa-signature" style="color:var(--udise-primary);"></i> Official Declaration</h3>
                <div class="udise-section-desc">Authenticate and sign off the data entry before submitting UDISE records.</div>

                <div class="udise-dec-box">
                    <i class="fas fa-exclamation-triangle udise-dec-icon"></i>
                    <div class="udise-dec-text">
                        <strong>Certification Statement:</strong> I hereby certify that the information entered in this UDISE+ report form is verified, authentic, and matches our official records. Any false statements can compromise official data collection.
                    </div>
                </div>

                <div style="margin-bottom: 20px;">
                    <label class="udise-checkbox-card" style="padding-left:0;">
                        <input type="hidden" name="declared_confirm" value="0">
                        <input type="checkbox" name="declared_confirm" value="1" class="udise-checkbox" required
                               {{ ($udise['declared_confirm'] ?? '') == '1' ? 'checked' : '' }}>
                        <span class="udise-checkbox-label" style="font-weight: 700;">I accept and confirm the certification statement above.</span>
                    </label>
                </div>

                <div class="udise-grid-2">
                    <div class="udise-form-group">
                        <label class="udise-label">Declared By Name*</label>
                        <input type="text" name="declared_by" class="udise-input" 
                               value="{{ $udise['declared_by'] ?? '' }}" placeholder="Name of Declarant" required>
                    </div>
                    <div class="udise-form-group">
                        <label class="udise-label">Designation*</label>
                        <input type="text" name="declared_designation" class="udise-input" 
                               value="{{ $udise['declared_designation'] ?? '' }}" placeholder="e.g. Principal, Admin Officer" required>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Toast Notification container --}}
<div id="udiseToast">
    <i class="fas fa-circle-check" style="color:var(--udise-primary);"></i>
    <span id="udiseToastMsg">Changes saved!</span>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- TAB SWITCHING ---
        const tabButtons = document.querySelectorAll('.udise-tab-btn');
        const tabPanes = document.querySelectorAll('.udise-tab-content');

        tabButtons.forEach(btn => {
            btn.addEventListener('click', function () {
                const tab = this.getAttribute('data-tab');

                // Update active tabs buttons
                tabButtons.forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Update active tab pane
                tabPanes.forEach(p => p.classList.remove('active'));
                document.getElementById(`tab-${tab}`).classList.add('active');
            });
        });

        // --- FLASH MESSAGE TOAST ---
        @if(session('success'))
            showToast("{{ session('success') }}");
        @endif

        function showToast(msg) {
            const toast = document.getElementById('udiseToast');
            const toastMsg = document.getElementById('udiseToastMsg');
            toastMsg.innerText = msg;
            toast.classList.add('show');
            setTimeout(() => {
                toast.classList.remove('show');
            }, 3000);
        }
    });
</script>
@endsection
