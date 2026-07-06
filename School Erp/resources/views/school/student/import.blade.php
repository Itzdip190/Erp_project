@extends('layouts.app')

@section('page-title', 'Bulk Student Import')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-import" style="color:var(--gold);margin-right:8px;"></i>Bulk Student Import Wizard</h1>
        <p>Import thousands of students using standard Excel/CSV templates in one click</p>
    </div>
</div>

<div class="grid-2">
    <!-- Import Form Card -->
    <div class="card">
        <div class="card-hdr">
            <h3>Upload Spreadsheet</h3>
        </div>
        <div class="card-body">
            <p style="color:var(--t2); font-size:13px; line-height:1.6; margin-bottom:20px;">
                Download the spreadsheet template, populate student records (including class and section mappings), and upload it here. The process will run in the background.
            </p>
            
            <form id="bulkImportFormDedicated" enctype="multipart/form-data" style="display:flex; flex-direction:column; gap:16px;">
                @csrf
                <div class="form-group">
                    <label class="form-label">Select Template File (.xlsx, .csv)</label>
                    <input type="file" name="file" id="importFile" class="form-control" required accept=".csv,.xlsx">
                </div>
                
                <button type="submit" class="btn btn-gold" style="justify-content:center; padding:12px;">
                    <i class="fas fa-cloud-upload-alt"></i> Upload & Process Import
                </button>
            </form>
            <div id="importProgressContainer" style="display:none; margin-top:20px; background:rgba(0,0,0,0.02); border:1px solid var(--border); border-radius:10px; padding:16px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:8px; font-size:12.5px; font-weight:600; color:var(--navy);">
                    <span id="progressStatusText">Initializing spreadsheet...</span>
                    <span id="progressPercentText">0%</span>
                </div>
                <div style="background:var(--page); height:12px; border-radius:6px; overflow:hidden; border:1px solid rgba(0,0,0,0.05); position:relative;">
                    <div id="importProgressBar" style="width:0%; height:100%; background:linear-gradient(90deg, var(--gold) 0%, #f59e0b 100%); transition:width 0.2s ease; border-radius:6px;"></div>
                </div>
                <div style="display:flex; justify-content:space-between; margin-top:8px; font-size:11.5px; color:var(--t2);">
                    <span id="progressDetailText">Processed: 0 / 0 rows</span>
                    <span>Success: <strong id="progressSuccessText" style="color:var(--green);">0</strong> | Failed: <strong id="progressFailedText" style="color:var(--red);">0</strong></span>
                </div>
            </div>
            <div id="importFeedbackDedicated" style="margin-top:16px; display:none; font-size:13px; padding:10px; border-radius:6px; background:var(--page);"></div>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="card">
        <div class="card-hdr">
            <h3>Instructions & Template</h3>
        </div>
        <div class="card-body">
            <h4 style="font-size:13px; font-weight:700; color:var(--navy); margin-bottom:12px;">Download Blank Template:</h4>
            <a href="{{ route('school.students.import-template') }}" class="btn btn-outline" style="margin-bottom:20px;">
                <i class="fas fa-download"></i> Download Excel Template
            </a>

            <h4 style="font-size:13px; font-weight:700; color:var(--navy); margin-bottom:8px;">Rules & Formats:</h4>
            <ul style="list-style-type:square; padding-left:16px; font-size:12.5px; color:var(--t2); display:flex; flex-direction:column; gap:8px;">
                <li><strong>first_name, last_name:</strong> Required text fields.</li>
                <li><strong>gender:</strong> Must be either <code>male</code>, <code>female</code>, or <code>other</code>.</li>
                <li><strong>date_of_birth:</strong> Valid date formatted as <code>YYYY-MM-DD</code>.</li>
                <li><strong>guardian_email:</strong> Optional. If provided, automatically triggers parent login creation.</li>
                <li><strong>class_id, section_id:</strong> Numeric database IDs for Class and Section.</li>
            </ul>
        </div>
    </div>
</div>

<div class="card" style="margin-top:24px;">
    <div class="card-hdr">
        <h3><i class="fas fa-history" style="color:var(--gold);margin-right:8px;"></i>Recent Spreadsheet Imports History</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Status</th>
                    <th>Total Rows</th>
                    <th>Success Rows</th>
                    <th>Failed Rows</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            @if($log->status === 'completed')
                                @if($log->failed_rows > 0)
                                    <span class="badge badge-warning" style="background:rgba(245,158,11,.1); color:var(--gold); border:1px solid rgba(245,158,11,.2);">Partial Success</span>
                                @else
                                    <span class="badge badge-success" style="background:rgba(16,185,129,.1); color:var(--green); border:1px solid rgba(16,185,129,.2);">Completed</span>
                                @endif
                            @elseif($log->status === 'failed')
                                <span class="badge badge-danger" style="background:rgba(239,68,68,.1); color:var(--red); border:1px solid rgba(239,68,68,.2);">Failed</span>
                            @else
                                <span class="badge badge-blue" style="background:rgba(59,130,246,.1); color:var(--blue); border:1px solid rgba(59,130,246,.2);">{{ ucfirst($log->status) }}</span>
                            @endif
                        </td>
                        <td>{{ $log->total_rows }}</td>
                        <td><span style="color:var(--green); font-weight:600;">{{ $log->success_rows }}</span></td>
                        <td><span style="color:var(--red); font-weight:600;">{{ $log->failed_rows }}</span></td>
                        <td>
                            @if(!empty($log->errors))
                                <button type="button" class="btn btn-outline btn-xs" style="padding:4px 8px; font-size:11px;" onclick="toggleErrorDetails({{ $log->id }})">
                                    <i class="fas fa-exclamation-triangle"></i> View Errors
                                </button>
                            @else
                                <span style="color:var(--t2); font-size:12px;">No errors</span>
                            @endif
                        </td>
                    </tr>
                    @if(!empty($log->errors))
                        <tr id="error-details-{{ $log->id }}" style="display:none; background:rgba(0,0,0,0.02);">
                            <td colspan="6" style="padding:15px 20px;">
                                <div style="background:rgba(239,68,68,0.03); border: 1px solid rgba(239,68,68,0.15); border-radius:8px; padding:12px;">
                                    <h4 style="font-size:13px; color:var(--red); margin-bottom:8px; font-weight:700;"><i class="fas fa-times-circle"></i> Validation & DB Errors:</h4>
                                    <div style="max-height:150px; overflow-y:auto;">
                                        <ul style="list-style:none; padding-left:0; margin:0; display:flex; flex-direction:column; gap:6px;">
                                            @foreach($log->errors as $err)
                                                <li style="font-size:12px; color:var(--t1); text-align:left;">
                                                    <strong style="color:var(--red);">Row {{ $err['row'] ?? 'N/A' }}:</strong> {{ $err['error'] ?? 'Unknown error' }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color:var(--t2); padding:30px;">
                            No spreadsheet imports found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleErrorDetails(id) {
    const el = document.getElementById('error-details-' + id);
    if (el.style.display === 'none') {
        el.style.display = 'table-row';
    } else {
        el.style.display = 'none';
    }
}

$('#bulkImportFormDedicated').on('submit', function(e) {
    e.preventDefault();
    let fd = new FormData(this);
    let fb = $('#importFeedbackDedicated');
    let progressContainer = $('#importProgressContainer');
    let progressBar = $('#importProgressBar');
    let percentText = $('#progressPercentText');
    let statusText = $('#progressStatusText');
    let detailText = $('#progressDetailText');
    let successText = $('#progressSuccessText');
    let failedText = $('#progressFailedText');
    
    fb.hide().html('');
    progressContainer.show();
    progressBar.css('width', '0%');
    percentText.text('0%');
    statusText.text('Uploading and parsing spreadsheet... Please wait.');
    detailText.text('Processed: 0 rows');
    successText.text('0');
    failedText.text('0');

    let pollingInterval = null;

    $.ajax({
        url: "{{ route('school.students.import') }}",
        type: "POST", 
        data: fd, 
        processData: false, 
        contentType: false,
        success: function(r) {
            if (r.success && r.import_log_id) {
                let logId = r.import_log_id;
                let totalRows = r.total_rows;
                
                statusText.text('File uploaded. Processing student records...');
                detailText.text('Processed: 0 / ' + totalRows + ' rows');

                // Start polling progress
                pollingInterval = setInterval(function() {
                    $.ajax({
                        url: "/school/students/import-progress/" + logId,
                        type: "GET",
                        success: function(progress) {
                            if (progress.success) {
                                let success = parseInt(progress.success_rows || 0);
                                let failed = parseInt(progress.failed_rows || 0);
                                let processed = success + failed;
                                let pct = totalRows > 0 ? Math.min(100, Math.round((processed / totalRows) * 100)) : 0;
                                
                                progressBar.css('width', pct + '%');
                                percentText.text(pct + '%');
                                successText.text(success);
                                failedText.text(failed);
                                detailText.text('Processed: ' + processed + ' / ' + totalRows + ' rows');
                                
                                if (progress.status === 'processing') {
                                    statusText.text('Importing and validating student data...');
                                } else if (progress.status === 'completed' || progress.status === 'failed') {
                                    clearInterval(pollingInterval);
                                    progressBar.css('width', '100%');
                                    percentText.text('100%');
                                    
                                    if (progress.status === 'completed' && failed === 0) {
                                        statusText.text('Import completed successfully!');
                                        fb.show().css({
                                            'background': 'rgba(16,185,129,0.08)',
                                            'border': '1px solid rgba(16,185,129,0.3)',
                                            'color': 'var(--green)'
                                        }).html('<span style="font-weight:600;"><i class="fas fa-check-circle"></i> Bulk import completed successfully. Page will refresh to update logs.</span>');
                                        
                                        setTimeout(() => { window.location.reload(); }, 2500);
                                    } else {
                                        statusText.text('Import completed with validation failures.');
                                        let msg = 'Import completed with validation failures: ' + success + ' imported, ' + failed + ' failed.';
                                        let html = '<div style="color:var(--red); font-weight:600; margin-bottom:8px;"><i class="fas fa-exclamation-triangle"></i> ' + msg + '</div>';
                                        
                                        if (progress.errors && progress.errors.length > 0) {
                                            html += '<div style="margin-top:10px; max-height:200px; overflow-y:auto; background:rgba(239,68,68,0.03); padding:10px; border:1px solid rgba(239,68,68,0.15); border-radius:6px; text-align:left;">';
                                            html += '<strong style="color:var(--red); font-size:12px; display:block; margin-bottom:6px;">Validation Errors details:</strong>';
                                            html += '<ul style="margin:0; padding-left:16px; color:var(--t1); font-size:12px; display:flex; flex-direction:column; gap:4px;">';
                                            progress.errors.forEach(err => {
                                                html += '<li><strong style="color:var(--red);">Row ' + err.row + ':</strong> ' + err.error + '</li>';
                                            });
                                            html += '</ul></div>';
                                        }
                                        
                                        fb.show().css({
                                            'background': 'rgba(239,68,68,0.08)',
                                            'border': '1px solid rgba(239,68,68,0.3)',
                                            'color': 'var(--red)'
                                        }).html(html);
                                    }
                                }
                            }
                        }
                    });
                }, 800);

                // Start processing import (runs in the background on server)
                $.ajax({
                    url: "/school/students/import-process/" + logId,
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(res) {
                        statusText.text('Import processing launched in background...');
                    },
                    error: function(xhr) {
                        clearInterval(pollingInterval);
                        let errorMsg = 'Failed to launch import process.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        }
                        statusText.text('Import failed.');
                        fb.show().css({
                            'background': 'rgba(239,68,68,0.08)',
                            'border': '1px solid rgba(239,68,68,0.3)',
                            'color': 'var(--red)'
                        }).html('<span><i class="fas fa-exclamation-circle"></i> ' + errorMsg + '</span>');
                    }
                });
            } else {
                fb.show().css({
                    'background': 'rgba(239,68,68,0.08)',
                    'border': '1px solid rgba(239,68,68,0.3)',
                    'color': 'var(--red)'
                }).html('<span><i class="fas fa-exclamation-circle"></i> ' + (r.message || 'Unknown upload error') + '</span>');
            }
        },
        error: function(xhr) {
            let errorMsg = 'Error launching worker. Check file format.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            fb.show().css({
                'background': 'rgba(239,68,68,0.08)',
                'border': '1px solid rgba(239,68,68,0.3)',
                'color': 'var(--red)'
            }).html('<span><i class="fas fa-exclamation-circle"></i> ' + errorMsg + '</span>');
        }
    });
});
</script>
@endsection
