@extends('layouts.app')

@section('page-title', 'Siblings List')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-people-roof" style="color:var(--gold);margin-right:8px;"></i>Siblings Group Registry</h1>
        <p>Registry of students belonging to the same family grouped by guardian details</p>
    </div>
</div>

<div style="display:flex; flex-direction:column; gap:20px;">
    @forelse($groupedSiblings as $phone => $siblings)
        @php
            $guardianName = $siblings->first()?->guardian_name;
            $guardianEmail = $siblings->first()?->guardian_email;
        @endphp
        <div class="card">
            <div class="card-hdr" style="background:var(--navy3);">
                <h3 style="color:#fff;">
                    <i class="fas fa-user-shield" style="color:var(--gold);margin-right:8px;"></i>
                    Guardian: {{ $guardianName }} ({{ $phone }})
                </h3>
                <span class="badge badge-blue">{{ $siblings->count() }} Children enrolled</span>
            </div>
            <div class="card-body" style="padding:0;">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Admission ID</th>
                            <th>Student Name</th>
                            <th>Class & Section</th>
                            <th>Roll Number</th>
                            <th>Relationship</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($siblings as $child)
                            <tr>
                                <td><span class="badge badge-blue">{{ $child->admission_number }}</span></td>
                                <td style="font-weight:700;">{{ $child->full_name }}</td>
                                <td>{{ $child->class?->name }} - Sec {{ $child->section?->name }}</td>
                                <td>{{ $child->roll_number ?? 'N/A' }}</td>
                                <td><span class="badge badge-warning">{{ ucfirst($child->guardian_relationship) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body" style="text-align:center; padding:50px; color:var(--t3);">
                <i class="fas fa-folder-open" style="font-size:32px; display:block; margin-bottom:10px; color:var(--border);"></i>
                No sibling relations identified in the current database logs.
            </div>
        </div>
    @endforelse
</div>
@endsection
