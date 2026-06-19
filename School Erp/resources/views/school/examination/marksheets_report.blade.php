@extends('layouts.app')

@section('page-title', 'Marksheets & ORSS Report')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-invoice" style="color:var(--gold);margin-right:8px;"></i>Marksheets & ORSS Class Reports</h1>
        <p>Compile comparative grade ledger sheets for whole classes and sections</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr">
        <h3>Filter by Class</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.examination.marksheets-report') }}" style="display:flex; gap:12px; align-items:end;">
            <div class="form-group" style="margin:0; flex:1;">
                <label class="form-label">Class</label>
                <select name="class_id" class="form-control" required>
                    <option value="">-- Select Class --</option>
                    @foreach($classes as $c)
                        <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> Load Marksheet</button>
        </form>
    </div>
</div>

@if($classId && isset($reportData))
<div class="card">
    <div class="card-hdr">
        <h3>Class Marks Ledger Spreadsheet</h3>
    </div>
    <div class="card-body">
        <div class="table-wrap">
            <table class="tbl" style="border: 1px solid var(--border);">
                <thead>
                    <tr>
                        <th>Roll No.</th>
                        <th>Student Name</th>
                        @foreach($subjects as $sub)
                            <th>{{ $sub->name }}</th>
                        @endforeach
                        <th>Total Obtained</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reportData as $row)
                        <tr>
                            <td>{{ $row['student']->roll_number ?? '—' }}</td>
                            <td><strong>{{ $row['student']->full_name }}</strong></td>
                            @php $totalObtained = 0; @endphp
                            @foreach($subjects as $sub)
                                @php $scoreObj = $row['marks']->get($sub->id); @endphp
                                <td>
                                    @if($scoreObj)
                                        {{ $scoreObj->marks_obtained }}
                                        @php $totalObtained += $scoreObj->marks_obtained; @endphp
                                    @else
                                        <span style="color:var(--t3);">—</span>
                                    @endif
                                </td>
                            @endforeach
                            <td><strong style="color:var(--navy);">{{ $totalObtained }}</strong></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $subjects->count() + 3 }}" style="text-align:center; padding:30px; color:var(--t3);">No marks sheets compiled yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
