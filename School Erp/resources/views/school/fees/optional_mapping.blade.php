@extends('layouts.app')

@section('page-title', 'Optional Fee Mapping')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-route" style="color:var(--gold);margin-right:8px;"></i>Optional Fee Mapping</h1>
        <p>Map additional optional fees (e.g. Transport Bus Fees, Extra-Curricular Sports, Hostel) to individual students</p>
    </div>
</div>

<div class="grid-3">
    <!-- Mapping Form Card -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Map Optional Fee</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.fees.optional-mapping') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Student</label>
                    <select name="student_id" class="form-control" required>
                        <option value="">Select Student</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->admission_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Optional Fee Category</label>
                    <select name="fee_category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-link"></i> Map Optional Fee
                </button>
            </form>
        </div>
    </div>

    <!-- Active Mappings List -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Active Mapped Optional Fees</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Student Details</th>
                            <th>Class & Section</th>
                            <th>Mapped Optional Fee</th>
                            <th>Mapping Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($mappings as $map)
                        <tr>
                            <td>
                                <strong style="color:var(--navy);">{{ $map->student->full_name }}</strong>
                                <small style="display:block; color:var(--t3);">{{ $map->student->admission_id }}</small>
                            </td>
                            <td>{{ optional($map->student->class)->name ?? 'N/A' }} - {{ optional($map->student->section)->name ?? 'N/A' }}</td>
                            <td><span class="badge badge-purple">{{ $map->category->name }}</span></td>
                            <td>{{ $map->created_at->format('Y-m-d') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:20px; color:var(--t3);">No optional fee mappings created yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
