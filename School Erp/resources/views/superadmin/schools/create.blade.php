@extends('superadmin.layouts.master')

@section('styles')
<style>
/* ─── CREATE SCHOOL FORM ───────────────────────────────────── */
.sa-form-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    box-shadow: 0 4px 24px rgba(0,0,0,0.03);
    overflow: hidden;
    max-width: 900px;
    margin: 0 auto 30px;
}

.sa-form-hdr {
    padding: 20px 24px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    align-items: center;
    gap: 12px;
}
.sa-form-hdr-icon {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: rgba(99,102,241,0.1);
    color: #6366f1;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
}
.sa-form-hdr h3 { font-size: 15px; font-weight: 800; color: #1e1b4b; margin: 0; }
.sa-form-hdr p { font-size: 11px; color: #64748b; margin: 2px 0 0; }

.sa-form-body { padding: 24px; }

.sa-form-section-title {
    font-size: 11.5px;
    font-weight: 800;
    color: #4f46e5;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    margin-bottom: 18px;
    padding-bottom: 6px;
    border-bottom: 1.5px solid rgba(79,70,229,0.1);
}

.sa-form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
    margin-bottom: 16px;
}
@media(max-width: 600px) { .sa-form-row { grid-template-columns: 1fr; } }

.sa-form-group { margin-bottom: 16px; }
.sa-form-group label {
    display: block;
    font-size: 11.5px;
    font-weight: 700;
    color: #374151;
    margin-bottom: 6px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}
.sa-form-group label span { color: #ef4444; }

.sa-input, .sa-select, .sa-textarea {
    width: 100%;
    padding: 10px 14px;
    border: 1.5px solid #e2e8f0;
    border-radius: 10px;
    font-size: 13.5px;
    outline: none;
    background: #f8fafc;
    color: #1e1b4b;
    transition: all .2s;
    font-family: 'Lato', sans-serif;
}
.sa-input:focus, .sa-select:focus, .sa-textarea:focus {
    border-color: #6366f1;
    background: #fff;
    box-shadow: 0 0 0 3px rgba(99,102,241,0.12);
}
.sa-textarea { height: 80px; resize: vertical; }

.sa-select {
    cursor: pointer;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%236b7280' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 14px center;
    padding-right: 36px;
}

.sa-submit-row {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
    border-top: 1px solid #f1f5f9;
    padding-top: 20px;
}
.btn-sa-cancel {
    padding: 10px 20px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    color: #4b5563;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex; align-items: center; justify-content: center;
    transition: all .2s;
}
.btn-sa-cancel:hover { background: #f8fafc; border-color: #cbd5e1; }

.btn-sa-submit {
    padding: 10px 24px;
    border-radius: 10px;
    border: none;
    background: linear-gradient(135deg, #4f46e5, #6366f1);
    color: #fff;
    font-size: 13px; font-weight: 700;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(99,102,241,0.3);
    display: inline-flex; align-items: center; gap: 8px;
    transition: all .2s;
}
.btn-sa-submit:hover { transform: translateY(-1px); box-shadow: 0 6px 18px rgba(99,102,241,0.4); }

/* Error messages */
.form-err { font-size: 11px; color: #ef4444; margin-top: 4px; display: block; font-weight: 600; }

/* Dark mode overrides */
body.dark-mode .sa-form-card { background: #111827 !important; border-color: #1e293b !important; }
body.dark-mode .sa-form-hdr { border-bottom-color: #1e293b !important; }
body.dark-mode .sa-form-hdr h3,
body.dark-mode .sa-form-group label { color: #f1f5f9 !important; }
body.dark-mode .sa-form-hdr p { color: #64748b !important; }
body.dark-mode .sa-input,
body.dark-mode .sa-select,
body.dark-mode .sa-textarea { background: #1f2937 !important; border-color: #374151 !important; color: #f1f5f9 !important; }
body.dark-mode .sa-input:focus,
body.dark-mode .sa-select:focus,
body.dark-mode .sa-textarea:focus { background: #111827 !important; border-color: #6366f1 !important; }
body.dark-mode .sa-submit-row { border-top-color: #1e293b !important; }
body.dark-mode .btn-sa-cancel { background: #1f2937 !important; border-color: #374151 !important; color: #cbd5e1 !important; }
body.dark-mode .btn-sa-cancel:hover { background: #111827 !important; }
</style>
@endsection

@section('content')
<div class="sa-form-card">
    <div class="sa-form-hdr">
        <div class="sa-form-hdr-icon">
            <i class="fas fa-plus-circle"></i>
        </div>
        <div>
            <h3>Onboard New School</h3>
            <p>Setup a new tenant database entry and primary administrative account</p>
        </div>
    </div>

    <form action="{{ route('superadmin.schools.store') }}" method="POST">
        @csrf
        <div class="sa-form-body">
            
            {{-- 1. School Information --}}
            <div class="sa-form-section-title">School Details</div>
            
            <div class="sa-form-row">
                <div class="sa-form-group">
                    <label>School Name <span>*</span></label>
                    <input type="text" name="name" class="sa-input" placeholder="e.g. Oakridge International School" value="{{ old('name') }}" required>
                    @error('name')<span class="form-err">{{ $message }}</span>@enderror
                </div>
                <div class="sa-form-group">
                    <label>State <span>*</span></label>
                    <select name="state" class="sa-select" required>
                        <option value="">Select State...</option>
                        @foreach($states as $codeVal => $stateName)
                            <option value="{{ $codeVal }}" {{ old('state') == $codeVal ? 'selected' : '' }}>{{ $stateName }}</option>
                        @endforeach
                    </select>
                    @error('state')<span class="form-err">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="sa-form-row">
                <div class="sa-form-group">
                    <label>Unique Code (Account ID) <span>*</span></label>
                    <input type="text" name="code" class="sa-input" placeholder="Select state to generate ID" value="{{ old('code') }}" readonly required>
                    @error('code')<span class="form-err">{{ $message }}</span>@enderror
                </div>
                <div class="sa-form-group">
                    <label>School Board / Type <span>*</span></label>
                    <select name="school_type" class="sa-select" required>
                        <option value="">Select Board...</option>
                        <option value="CBSE" {{ old('school_type') == 'CBSE' ? 'selected' : '' }}>CBSE</option>
                        <option value="CBSE PATTERN" {{ old('school_type') == 'CBSE PATTERN' ? 'selected' : '' }}>CBSE PATTERN</option>
                        <option value="ICSE" {{ old('school_type') == 'ICSE' ? 'selected' : '' }}>ICSE</option>
                        <option value="STATE BOARD" {{ old('school_type') == 'STATE BOARD' ? 'selected' : '' }}>STATE BOARD</option>
                    </select>
                    @error('school_type')<span class="form-err">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="sa-form-row">
                <div class="sa-form-group">
                    <label>Director/Principal Full Name <span>*</span></label>
                    <input type="text" name="director_name" class="sa-input" placeholder="e.g. Dr. John Doe" value="{{ old('director_name') }}" required>
                    @error('director_name')<span class="form-err">{{ $message }}</span>@enderror
                </div>
                <div class="sa-form-group">
                    <label>School Email Address <span>*</span></label>
                    <input type="email" name="email" class="sa-input" placeholder="e.g. contact@oakridgeschool.in" value="{{ old('email') }}" required>
                    @error('email')<span class="form-err">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="sa-form-row">
                <div class="sa-form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" class="sa-input" placeholder="e.g. +91 98765 43210" value="{{ old('phone') }}">
                    @error('phone')<span class="form-err">{{ $message }}</span>@enderror
                </div>
                <div class="sa-form-group">
                    <label>Custom Domain / Domain Prefix</label>
                    <input type="text" name="custom_domain" class="sa-input" placeholder="e.g. oakridge.schoolcloud.in" value="{{ old('custom_domain') }}">
                    @error('custom_domain')<span class="form-err">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="sa-form-row">
                <div class="sa-form-group">
                    <label>Status <span>*</span></label>
                    <select name="status" class="sa-select" required>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="suspended" {{ old('status') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                    </select>
                    @error('status')<span class="form-err">{{ $message }}</span>@enderror
                </div>
                <div class="sa-form-group">
                    <label>Subscription Plan</label>
                    <select name="plan_id" class="sa-select">
                        <option value="">-- No Active Subscription (None) --</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->id }}" {{ old('plan_id') == $plan->id ? 'selected' : '' }}>
                                {{ $plan->name }} (₹{{ number_format($plan->price, 0) }} / year)
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id')<span class="form-err">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="sa-form-group">
                <label>Address</label>
                <textarea name="address" class="sa-textarea" placeholder="Enter complete physical address…">{{ old('address') }}</textarea>
                @error('address')<span class="form-err">{{ $message }}</span>@enderror
            </div>

            {{-- 2. Academic Session Information --}}
            <div class="sa-form-section-title" style="margin-top:28px;">Academic Session Details</div>

            <div class="sa-form-row">
                <div class="sa-form-group">
                    <label>Academic Session Name <span>*</span></label>
                    <input type="text" name="academic_session_name" class="sa-input" placeholder="e.g. 2026-2027" value="{{ old('academic_session_name', date('Y') . '-' . (date('Y') + 1)) }}" required>
                    @error('academic_session_name')<span class="form-err">{{ $message }}</span>@enderror
                </div>
                <div class="sa-form-group">
                    <label>Session Start Date <span>*</span></label>
                    <input type="date" name="academic_session_start_date" class="sa-input" value="{{ old('academic_session_start_date', date('Y-04-01')) }}" required>
                    @error('academic_session_start_date')<span class="form-err">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="sa-form-row">
                <div class="sa-form-group">
                    <label>Session End Date <span>*</span></label>
                    <input type="date" name="academic_session_end_date" class="sa-input" value="{{ old('academic_session_end_date', date('Y-03-31', strtotime('+1 year'))) }}" required>
                    @error('academic_session_end_date')<span class="form-err">{{ $message }}</span>@enderror
                </div>
                <div class="sa-form-group">
                    <!-- balanced space -->
                </div>
            </div>

            {{-- 3. Admin Account Information --}}
            <div class="sa-form-section-title" style="margin-top:28px;">Administrative Account</div>

            <div class="sa-form-row">
                <div class="sa-form-group">
                    <label>Admin User Name <span>*</span></label>
                    <input type="text" name="admin_name" class="sa-input" placeholder="e.g. Principal John Doe" value="{{ old('admin_name') }}" required>
                    @error('admin_name')<span class="form-err">{{ $message }}</span>@enderror
                </div>
                <div class="sa-form-group">
                    <label>Admin User Email <span>*</span></label>
                    <input type="email" name="admin_email" id="admin_email_display" class="sa-input" placeholder="Auto-filled from School Email" value="{{ old('admin_email') }}" readonly style="background:#f1f5f9; cursor:not-allowed; color:#64748b;">
                    <span style="font-size:11px;color:#64748b;margin-top:4px;display:block;">&#128274; Auto-synced from School Email Address above</span>
                    @error('admin_email')<span class="form-err">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- Auto-generated password notice --}}
            <div style="background:linear-gradient(135deg,rgba(99,102,241,0.08),rgba(79,70,229,0.05)); border:1.5px dashed rgba(99,102,241,0.3); border-radius:12px; padding:16px 20px; display:flex; align-items:flex-start; gap:14px; margin-bottom:4px;">
                <div style="background:rgba(99,102,241,0.12); border-radius:8px; width:36px; height:36px; display:flex; align-items:center; justify-content:center; flex-shrink:0; color:#4f46e5; font-size:16px;">&#128274;</div>
                <div>
                    <div style="font-size:12px; font-weight:800; color:#4f46e5; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:4px;">Auto-Generated Password</div>
                    <div style="font-size:13px; color:#374151; line-height:1.6;">
                        The admin password is auto-generated as the <strong>first 4 letters of the school name</strong> (lowercase) + <code style="background:#e0e7ff; color:#4f46e5; padding:2px 6px; border-radius:4px; font-weight:700;">@123</code><br>
                        <span style="color:#64748b;">Example: &quot;Oakridge School&quot; &rarr; <code style="background:#e0e7ff; color:#4f46e5; padding:2px 6px; border-radius:4px;">oakr@123</code></span>
                    </div>
                </div>
            </div>

            <div class="sa-submit-row">
                <a href="{{ route('superadmin.dashboard') }}" class="btn-sa-cancel">Cancel</a>
                <button type="submit" class="btn-sa-submit">
                    <i class="fas fa-check-double"></i> Create School & Admin
                </button>
            </div>

        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stateSelect = document.querySelector('select[name="state"]');
        const codeInput = document.querySelector('input[name="code"]');
        
        if (stateSelect && codeInput) {
            stateSelect.addEventListener('change', function() {
                const stateVal = this.value;
                if (!stateVal) {
                    codeInput.value = '';
                    return;
                }
                
                codeInput.value = 'Generating...';
                
                fetch(`/school/signup/next-code?state=${stateVal}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.code) {
                            codeInput.value = data.code;
                        } else {
                            codeInput.value = '';
                        }
                    })
                    .catch(err => {
                        console.error('Error fetching next code:', err);
                        codeInput.value = '';
                    });
            });
        }

        // Sync Director Name -> Admin Name
        const directorInput = document.querySelector('input[name="director_name"]');
        const adminNameInput = document.querySelector('input[name="admin_name"]');
        
        if (directorInput && adminNameInput) {
            let userInteracted = false;
            adminNameInput.addEventListener('input', function() {
                userInteracted = true;
            });
            directorInput.addEventListener('input', function() {
                if (!userInteracted) {
                    adminNameInput.value = this.value;
                }
            });
        }

        // Sync School Email -> Admin Email (always mirror)
        const schoolEmailInput = document.querySelector('input[name="email"]');
        const adminEmailDisplay = document.getElementById('admin_email_display');

        if (schoolEmailInput && adminEmailDisplay) {
            // Initialise on load
            if (!adminEmailDisplay.value && schoolEmailInput.value) {
                adminEmailDisplay.value = schoolEmailInput.value;
            }
            schoolEmailInput.addEventListener('input', function() {
                adminEmailDisplay.value = this.value;
            });
        }
    });
</script>
@endsection
