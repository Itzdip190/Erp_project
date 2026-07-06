<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Admission Form - {{ $student->full_name }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #333333;
            font-size: 13px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #f59e0b;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .school-name {
            font-size: 24px;
            font-weight: bold;
            color: #1e3a8a;
            margin: 0 0 5px 0;
            text-transform: uppercase;
        }
        .form-title {
            font-size: 16px;
            font-weight: bold;
            color: #f59e0b;
            margin: 0;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .section-title {
            background-color: #f3f4f6;
            color: #1e3a8a;
            font-weight: bold;
            padding: 6px 10px;
            margin-top: 15px;
            margin-bottom: 10px;
            font-size: 14px;
            border-left: 4px solid #f59e0b;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        th, td {
            padding: 6px 8px;
            text-align: left;
            vertical-align: top;
        }
        .field-label {
            font-weight: bold;
            color: #4b5563;
            width: 30%;
        }
        .field-value {
            color: #111827;
            width: 70%;
        }
        .grid-table td {
            width: 50%;
            padding: 4px 8px;
        }
        .grid-label {
            font-weight: bold;
            color: #4b5563;
            display: inline-block;
            width: 150px;
        }
        .grid-value {
            color: #111827;
        }
        .photo-box {
            border: 1px solid #d1d5db;
            width: 120px;
            height: 140px;
            text-align: center;
            vertical-align: middle;
            font-size: 11px;
            color: #9ca3af;
        }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #e5e7eb;
            padding-top: 30px;
        }
        .signature-table td {
            text-align: center;
            width: 33%;
        }
        .signature-line {
            border-top: 1px solid #4b5563;
            width: 80%;
            margin: 0 auto 5px auto;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="school-name">{{ $student->school?->name ?? 'SchoolCloud ERP' }}</h1>
        <h2 class="form-title">Student Admission Form</h2>
    </div>

    <table style="margin-bottom: 25px;">
        <tr>
            <td style="width: 75%; padding: 0;">
                <table>
                    <tr>
                        <td class="field-label">Admission No:</td>
                        <td class="field-value" style="font-weight: bold; color: #1e3a8a;">{{ $student->admission_number }}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Admission Date:</td>
                        <td class="field-value">{{ $student->admission_date ? $student->admission_date->format('d M Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Class & Section:</td>
                        <td class="field-value">{{ $student->class?->name ?? 'N/A' }} - {{ $student->section?->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Roll Number:</td>
                        <td class="field-value">{{ $student->roll_number ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Admission Type:</td>
                        <td class="field-value">{{ $student->admission_type ?? 'N/A' }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 25%; text-align: right; padding: 0;">
                <div style="float: right;">
                    @if($student->photo && file_exists(public_path('storage/' . $student->photo)))
                        <img src="{{ public_path('storage/' . $student->photo) }}" style="width: 120px; height: 140px; border: 1px solid #d1d5db; object-fit: cover;">
                    @else
                        <div class="photo-box" style="line-height: 140px;">Affix Photo Here</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">1. Personal Details</div>
    <table class="grid-table">
        <tr>
            <td>
                <span class="grid-label">Full Name:</span>
                <span class="grid-value" style="font-weight: bold;">{{ $student->full_name }}</span>
            </td>
            <td>
                <span class="grid-label">Gender:</span>
                <span class="grid-value">{{ ucfirst($student->gender) }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">Date of Birth:</span>
                <span class="grid-value">{{ $student->date_of_birth ? $student->date_of_birth->format('d M Y') : 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">Age:</span>
                <span class="grid-value">{{ $student->detailed_age }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">Religion:</span>
                <span class="grid-value">{{ $student->religion ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">Category:</span>
                <span class="grid-value">{{ $student->category?->name ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">Caste / Sub-Caste:</span>
                <span class="grid-value">{{ $student->caste ?? 'N/A' }} {{ $student->sub_caste ? '(' . $student->sub_caste . ')' : '' }}</span>
            </td>
            <td>
                <span class="grid-label">RTE Student:</span>
                <span class="grid-value" style="font-weight: bold; color: {{ $student->is_rte ? '#b91c1c' : '#047857' }}">{{ $student->is_rte ? 'Yes' : 'No' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">Blood Group:</span>
                <span class="grid-value">{{ $student->blood_group ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">Preferred Phone:</span>
                <span class="grid-value">{{ $student->phone ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">National ID:</span>
                <span class="grid-value">{{ $student->national_id ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">Preferred WhatsApp:</span>
                <span class="grid-value">{{ $student->whatsapp_number ?? 'N/A' }}</span>
            </td>
        </tr>
    </table>

    <div class="section-title">2. Parent & Guardian Information</div>
    <table class="grid-table">
        <tr>
            <td>
                <span class="grid-label">Father's Name:</span>
                <span class="grid-value">{{ $student->father_name ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">Mother's Name:</span>
                <span class="grid-value">{{ $student->mother_name ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">Father's Phone:</span>
                <span class="grid-value">{{ $student->father_phone ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">Mother's Phone:</span>
                <span class="grid-value">{{ $student->mother_phone ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">Father's Email:</span>
                <span class="grid-value">{{ $student->father_email ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">Mother's Email:</span>
                <span class="grid-value">{{ $student->mother_email ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-top: 1px dashed #e5e7eb; padding-top: 10px; margin-top: 5px;">
                <span class="grid-label" style="font-weight: bold; color: #1e3a8a;">Guardian Name:</span>
                <span class="grid-value" style="font-weight: bold;">{{ $student->guardian_name }} ({{ ucfirst($student->guardian_relationship) }})</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">Guardian Phone:</span>
                <span class="grid-value">{{ $student->guardian_phone }}</span>
            </td>
            <td>
                <span class="grid-label">Guardian Email:</span>
                <span class="grid-value">{{ $student->guardian_email ?? 'N/A' }}</span>
            </td>
        </tr>
    </table>

    <div class="section-title">3. Contact & Address Details</div>
    <table>
        <tr>
            <td class="field-label">Current Address:</td>
            <td class="field-value">
                {{ $student->address }}<br>
                @if($student->address_line_2){{ $student->address_line_2 }}<br>@endif
                {{ $student->city }}, {{ $student->state }} - {{ $student->pincode }}
            </td>
        </tr>
        @if($student->permanent_address)
        <tr>
            <td class="field-label">Permanent Address:</td>
            <td class="field-value">
                {{ $student->permanent_address }}<br>
                @if($student->permanent_address_line_2){{ $student->permanent_address_line_2 }}<br>@endif
                {{ $student->permanent_city }}, {{ $student->permanent_state }} - {{ $student->permanent_pincode }}
            </td>
        </tr>
        @endif
    </table>

    <div class="section-title">4. Medical Information</div>
    <table class="grid-table">
        <tr>
            <td>
                <span class="grid-label">Height / Weight:</span>
                <span class="grid-value">{{ $student->medical_height ?? 'N/A' }} / {{ $student->medical_weight ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">Vision (L/R):</span>
                <span class="grid-value">{{ $student->medical_vision_left ?? 'N/A' }} / {{ $student->medical_vision_right ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span class="grid-label">Allergies / Disabilities:</span>
                <span class="grid-value">{{ $student->medical_allergies ?? 'None' }} / {{ $student->medical_disabilities ?? 'None' }}</span>
            </td>
        </tr>
    </table>

    <div class="section-title">5. Attendance Report</div>
    <table style="margin-bottom: 10px;">
        <tr>
            <td style="font-weight: bold; width: 25%;">Attendance Rate:</td>
            <td style="color: #047857; font-weight: bold; width: 25%;">{{ $attendancePercentage }}%</td>
            <td style="font-weight: bold; width: 25%;">Total school days:</td>
            <td style="width: 25%;">{{ $totalDays }} days</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Days Present:</td>
            <td style="color: #047857; font-weight: bold;">{{ $presentDays }}</td>
            <td style="font-weight: bold;">Days Absent / Late:</td>
            <td><span style="color: #b91c1c; font-weight: bold;">{{ $absentDays }}</span> / <span style="color: #d97706; font-weight: bold;">{{ $lateDays }}</span></td>
        </tr>
    </table>

    <div class="section-title">6. Sibling Information</div>
    @if(count($siblings) > 0)
        <table style="width: 100%; border: 1px solid #e5e7eb; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #f9fafb;">
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Admission ID</th>
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Sibling Name</th>
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Class & Section</th>
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Roll Number</th>
                </tr>
            </thead>
            <tbody>
                @foreach($siblings as $sib)
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">{{ $sib->admission_number }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px; font-weight: bold;">{{ $sib->full_name }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">{{ $sib->class?->name ?? 'N/A' }} - {{ $sib->section?->name ?? 'N/A' }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">{{ $sib->roll_number ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="font-size: 11px; color: #6b7280; padding: 5px; margin-bottom: 15px;">No siblings registered in this school.</div>
    @endif

    <div class="section-title">7. Academic Exam Report</div>
    @if(count($marks) > 0)
        <table style="width: 100%; border: 1px solid #e5e7eb; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #f9fafb;">
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Exam Name</th>
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Subject</th>
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Obtained Marks</th>
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Max Marks</th>
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Grade</th>
                </tr>
            </thead>
            <tbody>
                @foreach($marks as $m)
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px; font-weight: bold; color: #1e3a8a;">{{ $m->exam_name }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">{{ $m->subject?->name ?? 'N/A' }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px; font-weight: bold; color: {{ ($m->marks_obtained / ($m->max_marks ?: 100)) < 0.4 ? '#b91c1c' : '#047857' }}">{{ $m->marks_obtained }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">{{ $m->max_marks }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px; font-weight: bold; color: #047857;">{{ $m->grade ?? 'N/A' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="font-size: 11px; color: #6b7280; padding: 5px; margin-bottom: 15px;">No exam results or marks records found.</div>
    @endif

    <div class="section-title">8. Transport Mapping Details</div>
    @if($student->transport_route || $student->transport_stop || $student->transport_vehicle_code)
        <table class="grid-table" style="margin-bottom: 15px;">
            <tr>
                <td>
                    <span class="grid-label" style="width:120px;">Transport Route:</span>
                    <span class="grid-value">{{ $student->transport_route ?? 'N/A' }}</span>
                </td>
                <td>
                    <span class="grid-label" style="width:120px;">Bus Stop Location:</span>
                    <span class="grid-value">{{ $student->transport_stop ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="grid-label" style="width:120px;">Pickup Vehicle:</span>
                    <span class="grid-value">{{ $student->transport_vehicle_code ?? 'N/A' }}</span>
                </td>
                <td>
                    <span class="grid-label" style="width:120px;">Dropoff Vehicle:</span>
                    <span class="grid-value">{{ $student->transport_drop_vehicle_code ?? 'N/A' }}</span>
                </td>
            </tr>
            <tr>
                <td>
                    <span class="grid-label" style="width:120px;">Mapped Month:</span>
                    <span class="grid-value">{{ $student->transport_month ?? 'N/A' }}</span>
                </td>
                <td>&nbsp;</td>
            </tr>
        </table>
    @else
        <div style="font-size: 11px; color: #6b7280; padding: 5px; margin-bottom: 15px;">This student is not currently mapped to any school transport routes.</div>
    @endif

    <div class="section-title">9. Financial Fees Ledger</div>
    <table style="margin-bottom: 10px;">
        <tr>
            <td style="font-weight: bold; width: 25%;">Opening Dues:</td>
            <td style="color: #b91c1c; font-weight: bold; width: 25%;">₹{{ number_format($student->opening_due_balance, 2) }}</td>
            <td style="font-weight: bold; width: 25%;">Total Assigned Fees:</td>
            <td style="width: 25%;">₹{{ number_format($fees->sum('amount'), 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight: bold;">Total Paid:</td>
            <td style="color: #047857; font-weight: bold;">₹{{ number_format($fees->sum('paid_amount'), 2) }}</td>
            <td style="font-weight: bold;">Outstanding Balance:</td>
            <td style="color: #b91c1c; font-weight: bold;">₹{{ number_format(max(0, $fees->sum('amount') - $fees->sum('paid_amount')), 2) }}</td>
        </tr>
    </table>

    @if(count($fees) > 0)
        <table style="width: 100%; border: 1px solid #e5e7eb; border-collapse: collapse; margin-bottom: 15px;">
            <thead>
                <tr style="background-color: #f9fafb;">
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Category</th>
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Installment</th>
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Due Date</th>
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Amount</th>
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Paid Amount</th>
                    <th style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fees as $f)
                    <tr>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px; font-weight: bold;">{{ $f->category?->name ?? 'N/A' }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">Installment #{{ $f->installment_no }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">{{ $f->due_date ? \Carbon\Carbon::parse($f->due_date)->format('d M Y') : '—' }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px; font-weight: bold;">₹{{ number_format($f->amount, 2) }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px; font-weight: bold; color: #047857;">₹{{ number_format($f->paid_amount, 2) }}</td>
                        <td style="border: 1px solid #e5e7eb; padding: 5px; font-size: 11px;">
                            @if(strtolower($f->status) === 'paid')
                                <span style="color: #047857; font-weight: bold;">Paid</span>
                            @elseif(strtolower($f->status) === 'partial' || strtolower($f->status) === 'partially_paid')
                                <span style="color: #d97706; font-weight: bold;">Partial</span>
                            @else
                                <span style="color: #b91c1c; font-weight: bold;">Unpaid</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div style="font-size: 11px; color: #6b7280; padding: 5px; margin-bottom: 15px;">No active fee allocations or maps found.</div>
    @endif

    <div class="footer">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <div>Student Signature</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div>Parent/Guardian Signature</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div>Authorized Principal/Officer</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
