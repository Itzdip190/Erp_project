@extends('layouts.app')

@section('page-title', 'Download Statistics')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-chart-line" style="color:var(--gold);margin-right:8px;"></i>Download Statistics</h1>
        <p>Monitor document downloads, report card generation, certificates, and resource downloads across all user roles</p>
    </div>
    <div class="page-hdr-right">
    </div>
</div>

<!-- Counters Grid -->
<div class="grid-3" style="margin-bottom:20px;">
    <div class="card" style="margin-bottom:0; background:linear-gradient(135deg, rgba(59,130,246,0.08), rgba(59,130,246,0.02)); border-left:4px solid var(--blue);">
        <div class="card-body" style="padding:22px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <span style="font-size:11px; text-transform:uppercase; font-weight:700; color:var(--t2); letter-spacing:0.5px;">Student Downloads</span>
                    <h2 style="font-size:28px; font-weight:800; color:var(--navy); margin-top:5px;">{{ $studentDownloadsCount }}</h2>
                </div>
                <div style="width:46px; height:46px; border-radius:12px; background:rgba(59,130,246,0.15); display:flex; align-items:center; justify-content:center; color:var(--blue); font-size:20px;">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <p style="font-size:11px; color:var(--t2); margin-top:8px;"><i class="fas fa-caret-up" style="color:var(--green); margin-right:3px;"></i>12% increase since last week</p>
        </div>
    </div>

    <div class="card" style="margin-bottom:0; background:linear-gradient(135deg, rgba(139,92,246,0.08), rgba(139,92,246,0.02)); border-left:4px solid var(--purple);">
        <div class="card-body" style="padding:22px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <span style="font-size:11px; text-transform:uppercase; font-weight:700; color:var(--t2); letter-spacing:0.5px;">Staff & Teacher Downloads</span>
                    <h2 style="font-size:28px; font-weight:800; color:var(--navy); margin-top:5px;">{{ $staffDownloadsCount }}</h2>
                </div>
                <div style="width:46px; height:46px; border-radius:12px; background:rgba(139,92,246,0.15); display:flex; align-items:center; justify-content:center; color:var(--purple); font-size:20px;">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
            </div>
            <p style="font-size:11px; color:var(--t2); margin-top:8px;"><i class="fas fa-caret-up" style="color:var(--green); margin-right:3px;"></i>8% increase since last week</p>
        </div>
    </div>

    <div class="card" style="margin-bottom:0; background:linear-gradient(135deg, rgba(245,158,11,0.08), rgba(245,158,11,0.02)); border-left:4px solid var(--gold);">
        <div class="card-body" style="padding:22px;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <div>
                    <span style="font-size:11px; text-transform:uppercase; font-weight:700; color:var(--t2); letter-spacing:0.5px;">Parent Downloads</span>
                    <h2 style="font-size:28px; font-weight:800; color:var(--navy); margin-top:5px;">{{ $parentDownloadsCount }}</h2>
                </div>
                <div style="width:46px; height:46px; border-radius:12px; background:var(--gold-bg); display:flex; align-items:center; justify-content:center; color:var(--gold); font-size:20px;">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <p style="font-size:11px; color:var(--t2); margin-top:8px;"><i class="fas fa-caret-up" style="color:var(--green); margin-right:3px;"></i>15% increase since last week</p>
        </div>
    </div>
</div>

<!-- History log -->
<div class="card">
    <div class="card-hdr">
        <h3>Recent Document Downloads & Issue Logs</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Document Name</th>
                    <th>Document Type</th>
                    <th>Issued To (Student)</th>
                    <th>Class & Section</th>
                    <th>Issued Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($documents as $doc)
                    <tr>
                        <td>
                            <strong style="color:var(--navy);">{{ $doc->title }}</strong>
                            @if($doc->file_path)
                                <small style="display:block; color:var(--t3);">{{ basename($doc->file_path) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-purple">{{ ucfirst($doc->type) }}</span>
                        </td>
                        <td>
                            @if($doc->student)
                                <span style="font-weight:700; color:var(--t1);">{{ $doc->student->full_name }}</span>
                                <small style="display:block; color:var(--t3);">ID: {{ $doc->student->admission_number }}</small>
                            @else
                                <span style="color:var(--t3);">Deleted Student</span>
                            @endif
                        </td>
                        <td>
                            @if($doc->student && $doc->student->class)
                                {{ $doc->student->class->name }}
                                @if($doc->student->section)
                                    - {{ $doc->student->section->name }}
                                @endif
                            @else
                                <span style="color:var(--t3);">N/A</span>
                            @endif
                        </td>
                        <td>
                            {{ $doc->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td>
                            @if($doc->file_path)
                                <a href="#" class="btn btn-outline" style="padding:4px 8px; font-size:11px;" onclick="event.preventDefault(); showToast('Document download started.');">
                                    <i class="fas fa-download"></i> Download
                                </a>
                            @else
                                <span style="color:var(--t3);">No File Attached</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; padding:40px; color:var(--t3);">
                            <i class="fas fa-file-excel" style="font-size:32px; color:var(--t3); margin-bottom:12px; display:block;"></i>
                            No document downloads logged.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
