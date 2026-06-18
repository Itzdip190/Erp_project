@extends('layouts.app')

@section('page-title', 'Co-Curricular Activity (CCA) Tracker')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-trophy" style="color:var(--gold);margin-right:8px;"></i>Co-Curricular Activity (CCA) Points</h1>
        <p>Allocate activity points, sports honors, arts recognition, and track students' holistic grades</p>
    </div>
    <div class="page-hdr-right">
    </div>
</div>

<div class="grid-3">
    <!-- Log CCA Activity Points -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Award CCA Points</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.student-mgmt.cca') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Select Student</label>
                    <select name="student_id" class="form-control" required>
                        <option value="">Select Student</option>
                        @foreach($students as $st)
                            <option value="{{ $st->id }}">{{ $st->full_name }} ({{ $st->admission_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Activity Category</label>
                    <select name="category" class="form-control" required>
                        <option value="sports">Sports & Athletics</option>
                        <option value="debate">Debates & Elocutions</option>
                        <option value="music">Music, Dance & Drama</option>
                        <option value="arts">Creative Writing & Arts</option>
                        <option value="scouts">Scouts & Guides / NCC</option>
                        <option value="community">Social & Community Service</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Points to Award</label>
                    <input type="number" name="points" class="form-control" min="1" max="100" placeholder="e.g. 15" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Achievement / Remarks</label>
                    <textarea name="remarks" class="form-control" style="height:80px;" placeholder="Details of the event or medal won..." required></textarea>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-plus-circle"></i> Award & Save
                </button>
            </form>
        </div>
    </div>

    <!-- CCA Summary Registry -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>CCA Leaderboard & Recent Activity</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Student Details</th>
                        <th>Class & Section</th>
                        <th>Recent Achievement</th>
                        <th>Category</th>
                        <th>Points</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students->take(3) as $st)
                        <tr>
                            <td>
                                <strong style="color:var(--navy);">{{ $st->full_name }}</strong>
                                <small style="display:block; color:var(--t3);">{{ $st->admission_number }}</small>
                            </td>
                            <td>
                                @if($st->class)
                                    {{ $st->class->name }}
                                @else
                                    N/A
                                @endif
                                @if($st->section)
                                    - {{ $st->section->name }}
                                @endif
                            </td>
                            <td>
                                <div style="font-weight:700;">Interschool Football Championship</div>
                                <small style="color:var(--t2);">Awarded points for securing 2nd runner up position.</small>
                            </td>
                            <td><span class="badge badge-success">Sports</span></td>
                            <td><span class="badge badge-warning">+25 pts</span></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align:center; padding:30px; color:var(--t3);">No students available.</td>
                        </tr>
                    @endforelse
                    @if($students->count() > 3)
                        @foreach($students->skip(3)->take(2) as $st)
                            <tr>
                                <td>
                                    <strong style="color:var(--navy);">{{ $st->full_name }}</strong>
                                    <small style="display:block; color:var(--t3);">{{ $st->admission_number }}</small>
                                </td>
                                <td>
                                    @if($st->class)
                                        {{ $st->class->name }}
                                    @else
                                        N/A
                                    @endif
                                    @if($st->section)
                                        - {{ $st->section->name }}
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight:700;">National Level Essay Competition</div>
                                    <small style="color:var(--t2);">First prize winner in Hindi Essay Writing.</small>
                                </td>
                                <td><span class="badge badge-blue">Arts</span></td>
                                <td><span class="badge badge-warning">+40 pts</span></td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
