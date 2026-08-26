{{-- MODERN ENTERPRISE GATE PASS TEMPLATE --}}
@php
    $resolvedLogo = $schoolLogo ?? ($student->school?->logo_base64 ?: ($student->school?->logo_url ?: null));
@endphp
<div class="gate-pass-sheet modern-theme" style="background:#ffffff; color:#0f172a; font-family:'Plus Jakarta Sans', Arial, sans-serif; border-radius:12px; box-shadow:0 12px 30px rgba(0,0,0,0.08); width:100%; max-width:720px; margin:0 auto; box-sizing:border-box; border:1px solid #e2e8f0; overflow:hidden;">
    
    <!-- Modern Header Banner -->
    <div style="background:linear-gradient(135deg, #1e3a8a 0%, #2563eb 100%); color:#ffffff; padding:22px 28px; position:relative;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px;">
            <div style="display:flex; align-items:center; gap:16px;">
                <div style="width:60px; height:60px; border-radius:10px; background:#ffffff; padding:4px; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 10px rgba(0,0,0,0.15);">
                    <img id="pv-school-logo-modern" src="{{ $resolvedLogo ?: '' }}" alt="Logo" onerror="this.style.display='none'; document.getElementById('pv-school-logo-placeholder-modern').style.display='block';" style="width:100%; height:100%; object-fit:contain; {{ empty($resolvedLogo) ? 'display:none;' : '' }}">
                    <div id="pv-school-logo-placeholder-modern" style="color:#1e3a8a; font-size:22px; display:{{ !empty($resolvedLogo) ? 'none' : 'block' }};">
                        <i class="fa fa-graduation-cap"></i>
                    </div>
                </div>
                <div>
                    <h2 id="pv-school-name-modern" style="font-size:20px; font-weight:800; margin:0 0 2px 0; color:#ffffff; letter-spacing:-0.3px;">
                        {{ $schoolName ?? $student->school?->name ?? "School ERP" }}
                    </h2>
                    <div style="font-size:12px; color:rgba(255,255,255,0.85); display:flex; align-items:center; gap:6px; flex-wrap:wrap;">
                        <span id="pv-school-address-modern">{{ $schoolAddress ?? $student->school?->address ?? '' }}</span>
                        <span id="pv-school-city-state-modern">{{ ($schoolCity ?? '') ? ', ' . $schoolCity : '' }}{{ ($schoolState ?? '') ? ', ' . $schoolState : '' }}</span>
                        <span id="pv-school-pincode-modern" style="background:rgba(255,255,255,0.2); padding:1px 6px; border-radius:4px; font-weight:600; font-size:11px;">{{ $schoolPincode ?? '' }}</span>
                    </div>
                </div>
            </div>

            <!-- Pass Badge -->
            <div style="text-align:right;">
                <div style="background:#ffffff; color:#1e3a8a; font-weight:800; font-size:12px; padding:4px 12px; border-radius:20px; text-transform:uppercase; letter-spacing:0.5px; display:inline-block; margin-bottom:4px; box-shadow:0 2px 6px rgba(0,0,0,0.1);">
                    Gate Pass
                </div>
                <div style="font-size:11px; color:rgba(255,255,255,0.9); font-weight:600;">
                    Ref: <span id="pv-ref-no-modern">{{ $autoNumber ?? '2026/GP/1' }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Pass Body -->
    <div style="padding:24px 28px;">
        
        <!-- Metadata Bar -->
        <div style="display:flex; justify-content:space-between; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:10px 16px; margin-bottom:20px;">
            <div style="display:flex; align-items:center; gap:8px; font-size:12.5px; color:#475569;">
                <i class="fa fa-calendar-alt" style="color:#2563eb;"></i>
                <span>Issued On: <strong id="pv-date-modern" style="color:#0f172a;">{{ $currentDateTime ?? now()->format('d M Y h:i a') }}</strong></span>
            </div>
            <div style="display:flex; align-items:center; gap:6px;">
                <span class="badge" style="background:#dcfce7; color:#15803d; border:1px solid #bbf7d0; padding:2px 8px; border-radius:6px; font-size:11px; font-weight:700;">
                    <i class="fa fa-check-circle"></i> AUTHORIZED
                </span>
            </div>
        </div>

        <!-- Student & Guardian Grid -->
        <div style="display:grid; grid-template-columns:100px 1fr 1fr; gap:18px; margin-bottom:20px; align-items:stretch;">
            <!-- Student Photo -->
            <div style="text-align:center;">
                <div style="width:100px; height:120px; border-radius:8px; border:2px solid #2563eb; overflow:hidden; background:#eff6ff; display:flex; align-items:center; justify-content:center; box-shadow:0 4px 8px rgba(37,99,235,0.1);">
                    <img id="pv-student-photo-modern" src="{{ $student->photo_url }}" alt="Photo" style="width:100%; height:100%; object-fit:cover; {{ !$student->photo ? 'display:none;' : '' }}">
                    <div id="pv-student-photo-placeholder-modern" style="display:{{ $student->photo ? 'none' : 'flex' }}; flex-direction:column; align-items:center; color:#93c5fd; font-size:11px;">
                        <i class="fa fa-user" style="font-size:32px; margin-bottom:4px;"></i>
                        <span>Student</span>
                    </div>
                </div>
            </div>

            <!-- Student Card -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px; font-size:12.5px;">
                <div style="font-size:11px; font-weight:700; color:#2563eb; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">
                    Student Information
                </div>
                <div style="margin-bottom:4px;">
                    <span style="color:#64748b;">Name:</span> <strong id="pv-student-name-modern" style="color:#0f172a;">{{ $student->full_name }}</strong>
                </div>
                <div style="margin-bottom:4px;">
                    <span style="color:#64748b;">Adm No:</span> <strong id="pv-adm-no-modern" style="color:#0f172a;">{{ $student->admission_number }}</strong>
                </div>
                <div style="margin-bottom:4px;">
                    <span style="color:#64748b;">Class / Sec:</span> <strong id="pv-class-sec-modern" style="color:#0f172a;">{{ $student->class?->name ?? '' }} - {{ $student->section?->name ?? '' }}</strong>
                </div>
                <div>
                    <span style="color:#64748b;">Father:</span> <span id="pv-father-name-modern" style="color:#0f172a; font-weight:600;">{{ $student->father_name ?? 'N/A' }}</span>
                </div>
            </div>

            <!-- Guardian Card -->
            <div style="background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px; font-size:12.5px;">
                <div style="font-size:11px; font-weight:700; color:#2563eb; text-transform:uppercase; letter-spacing:0.5px; margin-bottom:6px;">
                    Accompanying Person
                </div>
                <div style="margin-bottom:4px;">
                    <span style="color:#64748b;">Guardian:</span> <strong id="pv-guardian-name-modern" style="color:#0f172a;">{{ $student->father_name ?? $student->guardian_name ?? 'Father' }}</strong>
                </div>
                <div style="margin-bottom:4px;">
                    <span style="color:#64748b;">Relation:</span> <span id="pv-guardian-relation-modern" class="badge" style="background:#dbeafe; color:#1e40af; font-weight:700; font-size:11px; padding:1px 6px; border-radius:4px;">Father</span>
                </div>
                <div style="margin-bottom:4px;">
                    <span style="color:#64748b;">Contact:</span> <span id="pv-guardian-phone-modern" style="color:#0f172a; font-weight:600;">{{ $student->father_phone ?? $student->guardian_phone ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Reason & Departure Details Banner -->
        <div style="background:#eff6ff; border-left:4px solid #2563eb; border-radius:0 8px 8px 0; padding:12px 16px; margin-bottom:24px;">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px;">
                <div>
                    <div style="font-size:11px; font-weight:700; color:#1e40af; text-transform:uppercase;">Reason for Early Departure</div>
                    <div id="pv-reason-modern" style="font-size:14px; font-weight:700; color:#1e3a8a; margin-top:2px;">
                        Medical Checkup / Illness
                    </div>
                    <div id="pv-remarks-box-modern" style="font-size:11.5px; color:#475569; margin-top:4px; display:none;">
                        Note: <span id="pv-remarks-modern"></span>
                    </div>
                </div>
                <div id="pv-return-time-box-modern" style="text-align:right; display:none;">
                    <div style="font-size:11px; font-weight:700; color:#1e40af; text-transform:uppercase;">Expected Return</div>
                    <div id="pv-return-time-modern" style="font-size:13px; font-weight:700; color:#1e3a8a; margin-top:2px;">
                        --
                    </div>
                </div>
            </div>
        </div>

        <!-- Signatures & QR Section -->
        <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:12px; text-align:center; padding-top:14px; border-top:1px dashed #cbd5e1; font-size:11.5px;">
            <div>
                <div style="height:36px; border-bottom:1px solid #cbd5e1; margin-bottom:6px;"></div>
                <span style="font-weight:700; color:#334155;">Student</span>
            </div>
            <div>
                <div style="height:36px; border-bottom:1px solid #cbd5e1; margin-bottom:6px;"></div>
                <span style="font-weight:700; color:#334155;">Guardian</span>
            </div>
            <div>
                <div style="height:36px; border-bottom:1px solid #cbd5e1; margin-bottom:6px;"></div>
                <span style="font-weight:700; color:#334155;">Security Gate</span>
            </div>
            <div>
                <div style="height:36px; border-bottom:1px solid #cbd5e1; margin-bottom:6px;"></div>
                <span style="font-weight:700; color:#2563eb;">Authorized Seal</span>
            </div>
        </div>
    </div>
</div>
