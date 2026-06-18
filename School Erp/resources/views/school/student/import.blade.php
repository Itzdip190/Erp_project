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
@endsection

@section('scripts')
<script>
$('#bulkImportFormDedicated').on('submit', function(e) {
    e.preventDefault();
    let fd = new FormData(this);
    let fb = $('#importFeedbackDedicated');
    fb.show().html('<span style="color:var(--gold);"><i class="fas fa-spinner fa-spin"></i> Reading spreadsheet and starting worker...</span>');
    $.ajax({
        url: "{{ route('school.students.import') }}",
        type: "POST", data: fd, processData: false, contentType: false,
        success: function(r) {
            fb.html(r.success
                ? '<span style="color:var(--green);"><i class="fas fa-check-circle"></i> Bulk import worker started in background. Refresh shortly to see imported records.</span>'
                : '<span style="color:var(--red);"><i class="fas fa-exclamation-circle"></i> ' + r.message + '</span>');
        },
        error: function() { fb.html('<span style="color:var(--red);"><i class="fas fa-exclamation-circle"></i> Error launching worker. Check file format.</span>'); }
    });
});
</script>
@endsection
