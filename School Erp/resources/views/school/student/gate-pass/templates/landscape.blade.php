{{-- LANDSCAPE DUAL-COPY GATE PASS TEMPLATE (School Copy + Security / Parent Copy) --}}
@php
    $resolvedLogo = $schoolLogo ?? ($student->school?->logo_base64 ?: ($student->school?->logo_url ?: null));
@endphp
<div class="gate-pass-sheet landscape-theme" style="background:#ffffff; color:#1e293b; font-family:'Segoe UI', Arial, sans-serif; border-radius:10px; box-shadow:0 8px 24px rgba(0,0,0,0.06); width:100%; max-width:900px; margin:0 auto; box-sizing:border-box; border:1px solid #cbd5e1; padding:20px 24px;">
    
    <div style="display:grid; grid-template-columns: 1fr 1px 1fr; gap:20px; align-items:stretch;">
        
        <!-- LEFT: SCHOOL COPY -->
        <div>
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px; border-bottom:1.5px solid #2563eb; padding-bottom:6px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    @if(!empty($resolvedLogo))
                        <img src="{{ $resolvedLogo }}" alt="Logo" onerror="this.style.display='none';" style="width:32px; height:32px; object-fit:contain;">
                    @endif
                    <div>
                        <h3 id="pv-school-name-land1" style="font-size:14px; font-weight:800; color:#0f172a; margin:0;">
                            {{ $schoolName ?? $student->school?->name ?? "School Name" }}
                        </h3>
                        <div style="font-size:10px; color:#64748b;">
                            <span id="pv-school-address-land1">{{ $schoolAddress ?? '' }}</span>
                        </div>
                    </div>
                </div>
                <div style="text-align:right;">
                    <span style="background:#eff6ff; color:#2563eb; font-weight:800; font-size:10px; padding:2px 8px; border-radius:4px; text-transform:uppercase; border:1px solid #bfdbfe;">
                        School Copy
                    </span>
                    <div style="font-size:10px; font-weight:700; color:#475569; margin-top:2px;">#<span id="pv-ref-no-land1">{{ $autoNumber ?? '2026/GP/1' }}</span></div>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; font-size:10.5px; color:#64748b; margin-bottom:10px;">
                <div>Date: <strong id="pv-date-land1" style="color:#0f172a;">{{ $currentDateTime ?? now()->format('d M Y h:i a') }}</strong></div>
                <div>Status: <strong style="color:#16a34a;">ISSUED</strong></div>
            </div>

            <!-- Details -->
            <table style="width:100%; border-collapse:collapse; font-size:11.5px; margin-bottom:12px;">
                <tr style="height:22px;">
                    <td style="width:100px; color:#64748b; font-weight:600;">Student:</td>
                    <td><strong id="pv-student-name-land1" style="color:#0f172a;">{{ $student->full_name }}</strong></td>
                </tr>
                <tr style="height:22px;">
                    <td style="color:#64748b; font-weight:600;">Class / Sec:</td>
                    <td id="pv-class-sec-land1" style="color:#0f172a; font-weight:600;">{{ $student->class?->name ?? '' }} - {{ $student->section?->name ?? '' }} (Adm: {{ $student->admission_number }})</td>
                </tr>
                <tr style="height:22px;">
                    <td style="color:#64748b; font-weight:600;">Guardian:</td>
                    <td><strong id="pv-guardian-name-land1" style="color:#0f172a;">{{ $student->father_name ?? 'Father' }}</strong> (<span id="pv-guardian-relation-land1">Father</span>)</td>
                </tr>
                <tr style="height:22px;">
                    <td style="color:#64748b; font-weight:600;">Reason:</td>
                    <td><span id="pv-reason-land1" style="background:#dbeafe; color:#1e40af; padding:1px 6px; border-radius:3px; font-weight:600; font-size:11px;">Medical / Illness</span></td>
                </tr>
            </table>

            <!-- Signatures -->
            <div style="display:flex; justify-content:space-between; text-align:center; font-size:10px; border-top:1px dashed #cbd5e1; padding-top:10px; margin-top:14px;">
                <div style="width:45%;">
                    <div style="height:20px; border-bottom:1px solid #94a3b8; margin-bottom:2px;"></div>
                    <span>Guardian Sign</span>
                </div>
                <div style="width:45%;">
                    <div style="height:20px; border-bottom:1px solid #94a3b8; margin-bottom:2px;"></div>
                    <span>Principal / Auth</span>
                </div>
            </div>
        </div>

        <!-- PERFORATED DIVIDER -->
        <div style="border-left:1px dashed #94a3b8; height:100%; position:relative;">
            <div style="position:absolute; top:50%; left:50%; transform:translate(-50%, -50%); background:#ffffff; padding:4px 0; color:#94a3b8; font-size:10px;">
                <i class="fa fa-cut"></i>
            </div>
        </div>

        <!-- RIGHT: SECURITY / PARENT COPY -->
        <div>
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:8px; border-bottom:1.5px solid #10b981; padding-bottom:6px;">
                <div style="display:flex; align-items:center; gap:8px;">
                    @if(!empty($resolvedLogo))
                        <img src="{{ $resolvedLogo }}" alt="Logo" onerror="this.style.display='none';" style="width:32px; height:32px; object-fit:contain;">
                    @endif
                    <div>
                        <h3 id="pv-school-name-land2" style="font-size:14px; font-weight:800; color:#0f172a; margin:0;">
                            {{ $schoolName ?? $student->school?->name ?? "School Name" }}
                        </h3>
                        <div style="font-size:10px; color:#64748b;">
                            <span id="pv-school-address-land2">{{ $schoolAddress ?? '' }}</span>
                        </div>
                    </div>
                </div>
                <div style="text-align:right;">
                    <span style="background:#ecfdf5; color:#059669; font-weight:800; font-size:10px; padding:2px 8px; border-radius:4px; text-transform:uppercase; border:1px solid #a7f3d0;">
                        Gate / Parent Copy
                    </span>
                    <div style="font-size:10px; font-weight:700; color:#475569; margin-top:2px;">#<span id="pv-ref-no-land2">{{ $autoNumber ?? '2026/GP/1' }}</span></div>
                </div>
            </div>

            <div style="display:flex; justify-content:space-between; font-size:10.5px; color:#64748b; margin-bottom:10px;">
                <div>Date: <strong id="pv-date-land2" style="color:#0f172a;">{{ $currentDateTime ?? now()->format('d M Y h:i a') }}</strong></div>
                <div>Status: <strong style="color:#16a34a;">AUTHORIZED</strong></div>
            </div>

            <!-- Details -->
            <table style="width:100%; border-collapse:collapse; font-size:11.5px; margin-bottom:12px;">
                <tr style="height:22px;">
                    <td style="width:100px; color:#64748b; font-weight:600;">Student:</td>
                    <td><strong id="pv-student-name-land2" style="color:#0f172a;">{{ $student->full_name }}</strong></td>
                </tr>
                <tr style="height:22px;">
                    <td style="color:#64748b; font-weight:600;">Class / Sec:</td>
                    <td id="pv-class-sec-land2" style="color:#0f172a; font-weight:600;">{{ $student->class?->name ?? '' }} - {{ $student->section?->name ?? '' }} (Adm: {{ $student->admission_number }})</td>
                </tr>
                <tr style="height:22px;">
                    <td style="color:#64748b; font-weight:600;">Guardian:</td>
                    <td><strong id="pv-guardian-name-land2" style="color:#0f172a;">{{ $student->father_name ?? 'Father' }}</strong> (<span id="pv-guardian-relation-land2">Father</span>)</td>
                </tr>
                <tr style="height:22px;">
                    <td style="color:#64748b; font-weight:600;">Reason:</td>
                    <td><span id="pv-reason-land2" style="background:#d1fae5; color:#065f46; padding:1px 6px; border-radius:3px; font-weight:600; font-size:11px;">Medical / Illness</span></td>
                </tr>
            </table>

            <!-- Signatures -->
            <div style="display:flex; justify-content:space-between; text-align:center; font-size:10px; border-top:1px dashed #cbd5e1; padding-top:10px; margin-top:14px;">
                <div style="width:45%;">
                    <div style="height:20px; border-bottom:1px solid #94a3b8; margin-bottom:2px;"></div>
                    <span>Security Gate Check</span>
                </div>
                <div style="width:45%;">
                    <div style="height:20px; border-bottom:1px solid #94a3b8; margin-bottom:2px;"></div>
                    <span>Re-entry Time / Stamp</span>
                </div>
            </div>
        </div>
    </div>
</div>
