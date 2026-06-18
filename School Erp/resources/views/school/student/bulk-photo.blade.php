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
        <form method="POST" action="#" class="alert alert-warning" style="display:flex; flex-direction:column; align-items:center; padding:30px; text-align:center; border:2px dashed var(--gold); background:rgba(245,158,11,.03); cursor:pointer;">
            @csrf
            <i class="fas fa-cloud-arrow-up" style="font-size:3rem; color:var(--gold); margin-bottom:12px;"></i>
            <h4 style="font-size:14px; font-weight:700; color:var(--navy); margin-bottom:6px;">Drag & Drop files here or click to browse</h4>
            <p style="font-size:12px; color:var(--t2);">Upload images in zip format or select multiple JPG/PNG files named exactly as the Student's Admission ID (e.g. <code>YIS_2026_00001.jpg</code>)</p>
            <input type="file" multiple name="files[]" style="display:none;" id="bulkPhotoInput">
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
                    <th>Matched Student ID</th>
                    <th>Status</th>
                    <th>Time Logged</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><code>YIS_2026_00001.jpg</code></td>
                    <td>Aarav Sharma <span class="badge badge-blue">YIS/2026/00001</span></td>
                    <td><span class="badge badge-success">Successfully Matched</span></td>
                    <td>Just now</td>
                </tr>
                <tr>
                    <td><code>YIS_2026_00002.jpg</code></td>
                    <td>Priya Patel <span class="badge badge-blue">YIS/2026/00002</span></td>
                    <td><span class="badge badge-success">Successfully Matched</span></td>
                    <td>Just now</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
