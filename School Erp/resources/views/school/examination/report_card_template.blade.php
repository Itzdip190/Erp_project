@extends('layouts.app')

@section('page-title', 'Report Card Template Designer')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-palette" style="color:var(--gold);margin-right:8px;"></i>Report Card Template Designer</h1>
        <p>Design header logos, grade descriptions, signature boxes, and styling layouts for printed certificates</p>
    </div>
</div>

<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-hdr">
        <h3>Report Card Layout Settings</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('school.examination.report-card-template') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">School Header Logo Alignment</label>
                <select class="form-control" name="logo_align">
                    <option value="center">Centered Header</option>
                    <option value="left">Left logo, right name</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Authorized Signatures Label</label>
                <input type="text" class="form-control" name="signature_label" value="Principal & Examination Controller" required style="width:100%;">
            </div>
            <div style="display:flex; flex-direction:column; gap:12px; margin-bottom:20px;">
                <label style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="show_rank" checked> Include class rank metrics
                </label>
                <label style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="show_attendance" checked> Include attendance summary percentage
                </label>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                <i class="fas fa-save"></i> Save Template Layout
            </button>
        </form>
    </div>
</div>
@endsection
