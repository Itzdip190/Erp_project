<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Print Gate Pass #{{ $gatePass->gate_pass_number }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        @page {
            size: {{ $gatePass->template === 'landscape' ? 'A4 landscape' : 'A4 portrait' }};
            margin: 10mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #ffffff;
            color: #0f172a;
            font-family: 'Segoe UI', Arial, sans-serif;
            padding: 10px;
        }
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
            }
            .gate-pass-sheet {
                box-shadow: none !important;
                border: 1px solid #94a3b8 !important;
            }
        }
    </style>
</head>
<body>

<div class="no-print" style="background:#f1f5f9; padding:12px 20px; margin-bottom:20px; border-radius:8px; display:flex; justify-content:space-between; align-items:center; border:1px solid #cbd5e1;">
    <div style="font-size:14px; font-weight:700; color:#0f172a;">
        <i class="fa fa-print" style="color:#2563eb; margin-right:6px;"></i> Printing Gate Pass #{{ $gatePass->gate_pass_number }} ({{ $student->full_name }})
    </div>
    <div style="display:flex; gap:10px;">
        <button onclick="window.print()" style="background:#2563eb; color:#ffffff; font-weight:700; border:none; padding:8px 18px; border-radius:6px; cursor:pointer;">
            <i class="fa fa-print"></i> Print Now
        </button>
        <button onclick="window.close()" style="background:#64748b; color:#ffffff; font-weight:700; border:none; padding:8px 16px; border-radius:6px; cursor:pointer;">
            Close Window
        </button>
    </div>
</div>

<div style="display:flex; justify-content:center;">
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
        @include('school.student.gate-pass.templates.modern')
    @elseif($template === 'minimal')
        @include('school.student.gate-pass.templates.minimal')
    @elseif($template === 'portrait')
        @include('school.student.gate-pass.templates.portrait')
    @elseif($template === 'landscape')
        @include('school.student.gate-pass.templates.landscape')
    @else
        @include('school.student.gate-pass.templates.classic')
    @endif
</div>

<script>
    window.onload = function() {
        setTimeout(function() {
            window.print();
        }, 400);
    };
</script>

</body>
</html>
