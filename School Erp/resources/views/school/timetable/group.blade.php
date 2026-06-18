@extends('layouts.app')

@section('page-title', 'Group-wise Timetable')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-layer-group" style="color:var(--gold);margin-right:8px;"></i>Group-wise Timetable</h1>
        <p>Global dashboard overview of scheduled classes and timetables</p>
    </div>
</div>

<div style="display:flex; flex-direction:column; gap:20px;">
    @foreach($classes as $c)
        @foreach($c->sections as $sec)
            @php
                $key = $c->id . '-' . $sec->id;
                $slots = $timetables->get($key, collect());
            @endphp
            <div class="card">
                <div class="card-hdr" style="background:var(--navy3);">
                    <h3 style="color:#fff;">
                        <i class="fas fa-school" style="color:var(--gold);margin-right:6px;"></i>
                        {{ $c->name }} - Section {{ $sec->name }} Timetable
                    </h3>
                    <a href="{{ route('school.timetable.class', ['class_id' => $c->id, 'section_id' => $sec->id]) }}" class="btn btn-gold" style="padding:4px 10px; font-size:11px;">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                </div>
                <div class="card-body" style="padding:0;">
                    <div class="table-wrap">
                        <table class="tbl">
                            <thead>
                                <tr>
                                    <th>Day</th>
                                    <th>Scheduled Periods</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                    @php
                                        $daySlots = $slots->where('day_of_week', $day)->sortBy('start_time');
                                    @endphp
                                    <tr>
                                        <td style="font-weight:700; width:120px; background:var(--page);">{{ $day }}</td>
                                        <td>
                                            <div style="display:flex; gap:10px; flex-wrap:wrap;">
                                                @forelse($daySlots as $slot)
                                                    <div style="background:var(--page); border:1px solid var(--border); padding:8px 12px; border-radius:8px; min-width:140px; border-left:3px solid var(--gold);">
                                                        <strong style="display:block; font-size:12px; color:var(--navy);">{{ $slot->subject?->name }}</strong>
                                                        <span style="display:block; font-size:10.5px; color:var(--t2);">{{ $slot->teacher?->full_name }}</span>
                                                        <small class="badge badge-blue" style="font-size:9.5px; margin-top:4px; padding:1px 5px;">{{ $slot->start_time }}</small>
                                                    </div>
                                                @empty
                                                    <span style="color:var(--t3); font-size:11.5px; font-style:italic;">No periods scheduled</span>
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
        @endforeach
    @endforeach
</div>
@endsection
