<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle ?? 'Sibling Family Relationship Report' }}</title>
    <style>
        @page {
            margin: 25px 25px 35px 25px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 9.5px;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
            border-bottom: 2px solid #7c3aed;
            padding-bottom: 8px;
        }
        .header-logo {
            width: 55px;
            height: auto;
            max-height: 55px;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #7c3aed;
            margin: 0;
            text-transform: uppercase;
        }
        .school-info {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 2px;
        }
        .report-title-box {
            background: #f5f3ff;
            border: 1px solid #ddd6fe;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 12px;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #5b21b6;
            margin: 0;
        }
        .meta-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            color: #4c1d95;
            margin-top: 4px;
        }
        .meta-grid td {
            padding: 2px 0;
        }

        /* KPI Bar */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .kpi-cell {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 6px 8px;
            text-align: center;
        }
        .kpi-lbl {
            font-size: 7.5px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
        }
        .kpi-val {
            font-size: 11px;
            font-weight: bold;
            color: #0f172a;
            margin-top: 2px;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .data-table th {
            background: #7c3aed;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 7px;
            text-align: left;
            border: 1px solid #7c3aed;
        }
        .data-table td {
            padding: 5px 7px;
            font-size: 8.5px;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }
        .data-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 7.5px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-purple { background: #f3e8ff; color: #6b21a8; }
        .badge-info   { background: #dbeafe; color: #1e40af; }

        .footer-note {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 4px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
    </style>
</head>
<body>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            @if(!empty($school->logo) && file_exists(public_path('storage/' . $school->logo)))
                <td style="width: 65px; vertical-align: middle;">
                    <img src="{{ public_path('storage/' . $school->logo) }}" class="header-logo" alt="Logo">
                </td>
            @endif
            <td style="vertical-align: middle;">
                <h1 class="school-name">{{ $school->name ?? 'SCHOOL ERP' }}</h1>
                <div class="school-info">
                    {{ $school->address ?? '' }} {{ $school->city ? ', ' . $school->city : '' }} {{ $school->phone ? ' | Phone: ' . $school->phone : '' }}
                </div>
            </td>
            <td class="text-right" style="vertical-align: middle;">
                <div style="font-size: 10px; font-weight: bold; color: #7c3aed;">OFFICIAL SIBLING REPORT</div>
                <div style="font-size: 8px; color: #64748b;">Generated: {{ now()->format('d M Y, h:i A') }}</div>
                <div style="font-size: 8px; color: #64748b;">By: {{ auth()->user()->name ?? 'System' }}</div>
            </td>
        </tr>
    </table>

    <!-- Report Title Box -->
    <div class="report-title-box">
        <h2 class="report-title">{{ $reportTitle }}</h2>
        <table class="meta-grid">
            <tr>
                <td><strong>Academic Session:</strong> {{ $sessionName }}</td>
                <td><strong>Date Range:</strong> {{ $dateFrom }} to {{ $dateTo }}</td>
                <td><strong>Filters Applied:</strong> {{ $filterSummary }}</td>
            </tr>
        </table>
    </div>

    <!-- Summary KPI Cards Bar -->
    @if(!empty($kpis))
    <table class="kpi-table">
        <tr>
            @foreach($kpis as $kpi)
            <td class="kpi-cell">
                <div class="kpi-lbl">{{ $kpi['label'] }}</div>
                <div class="kpi-val">{{ $kpi['value'] }}</div>
            </td>
            @endforeach
        </tr>
    </table>
    @endif

    <!-- Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 12%;">Family Phone</th>
                <th style="width: 18%;">Parent / Guardian</th>
                <th style="width: 18%;">Student Name</th>
                <th style="width: 10%;">Adm No</th>
                <th style="width: 12%;">Class & Sec</th>
                <th style="width: 20%;">Sibling(s) Enrolled</th>
                <th style="width: 6%; text-align: center;">Total</th>
            </tr>
        </thead>
        <tbody>
            @php $count = 1; @endphp
            @forelse($siblingGroups as $group)
                @php
                    $parentName = $group->first()->father_name ?? ($group->first()->guardian_name ?? '—');
                    $phone = $group->first()->guardian_phone ?? ($group->first()->father_phone ?? '—');
                @endphp
                @foreach($group as $st)
                    @php
                        $otherSiblings = $group->reject(fn($s) => $s->id === $st->id);
                        $siblingText = $otherSiblings->map(fn($s) => $s->full_name . ' (' . ($s->class?->name ?? '—') . ')')->implode(', ');
                    @endphp
                    <tr>
                        <td>{{ $count++ }}</td>
                        <td style="font-weight: bold;">{{ $phone }}</td>
                        <td>{{ $parentName }}</td>
                        <td style="font-weight: bold; color: #5b21b6;">{{ $st->full_name }}</td>
                        <td>{{ $st->admission_number }}</td>
                        <td>{{ ($st->class?->name ?? '—') . ($st->section ? ' - ' . $st->section->name : '') }}</td>
                        <td style="color: #4c1d95;">{{ $siblingText ?: '—' }}</td>
                        <td class="text-center">
                            <span class="badge badge-purple">{{ $group->count() }}</span>
                        </td>
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 15px; color: #64748b;">
                        No sibling family groups found matching the criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        {{ $school->name ?? 'School ERP' }} — Sibling Family Relationship Report — Confidentially Generated Record
    </div>

    <script type="text/php">
        if (isset($pdf)) {
            $text = "Page {PAGE_NUM} of {PAGE_COUNT}";
            $font = $fontMetrics->get_font("DejaVu Sans", "bold");
            $size = 7.5;
            $color = array(0.4, 0.4, 0.4);
            $y = $pdf->get_height() - 22;
            $x = $pdf->get_width() - 80;
            $pdf->page_text($x, $y, $text, $font, $size, $color);
        }
    </script>
</body>
</html>
