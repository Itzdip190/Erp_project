<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitor Pass - {{ $visitor->pass_number }}</title>
    <style>
        body { margin: 0; padding: 0; background-color: #f1f5f9; font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
        table { border-collapse: collapse; }
        .email-container { max-width: 580px; margin: 30px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.08); border: 1px solid #e2e8f0; }
        .header-top { background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 50%, #2563eb 100%); padding: 24px 28px; text-align: left; }
        .header-title { color: #ffffff; font-size: 18px; font-weight: 800; margin: 0 0 4px; letter-spacing: -0.3px; }
        .header-sub { color: rgba(255,255,255,0.85); font-size: 13px; margin: 0; }
        .gold-stripe { background: #f59e0b; height: 4px; width: 100%; }
        .email-body { padding: 30px 28px; }
        .greeting-text { font-size: 16px; color: #1e293b; font-weight: 700; margin-bottom: 8px; }
        .intro-text { font-size: 14px; color: #475569; line-height: 1.6; margin-bottom: 24px; }
        
        /* Visitor Pass Badge Card */
        .pass-badge-box { background: #ffffff; border: 2px dashed #0056b3; border-radius: 14px; overflow: hidden; margin: 20px 0 24px; box-shadow: 0 4px 15px rgba(0,86,179,0.08); }
        .pass-header { background: #0056b3; color: #ffffff; padding: 12px 18px; display: table; width: 100%; box-sizing: border-box; }
        .pass-header-left { display: table-cell; vertical-align: middle; }
        .pass-header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .badge-school-name { font-size: 14px; font-weight: 800; color: #ffffff; text-transform: uppercase; margin: 0; }
        .badge-school-code { background: #ffffff; color: #0056b3; font-size: 10px; font-weight: 800; padding: 3px 8px; border-radius: 4px; display: inline-block; }
        .pass-sub-banner { background: #eff6ff; color: #1e40af; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.8px; padding: 6px 16px; text-align: center; border-bottom: 1px solid #bfdbfe; }
        
        .pass-content { padding: 18px 20px; }
        .visitor-info-row { display: table; width: 100%; margin-bottom: 14px; }
        .visitor-info-cell { display: table-cell; vertical-align: top; }
        .visitor-name { font-size: 17px; font-weight: 800; color: #0f172a; margin: 0 0 4px; }
        .visitor-type-tag { display: inline-block; background: #0056b3; color: #ffffff; font-size: 10.5px; font-weight: 700; padding: 2px 10px; border-radius: 12px; margin-bottom: 6px; }
        .visitor-meta-text { font-size: 12px; color: #334155; margin: 2px 0; }
        
        .qr-cell { display: table-cell; vertical-align: top; text-align: right; width: 115px; }
        .qr-wrapper { border: 1.5px solid #0056b3; border-radius: 8px; padding: 6px; background: #ffffff; display: inline-block; text-align: center; }
        .qr-img { width: 95px; height: 95px; display: block; }
        .qr-hint { font-size: 8.5px; font-weight: 800; color: #dc2626; text-transform: uppercase; margin-top: 3px; display: block; }
        
        .details-grid { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 14px; margin-bottom: 14px; }
        .detail-item { font-size: 12px; color: #475569; margin-bottom: 6px; }
        .detail-item:last-child { margin-bottom: 0; }
        .detail-label { font-weight: 600; color: #64748b; }
        .detail-val { font-weight: 700; color: #0f172a; }
        .pass-num-highlight { color: #dc2626; font-size: 13.5px; font-weight: 800; }
        
        .purpose-box { background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 8px; padding: 10px 14px; font-size: 12px; color: #166534; margin-bottom: 12px; }
        .purpose-label { font-weight: 700; color: #15803d; }
        
        .instructions-card { background: #fffbeb; border: 1px solid #fef3c7; border-radius: 10px; padding: 14px 16px; margin: 20px 0; }
        .instructions-title { font-size: 12.5px; font-weight: 800; color: #92400e; margin: 0 0 6px; display: flex; align-items: center; gap: 6px; }
        .instructions-list { margin: 0; padding-left: 18px; font-size: 12px; color: #78350f; line-height: 1.5; }
        
        .footer-sec { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 20px 28px; text-align: center; }
        .footer-text { font-size: 11.5px; color: #94a3b8; margin: 0; line-height: 1.5; }
    </style>
</head>
<body>
    <div class="email-container">
        <!-- Header -->
        <div class="header-top">
            <h1 class="header-title">Visitor Entry Request Approved</h1>
            <p class="header-sub">{{ $schoolName }} Front Desk Security</p>
        </div>
        <div class="gold-stripe"></div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting-text">Hello {{ $visitor->full_name }},</div>
            <div class="intro-text">
                Your visitor registration request has been <strong>approved</strong> by the school front desk. Please present this digital pass on your mobile or show the QR code at the security gate for smooth entry and exit.
            </div>

            <!-- Digital Visitor Pass -->
            <div class="pass-badge-box">
                <div class="pass-header">
                    <div class="pass-header-left">
                        <span class="badge-school-name">{{ $schoolName }}</span>
                    </div>
                    <div class="pass-header-right">
                        <span class="badge-school-code">CODE: {{ $schoolCode }}</span>
                    </div>
                </div>
                <div class="pass-sub-banner">
                    OFFICIAL DIGITAL VISITOR PASS
                </div>

                <div class="pass-content">
                    <div class="visitor-info-row">
                        <div class="visitor-info-cell">
                            <div class="visitor-name">{{ $visitor->full_name }}</div>
                            <div class="visitor-type-tag">{{ $visitor->visitor_type ?? 'Visitor' }}</div>
                            <div class="visitor-meta-text"><strong>Mobile:</strong> {{ $visitor->mobile_number }}</div>
                            @if($visitor->vehicle_number)
                            <div class="visitor-meta-text"><strong>Vehicle No:</strong> {{ $visitor->vehicle_number }}</div>
                            @endif
                            <div class="visitor-meta-text"><strong>Head Count:</strong> {{ $visitor->entourage_count ?? 1 }} Person(s)</div>
                        </div>
                        <div class="qr-cell">
                            <div class="qr-wrapper">
                                <img src="{{ $qrCodeUrl }}" alt="Scan QR" class="qr-img">
                                <span class="qr-hint">SCAN TO OUT</span>
                            </div>
                        </div>
                    </div>

                    <!-- Details Box -->
                    <div class="details-grid">
                        <div class="detail-item">
                            <span class="detail-label">Pass Reference:</span>
                            <span class="detail-val pass-num-highlight">{{ $visitor->pass_number }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Meeting Host / Person:</span>
                            <span class="detail-val">{{ $visitor->host_name ?: ($visitor->whom_to_meet_type ?? 'Front Desk') }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Entry Gate:</span>
                            <span class="detail-val">{{ $visitor->security_gate ?? 'Main Gate 1' }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Valid / Check-In Time:</span>
                            <span class="detail-val">{{ $visitor->check_in_at ? $visitor->check_in_at->format('d M Y, h:i A') : ($visitor->approved_at ? $visitor->approved_at->format('d M Y, h:i A') : date('d M Y, h:i A')) }}</span>
                        </div>
                    </div>

                    <!-- Purpose Box -->
                    <div class="purpose-box">
                        <span class="purpose-label">Visit Purpose:</span> {{ $visitor->visit_purpose }}
                        @if($visitor->detailed_purpose_remarks)
                        <br><span style="font-size: 11px; color: #166534;">Remarks: {{ $visitor->detailed_purpose_remarks }}</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Gate Guidelines -->
            <div class="instructions-card">
                <div class="instructions-title">📋 Important Gate Guidelines:</div>
                <ul class="instructions-list">
                    <li>Please keep this email or pass screenshot accessible on your phone while on school premises.</li>
                    <li>Scan your QR code at the security terminal when checking out at the exit gate.</li>
                    <li>Visitors must adhere to school campus safety and code of conduct policies at all times.</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer-sec">
            <p class="footer-text">
                <strong>{{ $schoolName }}</strong><br>
                @if($schoolAddress){{ $schoolAddress }}<br>@endif
                @if($schoolPhone)Contact / Front Desk: {{ $schoolPhone }}<br>@endif
                This is an automated pass notification generated by Educorerp.
            </p>
        </div>
    </div>
</body>
</html>
