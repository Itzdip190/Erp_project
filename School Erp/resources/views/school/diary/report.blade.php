@extends('layouts.app')

@section('page-title', 'Daily Diary Report')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-file-alt" style="color:var(--gold);margin-right:8px;"></i>Daily Diary Report</h1>
        <p>Audit homework, study assignments, and class circulars logged on specific dates</p>
    </div>
</div>

<div class="card">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Search Digital Diary Logs</h3>
        <form method="GET" action="{{ route('school.diary.report') }}" style="display:flex; gap:8px;">
            <select name="class_id" class="form-control" style="width:auto;">
                <option value="">All Classes</option>
                @foreach($classes as $c)
                    <option value="{{ $c->id }}" {{ $selectedClassId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
            <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" style="width:auto;">
            <button type="submit" class="btn btn-navy"><i class="fas fa-search"></i> Search</button>
        </form>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Class & Section</th>
                        <th>Diary Entry Particulars</th>
                        <th>Teacher Assigned</th>
                        <th>Diary Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($diaries as $diary)
                    <tr>
                        <td><strong>{{ $diary->class->name }}</strong> - {{ $diary->section->name }}</td>
                        <td>
                            <div style="font-weight:700; color:var(--navy);">{{ $diary->title }}</div>
                            <p style="color:var(--t2); font-size:12.5px; margin-top:4px;">{{ $diary->content }}</p>
                        </td>
                        <td>{{ optional($diary->teacher)->full_name ?? 'School Administration' }}</td>
                        <td><span style="font-family:monospace;">{{ $diary->diary_date }}</span></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="text-align:center; padding:24px; color:var(--t2);">
                            No diary logs recorded for selected query criteria.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
