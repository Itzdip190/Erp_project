@extends('layouts.app')

@section('page-title', 'Bulk Admission Number Change')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-barcode" style="color:var(--gold);margin-right:8px;"></i>Bulk Admission ID Alignment</h1>
        <p>Edit or regenerate students' Admission IDs globally or prefix codes in bulk</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Bulk Admission ID Management Sheet</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <form method="POST" action="{{ route('school.student-mgmt.bulk-admission-number') }}">
            @csrf
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Current Admission ID</th>
                        <th>Student Name</th>
                        <th>Class & Section</th>
                        <th>New Admission ID (Editable)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $st)
                        <tr>
                            <td><span class="badge badge-blue">{{ $st->admission_number }}</span></td>
                            <td style="font-weight:700;">{{ $st->full_name }}</td>
                            <td>{{ $st->class?->name }} - Sec {{ $st->section?->name }}</td>
                            <td>
                                <input type="text" name="new_admission[{{ $st->id }}]" value="{{ $st->admission_number }}" class="form-control" style="max-width:260px; font-weight:700;">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:30px; color:var(--t3);">No student records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($students->isNotEmpty())
                <div style="padding:20px; text-align:right; border-top:1px solid var(--border);">
                    <button type="submit" class="btn btn-gold"><i class="fas fa-sync-alt"></i> Update Admission IDs</button>
                </div>
            @endif
        </form>
    </div>
</div>
@endsection
