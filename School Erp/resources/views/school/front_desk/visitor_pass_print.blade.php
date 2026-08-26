<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Pass - {{ $visitor->pass_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        body { background: #f1f5f9; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 24px; min-height: 100vh; }
        
        .no-print-bar {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }
        .btn {
            padding: 8px 20px;
            border-radius: 8px;
            font-size: 13.5px;
            font-weight: 700;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: 0.2s;
        }
        .btn-print { background: #1d4ed8; color: #fff; }
        .btn-print:hover { background: #1e40af; }
        .btn-close-window { background: #64748b; color: #fff; }
        .btn-close-window:hover { background: #475569; }

        /* Exact Scan Card Layout matching Screenshot */
        .scan-card-container {
            width: 380px;
            background: #ffffff;
            border: 2px dashed #475569;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px -5px rgba(0,0,0,0.1);
            position: relative;
        }

        /* Top Blue Header */
        .card-top-header {
            background: #0056b3;
            color: #ffffff;
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .header-left-brand {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .header-logo-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex-shrink: 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.15);
        }
        .header-logo-circle img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 2px;
        }
        .header-logo-circle i {
            color: #0056b3;
            font-size: 18px;
        }
        .header-school-name {
            font-size: 13.5px;
            font-weight: 800;
            letter-spacing: -0.2px;
            text-transform: uppercase;
            color: #ffffff;
            line-height: 1.2;
        }
        .header-code-badge {
            background: #ffffff;
            color: #0056b3;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            overflow: hidden;
            border: 1px solid #ffffff;
        }
        .header-code-badge span.code-label {
            background: #dc2626;
            color: #ffffff;
            padding: 2px 5px;
            font-size: 9px;
        }
        .header-code-badge span.code-val {
            padding: 2px 6px;
            color: #0056b3;
            font-weight: 800;
        }

        /* Gold Stripe */
        .card-gold-stripe {
            background: #f59e0b;
            height: 4px;
            width: 100%;
        }

        /* Sub-Banner Title */
        .card-sub-banner {
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            text-align: center;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 800;
            color: #334155;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        /* Body Info */
        .card-body-content {
            padding: 16px 18px;
        }

        .visitor-profile-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 14px;
        }
        .visitor-meta-left {
            flex: 1;
        }
        .v-label-small {
            font-size: 10px;
            color: #64748b;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .v-name-bold {
            font-size: 17px;
            font-weight: 800;
            color: #0056b3;
            line-height: 1.25;
            margin-top: 2px;
            word-break: break-word;
        }
        .v-type-pill {
            display: inline-block;
            background: #0056b3;
            color: #ffffff;
            padding: 2px 10px;
            border-radius: 12px;
            font-size: 10.5px;
            font-weight: 700;
            margin-top: 5px;
            margin-bottom: 6px;
        }
        .v-detail-line {
            font-size: 11.5px;
            color: #1e293b;
            font-weight: 600;
            line-height: 1.45;
        }
        .v-detail-line strong {
            color: #0f172a;
            font-weight: 700;
        }

        /* QR Code Box */
        .qr-code-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1.5px dashed #0056b3;
            border-radius: 10px;
            padding: 6px;
            background: #ffffff;
            flex-shrink: 0;
        }
        .qr-code-img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            display: block;
        }
        .qr-sub-text {
            font-size: 9.5px;
            font-weight: 800;
            color: #dc2626;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            margin-top: 3px;
        }

        /* Detail Box Grid */
        .card-details-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 10px 12px;
            margin-bottom: 10px;
            font-size: 11px;
        }
        .detail-row-2col {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }
        .detail-row-2col:last-child {
            margin-bottom: 0;
        }
        .ref-code-red {
            color: #dc2626;
            font-weight: 800;
            font-size: 12.5px;
        }
        .pill-black {
            background: #0f172a;
            color: #ffffff;
            padding: 1px 7px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 800;
            display: inline-block;
        }
        .pill-cyan {
            background: #06b6d4;
            color: #ffffff;
            padding: 1px 7px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: 800;
            display: inline-block;
        }

        /* Purpose Box */
        .card-purpose-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 9px 12px;
            font-size: 11px;
            margin-bottom: 14px;
        }
        .purpose-highlight {
            color: #0056b3;
            font-weight: 700;
        }

        /* Bottom Buttons inside Pass */
        .card-actions-row {
            display: flex;
            gap: 10px;
            justify-content: center;
            padding-top: 6px;
        }
        .btn-card-print {
            flex: 1;
            background: #0056b3;
            color: #ffffff;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            text-transform: uppercase;
        }
        .btn-card-new {
            flex: 1;
            background: #ffffff;
            color: #334155;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 8px 12px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            text-transform: uppercase;
        }

        @media print {
            body { background: #fff; padding: 0; }
            .no-print-bar { display: none !important; }
            .card-actions-row { display: none !important; }
            .scan-card-container { box-shadow: none; width: 360px; margin: auto; page-break-inside: avoid; }
        }
    </style>
</head>
<body>

    <div class="no-print-bar">
        <button class="btn btn-print" onclick="window.print()">
            <i class="fas fa-print"></i> Print Pass
        </button>
        <button class="btn btn-close-window" onclick="window.close()">
            <i class="fas fa-times"></i> Close Window
        </button>
    </div>

    @php
        $school = $visitor->school ?? \App\Models\School::find($visitor->school_id);
        $schoolName = $visitor->meta_data['school_name'] ?? ($school?->name ?? 'EDUZEN DEMO PANEL');
        $schoolCode = $school?->code ? strtoupper($school->code) : 'EDUZEN';
        $schoolLogo = $visitor->meta_data['school_logo'] ?? ($school?->logo ? (str_starts_with($school->logo, 'http') ? $school->logo : asset('storage/' . ltrim($school->logo, '/'))) : null);
        $qrData = $visitor->pass_number;
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=180x180&data=" . urlencode($qrData);
    @endphp

    <!-- Scan Card matching Screenshot -->
    <div class="scan-card-container">
        <!-- Top Header -->
        <div class="card-top-header">
            <div class="header-left-brand">
                <div class="header-logo-circle">
                    @if(!empty($schoolLogo))
                        <img src="{{ $schoolLogo }}" alt="Logo">
                    @else
                        <i class="fas fa-graduation-cap"></i>
                    @endif
                </div>
                <div class="header-school-name">{{ $schoolName }}</div>
            </div>
            <div class="header-code-badge">
                <span class="code-label">CODE</span>
                <span class="code-val">{{ $schoolCode }}</span>
            </div>
        </div>

        <!-- Gold Stripe -->
        <div class="card-gold-stripe"></div>

        <!-- Sub-Banner -->
        <div class="card-sub-banner">
            VISITOR ENTRY PASS
        </div>

        <!-- Body -->
        <div class="card-body-content">
            <div class="visitor-profile-row">
                <div class="visitor-meta-left" style="display: flex; gap: 12px; align-items: flex-start;">
                    @if($visitor->photo_url)
                    <div style="width: 58px; height: 58px; border-radius: 8px; overflow: hidden; border: 1.5px solid #0056b3; flex-shrink: 0; margin-top: 4px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                        <img src="{{ $visitor->photo_url }}" alt="Visitor Photo" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    @endif
                    <div>
                        <div class="v-label-small">VISITOR NAME</div>
                        <div class="v-name-bold">{{ $visitor->full_name }}</div>
                        <div><span class="v-type-pill">{{ $visitor->visitor_type }}</span></div>
                        <div class="v-detail-line"><strong>Mobile:</strong> {{ $visitor->mobile_number }}</div>
                        <div class="v-detail-line"><strong>Vehicle:</strong> {{ $visitor->vehicle_number ?: 'N/A' }}</div>
                    </div>
                </div>
                
                <div class="qr-code-wrapper">
                    <img src="{{ $qrUrl }}" alt="QR Code" class="qr-code-img">
                    <span class="qr-sub-text">SCAN TO OUT</span>
                </div>
            </div>

            <!-- Details Block -->
            <div class="card-details-box">
                <div class="detail-row-2col">
                    <div><span class="v-label-small">PASS REFERENCE</span></div>
                    <div><span class="v-label-small">MEETING HOST</span></div>
                </div>
                <div class="detail-row-2col" style="margin-bottom: 8px;">
                    <div><span class="ref-code-red">{{ $visitor->pass_number }}</span></div>
                    <div style="font-weight: 700; color: #1e293b;">{{ $visitor->host_name ?: 'N/A' }}</div>
                </div>

                <div class="detail-row-2col">
                    <div>In: <strong>{{ $visitor->check_in_at ? $visitor->check_in_at->format('d-M-Y h:i A') : $visitor->created_at->format('d-M-Y h:i A') }}</strong></div>
                    <div>Type: <strong>{{ $visitor->whom_to_meet_type ?: 'Official' }}</strong></div>
                </div>

                <div class="detail-row-2col" style="margin-top: 6px;">
                    <div>Gate: <span class="pill-black">{{ $visitor->security_gate }}</span></div>
                    <div>Count: <span class="pill-cyan">{{ $visitor->entourage_count }}</span> Head(s)</div>
                </div>
            </div>

            <!-- Purpose Block -->
            <div class="card-purpose-box">
                <div><strong>Purpose:</strong> <span class="purpose-highlight">{{ $visitor->visit_purpose }}</span></div>
                <div style="margin-top: 2px;"><strong>Remarks:</strong> {{ $visitor->detailed_purpose_remarks ?: 'N/A' }}</div>
            </div>

            <!-- Buttons inside pass -->
            <div class="card-actions-row">
                <button type="button" class="btn-card-print" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Print Pass
                </button>
                <a href="{{ route('school.front-desk.visitor-registration') }}" class="btn-card-new">
                    New Check-In
                </a>
            </div>
        </div>
    </div>

</body>
</html>
