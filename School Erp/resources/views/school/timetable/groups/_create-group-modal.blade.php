{{-- 3-Step Stepper Wizard Modal for Creating Timetable Group Templates --}}
<style>
/* ═══════════════════════════════════════════════════════════════
   STEPPER WIZARD MODAL STYLING
   ═══════════════════════════════════════════════════════════════ */
.wizard-modal {
    max-width: 950px !important; /* Larger section for premium look */
}

/* Stepper Header (Progress Indicator) */
.step-indicator {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 32px;
    padding: 0 24px;
    position: relative;
}
.step-indicator::before {
    content: "";
    position: absolute;
    top: 19px;
    left: 45px;
    right: 45px;
    height: 2px;
    border-top: 2.5px dotted #cbd5e1;
    background: none;
    z-index: 1;
}
.step-node {
    position: relative;
    z-index: 3;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    cursor: pointer;
}
.step-circle {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: #fff;
    border: 2px solid #cbd5e1;
    color: #64748b;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 14px;
    transition: all 0.3s ease;
}
.step-node.active .step-circle {
    border-color: #ea580c;
    color: #fff;
    background: #ea580c;
    box-shadow: 0 0 0 4px rgba(234, 88, 12, 0.15);
}
.step-node.completed .step-circle {
    border-color: #10b981;
    background: #10b981;
    color: #fff;
}
.step-label {
    font-size: 13px;
    font-weight: 700;
    color: #64748b;
    letter-spacing: 0.3px;
}
.step-node.active .step-label {
    color: #ea580c;
}
.step-node.completed .step-label {
    color: #10b981;
}

/* Wizard Steps Content */
.wizard-step-content {
    display: none;
    animation: slideInLeft 0.35s ease;
}
.wizard-step-content.active {
    display: block;
}

@keyframes slideInLeft {
    from { transform: translateX(20px); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

/* Class-Section Selector */
.classes-selector-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 16px;
    max-height: 380px;
    overflow-y: auto;
    padding-right: 8px;
}
.class-selector-card {
    border: 1.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    background: #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.class-selector-title {
    font-size: 14.5px;
    font-weight: 750;
    color: #0f172a;
    margin-bottom: 12px;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 6px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.class-select-all {
    font-size: 11px;
    color: #2563eb;
    cursor: pointer;
    font-weight: 600;
}
.sections-chips-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}
.section-chip-checkbox {
    border: 1.5px solid #cbd5e1;
    border-radius: 20px;
    padding: 6px 14px;
    cursor: pointer;
    font-weight: 700;
    font-size: 12.5px;
    color: #475569;
    background: #fff;
    transition: all 0.2s;
}
.section-chip-checkbox input {
    display: none;
}
.section-chip-checkbox.selected {
    border-color: #ea580c;
    background: #fdf8f6;
    color: #ea580c;
}

/* Periods Config Table */
.periods-config-table {
    width: 100%;
    margin-top: 12px;
    border-collapse: collapse;
}
.periods-config-table th {
    font-size: 12px;
    color: #64748b;
    text-transform: uppercase;
    text-align: left;
    padding: 6px 12px;
    font-weight: 700;
}
.periods-config-table td {
    padding: 8px 12px;
    border-bottom: 1px solid #f1f5f9;
}

/* Summary Layout */
.summary-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    margin-bottom: 16px;
}
.summary-row {
    display: flex;
    justify-content: space-between;
    padding: 6px 0;
    border-bottom: 1px solid #f1f5f9;
    font-size: 13px;
}
.summary-row:last-child {
    border-bottom: none;
}
.summary-lbl {
    font-weight: 700;
    color: #64748b;
}
.summary-val {
    font-weight: 600;
    color: #1e293b;
    text-align: right;
}

/* ═══════════════════════════════════════════════════════════════
   PREMIUM OVERLAPPING INPUTS
   ═══════════════════════════════════════════════════════════════ */
.premium-form-group {
    position: relative;
    margin-top: 14px;
    margin-bottom: 14px;
}
.premium-form-label {
    position: absolute;
    top: -9px;
    left: 12px;
    background: #fff;
    padding: 0 6px;
    font-size: 11.5px;
    font-weight: 700;
    color: #475569;
    z-index: 5;
    text-transform: none;
    letter-spacing: 0.3px;
    border-radius: 4px;
}
.premium-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    width: 100%;
}
.premium-input-icon {
    position: absolute;
    left: 14px;
    color: #94a3b8;
    font-size: 15px;
    pointer-events: none;
}
.premium-input-icon-right {
    position: absolute;
    right: 14px;
    color: #94a3b8;
    font-size: 15px;
    pointer-events: none;
}
.premium-form-control {
    padding: 11px 14px 11px 40px;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    color: #1e293b;
    outline: none;
    transition: all 0.2s;
    width: 100%;
    background: #fff;
    font-family: inherit;
    font-weight: 600;
    height: 44px;
}
.premium-form-control:focus {
    border-color: #ea580c;
    box-shadow: 0 0 0 3px rgba(234, 88, 12, 0.1);
}

/* Active / Inactive radio toggle */
.active-toggle-container {
    display: flex;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    height: 44px;
    overflow: hidden;
    background: #fff;
}
.active-toggle-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 700;
    color: #475569;
    transition: all 0.2s;
    border-right: 1.5px solid #cbd5e1;
    user-select: none;
}
.active-toggle-btn:last-child {
    border-right: none;
}
.active-toggle-btn input[type="radio"] {
    display: none;
}
.radio-custom-circle {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid #cbd5e1;
    display: inline-block;
    position: relative;
    transition: all 0.2s;
}
.active-toggle-btn input[type="radio"]:checked + .radio-custom-circle {
    border-color: #ea580c;
    background: #ea580c;
}
.active-toggle-btn input[type="radio"]:checked + .radio-custom-circle::after {
    content: "";
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #fff;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}
.active-toggle-btn:has(input[type="radio"]:checked) {
    background: #fdf8f6;
    color: #ea580c;
}

/* Multi-select Dropdown for Days */
.select-days-dropdown-trigger {
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    padding: 6px 40px;
    min-height: 44px;
    background: #fff;
    position: relative;
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 6px;
    cursor: pointer;
    width: 100%;
}
.trigger-icon-left {
    position: absolute;
    left: 14px;
    color: #94a3b8;
    font-size: 16px;
    pointer-events: none;
}
.trigger-icon-right {
    position: absolute;
    right: 14px;
    color: #94a3b8;
    font-size: 16px;
    pointer-events: none;
}
.selected-days-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}
.day-chip {
    background: #eff6ff;
    border: 1px solid #bfdbfe;
    border-radius: 16px;
    padding: 2px 10px;
    font-size: 12px;
    font-weight: 700;
    color: #1e40af;
    display: flex;
    align-items: center;
    gap: 6px;
}
.day-chip-close {
    cursor: pointer;
    color: #3b82f6;
    font-size: 11px;
    font-weight: 800;
}
.day-chip-close:hover {
    color: #ef4444;
}
.select-days-dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background: #fff;
    border: 1.5px solid #cbd5e1;
    border-radius: 8px;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
    display: none;
    z-index: 100;
    margin-top: 4px;
    max-height: 250px;
    overflow-y: auto;
}
.select-days-dropdown-menu.open {
    display: block;
}
.day-dropdown-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    cursor: pointer;
    font-weight: 600;
    font-size: 13.5px;
    color: #334155;
    transition: background 0.15s;
    user-select: none;
}
.day-dropdown-item:hover {
    background: #f8fafc;
}
.day-dropdown-item input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: #ea580c;
}

/* Premium Button Styling */
.btn-premium-primary {
    background: linear-gradient(135deg, #f97316 0%, #ea580c 100%) !important;
    border-color: #ea580c !important;
    color: #fff !important;
    font-family: 'Outfit', sans-serif !important;
    font-weight: 700 !important;
    padding: 10px 22px !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 12px rgba(234, 88, 12, 0.2) !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
    border: none !important;
}
.btn-premium-primary:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 16px rgba(234, 88, 12, 0.3) !important;
}
.btn-premium-outline {
    background: #fff !important;
    border: 1.5px solid #cbd5e1 !important;
    color: #475569 !important;
    font-family: 'Outfit', sans-serif !important;
    font-weight: 700 !important;
    padding: 10px 22px !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
}
.btn-premium-outline:hover {
    background: #f8fafc !important;
    border-color: #cbd5e1 !important;
}
</style>

<div class="inst-modal" id="modal-wizard">
    <div class="inst-modal-content wizard-modal">
        <div class="inst-modal-hdr" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); padding: 18px 24px;">
            <h3 style="font-size: 20px; font-weight: 800; font-family: 'Outfit', sans-serif;">Create Group Wise Timetable</h3>
            <button class="inst-modal-close" onclick="closeWizardModal()">&times;</button>
        </div>
        <div class="inst-modal-body" style="padding: 24px;">
            
            {{-- Stepper Progress Node --}}
            <div class="step-indicator">
                <div class="step-node active" id="node-1" onclick="goToStep(1)">
                    <div class="step-circle">1</div>
                    <span class="step-label">Select Period Details</span>
                </div>
                <div class="step-node" id="node-2" onclick="goToStep(2)">
                    <div class="step-circle">2</div>
                    <span class="step-label">Select Class & Section</span>
                </div>
                <div class="step-node" id="node-3" onclick="goToStep(3)">
                    <div class="step-circle">3</div>
                    <span class="step-label">Upload Timetable</span>
                </div>
            </div>

            <form id="wizardForm">
                @csrf
                <input type="hidden" name="academic_session_id" value="{{ $sessionId }}">

                {{-- ==================== STEP 1: PERIODS STRUCTURE ==================== --}}
                <div class="wizard-step-content active" id="step-1">
                    <div class="inst-form-grid">
                        <div class="premium-form-group">
                            <label class="premium-form-label">Group Name *</label>
                            <div class="premium-input-wrapper">
                                <span class="premium-input-icon"><i class="fas fa-book"></i></span>
                                <input type="text" name="group_name" id="wizard_group_name" class="premium-form-control" required placeholder="e.g. Primary Section Morning Shift">
                            </div>
                        </div>
                        <div class="premium-form-group" style="display: flex; align-items: flex-end; height: 100%;">
                            <div class="active-toggle-container" style="width: 100%; height: 44px; margin-top: auto;">
                                <label class="active-toggle-btn">
                                    <input type="radio" name="is_active" value="true" checked>
                                    <span class="radio-custom-circle"></span>
                                    <span>Active</span>
                                </label>
                                <label class="active-toggle-btn">
                                    <input type="radio" name="is_active" value="false">
                                    <span class="radio-custom-circle"></span>
                                    <span>Inactive</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="inst-form-grid">
                        <div class="premium-form-group">
                            <label class="premium-form-label">Class start time *</label>
                            <div class="premium-input-wrapper">
                                <input type="time" name="class_start_time" id="wizard_start_time" class="premium-form-control" value="08:05" required style="padding-left: 14px; padding-right: 40px;" oninput="calculatePeriodTimings()">
                                <span class="premium-input-icon-right"><i class="far fa-clock"></i></span>
                            </div>
                            <span style="font-size: 11px; color: #d97706; margin-top: 4px; display: block;">India timezone*</span>
                        </div>
                        <div class="premium-form-group">
                            <label class="premium-form-label">Number of periods *</label>
                            <div class="premium-input-wrapper">
                                <span class="premium-input-icon"><i class="fas fa-book"></i></span>
                                <input type="number" name="number_of_periods" id="wizard_num_periods" class="premium-form-control" min="1" max="15" value="3" required oninput="generatePeriodsConfig()">
                            </div>
                        </div>
                    </div>

                    <div class="premium-form-group" style="position: relative;">
                        <label class="premium-form-label">Select days *</label>
                        <div class="select-days-dropdown-trigger" onclick="toggleDaysDropdown(event)">
                            <span class="trigger-icon-left"><i class="fas fa-book"></i></span>
                            <div class="selected-days-chips" id="selected-days-chips-list">
                                <!-- chips populated dynamically -->
                            </div>
                            <span class="trigger-icon-right"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="select-days-dropdown-menu" id="days-dropdown-menu" onclick="event.stopPropagation();">
                            @php
                                $weekdays = ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"];
                            @endphp
                            @foreach($weekdays as $day)
                                <label class="day-dropdown-item">
                                    <input type="checkbox" name="applicable_days[]" value="{{ $day }}" class="day-chk" onchange="onDayCheckboxChange(this)" @if($day !== 'Sunday') checked @endif>
                                    <span>{{ $day }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <h4 style="font-size: 14px; font-weight: 700; color: #475569; margin: 24px 0 12px 0;">Period Duration (* in minutes)</h4>

                    <div style="background: #fff; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 20px; max-height: 380px; overflow-y: auto;">
                        <table class="periods-config-table" style="width: 100%; border-collapse: separate; border-spacing: 12px 0;">
                            <thead>
                                <tr style="text-align: left;">
                                    <th style="font-size: 13.5px; font-weight: 700; color: #1e3a8a; padding-bottom: 8px;">Period Name</th>
                                    <th style="font-size: 13.5px; font-weight: 700; color: #1e3a8a; padding-bottom: 8px;">Period Duration</th>
                                    <th style="font-size: 13.5px; font-weight: 700; color: #1e3a8a; padding-bottom: 8px;">Period Timings</th>
                                    <th style="width: 40px; padding-bottom: 8px;"></th>
                                </tr>
                            </thead>
                            <tbody id="wizard-periods-tbody">
                                {{-- generated dynamically by JS --}}
                            </tbody>
                        </table>
                    </div>

                    <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 14px;">
                        <button type="button" class="btn-premium-outline" onclick="addPeriodRow()" style="padding: 8px 16px !important; font-size: 12.5px;">+ Add Period</button>
                    </div>

                    <div class="inst-form-grid" style="margin-top: 20px;">
                        <div class="premium-form-group" style="margin: 0;">
                            <label class="premium-form-label">Start Date *</label>
                            <div class="premium-input-wrapper">
                                <input type="date" name="start_date" id="wizard_start_date" class="premium-form-control" value="{{ date('Y-04-01') }}" required style="padding-left: 14px; padding-right: 14px;">
                            </div>
                        </div>
                        <div class="premium-form-group" style="margin: 0;">
                            <label class="premium-form-label">End Date *</label>
                            <div class="premium-input-wrapper">
                                <input type="date" name="end_date" id="wizard_end_date" class="premium-form-control" value="{{ date('Y-03-31', strtotime('+1 year')) }}" required style="padding-left: 14px; padding-right: 14px;">
                            </div>
                        </div>
                    </div>

                    <div class="inst-form-footer">
                        <button type="button" class="btn-premium-outline" onclick="closeWizardModal()">Cancel</button>
                        <button type="button" class="btn-premium-primary" onclick="nextStep(2)">Next: Allocate Classes</button>
                    </div>
                </div>

                {{-- ==================== STEP 2: CLASS ALLOCATION ==================== --}}
                <div class="wizard-step-content" id="step-2">
                    <p style="font-size:13.5px; color:#64748b; margin-bottom:16px; font-weight:500;">Assign this weekly period structure template to classes. Active mappings on the same class sections will be overwritten.</p>

                    <div class="classes-selector-grid">
                        @foreach($classes as $c)
                            <div class="class-selector-card">
                                <div class="class-selector-title">
                                    <span style="font-weight: 700; color: #1e293b;">{{ $c->name }}</span>
                                    <span class="class-select-all" onclick="selectAllClassSections({{ $c->id }})" style="color: #ea580c; font-weight: 700;">Toggle All</span>
                                </div>
                                <div class="sections-chips-grid">
                                    @forelse($c->sections as $s)
                                        <label class="section-chip-checkbox" id="lbl-cs-{{ $c->id }}-{{ $s->id }}">
                                            <input type="checkbox" name="class_sections[]" value="{{ $c->id }}-{{ $s->id }}" class="cs-chk cs-chk-class-{{ $c->id }}" onchange="toggleSectionChip({{ $c->id }}, {{ $s->id }})">
                                            Section {{ $s->name }}
                                        </label>
                                    @empty
                                        <span style="font-size:11.5px; color:#94a3b8; font-style:italic;">No sections defined.</span>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="inst-form-footer">
                        <button type="button" class="btn-premium-outline" onclick="prevStep(1)">Back</button>
                        <button type="button" class="btn-premium-primary" onclick="nextStep(3)">Next: Verify & Save</button>
                    </div>
                </div>

                {{-- ==================== STEP 3: CONFIRM & SAVE ==================== --}}
                <div class="wizard-step-content" id="step-3">
                    <p style="font-size:13.5px; color:#64748b; margin-bottom:16px; font-weight:500;">Verify your timetable template setup before finishing.</p>

                    <div class="summary-box" style="border: 1.5px solid #cbd5e1; background: #fff;">
                        <div class="summary-row">
                            <span class="summary-lbl">Group Name</span>
                            <span class="summary-val" id="sum-group-name" style="color: #1e293b; font-weight: 700;">-</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-lbl">Start Time</span>
                            <span class="summary-val" id="sum-start-time" style="color: #1e293b; font-weight: 700;">-</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-lbl">Active Days</span>
                            <span class="summary-val" id="sum-active-days" style="color: #1e293b; font-weight: 700;">-</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-lbl">Validity Period</span>
                            <span class="summary-val" id="sum-valid-range" style="color: #1e293b; font-weight: 700;">-</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-lbl">Allocated Slots</span>
                            <span class="summary-val" id="sum-period-count" style="color: #ea580c; font-weight: 800;">-</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-lbl">Allocated Class Sections</span>
                            <span class="summary-val" id="sum-classes-count" style="color: #ea580c; font-weight: 800;">-</span>
                        </div>
                    </div>

                    <div class="inst-form-footer">
                        <button type="button" class="btn-premium-outline" onclick="prevStep(2)">Back</button>
                        <button type="button" id="submitWizardBtn" class="btn-premium-primary" onclick="submitWizard()" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important; border-color: #059669 !important; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2) !important;">Confirm & Save Template</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    let currentStep = 1;
    let editingGroupId = null;

    function openWizardModal() {
        editingGroupId = null;
        
        // Reset Modal Title
        const modalTitle = document.querySelector('#modal-wizard .inst-modal-hdr h3');
        if (modalTitle) modalTitle.textContent = 'Create Group Wise Timetable';

        // Clear wizard form values
        document.getElementById('wizardForm').reset();
        
        // Reset class selections chip visuals
        document.querySelectorAll('.cs-chk').forEach(chk => {
            chk.checked = false;
            const classId = chk.value.split('-')[0];
            const sectionId = chk.value.split('-')[1];
            const label = document.getElementById('lbl-cs-' + classId + '-' + sectionId);
            if (label) label.classList.remove('selected');
        });

        currentStep = 1;
        document.getElementById('modal-wizard').classList.add('active');
        generatePeriodsConfig();
        renderSelectedDaysChips();
        goToStep(1);
    }

    function openEditWizardModal(group) {
        editingGroupId = group.id;
        
        // Change Modal Title to Edit
        const modalTitle = document.querySelector('#modal-wizard .inst-modal-hdr h3');
        if (modalTitle) modalTitle.textContent = 'Edit Group Wise Timetable';

        // Populate basic details in Step 1
        document.getElementById('wizard_group_name').value = group.group_name;
        
        // Active status radio
        const activeRadio = document.querySelector(`input[name="is_active"][value="${group.is_active}"]`);
        if (activeRadio) activeRadio.checked = true;

        // Class Start Time
        const startTimeStr = group.class_start_time.substr(0, 5); // "08:00"
        document.getElementById('wizard_start_time').value = startTimeStr;

        // Validity dates
        const startD = group.start_date.substr(0, 10);
        const endD = group.end_date.substr(0, 10);
        document.getElementById('wizard_start_date').value = startD;
        document.getElementById('wizard_end_date').value = endD;

        // Number of periods
        document.getElementById('wizard_num_periods').value = group.number_of_periods;

        // Weekdays checkboxes
        document.querySelectorAll('.day-chk').forEach(chk => {
            chk.checked = group.applicable_days.includes(chk.value);
        });
        renderSelectedDaysChips();

        // Populate period table
        populatePeriodsConfig(group.periods);

        // Populate class sections
        document.querySelectorAll('.cs-chk').forEach(chk => {
            chk.checked = group.class_section_ids.includes(chk.value);
            const classId = chk.value.split('-')[0];
            const sectionId = chk.value.split('-')[1];
            const label = document.getElementById('lbl-cs-' + classId + '-' + sectionId);
            if (label) {
                if (chk.checked) {
                    label.classList.add('selected');
                } else {
                    label.classList.remove('selected');
                }
            }
        });

        // Open Wizard Modal
        currentStep = 1;
        document.getElementById('modal-wizard').classList.add('active');
        goToStep(1);
    }

    function populatePeriodsConfig(periods) {
        const tbody = document.getElementById('wizard-periods-tbody');
        tbody.innerHTML = '';
        document.getElementById('wizard_num_periods').value = periods.length;

        periods.forEach((p, i) => {
            // format times (H:i:s to 12-hour format)
            const formatTime12 = (timeStr) => {
                if (!timeStr) return '';
                const parts = timeStr.split(':');
                let h = parseInt(parts[0]);
                const m = parts[1];
                const ampm = h >= 12 ? 'PM' : 'AM';
                h = h % 12;
                h = h ? h : 12; // 0 should be 12
                return h.toString().padStart(2, '0') + ':' + m + ' ' + ampm;
            };

            const timingVal = `${formatTime12(p.start_time)} - ${formatTime12(p.end_time)}`;

            const row = `
                <tr data-index="${i}">
                    <td style="padding: 8px 0;">
                        <div class="premium-form-group" style="margin: 0;">
                           <label class="premium-form-label" style="font-size: 9.5px; top: -7px;">Enter Period Name *</label>
                           <div class="premium-input-wrapper">
                               <span class="premium-input-icon"><i class="fas fa-book"></i></span>
                               <input type="text" name="periods[${i}][period_name]" class="premium-form-control p-name-inp" value="${p.period_name}" required style="height: 40px; font-size: 13px; padding-left: 36px;">
                           </div>
                        </div>
                    </td>
                    <td style="padding: 8px 0;">
                        <div class="premium-form-group" style="margin: 0;">
                           <label class="premium-form-label" style="font-size: 9.5px; top: -7px;">Period Duration *</label>
                           <div class="premium-input-wrapper">
                               <span class="premium-input-icon"><i class="fas fa-clock"></i></span>
                               <input type="number" name="periods[${i}][duration_minutes]" class="premium-form-control p-dur-inp" value="${p.duration_minutes}" min="1" required style="height: 40px; font-size: 13px; padding-left: 36px;" oninput="calculatePeriodTimings()">
                           </div>
                        </div>
                    </td>
                    <td style="padding: 8px 0;">
                        <div class="premium-form-group" style="margin: 0;">
                           <label class="premium-form-label" style="font-size: 9.5px; top: -7px;">Period Timings</label>
                           <div class="premium-input-wrapper">
                               <span class="premium-input-icon"><i class="fas fa-clock"></i></span>
                               <input type="text" class="premium-form-control p-timing-inp" readonly style="height: 40px; font-size: 13px; padding-left: 36px; background: #f8fafc; color: #64748b;" value="${timingVal}" placeholder="08:05 AM - 08:50 AM">
                           </div>
                        </div>
                    </td>
                    <td style="padding: 8px 0; text-align: center; vertical-align: middle;">
                        <button type="button" class="btn-delete-period" onclick="deletePeriodRow(${i})" style="background: none; border: none; color: #ef4444; font-size: 16px; cursor: pointer; padding: 6px;">
                            <i class="far fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    }

    function closeWizardModal() {
        document.getElementById('modal-wizard').classList.remove('active');
    }

    // Toggle dropdown for select days
    function toggleDaysDropdown(e) {
        e.stopPropagation();
        const menu = document.getElementById('days-dropdown-menu');
        menu.classList.toggle('open');
    }

    function onDayCheckboxChange(chk) {
        renderSelectedDaysChips();
    }

    function renderSelectedDaysChips() {
        const container = document.getElementById('selected-days-chips-list');
        if (!container) return;
        
        container.innerHTML = '';
        const checkedDays = document.querySelectorAll('.day-chk:checked');
        
        if (checkedDays.length === 0) {
            container.innerHTML = '<span style="color:#94a3b8; font-size:13.5px; font-weight:500;">Select applicable weekdays</span>';
            return;
        }

        checkedDays.forEach(chk => {
            const day = chk.value;
            const chip = document.createElement('span');
            chip.className = 'day-chip';
            chip.innerHTML = `
                <span>${day.substr(0, 3)}</span>
                <span class="day-chip-close" onclick="removeDayChip(event, '${day}')">&times;</span>
            `;
            container.appendChild(chip);
        });
    }

    function removeDayChip(event, day) {
        event.stopPropagation();
        const chk = document.querySelector(`.day-chk[value="${day}"]`);
        if (chk) {
            chk.checked = false;
            renderSelectedDaysChips();
        }
    }

    // Close dropdown on click outside
    document.addEventListener('click', function(e) {
        const menu = document.getElementById('days-dropdown-menu');
        if (menu && menu.classList.contains('open')) {
            menu.classList.remove('open');
        }
    });

    function toggleSectionChip(classId, sectionId) {
        const label = document.getElementById('lbl-cs-' + classId + '-' + sectionId);
        const chk = label.querySelector('.cs-chk');
        if (chk.checked) {
            label.classList.add('selected');
        } else {
            label.classList.remove('selected');
        }
    }

    function selectAllClassSections(classId) {
        const checks = document.querySelectorAll('.cs-chk-class-' + classId);
        let allChecked = true;
        checks.forEach(chk => {
            if (!chk.checked) allChecked = false;
        });

        checks.forEach(chk => {
            chk.checked = !allChecked;
            const label = document.getElementById('lbl-cs-' + classId + '-' + chk.value.split('-')[1]);
            if (chk.checked) {
                label.classList.add('selected');
            } else {
                label.classList.remove('selected');
            }
        });
    }

    // Generate Period Rows Dynamically
    function generatePeriodsConfig() {
        const count = parseInt(document.getElementById('wizard_num_periods').value) || 0;
        const tbody = document.getElementById('wizard-periods-tbody');
        const existingData = [];
        
        // Grab current values if any to keep them on re-calculation
        tbody.querySelectorAll('tr').forEach((tr, index) => {
            const nameInp = tr.querySelector('.p-name-inp');
            const durInp = tr.querySelector('.p-dur-inp');
            if (nameInp && durInp) {
                existingData.push({
                    name: nameInp.value,
                    duration: durInp.value
                });
            }
        });

        tbody.innerHTML = '';

        let periodNumber = 1;
        for (let i = 0; i < count; i++) {
            let name = "";
            let dur = 45;

            // If we have existing user input, preserve it
            if (existingData[i]) {
                name = existingData[i].name;
                dur = existingData[i].duration;
                if (!name.toLowerCase().includes('break') && !name.toLowerCase().includes('interval')) {
                    periodNumber++;
                }
            } else {
                // Break slot default helper
                if (i === 3) {
                    name = "Short Break";
                    dur = 20;
                } else {
                    name = `Period ${periodNumber}`;
                    periodNumber++;
                }
            }

            const row = `
                <tr data-index="${i}">
                    <td style="padding: 8px 0;">
                        <div class="premium-form-group" style="margin: 0;">
                            <label class="premium-form-label" style="font-size: 9.5px; top: -7px;">Enter Period Name *</label>
                            <div class="premium-input-wrapper">
                                <span class="premium-input-icon"><i class="fas fa-book"></i></span>
                                <input type="text" name="periods[${i}][period_name]" class="premium-form-control p-name-inp" value="${name}" required style="height: 40px; font-size: 13px; padding-left: 36px;">
                            </div>
                        </div>
                    </td>
                    <td style="padding: 8px 0;">
                        <div class="premium-form-group" style="margin: 0;">
                            <label class="premium-form-label" style="font-size: 9.5px; top: -7px;">Period Duration *</label>
                            <div class="premium-input-wrapper">
                                <span class="premium-input-icon"><i class="fas fa-clock"></i></span>
                                <input type="number" name="periods[${i}][duration_minutes]" class="premium-form-control p-dur-inp" value="${dur}" min="1" required style="height: 40px; font-size: 13px; padding-left: 36px;" oninput="calculatePeriodTimings()">
                            </div>
                        </div>
                    </td>
                    <td style="padding: 8px 0;">
                        <div class="premium-form-group" style="margin: 0;">
                            <label class="premium-form-label" style="font-size: 9.5px; top: -7px;">Period Timings</label>
                            <div class="premium-input-wrapper">
                                <span class="premium-input-icon"><i class="fas fa-clock"></i></span>
                                <input type="text" class="premium-form-control p-timing-inp" readonly style="height: 40px; font-size: 13px; padding-left: 36px; background: #f8fafc; color: #64748b;" placeholder="08:05 AM - 08:50 AM">
                            </div>
                        </div>
                    </td>
                    <td style="padding: 8px 0; text-align: center; vertical-align: middle;">
                        <button type="button" class="btn-delete-period" onclick="deletePeriodRow(${i})" style="background: none; border: none; color: #ef4444; font-size: 16px; cursor: pointer; padding: 6px;">
                            <i class="far fa-trash-can"></i>
                        </button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        }

        calculatePeriodTimings();
    }

    // Add Period Row
    function addPeriodRow() {
        const numPeriodsInput = document.getElementById('wizard_num_periods');
        let count = parseInt(numPeriodsInput.value) || 0;
        numPeriodsInput.value = count + 1;
        generatePeriodsConfig();
    }

    // Delete Period Row
    function deletePeriodRow(index) {
        const tbody = document.getElementById('wizard-periods-tbody');
        const row = tbody.querySelector(`tr[data-index="${index}"]`);
        if (row) {
            row.remove();
            
            const numPeriodsInput = document.getElementById('wizard_num_periods');
            let count = parseInt(numPeriodsInput.value) || 0;
            if (count > 0) {
                numPeriodsInput.value = count - 1;
            }

            // Re-index remaining rows
            const rows = tbody.querySelectorAll('tr');
            rows.forEach((tr, newIdx) => {
                tr.setAttribute('data-index', newIdx);
                const nameInp = tr.querySelector('.p-name-inp');
                if (nameInp) nameInp.setAttribute('name', `periods[${newIdx}][period_name]`);
                const durInp = tr.querySelector('.p-dur-inp');
                if (durInp) durInp.setAttribute('name', `periods[${newIdx}][duration_minutes]`);
                const delBtn = tr.querySelector('.btn-delete-period');
                if (delBtn) delBtn.setAttribute('onclick', `deletePeriodRow(${newIdx})`);
            });

            calculatePeriodTimings();
        }
    }

    // Calculate Period Timings dynamically
    function calculatePeriodTimings() {
        const startTimeInput = document.getElementById('wizard_start_time').value;
        if (!startTimeInput) return;

        const timeParts = startTimeInput.split(':');
        let currentHour = parseInt(timeParts[0]);
        let currentMinute = parseInt(timeParts[1]);

        const rows = document.querySelectorAll('#wizard-periods-tbody tr');
        rows.forEach((tr) => {
            const durInput = tr.querySelector('.p-dur-inp');
            const timingInput = tr.querySelector('.p-timing-inp');
            if (!durInput || !timingInput) return;

            const duration = parseInt(durInput.value) || 0;

            let startH = currentHour;
            let startM = currentMinute;

            let endH = currentHour;
            let endM = currentMinute + duration;
            if (endM >= 60) {
                endH += Math.floor(endM / 60);
                endM = endM % 60;
            }
            endH = endH % 24;

            currentHour = endH;
            currentMinute = endM;

            const formatTime12 = (h, m) => {
                const ampm = h >= 12 ? 'PM' : 'AM';
                let displayH = h % 12;
                displayH = displayH ? displayH : 12;
                const displayM = m.toString().padStart(2, '0');
                return `${displayH.toString().padStart(2, '0')}:${displayM} ${ampm}`;
            };

            const startTimeStr = formatTime12(startH, startM);
            const endTimeStr = formatTime12(endH, endM);

            timingInput.value = `${startTimeStr} - ${endTimeStr}`;
        });
    }

    // Step Movement
    function goToStep(step) {
        if (step > currentStep) {
            for (let s = currentStep; s < step; s++) {
                if (!validateStep(s)) return;
            }
        }

        currentStep = step;
        
        for (let i = 1; i <= 3; i++) {
            const node = document.getElementById('node-' + i);
            if (i < step) {
                node.className = 'step-node completed';
            } else if (i === step) {
                node.className = 'step-node active';
            } else {
                node.className = 'step-node';
            }
        }

        document.querySelectorAll('.wizard-step-content').forEach(el => el.classList.remove('active'));
        document.getElementById('step-' + step).classList.add('active');

        if (step === 3) {
            updateSummaryScreen();
        }
    }

    function nextStep(step) {
        goToStep(step);
    }

    function prevStep(step) {
        goToStep(step);
    }

    // Step-wise validation
    function validateStep(step) {
        if (step === 1) {
            const name = document.getElementById('wizard_group_name').value.trim();
            if (!name) {
                alert('Please enter a Group Name.');
                return false;
            }
            
            const daysCount = document.querySelectorAll('.day-chk:checked').length;
            if (daysCount === 0) {
                alert('Please select at least one applicable weekday.');
                return false;
            }

            const numPeriods = parseInt(document.getElementById('wizard_num_periods').value);
            if (isNaN(numPeriods) || numPeriods <= 0) {
                alert('Please specify a valid number of periods.');
                return false;
            }

            let periodsValid = true;
            document.querySelectorAll('.p-name-inp').forEach(inp => {
                if (!inp.value.trim()) periodsValid = false;
            });
            document.querySelectorAll('.p-dur-inp').forEach(inp => {
                const val = parseInt(inp.value);
                if (isNaN(val) || val <= 0) periodsValid = false;
            });

            if (!periodsValid) {
                alert('Please enter valid details for all period names and durations.');
                return false;
            }
        }

        if (step === 2) {
            const classChecksCount = document.querySelectorAll('.cs-chk:checked').length;
            if (classChecksCount === 0) {
                alert('Please allocate at least one class section.');
                return false;
            }
        }

        return true;
    }

    // Summary calculation
    function updateSummaryScreen() {
        document.getElementById('sum-group-name').textContent = document.getElementById('wizard_group_name').value;
        
        const startTimeVal = document.getElementById('wizard_start_time').value;
        if (startTimeVal) {
            const parts = startTimeVal.split(':');
            let h = parseInt(parts[0]);
            const m = parts[1];
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12;
            h = h ? h : 12;
            document.getElementById('sum-start-time').textContent = h + ':' + m + ' ' + ampm;
        } else {
            document.getElementById('sum-start-time').textContent = '-';
        }

        const days = [];
        document.querySelectorAll('.day-chk:checked').forEach(c => {
            days.push(c.value.substr(0, 3));
        });
        document.getElementById('sum-active-days').textContent = days.join(', ');

        const start = document.getElementById('wizard_start_date').value;
        const end = document.getElementById('wizard_end_date').value;
        document.getElementById('sum-valid-range').textContent = start + ' to ' + end;

        const count = document.getElementById('wizard_num_periods').value;
        document.getElementById('sum-period-count').textContent = count + ' periods';

        const allocatedClassesCount = document.querySelectorAll('.cs-chk:checked').length;
        document.getElementById('sum-classes-count').textContent = allocatedClassesCount + ' sections';
    }

    // Submit wizard form via AJAX
    function submitWizard() {
        const btn = document.getElementById('submitWizardBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';

        const formData = new FormData(document.getElementById('wizardForm'));
        const data = {};
        formData.forEach((value, key) => {
            if (key.endsWith('[]')) {
                const cleanKey = key.slice(0, -2);
                if (!data[cleanKey]) data[cleanKey] = [];
                data[cleanKey].push(value);
            } else {
                data[key] = value;
            }
        });

        data.applicable_days = [];
        document.querySelectorAll('.day-chk:checked').forEach(chk => {
            data.applicable_days.push(chk.value);
        });

        data.class_sections = [];
        document.querySelectorAll('.cs-chk:checked').forEach(chk => {
            data.class_sections.push(chk.value);
        });

        data.periods = [];
        document.querySelectorAll('#wizard-periods-tbody tr').forEach((tr, index) => {
            const name = tr.querySelector('.p-name-inp').value;
            const duration = tr.querySelector('.p-dur-inp').value;
            const timingVal = tr.querySelector('.p-timing-inp').value;
            const times = timingVal.split(' - ');
            
            data.periods.push({
                period_name: name,
                duration_minutes: duration,
                start_time: times[0] || null,
                end_time: times[1] || null
            });
        });

        const method = typeof editingGroupId !== 'undefined' && editingGroupId ? 'PUT' : 'POST';
        const url = typeof editingGroupId !== 'undefined' && editingGroupId ? `/school/timetable/groups/${editingGroupId}` : '/school/timetable/groups';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => {
            if (!response.ok && response.status !== 422) {
                throw new Error('Server error: ' + response.status);
            }
            return response.json();
        })
        .then(result => {
            if (result.success) {
                const successMsg = typeof editingGroupId !== 'undefined' && editingGroupId ? 'Group template updated successfully!' : 'Group template created successfully!';
                showWizardToast(successMsg, 'success');
                setTimeout(() => location.reload(), 1200);
            } else {
                const msg = result.message || (result.errors ? Object.values(result.errors).flat().join(', ') : 'Validation error');
                showWizardToast('Failed: ' + msg, 'error');
                btn.disabled = false;
                btn.innerHTML = 'Confirm & Save Template';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showWizardToast('Something went wrong. Please check the form fields.', 'error');
            btn.disabled = false;
            btn.innerHTML = 'Confirm & Save Template';
        });
    }

    function showWizardToast(message, type) {
        const existingToast = document.getElementById('wizard-toast-notif');
        if (existingToast) existingToast.remove();
        const colors = { success: '#10b981', error: '#ef4444' };
        const toast = document.createElement('div');
        toast.id = 'wizard-toast-notif';
        toast.style.cssText = `
            position: fixed; bottom: 24px; right: 24px; z-index: 99999;
            background: ${colors[type] || '#10b981'}; color: #fff;
            padding: 12px 20px; border-radius: 10px;
            font-weight: 700; font-size: 13.5px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            display: flex; align-items: center; gap: 10px;
            font-family: 'Outfit', sans-serif;
        `;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => { toast.style.opacity = '0'; toast.style.transition = 'opacity 0.5s'; setTimeout(() => toast.remove(), 500); }, 3000);
    }
</script>
