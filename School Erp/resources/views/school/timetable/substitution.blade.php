@extends('layouts.app')

@section('page-title', "Teacher's Substitution")

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-people-arrows" style="color:var(--gold);margin-right:8px;"></i>Teacher's Substitution Portal</h1>
        <p>Assign substitute teachers for absent staff on scheduled periods</p>
    </div>
</div>

<div class="grid-3">
    <!-- Substitution Panel -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Find Substitution</h3>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('school.timetable.substitution') }}">
                <div class="form-group">
                    <label class="form-label">Select Date</label>
                    <input type="date" name="date" class="form-control" value="{{ $date }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Absent Teacher</label>
                    <select name="absent_teacher_id" class="form-control" required>
                        <option value="">Select Teacher</option>
                        @foreach($teachers as $t)
                            <option value="{{ $t->id }}" {{ $absentTeacherId == $t->id ? 'selected' : '' }}>
                                {{ $t->full_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-search"></i> Check Timetable Slots
                </button>
            </form>
        </div>
    </div>

    <!-- Active Schedules & Available Substitutes -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Timetable Slots on {{ $dayOfWeek }} ({{ $date }})</h3>
        </div>
        <div class="card-body" style="padding:0;">
            @if(!$absentTeacherId)
                <div style="text-align:center; padding:40px; color:var(--t3);">
                    <i class="fas fa-info-circle" style="font-size:32px; display:block; margin-bottom:10px; color:var(--border);"></i>
                    Select an absent teacher and a date to assign substitutions.
                </div>
            @else
                <div class="table-wrap">
                    <table class="tbl">
                        <thead>
                            <tr>
                                <th>Class & Slot</th>
                                <th>Subject</th>
                                <th>Assign Substitute</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($periodsToSubstitute as $period)
                                <tr>
                                    <td>
                                        <div style="font-weight:700;">{{ $period->class?->name }} - Sec {{ $period->section?->name }}</div>
                                        <small class="badge badge-blue" style="font-size:10px; padding:1px 5px; margin-top:2px;">{{ $period->start_time }}</small>
                                    </td>
                                    <td>
                                        <div>{{ $period->subject?->name }}</div>
                                        @if($period->room_number)
                                            <small style="color:var(--t3);"><i class="fas fa-location-dot"></i> {{ $period->room_number }}</small>
                                        @endif
                                        @php
                                            $designated = $designatedSubstitutes[$period->id] ?? null;
                                            $freeSuggestions = $substituteSuggestions[$period->id] ?? collect();
                                            $isDesignatedFree = $designated ? ($freeSuggestions->contains('id', $designated->id)) : false;
                                        @endphp
                                        @if($designated)
                                            <div style="margin-top: 5px; font-size:11px; color:#b87000; font-weight:600; display:flex; align-items:center; gap:4px;">
                                                <i class="fas fa-user-shield"></i> Designated: {{ $designated->full_name }}
                                                @if($isDesignatedFree)
                                                    <span style="color:#2e7d32; font-weight:bold;">(Free)</span>
                                                @else
                                                    <span style="color:#d32f2f; font-weight:bold;">(Busy)</span>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <form method="POST" action="{{ route('school.timetable.substitution.store') }}" style="display:flex; gap:6px; align-items:center;">
                                            @csrf
                                            <input type="hidden" name="date" value="{{ $date }}">
                                            <input type="hidden" name="timetable_id" value="{{ $period->id }}">
                                            <input type="hidden" name="original_staff_id" value="{{ $absentTeacherId }}">
                                            
                                            <select name="substitute_staff_id" class="form-control" style="max-width:180px; padding:6px 10px; font-size:12px;" required>
                                                <option value="">Select Free Teacher</option>
                                                @if($designated && !$isDesignatedFree)
                                                    <option value="{{ $designated->id }}" style="color:#d32f2f; font-weight:600;">{{ $designated->full_name }} (Designated - Busy)</option>
                                                @endif
                                                @foreach($freeSuggestions as $sub)
                                                    <option value="{{ $sub->id }}" {{ $designated && $designated->id == $sub->id ? 'selected' : '' }}>
                                                        {{ $sub->full_name }} {{ $designated && $designated->id == $sub->id ? '(Designated)' : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="btn btn-success" style="padding:6px 12px; font-size:12px;">
                                                <i class="fas fa-plus"></i> Assign
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align:center; padding:30px; color:var(--t3);">
                                        No active classes scheduled for this teacher on {{ $dayOfWeek }}.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Assigned Substitutions List -->
<div class="card" style="margin-top:20px;">
    <div class="card-hdr">
        <h3>Active Substitution Assignments for {{ $date }}</h3>
    </div>
    <div class="card-body" style="padding:0;">
        <div class="table-wrap">
            <table class="tbl">
                <thead>
                    <tr>
                        <th>Class & Slot</th>
                        <th>Subject</th>
                        <th>Absent Teacher</th>
                        <th>Substitute Assigned</th>
                        <th>Status</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($existingSubstitutions as $sub)
                        <tr>
                            <td>
                                <div style="font-weight:700;">{{ $sub->timetable?->class?->name }} - Sec {{ $sub->timetable?->section?->name }}</div>
                                <small class="badge badge-purple" style="font-size:10px; padding:1px 5px; margin-top:2px;">{{ $sub->timetable?->start_time }}</small>
                            </td>
                            <td>{{ $sub->timetable?->subject?->name }}</td>
                            <td style="color:var(--red); font-weight:600;"><i class="fas fa-user-times"></i> {{ $sub->originalTeacher?->full_name }}</td>
                            <td style="color:var(--green); font-weight:600;"><i class="fas fa-user-check"></i> {{ $sub->substituteTeacher?->full_name }}</td>
                            <td><span class="badge badge-success">{{ ucfirst($sub->status) }}</span></td>
                            <td style="text-align:right;">
                                <form action="{{ route('school.timetable.substitution.destroy', $sub->id) }}" method="POST" onsubmit="return confirm('Cancel this substitution?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding:5px 9px; font-size:11px;" title="Cancel"><i class="fas fa-ban"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center; padding:30px; color:var(--t3);">
                                No substitutions assigned for today.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
