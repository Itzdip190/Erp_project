<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Timetable - {{ $teacher->full_name ?? 'Timetable' }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0f172a;
            font-size: 10.5pt;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        
        /* Header */
        .pdf-header {
            width: 100%;
            border-bottom: 2px solid #1e3a8a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .school-title {
            font-size: 16pt;
            font-weight: bold;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .doc-subtitle {
            font-size: 11pt;
            font-weight: bold;
            color: #334155;
            margin-top: 2px;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 12px;
            font-size: 9pt;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 10px;
        }
        .meta-table td {
            padding: 3px 6px;
        }

        /* Timetable Grid Table */
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .grid-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid #1e3a8a;
        }
        .grid-table td {
            border: 1px solid #cbd5e1;
            padding: 6px 4px;
            text-align: center;
            vertical-align: middle;
            font-size: 8.5pt;
            height: 52px;
        }
        .day-header {
            background-color: #f1f5f9;
            font-weight: bold;
            color: #1e3a8a;
            width: 75px;
            text-transform: uppercase;
            font-size: 9pt;
        }
        
        .lunch-cell {
            background-color: #f8fafc;
            color: #475569;
            font-weight: bold;
            font-size: 10pt;
            letter-spacing: 4px;
            text-align: center;
            width: 32px;
            border-left: 2px solid #cbd5e1;
            border-right: 2px solid #cbd5e1;
        }

        .slot-card {
            background-color: #f0f9ff;
            border-left: 3px solid #2563eb;
            border-radius: 3px;
            padding: 4px;
            text-align: center;
        }
        .slot-subject {
            font-weight: bold;
            color: #1e3a8a;
            font-size: 9pt;
            margin-bottom: 2px;
            display: block;
        }
        .slot-class {
            color: #334155;
            font-weight: 600;
            font-size: 8pt;
            margin-bottom: 2px;
            display: block;
        }
        .slot-time {
            color: #2563eb;
            font-weight: bold;
            font-size: 7.5pt;
            display: block;
        }
        .slot-empty {
            color: #94a3b8;
            font-size: 10pt;
        }

        .footer {
            margin-top: 14px;
            width: 100%;
            font-size: 7.5pt;
            color: #64748b;
            text-align: right;
            border-top: 1px dashed #cbd5e1;
            padding-top: 4px;
        }
    </style>
</head>
<body>

    <div class="pdf-header">
        <div class="school-title">{{ $school->name ?? 'SCHOOL TIMETABLE MANAGEMENT' }}</div>
        <div class="doc-subtitle">TEACHER WORK SCHEDULE TIMETABLE</div>
    </div>

    <table class="meta-table">
        <tr>
            <td width="35%"><strong>Teacher:</strong> {{ $teacher->full_name }}</td>
            <td width="30%"><strong>Designation:</strong> {{ $teacher->designation?->name ?? 'Teacher' }}</td>
            <td width="35%"><strong>Employee ID:</strong> {{ $teacher->employee_id ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td><strong>Academic Year:</strong> {{ $selectedSession->name ?? date('Y') }}</td>
            <td><strong>Generated On:</strong> {{ date('d M Y, h:i A') }}</td>
            <td><strong>Status:</strong> Official Schedule</td>
        </tr>
    </table>

    <table class="grid-table">
        <thead>
            <tr>
                <th style="width:75px;">DAY</th>
                @foreach($periodColumns as $key => $col)
                    @if($key === 'lunch')
                        <th style="width:32px; background-color:#334155; border-color:#334155;">LUNCH</th>
                    @else
                        <th>
                            {{ $col['label'] }}
                            @if(isset($periodTimings[$key]) && $periodTimings[$key])
                                <div style="font-size:7pt; font-weight:normal; opacity:0.9; margin-top:2px;">
                                    {{ $periodTimings[$key]['start'] }} – {{ $periodTimings[$key]['end'] }}
                                </div>
                            @endif
                        </th>
                    @endif
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($days as $dayIndex => $day)
                <tr>
                    <td class="day-header">{{ $day }}</td>

                    @foreach($periodColumns as $key => $col)
                        @if($key === 'lunch')
                            @if($dayIndex === 0)
                                <td rowspan="{{ count($days) }}" class="lunch-cell">
                                    L<br>U<br>N<br>C<br>H
                                </td>
                            @endif
                        @else
                            @php
                                $cell = $grid[$day][$key] ?? null;
                            @endphp
                            <td>
                                @if($cell)
                                    <div class="slot-card" style="border-left-color: {{ $cell->subject?->color ?? '#2563eb' }}; background-color: {{ $cell->subject?->color ? $cell->subject->color . '10' : '#f0f9ff' }};">
                                        <span class="slot-subject">{{ $cell->subject?->name ?? 'Subject' }}</span>
                                        <span class="slot-class">
                                            {{ $cell->schoolClass?->name }} - {{ $cell->section?->name ? (str_starts_with($cell->section->name, 'Sec') ? $cell->section->name : 'Sec ' . $cell->section->name) : '' }}
                                        </span>
                                        <span class="slot-time">
                                            {{ date('g:i A', strtotime($cell->period->start_time)) }} – {{ date('g:i A', strtotime($cell->period->end_time)) }}
                                        </span>
                                    </div>
                                @else
                                    <span class="slot-empty">-</span>
                                @endif
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Printed from ERP System &bull; Page 1 of 1
    </div>

</body>
</html>
