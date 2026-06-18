@extends('layouts.app')

@section('page-title', 'Fee Bulk Upload')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-import" style="color:var(--gold);margin-right:8px;"></i>Bulk Fee Upload Portal</h1>
        <p>Import transaction records, bank collection statements, or assign outstanding fees to students in bulk via Excel/CSV sheets</p>
    </div>
</div>

<div class="grid-2">
    <!-- CSV Upload Card -->
    <div class="card">
        <div class="card-hdr">
            <h3>Upload Fee Data File</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.fees.bulk-upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group" style="border:2px dashed var(--border); border-radius:12px; padding:40px 20px; text-align:center; cursor:pointer; background:rgba(245,158,11,0.02); transition:.2s;" onmouseover="this.style.borderColor='var(--gold)';" onmouseout="this.style.borderColor='var(--border)';">
                    <i class="fas fa-cloud-upload-alt" style="font-size:38px; color:var(--gold); margin-bottom:12px;"></i>
                    <h4 style="margin-bottom:8px; font-weight:700; color:var(--navy);">Choose CSV or Excel spreadsheet</h4>
                    <p style="color:var(--t2); font-size:12px; margin-bottom:16px;">Allowed formats: .csv, .xls, .xlsx (Max size: 2MB)</p>
                    <input type="file" name="csv_file" class="form-control" style="display:inline-block; width:auto;" required>
                </div>
                
                <div style="display:flex; justify-content:space-between; align-items:center; margin-top:20px;">
                    <a href="#" class="btn btn-outline" onclick="event.preventDefault(); showToast('Downloading template sheet...');">
                        <i class="fas fa-download"></i> Download CSV Template
                    </a>
                    <button type="submit" class="btn btn-gold">
                        <i class="fas fa-upload"></i> Process Upload
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Mapping Guidelines -->
    <div class="card">
        <div class="card-hdr">
            <h3>Spreadsheet Field Specifications</h3>
        </div>
        <div class="card-body">
            <p style="font-size:12.5px; color:var(--t2); line-height:1.6; margin-bottom:15px;">
                Please format your spreadsheet exactly as follows to prevent parsing warnings. The first row must be the header.
            </p>
            <table class="tbl" style="font-size:12px;">
                <thead>
                    <tr>
                        <th>Column Header</th>
                        <th>Type</th>
                        <th>Required</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Admission_ID</strong></td>
                        <td>String</td>
                        <td>Yes</td>
                        <td>Unique Student Admission ID (e.g., YIS/2026/00001)</td>
                    </tr>
                    <tr>
                        <td><strong>Fee_Category</strong></td>
                        <td>String</td>
                        <td>Yes</td>
                        <td>Tuition Fee, Transport Fee, Exam Fee etc.</td>
                    </tr>
                    <tr>
                        <td><strong>Amount</strong></td>
                        <td>Numeric</td>
                        <td>Yes</td>
                        <td>Deposit transaction value (e.g. 2500)</td>
                    </tr>
                    <tr>
                        <td><strong>Due_Date</strong></td>
                        <td>Date</td>
                        <td>Yes</td>
                        <td>Format: YYYY-MM-DD</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
