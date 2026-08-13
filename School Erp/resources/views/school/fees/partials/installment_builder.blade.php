<div class="ib-wrapper">
    <!-- Frequency Selection -->
    <div class="ib-field-group">
        <label class="ib-label">Payment Frequency Preset</label>
        <div class="ib-freq-buttons" id="ibFreqGroup">
            <button type="button" class="ib-freq-btn active" data-value="custom">Custom</button>
            <button type="button" class="ib-freq-btn" data-value="monthly">Monthly</button>
            <button type="button" class="ib-freq-btn" data-value="quarterly">Quarterly</button>
            <button type="button" class="ib-freq-btn" data-value="yearly">Yearly</button>
        </div>
        <input type="hidden" name="installment_type" id="ibInstallmentType" value="custom">
    </div>

    <!-- Custom Count Slider -->
    <div class="ib-field-group" id="ibSliderWrapper">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:8px;">
            <label class="ib-label" style="margin-bottom:0;">Number of Installments</label>
            <span class="ib-slider-val" id="ibSliderVal">4</span>
        </div>
        <input type="range" name="custom_count" id="ibSlider" min="1" max="24" value="4" class="ib-range-slider">
    </div>

    <!-- Academic Session Bounds Display -->
    <div class="ib-session-info">
        <div class="ib-info-title">Academic Session Bound</div>
        <div class="ib-info-dates">
            <i class="fas fa-calendar-alt"></i> 
            <span id="ibSessionStartStr">{{ \Carbon\Carbon::parse($selectedSession->start_date)->format('d M Y') }}</span>
            &rarr;
            <span id="ibSessionEndStr">{{ \Carbon\Carbon::parse($selectedSession->end_date)->format('d M Y') }}</span>
        </div>
        <input type="hidden" id="ibSessionStart" value="{{ $selectedSession->start_date }}">
        <input type="hidden" id="ibSessionEnd" value="{{ $selectedSession->end_date }}">
    </div>

    <!-- Auto-Fine Policy Selection -->
    <div class="ib-fine-section">
        <div class="ib-fine-title"><i class="fas fa-hand-holding-dollar"></i> Overdue Fine Automation</div>
        <div class="ib-fine-grid">
            <div class="ib-field-group" style="margin-bottom:0;">
                <label class="ib-label">Late Fine Rule</label>
                <select name="fine_id" id="ibFineId" class="ib-select">
                    <option value="">None (No auto-fine)</option>
                    @foreach($fines as $fine)
                        <option value="{{ $fine->id }}">{{ $fine->name }} (₹{{ $fine->fine_amount }} - {{ $fine->fine_type }})</option>
                    @endforeach
                </select>
            </div>
            <div class="ib-field-group" style="margin-bottom:0;">
                <label class="ib-label">Default Grace Days</label>
                <input type="number" id="ibDefaultGrace" class="ib-input" value="5" min="0">
            </div>
        </div>
    </div>

    <!-- Generated Installments Table -->
    <div class="ib-table-section">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px; flex-wrap:wrap; gap:8px;">
            <div class="ib-table-title">Installment Schedule Grid</div>
            <button type="button" class="ib-reset-btn" id="ibResetBtn"><i class="fas fa-arrows-spin"></i> Re-spread Evenly</button>
        </div>

        <div class="ib-table-container">
            <table class="ib-table">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Installment Name</th>
                        <th>Start Date</th>
                        <th>End/Due Date</th>
                        <th style="width:100px;">Grace (Days)</th>
                    </tr>
                </thead>
                <tbody id="ibTableBody">
                    <!-- Dynamic Rows -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Serialized Output Hidden Input -->
    <input type="hidden" name="installments" id="ibSerializedInstallments" value="">
</div>

<style>
    /* Premium Modern Design System for Builder */
    .ib-wrapper {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        color: #1e293b;
        font-family: 'Inter', sans-serif;
    }
    .ib-field-group {
        margin-bottom: 20px;
    }
    .ib-label {
        display: block;
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .ib-freq-buttons {
        display: flex;
        gap: 8px;
        background: #f1f5f9;
        padding: 4px;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }
    .ib-freq-btn {
        flex: 1;
        padding: 10px 14px;
        border-radius: 6px;
        border: none;
        background: transparent;
        font-size: 13px;
        font-weight: 700;
        color: #64748b;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }
    .ib-freq-btn:hover {
        color: #334155;
    }
    .ib-freq-btn.active {
        background: #ffffff;
        color: #2563eb;
        box-shadow: 0 2px 6px rgba(37, 99, 235, 0.08), 0 1px 2px rgba(0, 0, 0, 0.04);
    }
    .ib-slider-val {
        background: #eff6ff;
        color: #2563eb;
        font-size: 13px;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 6px;
        border: 1px solid #bfdbfe;
    }
    .ib-range-slider {
        width: 100% !important;
        -webkit-appearance: none !important;
        appearance: none !important;
        height: 16px !important;
        border-radius: 8px !important;
        background: transparent !important; /* Let the track style define the background */
        outline: none !important;
        margin: 20px 0 !important;
    }
    /* Webkit (Chrome, Safari, Edge) Track */
    .ib-range-slider::-webkit-slider-runnable-track {
        width: 100% !important;
        height: 16px !important;
        cursor: pointer !important;
        background: #cbd5e1 !important;
        border-radius: 8px !important;
        border: none !important;
    }
    /* Firefox Track */
    .ib-range-slider::-moz-range-track {
        width: 100% !important;
        height: 16px !important;
        cursor: pointer !important;
        background: #cbd5e1 !important;
        border-radius: 8px !important;
        border: none !important;
    }
    /* Webkit Thumb */
    .ib-range-slider::-webkit-slider-thumb {
        -webkit-appearance: none !important;
        appearance: none !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 50% !important;
        background: #2563eb !important;
        cursor: pointer !important;
        margin-top: -8px !important; /* Center the thumb on the Webkit track: (track_height/2) - (thumb_height/2) = (16/2) - (32/2) = -8px */
        transition: transform 0.1s ease !important;
        box-shadow: 0 3px 8px rgba(0,0,0,0.3) !important;
        border: 3px solid #ffffff !important;
    }
    .ib-range-slider::-webkit-slider-thumb:hover {
        transform: scale(1.15) !important;
    }
    /* Firefox Thumb */
    .ib-range-slider::-moz-range-thumb {
        width: 32px !important;
        height: 32px !important;
        border-radius: 50% !important;
        background: #2563eb !important;
        cursor: pointer !important;
        box-shadow: 0 3px 8px rgba(0,0,0,0.3) !important;
        border: 3px solid #ffffff !important;
    }
    .ib-session-info {
        background: #f8fafc;
        border: 1px dashed #cbd5e1;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }
    .ib-info-title {
        font-size: 12px;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
    }
    .ib-info-dates {
        font-size: 13px;
        font-weight: 700;
        color: #0f172a;
    }
    .ib-info-dates i {
        color: #2563eb;
        margin-right: 4px;
    }
    .ib-fine-section {
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 10px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .ib-fine-title {
        font-size: 13px;
        font-weight: 800;
        color: #b45309;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .ib-fine-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 12px;
    }
    .ib-select, .ib-input {
        width: 100%;
        padding: 8px 12px;
        border-radius: 6px;
        border: 1px solid #d1d5db;
        background: #ffffff;
        font-size: 13px;
        color: #1e293b;
        outline: none;
    }
    .ib-select:focus, .ib-input:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.15);
    }
    .ib-table-section {
        margin-top: 20px;
    }
    .ib-table-title {
        font-size: 13px;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
    }
    .ib-reset-btn {
        background: #f8fafc;
        border: 1px solid #cbd5e1;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 700;
        color: #475569;
        border-radius: 6px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s ease;
    }
    .ib-reset-btn:hover {
        background: #f1f5f9;
        color: #1e293b;
        border-color: #94a3b8;
    }
    .ib-table-container {
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        overflow-x: auto;
    }
    .ib-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 13px;
        min-width: 580px;
    }
    .ib-table th {
        background: #f8fafc;
        padding: 10px 12px;
        font-weight: 700;
        color: #475569;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }
    .ib-table td {
        padding: 8px 12px;
        border-bottom: 1px solid #e2e8f0;
        vertical-align: middle;
    }
    .ib-table tr:last-child td {
        border-bottom: none;
    }
    .ib-table-input {
        width: 100%;
        padding: 6px 10px;
        border-radius: 4px;
        border: 1px solid #cbd5e1;
        font-size: 13px;
        color: #1e293b;
        outline: none;
    }
    .ib-table-input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    }
</style>

<script>
    (function() {
        const sessionStart = document.getElementById('ibSessionStart').value;
        const sessionEnd = document.getElementById('ibSessionEnd').value;
        
        let installments = [];
        let isManuallyEdited = false;

        // Elements
        const freqGroup = document.getElementById('ibFreqGroup');
        const sliderWrapper = document.getElementById('ibSliderWrapper');
        const slider = document.getElementById('ibSlider');
        const sliderVal = document.getElementById('ibSliderVal');
        const tableBody = document.getElementById('ibTableBody');
        const hiddenType = document.getElementById('ibInstallmentType');
        const hiddenSerialized = document.getElementById('ibSerializedInstallments');
        const resetBtn = document.getElementById('ibResetBtn');
        const fineSelect = document.getElementById('ibFineId');
        const graceInput = document.getElementById('ibDefaultGrace');
        const parentForm = hiddenType.closest('form');

        // Initial setup: Custom Mode with 4 installments
        updateFrequency('custom', 4);

        // Bind events
        freqGroup.addEventListener('click', function(e) {
            const btn = e.target.closest('.ib-freq-btn');
            if (!btn) return;

            freqGroup.querySelectorAll('.ib-freq-btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const freq = btn.getAttribute('data-value');
            updateFrequency(freq);
        });

        slider.addEventListener('input', function() {
            const val = parseInt(this.value);
            sliderVal.innerText = val;
            if (hiddenType.value === 'custom') {
                regenerateInstallments('custom', val);
            }
        });

        resetBtn.addEventListener('click', function() {
            isManuallyEdited = false;
            const freq = hiddenType.value;
            const count = freq === 'custom' ? parseInt(slider.value) : null;
            regenerateInstallments(freq, count);
        });

        graceInput.addEventListener('input', function() {
            const defaultGrace = parseInt(this.value) || 0;
            if (!isManuallyEdited) {
                // If not manually edited, override all row grace days with default
                installments.forEach(inst => {
                    inst.grace_days = defaultGrace;
                });
                renderTable();
            }
        });

        // Add form submit listener to serialize data and enforce name validation
        if (parentForm) {
            parentForm.addEventListener('submit', function(e) {
                // Read current input values from DOM to capture final manual edits
                const rows = tableBody.querySelectorAll('.ib-row');
                const finalInstallments = [];
                let hasError = false;

                rows.forEach(row => {
                    const nameInput = row.querySelector('.ib-name-input');
                    const isValid = validateRowInput(nameInput);
                    if (!isValid) {
                        hasError = true;
                    }
                    finalInstallments.push({
                        installment_no: parseInt(row.getAttribute('data-no')),
                        name: nameInput.value,
                        start_date: row.querySelector('.ib-start-input').value,
                        end_date: row.querySelector('.ib-end-input').value,
                        due_date: row.querySelector('.ib-end-input').value, // end_date is forced as due_date
                        grace_days: parseInt(row.querySelector('.ib-grace-input').value) || 0
                    });
                });

                if (hasError) {
                    e.preventDefault();
                    e.stopPropagation();
                    const firstErrInput = tableBody.querySelector('.ib-name-input[style*="ef4444"]');
                    if (firstErrInput) firstErrInput.focus();
                    return false;
                }

                hiddenSerialized.value = JSON.stringify(finalInstallments);
            });
        }

        function validateInstallmentName(name) {
            name = (name || '').trim();
            if (!name) return false;

            // Format 1: Installment N (e.g. Installment 1, Installment 2)
            if (/^Installment\s+[1-9]\d*$/i.test(name)) return true;

            // Format 2: Month Name + Year (e.g. June 2026, July 2026)
            const months = 'January|February|March|April|May|June|July|August|September|October|November|December|Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Sept|Oct|Nov|Dec';
            if (new RegExp('^(' + months + ')\\s+\\d{4}$', 'i').test(name)) return true;

            // Format 3: Quarter Format (e.g. Q1 (Jun-Aug 2026), Q2 (Sep-Nov 2026))
            const shortMonths = 'Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Sept|Oct|Nov|Dec';
            if (new RegExp('^Q\\d+\\s*\\((' + shortMonths + ')-(' + shortMonths + ')\\s+\\d{4}\\)$', 'i').test(name)) return true;

            // Format 4: Session Format (e.g. Session 2026-27, Session 2026-2027)
            if (/^Session\s+\d{4}(-\d{2,4})?$/i.test(name)) return true;

            return false;
        }

        function validateRowInput(inputEl) {
            if (!inputEl) return true;
            const val = inputEl.value;
            const errorDiv = inputEl.nextElementSibling;
            if (validateInstallmentName(val)) {
                inputEl.style.borderColor = '#cbd5e1';
                if (errorDiv) errorDiv.style.display = 'none';
                return true;
            } else {
                inputEl.style.borderColor = '#ef4444';
                if (errorDiv) errorDiv.style.display = 'block';
                return false;
            }
        }

        // Functions
        function updateFrequency(type, countOverride = null) {
            hiddenType.value = type;
            if (type === 'custom') {
                sliderWrapper.style.display = 'block';
                const count = countOverride !== null ? countOverride : parseInt(slider.value);
                slider.value = count;
                sliderVal.innerText = count;
                regenerateInstallments('custom', count);
            } else {
                sliderWrapper.style.display = 'none';
                regenerateInstallments(type);
            }
        }

        function regenerateInstallments(type, count = null) {
            const start = new Date(sessionStart);
            const end = new Date(sessionEnd);
            const defaultGrace = parseInt(graceInput.value) || 0;

            installments = [];

            if (type === 'monthly') {
                let current = new Date(start);
                let instNo = 1;
                while (current < end || current.getMonth() === end.getMonth() && current.getFullYear() === end.getFullYear()) {
                    let rStart = new Date(current);
                    if (instNo > 1) {
                        rStart.setDate(1);
                    }
                    let rEnd = new Date(rStart.getFullYear(), rStart.getMonth() + 1, 0); // Last day of month
                    if (rEnd > end) {
                        rEnd = new Date(end);
                    }

                    installments.push({
                        installment_no: instNo,
                        name: rStart.toLocaleString('default', { month: 'long', year: 'numeric' }),
                        start_date: formatDate(rStart),
                        end_date: formatDate(rEnd),
                        due_date: formatDate(rEnd),
                        grace_days: defaultGrace
                    });

                    current.setMonth(current.getMonth() + 1);
                    current.setDate(1);
                    instNo++;
                }
            } else if (type === 'quarterly') {
                // Chunk the monthly calculation
                let monthlySegs = [];
                let current = new Date(start);
                let instNo = 1;
                while (current < end || current.getMonth() === end.getMonth() && current.getFullYear() === end.getFullYear()) {
                    let rStart = new Date(current);
                    if (instNo > 1) {
                        rStart.setDate(1);
                    }
                    let rEnd = new Date(rStart.getFullYear(), rStart.getMonth() + 1, 0);
                    if (rEnd > end) {
                        rEnd = new Date(end);
                    }
                    monthlySegs.push({ start: rStart, end: rEnd });
                    current.setMonth(current.getMonth() + 1);
                    current.setDate(1);
                    instNo++;
                }

                // Chunk into 3-month segments
                let chunkIdx = 1;
                for (let i = 0; i < monthlySegs.length; i += 3) {
                    let group = monthlySegs.slice(i, i + 3);
                    let gStart = group[0].start;
                    let gEnd = group[group.length - 1].end;

                    installments.push({
                        installment_no: chunkIdx,
                        name: "Q" + chunkIdx + " (" + gStart.toLocaleString('default', { month: 'short' }) + "-" + gEnd.toLocaleString('default', { month: 'short' }) + " " + gStart.getFullYear() + ")",
                        start_date: formatDate(gStart),
                        end_date: formatDate(gEnd),
                        due_date: formatDate(gEnd),
                        grace_days: defaultGrace
                    });
                    chunkIdx++;
                }
            } else if (type === 'yearly') {
                installments.push({
                    installment_no: 1,
                    name: "Session " + start.getFullYear() + "-" + (end.getFullYear() % 100),
                    start_date: formatDate(start),
                    end_date: formatDate(end),
                    due_date: formatDate(end),
                    grace_days: defaultGrace
                });
            } else {
                // Custom Mode
                const totalDays = Math.floor((end - start) / (1000 * 60 * 60 * 24)) + 1;
                const segmentLength = Math.floor(totalDays / count);
                let currentStart = new Date(start);

                for (let i = 1; i <= count; i++) {
                    let rStart = new Date(currentStart);
                    let rEnd;
                    if (i === count) {
                        rEnd = new Date(end);
                    } else {
                        rEnd = new Date(rStart);
                        rEnd.setDate(rStart.getDate() + segmentLength - 1);
                    }

                    installments.push({
                        installment_no: i,
                        name: "Installment " + i,
                        start_date: formatDate(rStart),
                        end_date: formatDate(rEnd),
                        due_date: formatDate(rEnd),
                        grace_days: defaultGrace
                    });

                    currentStart = new Date(rEnd);
                    currentStart.setDate(rEnd.getDate() + 1);
                }
            }

            renderTable();
        }

        function serializeInstallments() {
            const rows = tableBody.querySelectorAll('.ib-row');
            const finalInstallments = [];
            rows.forEach(row => {
                finalInstallments.push({
                    installment_no: parseInt(row.getAttribute('data-no')),
                    name: row.querySelector('.ib-name-input').value,
                    start_date: row.querySelector('.ib-start-input').value,
                    end_date: row.querySelector('.ib-end-input').value,
                    due_date: row.querySelector('.ib-end-input').value, // end_date is forced as due_date
                    grace_days: parseInt(row.querySelector('.ib-grace-input').value) || 0
                });
            });
            hiddenSerialized.value = JSON.stringify(finalInstallments);
        }

        function renderTable() {
            tableBody.innerHTML = '';
            installments.forEach(inst => {
                const tr = document.createElement('tr');
                tr.className = 'ib-row';
                tr.setAttribute('data-no', inst.installment_no);
                tr.innerHTML = `
                    <td style="font-weight:700; color:#64748b;">${inst.installment_no}</td>
                    <td>
                        <input type="text" class="ib-table-input ib-name-input" value="${escapeHtml(inst.name)}" oninput="window.ibHandleEdit(this)" onchange="window.ibHandleEdit(this)">
                        <div class="ib-name-error-msg" style="display:none; color:#ef4444; font-size:11px; font-weight:600; margin-top:4px; line-height:1.3;">
                            Invalid Installment Name.<br>
                            Allowed formats:<br>
                            • Installment 1<br>
                            • June 2026<br>
                            • Q1 (Jun-Aug 2026)<br>
                            • Session 2026-27
                        </div>
                    </td>
                    <td>
                        <input type="date" class="ib-table-input ib-start-input" value="${inst.start_date}" onchange="window.ibHandleEdit(this)">
                    </td>
                    <td>
                        <input type="date" class="ib-table-input ib-end-input" value="${inst.end_date}" onchange="window.ibHandleEdit(this)">
                    </td>
                    <td>
                        <input type="number" class="ib-table-input ib-grace-input" value="${inst.grace_days}" min="0" oninput="window.ibHandleEdit(this)">
                    </td>
                `;
                tableBody.appendChild(tr);
            });
            serializeInstallments();
        }

        function formatDate(date) {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        }

        function escapeHtml(text) {
            return text
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        window.ibHandleEdit = function(inputEl) {
            isManuallyEdited = true;
            if (inputEl && inputEl.classList.contains('ib-name-input')) {
                validateRowInput(inputEl);
            }
            serializeInstallments();
        };

        // EXPOSED GLOBAL LOADER FOR EDIT MODALS
        window.loadInstallmentBuilderData = function(schedule) {
            if (!schedule) return;

            // Load fine policy
            fineSelect.value = schedule.fine_id || '';
            
            // Set installments count and details
            const insts = schedule.installments || [];
            if (insts.length > 0) {
                // Find first row to check grace period
                graceInput.value = insts[0].grace_days || 0;
            }

            // Set type
            const type = schedule.installment_type || 'custom';
            hiddenType.value = type;
            
            // Active button class update
            freqGroup.querySelectorAll('.ib-freq-btn').forEach(btn => {
                if (btn.getAttribute('data-value') === type) {
                    btn.classList.add('active');
                } else {
                    btn.classList.remove('active');
                }
            });

            if (type === 'custom') {
                sliderWrapper.style.display = 'block';
                slider.value = insts.length;
                sliderVal.innerText = insts.length;
            } else {
                sliderWrapper.style.display = 'none';
            }

            // Load installments array directly
            installments = insts.map(inst => ({
                installment_no: inst.installment_no,
                name: inst.name,
                start_date: inst.start_date,
                end_date: inst.end_date,
                due_date: inst.due_date || inst.end_date,
                grace_days: inst.grace_days
            }));

            isManuallyEdited = true; // Mark as edited so rendering doesn't overwrite it
            renderTable();
        };
    })();
</script>
