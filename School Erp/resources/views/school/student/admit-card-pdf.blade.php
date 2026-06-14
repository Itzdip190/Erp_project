<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admit Card - {{ $student->admission_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1E293B;
            line-height: 1.5;
            padding: 20px;
            background: #FFFFFF;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #3B82F6;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #1E3A8A;
            text-transform: uppercase;
        }
        .exam-title {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
            color: #4B5563;
        }
        .details-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }
        .details-table td {
            padding: 6px;
            font-size: 14px;
        }
        .label {
            font-weight: bold;
            color: #4B5563;
            width: 18%;
        }
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .schedule-table th, .schedule-table td {
            border: 1px solid #CBD5E1;
            padding: 10px;
            text-align: left;
            font-size: 13px;
        }
        .schedule-table th {
            background-color: #F1F5F9;
            color: #1E3A8A;
            font-weight: bold;
        }
        .instructions {
            border: 1px solid #FED7D7;
            background-color: #FFF5F5;
            padding: 15px;
            border-radius: 6px;
            font-size: 12px;
            color: #9B2C2C;
        }
        .instructions h4 {
            margin: 0 0 8px 0;
            text-transform: uppercase;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">Yash International School</div>
        <div class="exam-title">{{ $examName }}</div>
        <div style="font-size: 13px; color: #6B7280; margin-top: 5px;">ADMIT CARD</div>
    </div>

    <table class="details-table">
        <tr>
            <td class="label">Student Name:</td>
            <td style="font-weight: bold;">{{ $student->full_name }}</td>
            <td class="label">Admission No:</td>
            <td style="font-weight: bold;">{{ $student->admission_number }}</td>
        </tr>
        <tr>
            <td class="label">Class/Section:</td>
            <td>{{ $student->class?->name }} - {{ $student->section?->name }}</td>
            <td class="label">Roll Number:</td>
            <td>{{ $student->roll_number ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Guardian:</td>
            <td>{{ $student->guardian_name }}</td>
            <td class="label">Gender:</td>
            <td>{{ ucfirst($student->gender) }}</td>
        </tr>
    </table>

    <h3 style="color: #1E3A8A; font-size: 15px; margin-bottom: 10px; text-transform: uppercase;">Examination Schedule</h3>
    <table class="schedule-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Subject</th>
                <th>Timing</th>
                <th>Room No</th>
            </tr>
        </thead>
        <tbody>
            @foreach($timetable as $slot)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($slot['date'])->format('d M Y') }}</td>
                    <td style="font-weight: bold;">{{ $slot['subject'] }}</td>
                    <td>{{ $slot['time'] }}</td>
                    <td>{{ $slot['room'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="instructions">
        <h4>Important Instructions for Candidates</h4>
        <ol style="margin: 0; padding-left: 15px;">
            <li>Candidates must carry this admit card along with their valid student ID card to the exam hall.</li>
            <li>Please report to the exam center at least 15 minutes before the scheduled start time.</li>
            <li>No electronic devices, including smart watches or mobile phones, are permitted in the exam hall.</li>
            <li>Impersonation or use of unfair means will lead to immediate disqualification and disciplinary action.</li>
        </ol>
    </div>
</body>
</html>
