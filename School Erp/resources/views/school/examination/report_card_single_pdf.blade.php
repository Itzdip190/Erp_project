<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report Card - {{ $student->full_name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #1e293b; font-size: 12px; margin: 0; padding: 15px; }
        .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 10px; margin-bottom: 15px; }
        .school-name { color: #1e40af; font-size: 20px; font-weight: bold; text-transform: uppercase; margin: 0; }
        .school-address { font-size: 11px; color: #64748b; margin-top: 3px; }
        .session-title { background: #eff6ff; color: #1e40af; padding: 6px; font-weight: bold; font-size: 13px; text-align: center; margin-bottom: 15px; border-radius: 4px; }
        .student-details { width: 100%; margin-bottom: 15px; }
        .student-details td { padding: 4px 8px; vertical-align: top; }
        .lbl { font-weight: bold; color: #334155; }
        .val { border-bottom: 1px dotted #94a3b8; color: #0f172a; }
        .marks-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .marks-table th, .marks-table td { border: 1px solid #cbd5e1; padding: 6px 8px; text-align: center; }
        .marks-table th { background: #1e40af; color: #ffffff; font-weight: bold; font-size: 11px; }
        .marks-table td.sub-name { text-align: left; font-weight: bold; color: #0f172a; }
        .grade-tag { color: #2563eb; font-weight: bold; }
        .footer-signatures { width: 100%; margin-top: 30px; }
        .footer-signatures td { vertical-align: bottom; height: 50px; }
        .sign-line { border-top: 1px solid #334155; width: 80%; margin: 0 auto; text-align: center; font-size: 11px; font-weight: bold; padding-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="school-name">{{ $school?->name ?? 'EduTinker Public School' }}</h1>
        <div class="school-address">{{ $school?->address ?? 'Main Campus, Educational District' }}</div>
    </div>

    <div class="session-title">
        OFFICIAL REPORT CARD - ACADEMIC YEAR: {{ $activeAcademicYear }} ({{ $selectedExam }})
    </div>

    <table class="student-details">
        <tr>
            <td style="width: 50%;"><span class="lbl">Student Name:</span> <span class="val">{{ $student->full_name }}</span></td>
            <td style="width: 50%;"><span class="lbl">Admission No:</span> <span class="val">{{ $student->admission_number }}</span></td>
        </tr>
        <tr>
            <td><span class="lbl">Class & Section:</span> <span class="val">{{ $student->class?->name }} - {{ $student->section?->name }}</span></td>
            <td><span class="lbl">Roll No / PEN:</span> <span class="val">{{ $student->roll_number ?? '101' }} / {{ $student->pen_number ?? 'PEN-90021' }}</span></td>
        </tr>
        <tr>
            <td><span class="lbl">Father's/Guardian's Name:</span> <span class="val">{{ $student->father_name ?? 'Parent Name' }}</span></td>
            <td><span class="lbl">Date of Birth:</span> <span class="val">{{ $student->date_of_birth ?? '10/05/2012' }}</span></td>
        </tr>
    </table>

    <table class="marks-table">
        <thead>
            <tr>
                <th style="width: 35%; text-align: left;">Subject</th>
                <th style="width: 20%;">Marks Obtained</th>
                <th style="width: 20%;">Max Marks</th>
                <th style="width: 15%;">Percentage</th>
                <th style="width: 10%;">Grade</th>
            </tr>
        </thead>
        <tbody>
            @php $totObt = 0; $totMax = 0; @endphp
            @foreach($marks as $m)
                @php
                    $obt = (float)$m->marks_obtained;
                    $max = (float)$m->max_marks;
                    $totObt += $obt;
                    $totMax += $max;
                    $pct = $max > 0 ? round(($obt / $max) * 100, 1) : 0;
                    $grd = $m->grade ?? ($pct >= 90 ? 'A1' : ($pct >= 80 ? 'A2' : ($pct >= 70 ? 'B1' : 'B2')));
                @endphp
                <tr>
                    <td class="sub-name">{{ $m->subject?->name ?? 'Scholastic Subject' }}</td>
                    <td><strong>{{ $obt }}</strong></td>
                    <td>{{ (int)$max }}</td>
                    <td>{{ $pct }}%</td>
                    <td><span class="grade-tag">{{ $grd }}</span></td>
                </tr>
            @endforeach
            <tr style="background: #f8fafc; font-weight: bold;">
                <td style="text-align: left; color: #1e40af;">GRAND TOTAL</td>
                <td style="color: #1e40af;">{{ $totObt }}</td>
                <td>{{ $totMax }}</td>
                <td>{{ $totMax > 0 ? round(($totObt / $totMax) * 100, 1) : 0 }}%</td>
                <td>
                    @php $overallPct = $totMax > 0 ? round(($totObt / $totMax) * 100, 1) : 0; @endphp
                    <span style="color: #16a34a;">{{ $overallPct >= 90 ? 'A1' : ($overallPct >= 80 ? 'A2' : 'B1') }}</span>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="footer-signatures">
        <tr>
            <td style="width: 33%;">
                <div class="sign-line">Date: {{ date('d/m/Y') }}</div>
            </td>
            <td style="width: 33%;">
                <div class="sign-line">Class Teacher Signature</div>
            </td>
            <td style="width: 33%;">
                <div class="sign-line">Principal Signature ({{ $principal?->full_name ?? 'Dr. S. K. Sharma' }})</div>
            </td>
        </tr>
    </table>
</body>
</html>
