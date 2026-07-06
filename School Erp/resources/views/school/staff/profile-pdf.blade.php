<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Staff Profile - {{ $staff->full_name }}</title>
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
            border-bottom: 2px solid #3b82f6;
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
            color: #3b82f6;
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
            border-left: 4px solid #3b82f6;
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
            width: 50%;
        }
        .signature-line {
            border-top: 1px solid #4b5563;
            width: 70%;
            margin: 0 auto 5px auto;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1 class="school-name">{{ $staff->school?->name ?? 'SchoolCloud ERP' }}</h1>
        <h2 class="form-title">Staff Profile Record</h2>
    </div>

    <table style="margin-bottom: 25px;">
        <tr>
            <td style="width: 75%; padding: 0;">
                <table>
                    <tr>
                        <td class="field-label">Employee ID:</td>
                        <td class="field-value" style="font-weight: bold; color: #1e3a8a;">{{ $staff->employee_id }}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Joining Date:</td>
                        <td class="field-value">{{ $staff->joining_date ? $staff->joining_date->format('d M Y') : 'N/A' }}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Department / Designation:</td>
                        <td class="field-value">
                            {{ $staff->department?->name ?? 'N/A' }} / 
                            @if($staff->designations && $staff->designations->count() > 0)
                                {{ $staff->designations->pluck('name')->implode(', ') }}
                            @else
                                {{ $staff->designation?->name ?? 'N/A' }}
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="field-label">Employment Type:</td>
                        <td class="field-value">{{ ucfirst($staff->employment_type) }}</td>
                    </tr>
                    <tr>
                        <td class="field-label">Email Address:</td>
                        <td class="field-value">{{ $staff->email }}</td>
                    </tr>
                </table>
            </td>
            <td style="width: 25%; text-align: right; padding: 0;">
                <div style="float: right;">
                    @if($staff->photo && file_exists(public_path('storage/' . $staff->photo)))
                        <img src="{{ public_path('storage/' . $staff->photo) }}" style="width: 120px; height: 140px; border: 1px solid #d1d5db; object-fit: cover;">
                    @else
                        <div class="photo-box" style="line-height: 140px;">Affix Photo Here</div>
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="section-title">1. Personal & Contact details</div>
    <table class="grid-table">
        <tr>
            <td>
                <span class="grid-label">Full Name:</span>
                <span class="grid-value" style="font-weight: bold;">{{ $staff->full_name }}</span>
            </td>
            <td>
                <span class="grid-label">Gender:</span>
                <span class="grid-value">{{ ucfirst($staff->gender) }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">Date of Birth:</span>
                <span class="grid-value">{{ $staff->date_of_birth ? $staff->date_of_birth->format('d M Y') : 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">Age:</span>
                <span class="grid-value">{{ $staff->detailed_age }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">Mobile Number:</span>
                <span class="grid-value">{{ $staff->phone }}</span>
            </td>
            <td>
                <span class="grid-label">Alternate Mobile:</span>
                <span class="grid-value">{{ $staff->additional_fields['alternate_phone'] ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">Religion:</span>
                <span class="grid-value">{{ $staff->additional_fields['religion'] ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">Blood Group:</span>
                <span class="grid-value">{{ $staff->blood_group ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">Marital Status:</span>
                <span class="grid-value">{{ ucfirst($staff->additional_fields['marital_status'] ?? 'N/A') }}</span>
            </td>
            <td>
                <span class="grid-label">Aadhar Number:</span>
                <span class="grid-value">{{ $staff->additional_fields['aadhar_number'] ?? 'N/A' }}</span>
            </td>
        </tr>
    </table>

    <div class="section-title">2. Professional Details</div>
    <table class="grid-table">
        <tr>
            <td>
                <span class="grid-label">Qualification:</span>
                <span class="grid-value">{{ $staff->qualification ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">Total Experience:</span>
                <span class="grid-value">{{ $staff->experience_years ? $staff->experience_years . ' Years' : 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">Basic Salary:</span>
                <span class="grid-value">Rs. {{ number_format((float)$staff->basic_salary, 2) }}</span>
            </td>
            <td>
                <span class="grid-label">Father / Spouse Name:</span>
                <span class="grid-value">{{ $staff->additional_fields['father_name'] ?? 'N/A' }}</span>
            </td>
        </tr>
    </table>

    <div class="section-title">3. Address details</div>
    <table>
        <tr>
            <td class="field-label">Current Address:</td>
            <td class="field-value">
                {{ $staff->address ?? 'N/A' }}<br>
                @if($staff->city || $staff->state || $staff->pincode)
                    {{ $staff->city ?? '' }}, {{ $staff->state ?? '' }} - {{ $staff->pincode ?? '' }}
                @endif
            </td>
        </tr>
    </table>

    <div class="section-title">4. Bank & Tax Details</div>
    <table class="grid-table">
        <tr>
            <td>
                <span class="grid-label">Bank Name:</span>
                <span class="grid-value">{{ $staff->bank_name ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">Account Number:</span>
                <span class="grid-value">{{ $staff->bank_account_number ?? 'N/A' }}</span>
            </td>
        </tr>
        <tr>
            <td>
                <span class="grid-label">IFSC Code:</span>
                <span class="grid-value">{{ $staff->ifsc_code ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="grid-label">PAN Card Number:</span>
                <span class="grid-value">{{ $staff->pan_number ?? 'N/A' }}</span>
            </td>
        </tr>
    </table>

    <div class="footer">
        <table class="signature-table">
            <tr>
                <td>
                    <div class="signature-line"></div>
                    <div>Employee Signature</div>
                </td>
                <td>
                    <div class="signature-line"></div>
                    <div>Authorized Director/Principal</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
