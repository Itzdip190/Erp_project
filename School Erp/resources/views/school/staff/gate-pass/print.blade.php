<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Gate Pass - {{ $gatePass->gate_pass_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            background-color: #f1f5f9;
            margin: 0;
            padding: 24px 16px;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .no-print-bar {
            width: 100%;
            max-width: 720px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #ffffff;
            padding: 12px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            border: 1px solid #e2e8f0;
        }

        .btn-print {
            background: #2563eb;
            color: #ffffff;
            border: none;
            padding: 8px 18px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }
            .no-print-bar {
                display: none !important;
            }
            .gate-pass-sheet {
                box-shadow: none !important;
                border: 1px solid #94a3b8 !important;
                margin: 0 auto !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print-bar">
    <div>
        <strong>Staff Gate Pass #{{ $gatePass->gate_pass_number }}</strong>
        <div style="font-size: 12px; color: #64748b;">Issued on {{ $gatePass->pass_date ? $gatePass->pass_date->format('d M Y h:i a') : '' }}</div>
    </div>
    <div style="display: flex; gap: 8px;">
        <button class="btn-print" onclick="window.print()"><i class="fa fa-print"></i> Print Now</button>
        <a href="{{ route('school.staff.gate-passes.pdf', [$staff->id, $gatePass->id]) }}" class="btn-print" style="background:#0f172a; text-decoration:none;"><i class="fa fa-file-pdf"></i> Download PDF</a>
    </div>
</div>

<div style="display:flex; justify-content:center; width: 100%;">
    @php
        $template = $gatePass->template ?: 'classic';
        $schoolLogo = $gatePass->school_logo ?: ($school?->logo_base64 ?: ($school?->logo_url ?: null));
        $schoolName = $gatePass->school_name ?: ($school?->name ?: 'School Name');
        $schoolAddress = $gatePass->school_address ?: ($school?->address ?: '');
        $schoolCity = $gatePass->school_city ?: ($school?->city ?: '');
        $schoolState = $gatePass->school_state ?: ($school?->state ?: '');
        $schoolPincode = $gatePass->school_pincode ?: ($school?->pincode ?: '');
        $autoNumber = $gatePass->gate_pass_number;
        $currentDateTime = $gatePass->pass_date ? $gatePass->pass_date->format('d M Y h:i a') : now()->format('d M Y h:i a');
    @endphp

    @if($template === 'modern')
        @include('school.staff.gate-pass.templates.modern')
    @elseif($template === 'minimal')
        @include('school.staff.gate-pass.templates.minimal')
    @elseif($template === 'portrait')
        @include('school.staff.gate-pass.templates.portrait')
    @elseif($template === 'landscape')
        @include('school.staff.gate-pass.templates.landscape')
    @else
        @include('school.staff.gate-pass.templates.classic')
    @endif
</div>

</body>
</html>
