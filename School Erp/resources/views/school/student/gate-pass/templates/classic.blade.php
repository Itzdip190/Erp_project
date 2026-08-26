{{-- CLASSIC GATE PASS TEMPLATE (Matches Reference Images 2 & 5) --}}
@php
    $resolvedLogo = $schoolLogo ?? ($student->school?->logo_base64 ?: ($student->school?->logo_url ?: null));
@endphp
<div class="gate-pass-sheet classic-theme" style="background:#ffffff; color:#1e293b; font-family:'Segoe UI', Arial, sans-serif; padding:28px 32px; border-radius:8px; box-shadow:0 10px 25px rgba(0,0,0,0.06); width:100%; max-width:720px; margin:0 auto; box-sizing:border-box; border:1px solid #e2e8f0;">
    
    <!-- School Header -->
    <div style="display:flex; align-items:center; gap:20px; margin-bottom:12px;">
        <div style="flex-shrink:0;">
            <img id="pv-school-logo" src="{{ $resolvedLogo ?: '' }}" alt="School Logo" onerror="this.style.display='none'; document.getElementById('pv-school-logo-placeholder').style.display='flex';" style="width:70px; height:70px; object-fit:contain; {{ empty($resolvedLogo) ? 'display:none;' : '' }}">
            <div id="pv-school-logo-placeholder" style="width:70px; height:70px; border-radius:10px; background:#eff6ff; border:1px dashed #93c5fd; display:{{ !empty($resolvedLogo) ? 'none' : 'flex' }}; align-items:center; justify-content:center; color:#2563eb; font-size:24px;">
                <i class="fa fa-graduation-cap"></i>
            </div>
        </div>
        <div style="flex-grow:1; text-align:center;">
            <h1 id="pv-school-name" style="font-family:'Times New Roman', serif; font-size:22px; font-weight:800; color:#0f172a; margin:0 0 4px 0; letter-spacing:0.3px; line-height:1.2;">
                {{ $schoolName ?? $student->school?->name ?? "St. Xavier's High School" }}
            </h1>
            <div style="font-size:12px; color:#475569; display:flex; align-items:center; justify-content:center; gap:6px; flex-wrap:wrap;">
                <span id="pv-school-address">{{ $schoolAddress ?? $student->school?->address ?? 'Main Campus Road' }}</span>
                <span id="pv-school-city-state">{{ ($schoolCity ?? '') ? ', ' . $schoolCity : '' }}{{ ($schoolState ?? '') ? ', ' . $schoolState : '' }}</span>
                <span id="pv-school-pincode" style="background:#e0f2fe; color:#0284c7; padding:1px 6px; border-radius:4px; font-weight:600; font-size:11px; {{ empty($schoolPincode) ? 'display:none;' : '' }}">{{ $schoolPincode ?? '' }}</span>
            </div>
        </div>
    </div>

    <!-- Divider Line -->
    <hr style="border:none; border-top:1.5px solid #cbd5e1; margin:8px 0 14px 0;">

    <!-- Title -->
    <div style="text-align:center; margin-bottom:16px;">
        <h2 style="font-family:'Times New Roman', serif; font-size:17px; font-weight:800; color:#0f172a; text-decoration:underline; text-underline-offset:4px; margin:0; text-transform:capitalize; letter-spacing:0.5px;">
            Student Out Pass
        </h2>
    </div>

    <!-- Reference Number & Date Row -->
    <div style="display:flex; justify-content:space-between; align-items:center; font-size:12px; font-weight:700; color:#334155; margin-bottom:18px; padding-bottom:6px;">
        <div>
            <span>Ref No :- </span>
            <span id="pv-ref-no" style="color:#0f172a;">{{ $autoNumber ?? '2026/GP/1' }}</span>
        </div>
        <div>
            <span>Date :- </span>
            <span id="pv-date" style="color:#0f172a;">{{ $currentDateTime ?? now()->format('d M Y h:i a') }}</span>
        </div>
    </div>

    <!-- Student Details & Photo Split Layout -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:16px; gap:20px;">
        <!-- Left Key-Value Details -->
        <div style="flex-grow:1;">
            <table style="width:100%; border-collapse:collapse; font-size:13px; color:#1e293b;">
                <tr style="height:28px;">
                    <td style="width:150px; font-weight:700; color:#0f172a;">Student Name <span style="float:right; margin-right:12px;">:</span></td>
                    <td id="pv-student-name" style="font-weight:600; color:#1e293b;">{{ $student->full_name }}</td>
                </tr>
                <tr style="height:28px;">
                    <td style="font-weight:700; color:#0f172a;">Father's Name <span style="float:right; margin-right:12px;">:</span></td>
                    <td id="pv-father-name" style="font-weight:600; color:#1e293b;">{{ $student->father_name ?? 'N/A' }}</td>
                </tr>
                <tr style="height:28px;">
                    <td style="font-weight:700; color:#0f172a;">Class & Section <span style="float:right; margin-right:12px;">:</span></td>
                    <td id="pv-class-sec" style="font-weight:600; color:#1e293b;">{{ $student->class?->name ?? 'N/A' }} {{ $student->section?->name ?? '' }}</td>
                </tr>
                <tr style="height:28px;">
                    <td style="font-weight:700; color:#0f172a;">Admission / Roll <span style="float:right; margin-right:12px;">:</span></td>
                    <td id="pv-adm-roll" style="font-weight:600; color:#1e293b;">{{ $student->admission_number }} {{ $student->roll_number ? '(Roll: ' . $student->roll_number . ')' : '' }}</td>
                </tr>
            </table>
        </div>

        <!-- Right Student Photo Box -->
        <div style="flex-shrink:0; text-align:center;">
            <div style="width:90px; height:105px; border:1px solid #94a3b8; border-radius:6px; overflow:hidden; background:#f8fafc; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 5px rgba(0,0,0,0.05);">
                <img id="pv-student-photo" src="{{ $student->photo_url }}" alt="Student Photo" style="width:100%; height:100%; object-fit:cover; {{ !$student->photo ? 'display:none;' : '' }}">
                <div id="pv-student-photo-placeholder" style="display:{{ $student->photo ? 'none' : 'flex' }}; flex-direction:column; align-items:center; color:#94a3b8; font-size:10px;">
                    <i class="fa fa-user" style="font-size:28px; margin-bottom:4px;"></i>
                    <span>Photo</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Reason & Guardian Details (Badge Highlights matching image) -->
    <div style="margin-bottom:28px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 16px;">
        <table style="width:100%; border-collapse:collapse; font-size:13px;">
            <tr style="height:32px;">
                <td style="width:170px; font-weight:700; color:#0f172a;">Reason:</td>
                <td>
                    <span id="pv-reason" style="background:#bfdbfe; color:#1e3a8a; padding:3px 12px; border-radius:4px; font-weight:600; font-size:12.5px; display:inline-block;">
                        Medical Checkup / Illness
                    </span>
                </td>
            </tr>
            <tr style="height:32px;">
                <td style="font-weight:700; color:#0f172a;">Accompanying Guardian:</td>
                <td>
                    <span id="pv-guardian-name" style="background:#bfdbfe; color:#1e3a8a; padding:3px 12px; border-radius:4px; font-weight:600; font-size:12.5px; display:inline-block;">
                        {{ $student->father_name ?? $student->guardian_name ?? 'Father / Guardian' }}
                    </span>
                </td>
            </tr>
            <tr style="height:32px;">
                <td style="font-weight:700; color:#0f172a;">Relation with Student :</td>
                <td>
                    <span id="pv-guardian-relation" style="background:#bfdbfe; color:#1e3a8a; padding:3px 12px; border-radius:4px; font-weight:600; font-size:12.5px; display:inline-block;">
                        Father
                    </span>
                </td>
            </tr>
            <tr id="pv-row-return-time" style="height:32px; display:none;">
                <td style="font-weight:700; color:#0f172a;">Expected Return Time:</td>
                <td>
                    <span id="pv-return-time" style="background:#dbeafe; color:#1e40af; padding:3px 12px; border-radius:4px; font-weight:600; font-size:12.5px; display:inline-block;">
                        --
                    </span>
                </td>
            </tr>
            <tr id="pv-row-remarks" style="height:32px; display:none;">
                <td style="font-weight:700; color:#0f172a;">Remarks:</td>
                <td id="pv-remarks" style="color:#475569; font-size:12px; font-style:italic;">
                    --
                </td>
            </tr>
        </table>
    </div>

    <!-- Signatures Row (4 Signatures matching Image 5) -->
    <div style="margin-top:36px; padding-top:16px; border-top:1px dashed #cbd5e1; display:flex; justify-content:space-between; align-items:flex-end; font-size:12px; color:#1e293b;">
        <div style="text-align:center; width:22%;">
            <div style="height:32px; border-bottom:1px solid #94a3b8; margin-bottom:6px;"></div>
            <strong style="font-size:11.5px; color:#0f172a;">Student's Signature</strong>
        </div>
        <div style="text-align:center; width:22%;">
            <div style="height:32px; border-bottom:1px solid #94a3b8; margin-bottom:6px;"></div>
            <strong style="font-size:11.5px; color:#0f172a;">Guardian's Signature</strong>
        </div>
        <div style="text-align:center; width:22%;">
            <div style="height:32px; border-bottom:1px solid #94a3b8; margin-bottom:6px;"></div>
            <strong style="font-size:11.5px; color:#0f172a;">Security Guard</strong>
        </div>
        <div style="text-align:center; width:22%;">
            <div style="height:32px; border-bottom:1px solid #94a3b8; margin-bottom:6px;"></div>
            <strong style="font-size:11.5px; color:#0f172a;">Principal / Incharge</strong>
        </div>
    </div>

    <!-- Security Micro-Stamp & Verification Bar -->
    <div style="margin-top:18px; padding-top:8px; border-top:1px solid #f1f5f9; display:flex; justify-content:space-between; align-items:center; font-size:10px; color:#94a3b8;">
        <div>
            <i class="fa fa-shield-alt" style="color:#2563eb; margin-right:3px;"></i> System Verified &bull; Logged by ERP
        </div>
        <div>
            Valid only on date of issue &bull; Keep pass until re-entry
        </div>
    </div>
</div>
