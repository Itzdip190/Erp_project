@extends('layouts.app')

@section('page-title', 'Certificates Report')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-chart-pie" style="color:var(--gold);margin-right:8px;"></i>Certificates Reports Audit</h1>
        <p>Analyze issued certificate logs, audit template usage, and review document tracking numbers</p>
    </div>
</div>

<!-- Stats row -->
<div class="grid-2" style="margin-bottom:20px;">
    <div class="card" style="margin-bottom:0; background:linear-gradient(135deg, var(--navy), #1e293b); color:#fff;">
        <div class="card-body" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div style="font-size:12px; font-weight:700; text-transform:uppercase; opacity:0.75; margin-bottom:4px;">Total Certificates Issued</div>
                <div style="font-size:28px; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif;">{{ $issuedCount }}</div>
            </div>
            <i class="fas fa-stamp" style="font-size:36px; opacity:0.35;"></i>
        </div>
    </div>
    <div class="card" style="margin-bottom:0; background:linear-gradient(135deg, #0f766e, #0d9488); color:#fff;">
        <div class="card-body" style="display:flex; justify-content:space-between; align-items:center;">
            <div>
                <div style="font-size:12px; font-weight:700; text-transform:uppercase; opacity:0.75; margin-bottom:4px;">Available Templates</div>
                <div style="font-size:28px; font-weight:800; font-family:'Plus Jakarta Sans',sans-serif;">{{ $templatesCount }}</div>
            </div>
            <i class="fas fa-file-invoice" style="font-size:36px; opacity:0.35;"></i>
        </div>
    </div>
</div>

<div class="grid-3">
    <!-- Split by Type -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Certificate Split by Type</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Certificate Type</th>
                        <th>Issued Count</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($byType as $row)
                    <tr>
                        <td><strong style="color:var(--navy); text-transform:uppercase;">{{ $row->type }}</strong></td>
                        <td><strong>{{ $row->count }}</strong></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="2" style="text-align:center; padding:15px; color:var(--t3);">No certificate records.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Recent Logs Audit -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Recent Issued Audits</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Certificate Number</th>
                        <th>Template Used</th>
                        <th>Issue Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($certificates as $cert)
                    <tr>
                        <td>
                            <strong style="color:var(--navy);">{{ $cert->student->full_name }}</strong>
                            <small style="display:block; color:var(--t3);">{{ $cert->student->admission_id }}</small>
                        </td>
                        <td><strong style="color:var(--navy); font-family:monospace;">{{ $cert->certificate_number }}</strong></td>
                        <td>{{ $cert->template->name }}</td>
                        <td>{{ $cert->issue_date }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:20px; color:var(--t3);">No certificate logs found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
