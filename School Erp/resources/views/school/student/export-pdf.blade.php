<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Master Export PDF</title>
    <style>
        @page {
            margin: 15px;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 7.5px;
            color: #1e293b;
            background-color: #ffffff;
            margin: 0;
            padding: 0;
        }

        /* Banner Header */
        .banner {
            background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%);
            color: #ffffff;
            padding: 14px 20px;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .banner-table {
            width: 100%;
            border-collapse: collapse;
        }
        .banner-title {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin: 0;
            color: #ffffff;
        }
        .banner-subtitle {
            font-size: 9px;
            color: #bfdbfe;
            margin-top: 4px;
        }
        .banner-right {
            text-align: right;
            font-size: 8.5px;
            color: #e0e7ff;
        }

        /* KPI Summary Bar */
        .kpi-bar {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        .kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 8px 12px;
            text-align: center;
        }
        .kpi-title {
            font-size: 7px;
            text-transform: uppercase;
            color: #64748b;
            font-weight: bold;
            letter-spacing: 0.5px;
        }
        .kpi-value {
            font-size: 13px;
            font-weight: bold;
            color: #1e40af;
            margin-top: 2px;
        }
        .kpi-value.green { color: #166534; }
        .kpi-value.red { color: #991b1b; }

        /* Filter Pills */
        .filter-info {
            background: #eff6ff;
            border: 1px solid #dbeafe;
            border-radius: 6px;
            padding: 6px 12px;
            margin-bottom: 15px;
            font-size: 8px;
            color: #1e40af;
        }

        /* Master Data Table */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }
        .data-table th {
            background-color: #1d4ed8;
            color: #ffffff;
            font-size: 6.5px;
            font-weight: bold;
            text-transform: uppercase;
            padding: 6px 3px;
            border: 1px solid #1e40af;
            text-align: center;
            white-space: nowrap;
        }
        .data-table td {
            font-size: 6.5px;
            padding: 4px 3px;
            border: 1px solid #cbd5e1;
            color: #334155;
            vertical-align: middle;
            text-align: left;
            word-wrap: break-word;
        }
        .data-table tr:nth-child(even) td {
            background-color: #f8fafc;
        }
        .data-table tr:hover td {
            background-color: #f1f5f9;
        }

        /* Status Badge */
        .badge {
            display: inline-block;
            padding: 2px 5px;
            font-size: 6px;
            font-weight: bold;
            border-radius: 3px;
            text-align: center;
            text-transform: uppercase;
        }
        .badge-active {
            background-color: #dcfce7;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }
        .badge-inactive {
            background-color: #fee2e2;
            color: #b91c1c;
            border: 1px solid #fca5a5;
        }

        /* Footer */
        .footer {
            margin-top: 15px;
            text-align: center;
            font-size: 7.5px;
            color: #94a3b8;
            border-top: 1px solid #e2e8f0;
            padding-top: 8px;
        }
    </style>
</head>
<body>

    <!-- Header Banner -->
    <div class="banner">
        <table class="banner-table">
            <tr>
                <td>
                    <div class="banner-title">{{ $school->name ?? 'School ERP System' }}</div>
                    <div class="banner-subtitle">Student Master Records Export Report</div>
                </td>
                <td class="banner-right">
                    <div><strong>Generated:</strong> {{ date('d M Y, h:i A') }}</div>
                    <div><strong>Total Students:</strong> {{ count($students) }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Filter Context -->
    <div class="filter-info">
        <strong>Applied Filters:</strong> 
        Class: {{ $filters['class'] }} &nbsp;|&nbsp; 
        Section: {{ $filters['section'] }} &nbsp;|&nbsp; 
        Status Scope: {{ $filters['status'] }} &nbsp;|&nbsp; 
        Search Keyword: {{ $filters['search'] }}
    </div>

    <!-- KPI Summary Bar -->
    <table class="kpi-bar">
        <tr>
            <td style="width: 33%; padding-right: 6px;">
                <div class="kpi-card">
                    <div class="kpi-title">Total Records Exported</div>
                    <div class="kpi-value">{{ count($students) }}</div>
                </div>
            </td>
            <td style="width: 33%; padding-right: 6px; padding-left: 6px;">
                <div class="kpi-card">
                    <div class="kpi-title">Active Enrolments</div>
                    <div class="kpi-value green">{{ $students->where('is_active', 1)->count() }}</div>
                </div>
            </td>
            <td style="width: 33%; padding-left: 6px;">
                <div class="kpi-card">
                    <div class="kpi-title">Inactive / Deactivated</div>
                    <div class="kpi-value red">{{ $students->where('is_active', 0)->count() }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Main 43-Column Data Table -->
    <table class="data-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Adm No</th>
                <th>Adm Date</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Full Name</th>
                <th>Class</th>
                <th>Sec</th>
                <th>Roll No</th>
                <th>Session</th>
                <th>DOB</th>
                <th>Gender</th>
                <th>Religion</th>
                <th>Caste</th>
                <th>Sub Caste</th>
                <th>Category</th>
                <th>Sub Cat</th>
                <th>Blood</th>
                <th>Allergy</th>
                <th>Medical Condition</th>
                <th>Birthmark</th>
                <th>Adhar No</th>
                <th>Father Name</th>
                <th>Father Mobile</th>
                <th>Father ID</th>
                <th>Mother Name</th>
                <th>Mother Mobile</th>
                <th>Mother ID</th>
                <th>Address</th>
                <th>City</th>
                <th>State</th>
                <th>Country</th>
                <th>Zip</th>
                <th>Emerg. Name</th>
                <th>Emerg. Phone</th>
                <th>Doc Phone</th>
                <th>Doc Detail</th>
                <th>Email</th>
                <th>Adm Type</th>
                <th>Boarding</th>
                <th>Defence</th>
                <th>Transport</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($students as $index => $student)
            <tr>
                <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
                <td style="font-weight: bold;">{{ $student->admission_number ?? '—' }}</td>
                <td>{{ $student->admission_date ? \Carbon\Carbon::parse($student->admission_date)->format('d/m/Y') : '—' }}</td>
                <td>{{ $student->first_name ?? '—' }}</td>
                <td>{{ $student->last_name ?? '—' }}</td>
                <td><strong>{{ $student->full_name ?? '—' }}</strong></td>
                <td>{{ $student->class?->name ?? '—' }}</td>
                <td>{{ $student->section?->name ?? '—' }}</td>
                <td>{{ $student->roll_number ?? '—' }}</td>
                <td>{{ $student->academicSession?->name ?? ($student->admission_year ?? '—') }}</td>
                <td>{{ $student->date_of_birth ? \Carbon\Carbon::parse($student->date_of_birth)->format('d/m/Y') : '—' }}</td>
                <td>{{ ucfirst($student->gender ?? '—') }}</td>
                <td>{{ $student->religion ?? '—' }}</td>
                <td>{{ $student->caste ?? '—' }}</td>
                <td>{{ $student->sub_caste ?? '—' }}</td>
                <td>{{ $student->category_name ?? ($student->category?->name ?? '—') }}</td>
                <td>{{ $student->sub_category ?? '—' }}</td>
                <td>{{ $student->blood_group ?? '—' }}</td>
                <td>{{ $student->any_allergy ?? '—' }}</td>
                <td>{{ $student->medical_allergies ?? '—' }}</td>
                <td>{{ $student->birthmark ?? '—' }}</td>
                <td>{{ $student->national_id ?? '—' }}</td>
                <td>{{ $student->father_name ?? '—' }}</td>
                <td>{{ $student->father_phone ?? '—' }}</td>
                <td>{{ $student->father_id ?? '—' }}</td>
                <td>{{ $student->mother_name ?? '—' }}</td>
                <td>{{ $student->mother_phone ?? '—' }}</td>
                <td>{{ $student->mother_id ?? '—' }}</td>
                <td>{{ $student->address ?? '—' }}</td>
                <td>{{ $student->city ?? '—' }}</td>
                <td>{{ $student->state ?? '—' }}</td>
                <td>{{ $student->country ?? '—' }}</td>
                <td>{{ $student->pincode ?? '—' }}</td>
                <td>{{ $student->emergency_name ?? '—' }}</td>
                <td>{{ $student->emergency_number ?? '—' }}</td>
                <td>{{ $student->medical_doctor_phone ?? '—' }}</td>
                <td>{{ $student->medical_doctor_name ?? '—' }}</td>
                <td>{{ $student->email ?? '—' }}</td>
                <td>{{ $student->admission_type ?? '—' }}</td>
                <td>{{ $student->boarding_type ?? '—' }}</td>
                <td>{{ $student->defence_personal ?? '—' }}</td>
                <td>{{ $student->transport_route ?? ($student->transport_opted ? 'Yes' : 'No') }}</td>
                <td style="text-align: center;">
                    @if($student->is_active)
                        <span class="badge badge-active">Active</span>
                    @else
                        <span class="badge badge-inactive">Inactive</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="44" style="text-align: center; padding: 15px; color: #94a3b8;">
                    No student records found matching the criteria.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Confidential — {{ $school->name ?? 'School ERP' }} Student Information System — Page 1 of 1
    </div>

</body>
</html>
