@extends('layouts.app')

@section('page-title', 'Bus Attendance')

@section('content')
<div class="page-hdr">
    <div class="page-hdr-left">
        <h1><i class="fas fa-calendar-check" style="color:var(--gold);margin-right:8px;"></i>Bus Daily Boarding Attendance</h1>
        <p>Record daily pickup and drop boarding records for transport active students</p>
    </div>
</div>

<div class="card" style="margin-bottom: 24px;">
    <div class="card-body" style="padding: 20px;">
        <form method="GET" action="{{ route('school.transport.bus-attendance') }}" id="filterForm" style="display:flex; flex-wrap:wrap; gap:16px; align-items:flex-end;">
            <div class="form-group" style="margin:0; flex:1; min-width:180px;">
                <label class="form-label" style="margin-bottom:6px;">Select Date</label>
                <input type="date" name="date" class="form-control" value="{{ $selectedDate }}" onchange="this.form.submit()">
            </div>
            
            <div class="form-group" style="margin:0; flex:1; min-width:180px;">
                <label class="form-label" style="margin-bottom:6px;">Trip Type</label>
                <select name="trip_type" class="form-control" onchange="this.form.submit()">
                    <option value="pickup" {{ $selectedTripType === 'pickup' ? 'selected' : '' }}>Pick Up (Morning)</option>
                    <option value="drop" {{ $selectedTripType === 'drop' ? 'selected' : '' }}>Drop (Afternoon)</option>
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-hdr" style="display:flex; justify-content:space-between; align-items:center;">
        <h3>Boarding Log — {{ date('d M Y', strtotime($selectedDate)) }} ({{ ucfirst($selectedTripType) }})</h3>
        @if(count($students) > 0)
            <button type="button" class="btn btn-outline" style="padding:6px 12px; font-size:12px; font-weight:700;" onclick="toggleAllAttendance()">
                Select All Present
            </button>
        @endif
    </div>
    <div class="card-body" style="padding: 0;">
        <form method="POST" action="{{ route('school.transport.bus-attendance', ['date' => $selectedDate, 'trip_type' => $selectedTripType]) }}">
            @csrf
            <div class="table-responsive">
                <table class="fee-table" style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr>
                            <th style="padding:12px 18px; text-align:left; width:50px;">Status</th>
                            <th style="padding:12px 18px; text-align:left;">Student</th>
                            <th style="padding:12px 18px; text-align:left;">Class/Sec</th>
                            <th style="padding:12px 18px; text-align:left;">Route</th>
                            <th style="padding:12px 18px; text-align:left;">Stop</th>
                            <th style="padding:12px 18px; text-align:center;">Vehicle Assigned</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $stud)
                            @php
                                $status = $savedRecords[$stud->id] ?? 'absent';
                            @endphp
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:14px 18px; text-align:center;">
                                    <label class="switch-label">
                                        <input type="checkbox" name="attendance[{{ $stud->id }}]" value="present" class="att-checkbox" {{ $status === 'present' ? 'checked' : '' }}>
                                        <span class="slider round"></span>
                                    </label>
                                </td>
                                <td style="padding:14px 18px;">
                                    <div style="font-weight:700; color:#1e293b;">{{ $stud->full_name }}</div>
                                    <small style="color:#64748b;">Adm: {{ $stud->admission_number }}</small>
                                </td>
                                <td style="padding:14px 18px; font-weight:600; color:#475569;">{{ $stud->class?->name }} - {{ $stud->section?->name }}</td>
                                <td style="padding:14px 18px; color:#334155; font-weight:500;">{{ $stud->transport_route }}</td>
                                <td style="padding:14px 18px; color:#475569;">{{ $stud->transport_stop ?: '—' }}</td>
                                <td style="padding:14px 18px; text-align:center; font-weight:700; color:#2563eb;">
                                    {{ $selectedTripType === 'pickup' ? ($stud->transport_vehicle_code ?: '—') : ($stud->transport_drop_vehicle_code ?: '—') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="padding:40px; text-align:center; color:#64748b;">
                                    <i class="fas fa-bus" style="font-size:28px; color:#cbd5e1; margin-bottom:12px; display:block;"></i>
                                    No students are currently mapped to transport routes.<br>
                                    <a href="{{ route('school.transport.student-mapping') }}" class="btn btn-gold" style="margin-top:12px; display:inline-flex;">
                                        Map Students to Routes
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if(count($students) > 0)
                <div style="padding: 20px; border-top:1px solid #e2e8f0; display:flex; justify-content:flex-end;">
                    <button type="submit" class="btn btn-gold" style="padding: 10px 24px;">
                        <i class="fa fa-save"></i> Save Attendance Log
                    </button>
                </div>
            @endif
        </form>
    </div>
</div>

<style>
    /* iOS Toggle Switch Styles */
    .switch-label {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 24px;
    }
    .switch-label input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 24px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px; width: 18px;
        left: 3px; bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #10b981; /* green */
    }
    input:checked + .slider:before {
        transform: translateX(22px);
    }
</style>

<script>
    let allSelected = false;
    function toggleAllAttendance() {
        allSelected = !allSelected;
        const checkboxes = document.querySelectorAll('.att-checkbox');
        checkboxes.forEach(chk => {
            chk.checked = allSelected;
        });
        const btn = document.querySelector('button[onclick="toggleAllAttendance()"]');
        btn.innerText = allSelected ? 'Deselect All' : 'Select All Present';
    }
</script>
@endsection
