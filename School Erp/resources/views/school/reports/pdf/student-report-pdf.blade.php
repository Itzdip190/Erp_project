<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $reportTitle ?? 'Student Demographics & Enrolment Report' }}</title>
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
            border-bottom: 2px solid #1d4ed8;
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
            color: #1d4ed8;
            margin: 0;
            text-transform: uppercase;
        }
        .school-info {
            font-size: 8.5px;
            color: #64748b;
            margin-top: 2px;
        }
        .report-title-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            border-radius: 6px;
            padding: 8px 12px;
            margin-bottom: 12px;
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
            font-size: 8.5px;
            color: #475569;
            margin-top: 4px;
        }
        .meta-grid td {
            padding: 2px 0;
        }

        /* KPI Bar */
        .kpi-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
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
            background: #1d4ed8;
            color: #ffffff;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 7px;
            text-align: left;
            border: 1px solid #1d4ed8;
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
        .badge-success { background: #dcfce7; color: #166534; }
        .badge-danger  { background: #fee2e2; color: #991b1b; }
        .badge-info    { background: #dbeafe; color: #1e40af; }
        .badge-warning { background: #fef3c7; color: #92400e; }

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
                <div style="font-size: 10px; font-weight: bold; color: #1d4ed8;">OFFICIAL STUDENT REPORT</div>
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
                <th style="width: 3%;">#</th>
                <th style="width: 8%;">Adm / Roll</th>
                <th style="width: 15%;">Student Info</th>
                <th style="width: 8%;">Class & Sec</th>
                <th style="width: 18%;">Parents Details</th>
                <th style="width: 17%;">Medical Report</th>
                <th style="width: 16%;">Uploaded Documents</th>
                <th style="width: 10%;">Contact & Date</th>
                <th style="width: 5%; text-align: center;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $idx => $st)
                <tr>
                    <td>{{ $idx + 1 }}</td>
                    <td>
                        <div style="font-weight: bold; color: #1d4ed8;">{{ $st->admission_number }}</div>
                        @if($st->roll_number)<div style="font-size: 7.5px; color: #64748b;">Roll: {{ $st->roll_number }}</div>@endif
                    </td>
                    <td>
                        <div style="font-weight: bold; color: #0f172a;">{{ $st->full_name }}</div>
                        <div style="font-size: 7.5px; color: #475569;">
                            {{ $st->gender ?? '—' }}
                            @if($st->date_of_birth) | DOB: {{ \Carbon\Carbon::parse($st->date_of_birth)->format('d M Y') }}@endif
                            @if($st->blood_group) | <strong>({{ $st->blood_group }})</strong>@endif
                        </div>
                    </td>
                    <td>
                        <div style="font-weight: bold;">{{ $st->class?->name ?? '—' }}</div>
                        <div style="font-size: 7.5px; color: #64748b;">Sec: {{ $st->section?->name ?? '—' }}</div>
                    </td>
                    <td>
                        @if($st->father_name)
                            <div><strong>F:</strong> {{ $st->father_name }} @if($st->father_phone)({{ $st->father_phone }})@endif</div>
                        @endif
                        @if($st->mother_name)
                            <div style="font-size: 7.5px; color: #475569;"><strong>M:</strong> {{ $st->mother_name }} @if($st->mother_phone)({{ $st->mother_phone }})@endif</div>
                        @endif
                        @if(!$st->father_name && !$st->mother_name && $st->guardian_name)
                            <div style="font-size: 7.5px; color: #475569;"><strong>G:</strong> {{ $st->guardian_name }} @if($st->guardian_phone)({{ $st->guardian_phone }})@endif</div>
                        @endif
                        @if(!$st->father_name && !$st->mother_name && !$st->guardian_name)
                            <span style="color: #94a3b8;">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="font-size: 7.5px; line-height: 1.2;">
                            @if($st->medical_height || $st->medical_weight)
                                <div>
                                    @if($st->medical_height)<span>H: {{ $st->medical_height }}cm </span>@endif
                                    @if($st->medical_weight)<span>W: {{ $st->medical_weight }}kg</span>@endif
                                </div>
                            @endif
                            @if($st->medical_illness)<div style="color: #b91c1c;"><strong>Illness:</strong> {{ \Illuminate\Support\Str::limit($st->medical_illness, 25) }}</div>@endif
                            @if($st->medical_allergies)<div style="color: #d97706;"><strong>Allergy:</strong> {{ \Illuminate\Support\Str::limit($st->medical_allergies, 25) }}</div>@endif
                            @if($st->medical_history)<div style="color: #475569;"><strong>History:</strong> {{ \Illuminate\Support\Str::limit($st->medical_history, 25) }}</div>@endif
                            @if(!$st->medical_height && !$st->medical_weight && !$st->medical_illness && !$st->medical_allergies && !$st->medical_history)
                                <span style="color: #94a3b8;">Normal / None</span>
                            @endif
                        </div>
                    </td>
                    <td>
                        @if($st->documents && $st->documents->count() > 0)
                            <span class="badge badge-info" style="margin-bottom: 2px;">{{ $st->documents->count() }} Doc(s)</span>
                            <div style="font-size: 7px; color: #334155;">
                                {{ $st->documents->map(fn($d) => $d->document_type ?: 'Document')->implode(', ') }}
                            </div>
                        @else
                            <span class="badge badge-danger">No Docs</span>
                        @endif
                    </td>
                    <td>
                        <div>{{ $st->phone ?? ($st->guardian_phone ?? '—') }}</div>
                        <div style="font-size: 7.5px; color: #64748b;">
                            {{ $st->admission_date ? \Carbon\Carbon::parse($st->admission_date)->format('d M Y') : ($st->created_at ? $st->created_at->format('d M Y') : '—') }}
                        </div>
                    </td>
                    <td class="text-center">
                        <span class="badge {{ $st->is_active ? 'badge-success' : 'badge-danger' }}">
                            {{ $st->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center" style="padding: 15px; color: #64748b;">
                        No student records found matching the criteria.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer-note">
        {{ $school->name ?? 'School ERP' }} — Student Demographics & Document Report — Confidentially Generated Record
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
