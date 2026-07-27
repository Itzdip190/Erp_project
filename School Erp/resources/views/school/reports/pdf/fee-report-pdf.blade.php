<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle ?? 'Fee Report' }}</title>
    <style>
        @page {
            margin: 25px 25px 35px 25px;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            border-bottom: 2px solid #1d4ed8;
            padding-bottom: 10px;
        }
        .header-logo {
            width: 60px;
            height: auto;
            max-height: 60px;
        }
        .school-name {
            font-size: 18px;
            font-weight: bold;
            color: #1d4ed8;
            margin: 0;
            text-transform: uppercase;
        }
        .school-info {
            font-size: 9px;
            color: #64748b;
            margin-top: 2px;
        }
        .report-title-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 15px;
        }
        .report-title {
            font-size: 14px;
            font-weight: bold;
            color: #1e40af;
            margin: 0;
        }
        .meta-grid {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            color: #475569;
            margin-top: 4px;
        }
        .meta-grid td {
            padding: 2px 0;
        }
        
        /* Summary KPI Bar */
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
            font-size: 8px;
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
            background: #1d4ed8;
            color: #ffffff;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 8px;
            text-align: left;
            border: 1px solid #1d4ed8;
        }
        .data-table td {
            padding: 5px 8px;
            font-size: 9px;
            border-bottom: 1px solid #e2e8f0;
            border-left: 1px solid #e2e8f0;
            border-right: 1px solid #e2e8f0;
        }
        .data-table tr:nth-child(even) td {
            background: #f8fafc;
        }
        .data-table tr.total-row td {
            background: #eff6ff;
            font-weight: bold;
            font-size: 9.5px;
            color: #1e40af;
            border-top: 2px solid #1d4ed8;
            border-bottom: 2px solid #1d4ed8;
        }
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .badge-info { background: #dbeafe; color: #1e40af; }

        .footer-note {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            font-size: 8px;
            color: #94a3b8;
            text-align: center;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
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
                <td style="width: 70px; vertical-align: middle;">
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
                <div style="font-size: 10px; font-weight: bold; color: #1d4ed8;">OFFICIAL REPORT</div>
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
                @foreach($headers as $h)
                    <th class="{{ isset($h['class']) ? $h['class'] : '' }}">{{ $h['title'] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($headers as $key => $h)
                        @php
                            $fieldKey = is_numeric($key) ? ($h['key'] ?? '') : $key;
                            $val = $row[$fieldKey] ?? '—';
                            $align = isset($h['align']) ? 'text-' . $h['align'] : '';
                        @endphp
                        <td class="{{ $align }}">
                            @if(isset($h['type']) && $h['type'] === 'badge')
                                <span class="badge badge-{{ $row[$fieldKey . '_badge'] ?? 'info' }}">{{ $val }}</span>
                            @else
                                {!! $val !!}
                            @endif
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) }}" class="text-center" style="padding: 15px; color: #64748b;">
                        No records found matching the criteria.
                    </td>
                </tr>
            @endforelse

            @if(!empty($totals))
                <tr class="total-row">
                    @foreach($headers as $key => $h)
                        @php
                            $fieldKey = is_numeric($key) ? ($h['key'] ?? '') : $key;
                            $align = isset($h['align']) ? 'text-' . $h['align'] : '';
                        @endphp
                        <td class="{{ $align }}">
                            {!! $totals[$fieldKey] ?? '' !!}
                        </td>
                    @endforeach
                </tr>
            @endif
        </tbody>
    </table>

    <div class="footer-note">
        This report is computer generated by {{ $school->name ?? 'School ERP' }} on {{ now()->format('d-m-Y H:i') }}.
    </div>

</body>
</html>
