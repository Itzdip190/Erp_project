{{-- PORTRAIT STAFF GATE PASS TEMPLATE --}}
@php
    $resolvedLogo = $schoolLogo ?? ($staff->school?->logo_base64 ?: ($staff->school?->logo_url ?: null));
@endphp
<div class="gate-pass-sheet portrait-theme" style="background:#ffffff; color:#1e293b; font-family:'Segoe UI', Arial, sans-serif; border-radius:8px; box-shadow:0 6px 20px rgba(0,0,0,0.06); width:100%; max-width:440px; margin:0 auto; box-sizing:border-box; border:1px solid #cbd5e1; padding:20px;">
    
    <!-- Slip Header -->
    <div style="text-align:center; margin-bottom:12px; border-bottom:2px dashed #94a3b8; padding-bottom:10px;">
        @if(!empty($resolvedLogo))
        <div style="display:flex; justify-content:center; margin-bottom:6px;">
            <img id="pv-school-logo-portrait" src="{{ $resolvedLogo }}" alt="Logo" onerror="this.style.display='none';" style="width:44px; height:44px; object-fit:contain;">
        </div>
        @endif
        <h3 id="pv-school-name-portrait" style="font-size:16px; font-weight:800; color:#0f172a; margin:0 0 2px 0;">
            {{ $schoolName ?? $staff->school?->name ?? "School Name" }}
        </h3>
        <div style="font-size:10.5px; color:#64748b;">
            <span id="pv-school-address-portrait">{{ $schoolAddress ?? '' }}</span>
        </div>
        <div style="margin-top:6px; display:inline-block; background:#0f172a; color:#ffffff; font-size:11px; font-weight:800; padding:2px 10px; border-radius:12px; text-transform:uppercase; letter-spacing:0.5px;">
            STAFF EXIT PASS
        </div>
    </div>

    <!-- Slip Info -->
    <div style="display:flex; justify-content:space-between; font-size:11px; margin-bottom:10px; color:#475569; font-weight:600;">
        <div>Pass: <strong id="pv-ref-no-portrait" style="color:#0f172a;">{{ $autoNumber ?? '2026/SGP/1' }}</strong></div>
        <div id="pv-date-portrait">{{ $currentDateTime ?? now()->format('d M Y h:i a') }}</div>
    </div>

    <div style="display:flex; gap:12px; margin-bottom:12px; align-items:center; background:#f8fafc; padding:8px 10px; border-radius:6px; border:1px solid #e2e8f0;">
        <div style="width:50px; height:60px; border-radius:4px; overflow:hidden; border:1px solid #cbd5e1; flex-shrink:0;">
            <img id="pv-staff-photo-portrait" src="{{ $staff->photo_url }}" alt="Photo" style="width:100%; height:100%; object-fit:cover; {{ !$staff->photo ? 'display:none;' : '' }}">
            <div id="pv-staff-photo-placeholder-portrait" style="display:{{ $staff->photo ? 'none' : 'flex' }}; height:100%; align-items:center; justify-content:center; color:#94a3b8; font-size:9px;">
                Photo
            </div>
        </div>
        <div style="font-size:11.5px; line-height:1.4;">
            <div><strong id="pv-staff-name-portrait" style="color:#0f172a; font-size:13px;">{{ $staff->full_name }}</strong></div>
            <div style="color:#64748b;">Emp ID: <strong id="pv-emp-id-portrait" style="color:#334155;">{{ $staff->employee_id }}</strong></div>
            <div style="color:#64748b;">{{ optional($staff->designation)->name ?? 'Staff' }} ({{ optional($staff->department)->name ?? 'General' }})</div>
        </div>
    </div>

    <div style="font-size:11.5px; margin-bottom:14px; border:1px solid #e2e8f0; border-radius:6px; padding:8px 10px;">
        <div style="margin-bottom:4px;"><span style="color:#64748b;">Reason:</span> <strong id="pv-reason-portrait" style="color:#2563eb;">Official School Duty</strong></div>
        <div><span style="color:#64748b;">Expected Return:</span> <span id="pv-return-time-portrait" style="font-weight:600; color:#0f172a;">--</span></div>
    </div>

    <!-- Dual Signature -->
    <div style="display:flex; justify-content:space-between; text-align:center; font-size:10.5px; border-top:1px dashed #94a3b8; padding-top:12px; margin-top:8px;">
        <div style="width:45%;">
            <div style="height:22px; border-bottom:1px solid #94a3b8; margin-bottom:3px;"></div>
            <span>Staff Sign</span>
        </div>
        <div style="width:45%;">
            <div style="height:22px; border-bottom:1px solid #94a3b8; margin-bottom:3px;"></div>
            <span>Auth / Gate Sign</span>
        </div>
    </div>
</div>
