<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Class Timetable - {{ $className }} {{ $sectionName }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm 6mm 10mm;
        }
        html, body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #1e293b;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        .pdf-container {
            width: 100%;
        }
        .pdf-header {
            text-align: center;
            border-bottom: 1.5px solid #2563eb;
            padding-bottom: 4px;
            margin-bottom: 8px;
        }
        .pdf-school-name {
            font-size: 16px;
            font-weight: 800;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0 0 2px 0;
            line-height: 1.1;
        }
        .pdf-subtitle {
            font-size: 12px;
            font-weight: 700;
            color: #2563eb;
            margin: 0;
            line-height: 1.1;
        }
        .pdf-meta {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        .grid-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            page-break-inside: avoid;
        }
        .grid-table th {
            background-color: #0f172a;
            color: #ffffff;
            font-size: 9.5px;
            font-weight: 700;
            text-transform: uppercase;
            padding: 5px 3px;
            text-align: center;
            border: 1px solid #334155;
        }
        .grid-table th.period-th {
            width: 100px;
            background-color: #1e293b;
            text-align: left;
            padding-left: 6px;
        }
        .grid-table tr {
            page-break-inside: avoid;
        }
        .grid-table td {
            border: 1px solid #cbd5e1;
            vertical-align: top;
            padding: 3px;
        }
        .period-td {
            background-color: #f8fafc;
            font-weight: 700;
            text-align: left;
            padding: 4px 6px;
        }
        .period-name {
            font-size: 10px;
            font-weight: 800;
            color: #0f172a;
            line-height: 1.1;
        }
        .period-time {
            font-size: 8px;
            color: #475569;
            margin-top: 2px;
            white-space: nowrap;
        }
        .subject-card-box {
            border-left-width: 3.5px;
            border-left-style: solid;
            padding: 3px 5px;
            border-radius: 4px;
            min-height: 26px;
        }
        .card-subject-name {
            font-size: 9.5px;
            font-weight: 800;
            margin-bottom: 1px;
            line-height: 1.15;
            word-wrap: break-word;
        }
        .card-teacher-name {
            font-size: 8px;
            font-weight: 600;
            font-style: italic;
            opacity: 0.85;
            line-height: 1.1;
            word-wrap: break-word;
        }
        .break-cell {
            background-color: #f1f5f9;
            text-align: center;
            color: #64748b;
            font-weight: 700;
            font-size: 9px;
            vertical-align: middle;
            letter-spacing: 0.5px;
        }
        .pdf-footer {
            margin-top: 6px;
            font-size: 8px;
            color: #94a3b8;
            text-align: right;
            border-top: 1px solid #e2e8f0;
            padding-top: 3px;
        }
    </style>
</head>
<body>

    <div class="pdf-container">
        <div class="pdf-header">
            <h1 class="pdf-school-name">{{ $school->name ?? 'SCHOOL ERP' }}</h1>
            <h2 class="pdf-subtitle">CLASS TIME TABLE: {{ $className }} - {{ $sectionName }}</h2>
            <div class="pdf-meta">Academic Session: {{ $academicYear }} &nbsp;|&nbsp; Generated on: {{ date('d M Y, h:i A') }}</div>
        </div>

        <table class="grid-table">
            <thead>
                <tr>
                    <th class="period-th">Period / Time</th>
                    @foreach($days as $day)
                        @php
                            $isActive = is_array($group->applicable_days) && in_array($day, $group->applicable_days);
                        @endphp
                        <th style="{{ $isActive ? '' : 'background-color: #475569;' }}">
                            {{ $day }}
                            @if(!$isActive)
                                <span style="font-size:7px; font-weight:400; opacity:0.8;">(Off)</span>
                            @endif
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($periods as $period)
                    @php
                        $pNameLower = strtolower($period->period_name);
                        $isBreak = str_contains($pNameLower, 'break') || str_contains($pNameLower, 'interval');
                    @endphp
                    <tr>
                        <td class="period-td">
                            <div class="period-name">{{ $period->period_name }}</div>
                            <div class="period-time">
                                {{ date('g:i A', strtotime($period->start_time)) }} - {{ date('g:i A', strtotime($period->end_time)) }}
                            </div>
                        </td>

                        @foreach($days as $day)
                            @php
                                $isActive = is_array($group->applicable_days) && in_array($day, $group->applicable_days);
                                $cell = $gridData[$period->id][$day] ?? null;
                            @endphp

                            @if($isBreak)
                                <td class="break-cell">RECESS / BREAK</td>
                            @elseif($cell && $cell->subject)
                                @php
                                    $colorHex = $cell->subject->effective_color ?? $cell->subject->color ?? '#3B82F6';
                                    $cStyle = \App\Http\Controllers\School\ClassTimetableController::getSubjectColorStyles($colorHex);
                                @endphp
                                <td style="background-color: {{ $cStyle['bg'] }}; padding: 2px;">
                                    <div class="subject-card-box" style="border-left-color: {{ $cStyle['border'] }}; background-color: {{ $cStyle['bg'] }};">
                                        <div class="card-subject-name" style="color: {{ $cStyle['text'] }};">
                                            {{ $cell->subject->name }}
                                        </div>
                                        @if($cell->teacher)
                                            <div class="card-teacher-name" style="color: {{ $cStyle['text'] }};">
                                                {{ $cell->teacher->full_name }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            @elseif($isActive)
                                <td style="background-color: #ffffff;"></td>
                            @else
                                <td style="background-color: #f8fafc;"></td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pdf-footer">
            Generated by EduCore ERP &bull; Official Class Timetable
        </div>
    </div>

</body>
</html>
