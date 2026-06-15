@extends('layouts.app')

@section('title', 'Basic Institute Info')
@section('page-title', 'Basic Institute Info')

@section('content')
<div class="card" style="max-width:800px; margin:0 auto;">
    <div class="card-hdr">
        <h3>Edit Institute Information</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.settings.institute-info.update') }}" enctype="multipart/form-name" class="form-horizontal">
            @csrf
            @method('PUT')

            <div style="text-align:center; margin-bottom:24px;">
                <div style="width:100px; height:100px; border-radius:12px; background:#f3f4f6; border:1px dashed #cbd5e1; display:flex; align-items:center; justify-content:center; margin:0 auto 12px; overflow:hidden;">
                    @if($school->logo)
                        <img src="{{ Storage::disk('public')->url($school->logo) }}" style="width:100%; height:100%; object-fit:contain;" alt="Logo">
                    @else
                        <i class="fas fa-school" style="font-size:32px; color:#94a3b8;"></i>
                    @endif
                </div>
                <div class="form-group" style="max-width:250px; margin:0 auto;">
                    <label class="form-label">Institute Logo</label>
                    <input type="file" name="logo" class="form-control" accept="image/*">
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Institute Name <span style="color:var(--red);">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $school->name) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Institute Code <span style="color:var(--red);">*</span></label>
                    <input type="text" name="code" class="form-control" value="{{ old('code', $school->code) }}" required>
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label class="form-label">Official Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $school->email) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Contact Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $school->phone) }}">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Address</label>
                <textarea name="address" class="form-control" rows="4">{{ old('address', $school->address) }}</textarea>
            </div>

            <div style="display:flex; justify-content:flex-end; gap:8px; margin-top:20px; border-top:1px solid var(--border); padding-top:16px;">
                <a href="{{ route('school.dashboard') }}" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
