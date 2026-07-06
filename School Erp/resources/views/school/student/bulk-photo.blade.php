@extends('layouts.app')

@section('page-title', 'Bulk Photo/Document Upload')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-camera" style="color:var(--gold);margin-right:8px;"></i>Bulk Photo & Document Upload</h1>
        <p>Upload files in bulk and match them with student records using Admission IDs</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>File Upload Area</h3>
    </div>
    <div class="card-body">
        <form id="bulkPhotoForm" method="POST" action="{{ route('school.student-mgmt.bulk-photo.post') }}" enctype="multipart/form-data" class="alert alert-warning" style="display:flex; flex-direction:column; align-items:center; padding:30px; text-align:center; border:2px dashed var(--gold); background:rgba(245,158,11,.03); cursor:pointer; transition: all 0.3s ease;">
            @csrf
            <i class="fas fa-cloud-arrow-up" style="font-size:3rem; color:var(--gold); margin-bottom:12px;"></i>
            <h4 style="font-size:14px; font-weight:700; color:var(--navy); margin-bottom:6px;">Drag & Drop files here or click to browse</h4>
            <p style="font-size:12px; color:var(--t2);">Select multiple JPG/PNG image files named exactly as the Student's Admission ID (e.g. <code>YAS_2026_00001.jpg</code> or <code>YIS_2026_00001.png</code>)</p>
            <input type="file" multiple name="photos[]" style="display:none;" id="bulkPhotoInput" accept="image/*">
        </form>

        <div id="uploadProgressWrapper" style="display:none; margin-top:16px;">
            <div style="display:flex; justify-content:space-between; margin-bottom:6px; font-size:12px; font-weight:600; color:var(--navy);">
                <span id="uploadProgressStatus">Uploading files...</span>
                <span id="uploadProgressPercent">0%</span>
            </div>
            <div style="background:rgba(0,0,0,0.05); border-radius:10px; height:8px; overflow:hidden;">
                <div id="uploadProgressBar" style="background:var(--gold); width:0%; height:100%; transition:width 0.2s ease;"></div>
            </div>
        </div>
        <div id="uploadFeedback" style="display:none; margin-top:16px; padding:10px; border-radius:6px; font-size:13px; font-weight:600;"></div>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Match History & Status</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Uploaded Filename</th>
                    <th>Matched Student ID / Name</th>
                    <th>Status</th>
                    <th>Time Logged</th>
                </tr>
            </thead>
            <tbody id="matchHistoryBody">
                @if(session('matches'))
                    @foreach(session('matches') as $match)
                        <tr>
                            <td><code>{{ $match['filename'] }}</code></td>
                            <td>
                                @if($match['status'] === 'success')
                                    {{ $match['student_name'] }} <span class="badge badge-blue" style="background:rgba(59,130,246,.1); color:var(--blue); border:1px solid rgba(59,130,246,.2);">{{ $match['admission_number'] }}</span>
                                @else
                                    <span style="color:var(--red);">No match found</span>
                                @endif
                            </td>
                            <td>
                                @if($match['status'] === 'success')
                                    <span class="badge badge-success" style="background:rgba(16,185,129,.1); color:var(--green); border:1px solid rgba(16,185,129,.2);">Successfully Matched</span>
                                @else
                                    <span class="badge badge-danger" style="background:rgba(239,68,68,.1); color:var(--red); border:1px solid rgba(239,68,68,.2);">Failed to Match</span>
                                @endif
                            </td>
                            <td>Just now</td>
                        </tr>
                    @endforeach
                @else
                    <tr id="emptyMatchHistoryRow">
                        <td colspan="4" style="text-align:center; color:var(--t2); padding:30px;">
                            No photos uploaded in this session yet. Click the upload area above to select and upload photos.
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('bulkPhotoForm');
    const input = document.getElementById('bulkPhotoInput');
    const progressWrapper = document.getElementById('uploadProgressWrapper');
    const progressBar = document.getElementById('uploadProgressBar');
    const progressPercent = document.getElementById('uploadProgressPercent');
    const progressStatus = document.getElementById('uploadProgressStatus');
    const feedback = document.getElementById('uploadFeedback');
    const historyBody = document.getElementById('matchHistoryBody');
    const emptyRow = document.getElementById('emptyMatchHistoryRow');
    
    // Browse trigger
    form.addEventListener('click', function(e) {
        if (e.target !== input && !form.classList.contains('uploading')) {
            input.click();
        }
    });
    
    // File input select
    input.addEventListener('change', function() {
        if (input.files.length > 0) {
            uploadFiles(input.files);
        }
    });

    // Drag and Drop Visual Feedback Handlers
    ['dragenter', 'dragover'].forEach(eventName => {
        form.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!form.classList.contains('uploading')) {
                form.style.borderColor = 'var(--green)';
                form.style.background = 'rgba(16,185,129,0.05)';
            }
        }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        form.addEventListener(eventName, function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (!form.classList.contains('uploading')) {
                form.style.borderColor = 'var(--gold)';
                form.style.background = 'rgba(245,158,11,0.03)';
            }
        }, false);
    });

    // File Drop Handler
    form.addEventListener('drop', function(e) {
        if (form.classList.contains('uploading')) return;
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            uploadFiles(files);
        }
    }, false);

    // AJAX Upload logic with progress indicators
    function uploadFiles(files) {
        form.classList.add('uploading');
        progressWrapper.style.display = 'block';
        feedback.style.display = 'none';
        
        let fd = new FormData();
        for (let i = 0; i < files.length; i++) {
            fd.append('photos[]', files[i]);
        }
        fd.append('_token', "{{ csrf_token() }}");

        $.ajax({
            url: "{{ route('school.student-mgmt.bulk-photo.post') }}",
            type: "POST",
            data: fd,
            processData: false,
            contentType: false,
            xhr: function() {
                const xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        const percentComplete = Math.round((evt.loaded / evt.total) * 100);
                        progressBar.style.width = percentComplete + '%';
                        progressPercent.textContent = percentComplete + '%';
                        if (percentComplete === 100) {
                            progressStatus.textContent = 'Processing and matching photos...';
                        } else {
                            progressStatus.textContent = 'Uploading files...';
                        }
                    }
                }, false);
                return xhr;
            },
            success: function(r) {
                form.classList.remove('uploading');
                progressBar.style.width = '0%';
                progressPercent.textContent = '0%';
                progressWrapper.style.display = 'none';
                
                // Show Feedback Alert
                feedback.style.display = 'block';
                feedback.style.background = 'rgba(16,185,129,0.08)';
                feedback.style.border = '1px solid rgba(16,185,129,0.3)';
                feedback.style.color = 'var(--green)';
                feedback.textContent = r.message;
                
                // Remove empty history row if present
                if (emptyRow) {
                    emptyRow.remove();
                }

                // Render dynamic results
                if (r.matches && r.matches.length > 0) {
                    r.matches.forEach(match => {
                        const tr = document.createElement('tr');
                        
                        // Filename column
                        const tdFile = document.createElement('td');
                        const code = document.createElement('code');
                        code.textContent = match.filename;
                        tdFile.appendChild(code);
                        tr.appendChild(tdFile);

                        // Matched Name/ID column
                        const tdName = document.createElement('td');
                        if (match.status === 'success') {
                            tdName.textContent = match.student_name + ' ';
                            const badge = document.createElement('span');
                            badge.className = 'badge badge-blue';
                            badge.style.background = 'rgba(59,130,246,0.1)';
                            badge.style.color = 'var(--blue)';
                            badge.style.border = '1px solid rgba(59,130,246,0.2)';
                            badge.textContent = match.admission_number;
                            tdName.appendChild(badge);
                        } else {
                            const noMatch = document.createElement('span');
                            noMatch.style.color = 'var(--red)';
                            noMatch.textContent = 'No match found';
                            tdName.appendChild(noMatch);
                        }
                        tr.appendChild(tdName);

                        // Status Badge column
                        const tdStatus = document.createElement('td');
                        const statusBadge = document.createElement('span');
                        statusBadge.className = match.status === 'success' ? 'badge badge-success' : 'badge badge-danger';
                        if (match.status === 'success') {
                            statusBadge.style.background = 'rgba(16,185,129,0.1)';
                            statusBadge.style.color = 'var(--green)';
                            statusBadge.style.border = '1px solid rgba(16,185,129,0.2)';
                            statusBadge.textContent = 'Successfully Matched';
                        } else {
                            statusBadge.style.background = 'rgba(239,68,68,0.1)';
                            statusBadge.style.color = 'var(--red)';
                            statusBadge.style.border = '1px solid rgba(239,68,68,0.2)';
                            statusBadge.textContent = 'Failed to Match';
                        }
                        tdStatus.appendChild(statusBadge);
                        tr.appendChild(tdStatus);

                        // Time logged column
                        const tdTime = document.createElement('td');
                        tdTime.textContent = 'Just now';
                        tr.appendChild(tdTime);

                        // Prepend to top of table
                        historyBody.insertBefore(tr, historyBody.firstChild);
                    });
                }
            },
            error: function(xhr) {
                form.classList.remove('uploading');
                progressBar.style.width = '0%';
                progressPercent.textContent = '0%';
                progressWrapper.style.display = 'none';
                
                feedback.style.display = 'block';
                feedback.style.background = 'rgba(239,68,68,0.08)';
                feedback.style.border = '1px solid rgba(239,68,68,0.3)';
                feedback.style.color = 'var(--red)';
                
                let errorMsg = 'An error occurred during file upload.';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMsg = Object.values(xhr.responseJSON.errors).flat().join(', ');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                feedback.textContent = errorMsg;
            }
        });
    }
});
</script>
@endsection
