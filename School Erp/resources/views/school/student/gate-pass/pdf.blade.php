<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gate Pass - {{ $gatePass->gate_pass_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, Helvetica, sans-serif;
        }
        body {
            background: #ffffff;
            color: #1e293b;
            padding: 18px;
            font-size: 13px;
            line-height: 1.4;
        }
        .pass-container {
            width: 100%;
            border: 1.5px solid #94a3b8;
            border-radius: 8px;
            padding: 22px 26px;
            margin: 0 auto;
        }
        .school-name {
            font-family: 'Times New Roman', serif;
            font-size: 23px;
            font-weight: bold;
            color: #0f172a;
            letter-spacing: 0.3px;
        }
        .school-address {
            font-size: 11px;
            color: #475569;
            margin-top: 2px;
        }
        .divider {
            border: none;
            border-top: 1.5px solid #cbd5e1;
            margin: 10px 0 14px 0;
        }
        .pass-title {
            text-align: center;
            font-family: 'Times New Roman', serif;
            font-size: 18px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 16px;
            color: #0f172a;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 16px;
            font-weight: bold;
            font-size: 12px;
        }
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .details-table td {
            padding: 5px 0;
            vertical-align: middle;
            font-size: 13px;
        }
        .reason-box {
            background-color: #f1f5f9;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
            margin-bottom: 26px;
        }
        .badge-pill {
            background-color: #bfdbfe;
            color: #1e3a8a;
            padding: 3px 10px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
        }
        .signatures-table {
            width: 100%;
            border-top: 1px dashed #cbd5e1;
            padding-top: 18px;
            margin-top: 24px;
            text-align: center;
        }
        .sig-line {
            height: 35px;
            border-bottom: 1px solid #94a3b8;
            margin-bottom: 6px;
        }
        .photo-frame {
            width: 90px;
            height: 105px;
            border: 1px solid #94a3b8;
            border-radius: 4px;
            overflow: hidden;
            text-align: center;
            background: #f8fafc;
        }
        .photo-frame img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .footer-stamp {
            margin-top: 14px;
            border-top: 1px solid #f1f5f9;
            padding-top: 6px;
            font-size: 9px;
            color: #94a3b8;
        }
    </style>
</head>
<body>

@php
    $schoolName = $gatePass->school_name ?: ($school?->name ?: "School Name");
    $schoolAddress = $gatePass->school_address ?: ($school?->address ?: "");
    $schoolCityState = trim(($gatePass->school_city ?: $school?->city) . ', ' . ($gatePass->school_state ?: $school?->state), ', ');
    $schoolPincode = $gatePass->school_pincode ?: $school?->pincode;
    $studentName = $student->full_name;
    $fatherName = $student->father_name ?? 'N/A';
    $classSection = ($student->class?->name ?? '') . ' ' . ($student->section?->name ?? '');
    $admRoll = $student->admission_number . ($student->roll_number ? " (Roll: {$student->roll_number})" : '');
    $passDateFormatted = $gatePass->pass_date ? $gatePass->pass_date->format('d M Y h:i a') : now()->format('d M Y h:i a');
    
    // Resolve School Logo as Base64 for 100% reliable DomPDF rendering
    $schoolLogoBase64 = $school?->logo_base64;
    if (!$schoolLogoBase64 && $school?->logo) {
        $cleanLogo = ltrim($school->logo, '/');
        $cleanLogo = preg_replace('/^(public\/|storage\/)/', '', $cleanLogo);
        $possiblePaths = [
            storage_path('app/public/' . $cleanLogo),
            public_path('storage/' . $cleanLogo),
            public_path('uploads/schools/' . basename($cleanLogo)),
            public_path($cleanLogo),
            storage_path('app/' . $cleanLogo),
        ];
        foreach ($possiblePaths as $p) {
            if (file_exists($p) && is_file($p)) {
                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                $mime = ($ext === 'svg') ? 'image/svg+xml' : (($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png');
                $schoolLogoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($p));
                break;
            }
        }
    }

    // Resolve Student Photo as Base64
    $studentPhotoBase64 = null;
    if ($student->photo) {
        $cleanPhoto = ltrim($student->photo, '/');
        $cleanPhoto = preg_replace('/^(public\/|storage\/)/', '', $cleanPhoto);
        $photoPaths = [
            storage_path('app/public/' . $cleanPhoto),
            public_path('storage/' . $cleanPhoto),
            public_path($cleanPhoto),
            public_path('uploads/students/' . basename($cleanPhoto)),
        ];
        foreach ($photoPaths as $p) {
            if (file_exists($p) && is_file($p)) {
                $ext = strtolower(pathinfo($p, PATHINFO_EXTENSION));
                $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/png';
                $studentPhotoBase64 = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($p));
                break;
            }
        }
    }
@endphp

<div class="pass-container">
    
    <!-- School Header with Logo on Left -->
    <table style="width: 100%; margin-bottom: 8px;">
        <tr>
            @if($schoolLogoBase64)
            <td style="width: 75px; vertical-align: middle; text-align: left;">
                <img src="{{ $schoolLogoBase64 }}" style="width: 65px; height: 65px; object-fit: contain;">
            </td>
            @endif
            <td style="text-align: {{ $schoolLogoBase64 ? 'left' : 'center' }}; vertical-align: middle; padding-left: {{ $schoolLogoBase64 ? '12px' : '0' }};">
                <div class="school-name">{{ $schoolName }}</div>
                <div class="school-address">
                    {{ $schoolAddress }}
                    @if($schoolCityState) &bull; {{ $schoolCityState }} @endif
                    @if($schoolPincode) (PIN: {{ $schoolPincode }}) @endif
                </div>
            </td>
        </tr>
    </table>

    <hr class="divider">

    <div class="pass-title">Student Out Pass</div>

    <!-- Reference & Date -->
    <table class="meta-table">
        <tr>
            <td style="text-align: left;">Ref No :- {{ $gatePass->gate_pass_number }}</td>
            <td style="text-align: right;">Date :- {{ $passDateFormatted }}</td>
        </tr>
    </table>

    <!-- Student Details & Photo Split -->
    <table style="width: 100%; margin-bottom: 14px;">
        <tr>
            <td style="vertical-align: top; width: 80%;">
                <table class="details-table">
                    <tr>
                        <td style="width: 130px; font-weight: bold; color: #0f172a;">Student Name :</td>
                        <td style="font-weight: 600;">{{ $studentName }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #0f172a;">Father's Name :</td>
                        <td style="font-weight: 600;">{{ $fatherName }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #0f172a;">Class & Section :</td>
                        <td style="font-weight: 600;">{{ $classSection }}</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold; color: #0f172a;">Admission No :</td>
                        <td style="font-weight: 600;">{{ $admRoll }}</td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top; width: 20%; text-align: right;">
                <div class="photo-frame" style="margin-left: auto;">
                    @if($studentPhotoBase64)
                        <img src="{{ $studentPhotoBase64 }}" alt="Student Photo">
                    @else
                        <div style="padding-top: 35px; color: #94a3b8; font-size: 11px;">Photo</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <!-- Reason & Guardian Information -->
    <div class="reason-box">
        <table style="width: 100%; border-collapse: collapse;">
            <tr style="height: 28px;">
                <td style="width: 170px; font-weight: bold; color: #0f172a;">Reason:</td>
                <td><span class="badge-pill">{{ $gatePass->reason }}</span></td>
            </tr>
            <tr style="height: 28px;">
                <td style="font-weight: bold; color: #0f172a;">Accompanying Guardian:</td>
                <td><span class="badge-pill">{{ $gatePass->guardian_name }}</span></td>
            </tr>
            <tr style="height: 28px;">
                <td style="font-weight: bold; color: #0f172a;">Relation with Student :</td>
                <td><span class="badge-pill">{{ $gatePass->guardian_relation }}</span></td>
            </tr>
            @if($gatePass->expected_return_time)
            <tr style="height: 28px;">
                <td style="font-weight: bold; color: #0f172a;">Expected Return Time:</td>
                <td><strong>{{ $gatePass->expected_return_time }}</strong></td>
            </tr>
            @endif
            @if($gatePass->remarks)
            <tr style="height: 28px;">
                <td style="font-weight: bold; color: #0f172a;">Remarks:</td>
                <td style="color: #475569; font-style: italic;">{{ $gatePass->remarks }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Signatures Row (4 Signatures) -->
    <table class="signatures-table">
        <tr>
            <td style="width: 25%; padding: 0 8px;">
                <div class="sig-line"></div>
                <strong>Student's Signature</strong>
            </td>
            <td style="width: 25%; padding: 0 8px;">
                <div class="sig-line"></div>
                <strong>Guardian's Signature</strong>
            </td>
            <td style="width: 25%; padding: 0 8px;">
                <div class="sig-line"></div>
                <strong>Security:</strong>
            </td>
            <td style="width: 25%; padding: 0 8px;">
                <div class="sig-line"></div>
                <strong>Principal:</strong>
            </td>
        </tr>
    </table>

    <table class="footer-stamp" style="width: 100%;">
        <tr>
            <td style="text-align: left;">System Verified &bull; Issued by {{ $gatePass->issuedByUser?->name ?? 'Admin' }}</td>
            <td style="text-align: right;">Valid only on date of issue &bull; Keep pass safely</td>
        </tr>
    </table>

</div>

</body>
</html>
