@extends('layouts.app')

@section('page-title', 'Class Timetable')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-calendar-alt" style="color:var(--gold);margin-right:8px;"></i>Class Timetable Editor</h1>
        <p>Define weekly schedules, subject allocations and teaching periods</p>
    </div>
</div>

<!-- Selector Card -->
<div class="card">
    <div class="card-hdr">
        <h3>Select Class, Section & Day</h3>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('school.timetable.class') }}">
            <div class="grid-3" style="align-items:end;">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Class</label>
                    <select name="class_id" class="form-control" required>
                        <option value="">Select Class</option>
                        @foreach($classes as $c)
                            <option value="{{ $c->id }}" {{ $classId == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Section</label>
                    <select name="section_id" class="form-control" required>
                        <option value="">Select Section</option>
                        @foreach($sections as $s)
                            <option value="{{ $s->id }}" {{ $sectionId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Day of Week</label>
                    <select name="day_of_week" class="form-control" required>
                        @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                            <option value="{{ $day }}" {{ $dayOfWeek == $day ? 'selected' : '' }}>{{ $day }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="margin-top:14px;text-align:right;">
                <button type="submit" class="btn btn-gold"><i class="fas fa-search"></i> Load Timetable</button>
            </div>
        </form>
    </div>
</div>

@if($classId && $sectionId)
<div class="grid-2">
    <!-- Schedule List -->
    <div class="card">
        <div class="card-hdr">
            <h3><i class="fas fa-list" style="color:var(--gold);margin-right:6px;"></i>Schedules for {{ $dayOfWeek }}</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Timing</th>
                            <th>Subject</th>
                            <th>Teacher</th>
                            <th>Room No</th>
                            <th style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($periods as $period)
                            <tr>
                                <td><span class="badge badge-blue">{{ $period->start_time }} - {{ $period->end_time }}</span></td>
                                <td style="font-weight:700;">{{ $period->subject?->name }}</td>
                                <td>{{ $period->teacher?->full_name }}</td>
                                <td><span class="badge badge-warning">{{ $period->room_number ?? 'N/A' }}</span></td>
                                <td style="text-align:right;">
                                    <form action="{{ route('school.timetable.class.destroy', $period->id) }}" method="POST" onsubmit="return confirm('Delete this slot?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding:5px 9px;font-size:11px;"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center;padding:40px;color:var(--t3);">
                                    <i class="fas fa-calendar-xmark" style="font-size:32px;display:block;margin-bottom:10px;color:var(--border);"></i>
                                    No schedule set up for this day. Add periods using the form.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Add Period Form -->
    <div class="card">
        <div class="card-hdr">
            <h3><i class="fas fa-plus" style="color:var(--gold);margin-right:6px;"></i>Add Schedule Period</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.timetable.class.store') }}">
                @csrf
                <input type="hidden" name="class_id" value="{{ $classId }}">
                <input type="hidden" name="section_id" value="{{ $sectionId }}">
                <input type="hidden" name="day_of_week" value="{{ $dayOfWeek }}">

                <div class="grid-2">
                    <div class="form-group">
                        <label class="form-label">Start Time</label>
                        <input type="text" name="start_time" class="form-control" placeholder="e.g. 09:00 AM" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">End Time</label>
                        <input type="text" name="end_time" class="form-control" placeholder="e.g. 10:00 AM" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Subject</label>
                    <select name="subject_id" class="form-control" required>
                        <option value="">Select Subject</option>
                        @foreach($subjects as $sub)
                            <option value="{{ $sub->id }}">{{ $sub->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Teacher</label>
                    <select name="staff_id" class="form-control" required>
                        <option value="">Select Teacher</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}">{{ $t->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Room Number</label>
                    <input type="text" name="room_number" class="form-control" placeholder="e.g. Room 102">
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%;justify-content:center;margin-top:10px;">
                    <i class="fas fa-save"></i> Save Period Slot
                </button>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
