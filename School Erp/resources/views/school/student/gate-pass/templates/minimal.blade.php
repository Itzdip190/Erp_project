{{-- MINIMAL CLEAN GATE PASS TEMPLATE --}}
@php
    $resolvedLogo = $schoolLogo ?? ($student->school?->logo_base64 ?: ($student->school?->logo_url ?: null));
@endphp
<div class="gate-pass-sheet minimal-theme" style="background:#ffffff; color:#1e293b; font-family:'Inter', -apple-system, BlinkMacSystemFont, sans-serif; border-radius:6px; box-shadow:0 4px 15px rgba(0,0,0,0.05); width:100%; max-width:720px; margin:0 auto; box-sizing:border-box; border:1.5px solid #334155; padding:24px 28px;">
    
    <!-- Minimal Header -->
    <div style="display:flex; justify-content:space-between; align-items:center; border-bottom:2px solid #0f172a; padding-bottom:12px; margin-bottom:16px;">
        <div style="display:flex; align-items:center; gap:12px;">
            <img id="pv-school-logo-minimal" src="{{ $resolvedLogo ?: '' }}" alt="Logo" onerror="this.style.display='none';" style="width:48px; height:48px; object-fit:contain; {{ empty($resolvedLogo) ? 'display:none;' : '' }}">
            <div>
                <h2 id="pv-school-name-minimal" style="font-size:18px; font-weight:800; text-transform:uppercase; margin:0; letter-spacing:0.5px; color:#0f172a;">
                    {{ $schoolName ?? $student->school?->name ?? "School Name" }}
                </h2>
                <div style="font-size:11px; color:#64748b;">
                    <span id="pv-school-address-minimal">{{ $schoolAddress ?? $student->school?->address ?? '' }}</span>
                    <span id="pv-school-city-state-minimal">{{ ($schoolCity ?? '') ? ', ' . $schoolCity : '' }}</span>
                </div>
            </div>
        </div>
        <div style="text-align:right;">
            <div style="font-size:14px; font-weight:900; letter-spacing:1px; text-transform:uppercase; color:#0f172a;">GATE PASS</div>
            <div style="font-size:11px; font-family:monospace; color:#475569; font-weight:700;">#<span id="pv-ref-no-minimal">{{ $autoNumber ?? '2026/GP/1' }}</span></div>
        </div>
    </div>

    <!-- Metadata Line -->
    <div style="display:flex; justify-content:space-between; font-size:12px; margin-bottom:16px; color:#475569; border-bottom:1px solid #e2e8f0; padding-bottom:8px;">
        <div>Date & Time: <strong id="pv-date-minimal" style="color:#0f172a;">{{ $currentDateTime ?? now()->format('d M Y h:i a') }}</strong></div>
        <div>Status: <strong style="color:#0f172a; text-transform:uppercase;">VALID OUTPASS</strong></div>
    </div>

    <!-- Details Box -->
    <div style="display:flex; gap:16px; margin-bottom:18px; align-items:flex-start;">
        <div style="flex-grow:1;">
            <table style="width:100%; border-collapse:collapse; font-size:12.5px;">
                <tr style="border-bottom:1px solid #f1f5f9; height:26px;">
                    <td style="width:140px; color:#64748b; font-weight:600;">Student Name</td>
                    <td id="pv-student-name-minimal" style="font-weight:700; color:#0f172a;">{{ $student->full_name }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9; height:26px;">
                    <td style="color:#64748b; font-weight:600;">Admission / Roll</td>
                    <td id="pv-adm-roll-minimal" style="color:#0f172a;">{{ $student->admission_number }} {{ $student->roll_number ? ' / Roll: ' . $student->roll_number : '' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9; height:26px;">
                    <td style="color:#64748b; font-weight:600;">Class & Section</td>
                    <td id="pv-class-sec-minimal" style="color:#0f172a;">{{ $student->class?->name ?? '' }} {{ $student->section?->name ?? '' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9; height:26px;">
                    <td style="color:#64748b; font-weight:600;">Guardian Name</td>
                    <td id="pv-guardian-name-minimal" style="font-weight:700; color:#0f172a;">{{ $student->father_name ?? $student->guardian_name ?? 'Father' }}</td>
                </tr>
                <tr style="border-bottom:1px solid #f1f5f9; height:26px;">
                    <td style="color:#64748b; font-weight:600;">Relationship</td>
                    <td id="pv-guardian-relation-minimal" style="color:#0f172a;">Father</td>
                </tr>
                <tr style="height:26px;">
                    <td style="color:#64748b; font-weight:600;">Reason</td>
                    <td id="pv-reason-minimal" style="font-weight:700; color:#0f172a;">Medical Checkup / Illness</td>
                </tr>
            </table>
        </div>

        <div style="flex-shrink:0; width:80px; height:96px; border:1px solid #cbd5e1; border-radius:4px; overflow:hidden; background:#f8fafc; text-align:center;">
            <img id="pv-student-photo-minimal" src="{{ $student->photo_url }}" alt="Photo" style="width:100%; height:100%; object-fit:cover; {{ !$student->photo ? 'display:none;' : '' }}">
            <div id="pv-student-photo-placeholder-minimal" style="display:{{ $student->photo ? 'none' : 'flex' }}; height:100%; align-items:center; justify-content:center; color:#94a3b8; font-size:10px;">
                Photo
            </div>
        </div>
    </div>

    <!-- Signatures Row -->
    <div style="display:grid; grid-template-columns: repeat(4, 1fr); gap:12px; text-align:center; padding-top:20px; border-top:1px solid #0f172a; font-size:11px;">
        <div>
            <div style="height:28px; border-bottom:1px dashed #94a3b8; margin-bottom:4px;"></div>
            <span>Student</span>
        </div>
        <div>
            <div style="height:28px; border-bottom:1px dashed #94a3b8; margin-bottom:4px;"></div>
            <span>Guardian</span>
        </div>
        <div>
            <div style="height:28px; border-bottom:1px dashed #94a3b8; margin-bottom:4px;"></div>
            <span>Security</span>
        </div>
        <div>
            <div style="height:28px; border-bottom:1px dashed #94a3b8; margin-bottom:4px;"></div>
            <span>Authorized Signature</span>
        </div>
    </div>
</div>
