@extends('layouts.app')

@section('page-title', 'Teacher Timetable')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-chalkboard-teacher" style="color:var(--gold);margin-right:8px;"></i>Teacher Timetable</h1>
        <p>View daily scheduled teaching slots and subjects by teacher</p>
    </div>
</div>

<!-- Selector Card -->
<div class="card">
    <div class="card-hdr">
        <h3>Select Teacher</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.timetable.teacher') }}">
            <div class="grid-3" style="align-items:end;">
                <div class="form-group" style="margin-bottom:0; grid-column: span 2;">
                    <label class="form-label">Teacher</label>
                    <select name="teacher_id" class="form-control" required>
                        <option value="">Select Teacher</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ $teacherId == $t->id ? 'selected' : '' }}>
                                {{ $t->full_name }} ({{ $t->designation?->name ?? 'Teacher' }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <button type="submit" class="btn btn-gold" style="width:100%;"><i class="fas fa-search"></i> Load Schedule</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if($teacherId)
<div class="card">
    <div class="card-hdr">
        <h3>Weekly Work Schedule</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th style="width:150px;">Day</th>
                        <th>Teaching Schedule Slots</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                        @php
                            $daySlots = $timetableData->get($day, collect())->sortBy('start_time');
                        @endphp
                        <tr>
                            <td style="font-weight:700; background:var(--page);">{{ $day }}</td>
                            <td>
                                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                                    @forelse($daySlots as $slot)
                                        <div style="background:var(--page); border:1px solid var(--border); padding:8px 12px; border-radius:8px; min-width:160px; border-left:3px solid var(--navy);">
                                            <strong style="display:block; font-size:12px; color:var(--gold);">{{ $slot->subject?->name }}</strong>
                                            <span style="display:block; font-size:10.5px; color:var(--t1); font-weight:600;">
                                                {{ $slot->class?->name }} - Sec {{ $slot->section?->name }}
                                            </span>
                                            <small class="badge badge-purple" style="font-size:9.5px; margin-top:4px; padding:1px 5px;">
                                                {{ $slot->start_time }} - {{ $slot->end_time }}
                                            </small>
                                            @if($slot->room_number)
                                                <small style="display:block; font-size:10px; color:var(--t3); margin-top:3px;">
                                                    <i class="fas fa-location-dot"></i> Room: {{ $slot->room_number }}
                                                </small>
                                            @endif
                                        </div>
                                    @empty
                                        <span style="color:var(--t3); font-size:11.5px; font-style:italic;">No classes scheduled</span>
                                    @endforelse
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif
@endsection
