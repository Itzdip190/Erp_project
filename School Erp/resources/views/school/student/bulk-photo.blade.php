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
        <form id="bulkPhotoForm" method="POST" action="{{ route('school.student-mgmt.bulk-photo.post') }}" enctype="multipart/form-data" class="alert alert-warning" style="display:flex; flex-direction:column; align-items:center; padding:30px; text-align:center; border:2px dashed var(--gold); background:rgba(245,158,11,.03); cursor:pointer;">
            @csrf
            <i class="fas fa-cloud-arrow-up" style="font-size:3rem; color:var(--gold); margin-bottom:12px;"></i>
            <h4 style="font-size:14px; font-weight:700; color:var(--navy); margin-bottom:6px;">Drag & Drop files here or click to browse</h4>
            <p style="font-size:12px; color:var(--t2);">Select multiple JPG/PNG image files named exactly as the Student's Admission ID (e.g. <code>YAS_2026_00001.jpg</code> or <code>YIS_2026_00001.png</code>)</p>
            <input type="file" multiple name="photos[]" style="display:none;" id="bulkPhotoInput" accept="image/*">
        </form>
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
            <tbody>
                @if(session('matches'))
                    @foreach(session('matches') as $match)
                        <tr>
                            <td><code>{{ $match['filename'] }}</code></td>
                            <td>
                                @if($match['status'] === 'success')
                                    {{ $match['student_name'] }} <span class="badge badge-blue">{{ $match['admission_number'] }}</span>
                                @else
                                    <span style="color:var(--red);">No match found</span>
                                @endif
                            </td>
                            <td>
                                @if($match['status'] === 'success')
                                    <span class="badge badge-success">Successfully Matched</span>
                                @else
                                    <span class="badge badge-danger">Failed to Match</span>
                                @endif
                            </td>
                            <td>Just now</td>
                        </tr>
                    @endforeach
                @else
                    <tr>
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
    
    form.addEventListener('click', function(e) {
        if (e.target !== input) {
            input.click();
        }
    });
    
    input.addEventListener('change', function() {
        if (input.files.length > 0) {
            form.submit();
        }
    });
});
</script>
@endsection
