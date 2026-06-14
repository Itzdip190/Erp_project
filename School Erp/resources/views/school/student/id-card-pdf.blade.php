<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student ID Card - {{ $student->admission_number }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background: #020617;
            color: #FFFFFF;
            margin: 0;
            padding: 10px;
        }
        .card {
            width: 280px;
            height: 420px;
            background: #0B0F19;
            border-radius: 12px;
            border: 2px solid #3B82F6;
            margin: 20px auto;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        .header {
            font-size: 16px;
            font-weight: bold;
            color: #3B82F6;
            text-transform: uppercase;
            margin-bottom: 15px;
            letter-spacing: 1px;
        }
        .photo-container {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            border: 2.5px solid #3B82F6;
            margin: 0 auto 12px;
            overflow: hidden;
            background: #1F2937;
        }
        .photo-placeholder {
            width: 100%;
            height: 100%;
            line-height: 100px;
            font-size: 32px;
            color: #9CA3AF;
            background: #1F2937;
        }
        .name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 4px;
        }
        .admission {
            font-size: 11px;
            color: #9CA3AF;
            margin-bottom: 15px;
            font-weight: 600;
        }
        .info-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            text-align: left;
        }
        .info-table td {
            padding: 3px 0;
            font-size: 12px;
        }
        .info-label {
            color: #9CA3AF;
            font-weight: 600;
            width: 45%;
        }
        .qr-code {
            width: 80px;
            height: 80px;
            margin: 10px auto 0;
            background: #FFFFFF;
            padding: 4px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">Yash International School</div>
        
        <div class="photo-container">
            @if($student->photo)
                <!-- Absolute path mapping for local storage disk support in DomPDF -->
                <img src="{{ storage_path('app/public/' . $student->photo) }}" style="width: 100%; height: 100%; object-fit: cover;" />
            @else
                <div class="photo-placeholder">
                    {{ strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) }}
                </div>
            @endif
        </div>
        
        <div class="name">{{ $student->full_name }}</div>
        <div class="admission">{{ $student->admission_number }}</div>
        
        <table class="info-table">
            <tr>
                <td class="info-label">Class:</td>
                <td>{{ $student->class?->name }} - {{ $student->section?->name }}</td>
            </tr>
            <tr>
                <td class="info-label">Roll Number:</td>
                <td>{{ $student->roll_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Blood Group:</td>
                <td>{{ $student->blood_group ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td class="info-label">Guardian Phone:</td>
                <td>{{ $student->guardian_phone }}</td>
            </tr>
        </table>
        
        <div class="qr-code">
            <img src="data:image/png;base64,{{ $qrCode }}" style="width: 100%; height: 100%;" />
        </div>
    </div>
</body>
</html>
