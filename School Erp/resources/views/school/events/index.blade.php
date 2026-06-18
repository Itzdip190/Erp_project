@extends('layouts.app')

@section('page-title', 'Event & Holiday Management')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-calendar-alt" style="color:var(--gold);margin-right:8px;"></i>Event & Holiday Management</h1>
        <p>Schedule academic events, register national/state holidays, and post school calendars</p>
    </div>
</div>

<div class="grid-3">
    <!-- Event Creator Form -->
    <div class="card" style="grid-column: span 1;">
        <div class="card-hdr">
            <h3>Add Event / Holiday</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.events.index') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Event Title</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Independence Day, Annual Meet" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" style="height:80px;" placeholder="Brief details about the event..."></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="form-group" style="display:flex; align-items:center; gap:8px;">
                    <input type="checkbox" name="is_holiday" value="1" id="isHolidayCheck" style="width:16px; height:16px;">
                    <label for="isHolidayCheck" class="form-label" style="margin-bottom:0; cursor:pointer;">Official School Holiday (School Closed)</label>
                </div>

                <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">
                    <i class="fas fa-calendar-plus"></i> Save Event
                </button>
            </form>
        </div>
    </div>

    <!-- Active Events Directory -->
    <div class="card" style="grid-column: span 2;">
        <div class="card-hdr">
            <h3>Scheduled Events & Holidays</h3>
        </div>
        <div class="card-body" style="padding:0;">
            <div class="table-wrap">
                <table class="tbl">
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Description</th>
                            <th>Dates Duration</th>
                            <th>Type</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($events as $event)
                        <tr>
                            <td><strong style="color:var(--navy);">{{ $event->title }}</strong></td>
                            <td><span style="color:var(--t2); font-size:12.5px;">{{ $event->description ?? 'No description provided.' }}</span></td>
                            <td>
                                <span style="font-family:monospace; font-size:11.5px;">
                                    {{ $event->start_date }} 
                                    @if($event->start_date !== $event->end_date)
                                        to {{ $event->end_date }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                @if($event->is_holiday)
                                    <span class="badge badge-danger">Holiday (Closed)</span>
                                @else
                                    <span class="badge badge-success">Active Event</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" style="text-align:center; padding:20px; color:var(--t3);">No scheduled events or holidays.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
