@extends('layouts.app')

@section('title', 'Bulk Photo Upload')
@section('page-title', 'Bulk Photo Upload')

@section('content')
<div class="card" style="max-width:700px; margin:0 auto;">
    <div class="card-hdr">
        <h3>Bulk Upload Staff Photos</h3>
    </div>
    <div class="card-body">
        <p style="font-size:13px; color:var(--t2); margin-bottom:16px;">
            Select multiple images to upload. Each image file name MUST match the staff member's <strong>Employee ID</strong> exactly (e.g. <code>EMP001.jpg</code> or <code>EMP002.png</code>).
        </p>

        <form method="POST" action="{{ route('school.staff.bulk-photo.post') }}" enctype="multipart/form-name">
            @csrf
            <div class="form-group">
                <label class="form-label">Select Photo Files <span style="color:var(--red);">*</span></label>
                <input type="file" name="photos[]" class="form-control" multiple accept="image/*" required>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px; border-top:1px solid var(--border); padding-top:16px; margin-top:20px;">
                <a href="{{ route('school.staff.index') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Upload Photos</button>
            </div>
        </form>
    </div>
</div>
@endsection
