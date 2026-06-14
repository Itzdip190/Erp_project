@extends('layouts.app')

@section('title', 'Attendance Record')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
    <h2 style="font-family: 'Syne', sans-serif;">{{ $student->full_name }}'s Attendance Calendar</h2>
    <a href="{{ route('parent.dashboard') }}" class="btn-accent" style="background-color: #4B5563;">
        <i class="fa fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

<!-- Parameters selection filter -->
<div class="glass-card">
    <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem;">Select Calendar Month</h3>
    <form action="{{ route('parent.attendance.index') }}" method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1.5rem; align-items: end;">
        <input type="hidden" name="student_id" value="{{ $student->id }}">
        
        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Month</label>
            <select name="month" class="form-input" required>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ sprintf('%02d', $m) }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
            </select>
        </div>

        <div>
            <label class="form-label" style="display: block; margin-bottom: 0.5rem; font-size: 0.85rem; font-weight: 700; color: var(--text-muted);">Year</label>
            <select name="year" class="form-input" required>
                @for($y = date('Y') - 2; $y <= date('Y') + 1; $y++)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>

        <div>
            <button type="submit" class="btn-accent" style="width: 100%; justify-content: center;">
                <i class="fa fa-calendar-alt"></i> Refresh Calendar
            </button>
        </div>
    </form>
</div>

<!-- Attendance summary counters -->
<div class="grid-3" style="margin-top: 2rem; margin-bottom: 2rem;">
    <div class="glass-card" style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 0;">
        <div style="width: 50px; height: 50px; border-radius: 12px; background-color: rgba(16,185,129,0.1); display: flex; align-items: center; justify-content: center; color: var(--success); font-size: 1.5rem;">
            <i class="fa fa-calendar-check"></i>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ $summary['present'] }}</div>
            <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Present Days</div>
        </div>
    </div>

    <div class="glass-card" style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 0;">
        <div style="width: 50px; height: 50px; border-radius: 12px; background-color: rgba(239,68,68,0.1); display: flex; align-items: center; justify-content: center; color: var(--danger); font-size: 1.5rem;">
            <i class="fa fa-calendar-times"></i>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ $summary['absent'] }}</div>
            <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Absent Days</div>
        </div>
    </div>

    <div class="glass-card" style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 0;">
        <div style="width: 50px; height: 50px; border-radius: 12px; background-color: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1.5rem;">
            <i class="fa fa-percentage"></i>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ $summary['percentage'] }}%</div>
            <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Attendance Rate</div>
        </div>
    </div>
</div>

<!-- Calendar Grid -->
<div class="glass-card">
    <h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem;">Day-by-Day Calendar</h3>
    
    <div style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 1rem; text-align: center;">
        <!-- Day names -->
        @foreach(['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $dayName)
            <div style="font-weight: 700; color: var(--text-muted); padding: 0.5rem;">{{ $dayName }}</div>
        @endforeach

        <!-- Padding blocks for starting weekday offset -->
        @php
            $firstDayWeekday = (int) date('w', mktime(0, 0, 0, $month, 1, $year));
        @endphp
        @for($p = 0; $p < $firstDayWeekday; $p++)
            <div style="padding: 1.5rem; background-color: transparent;"></div>
        @endfor

        <!-- Calendar Days -->
        @foreach($calendar as $dayNum => $info)
            @php
                $status = $info['status'];
                $bgColor = 'rgba(255, 255, 255, 0.03)';
                $color = 'var(--text-main)';
                $borderColor = 'var(--border)';

                if ($status === 'present') {
                    $bgColor = 'rgba(16, 185, 129, 0.1)';
                    $color = 'var(--success)';
                    $borderColor = 'rgba(16, 185, 129, 0.3)';
                } elseif ($status === 'absent') {
                    $bgColor = 'rgba(239, 68, 68, 0.1)';
                    $color = 'var(--danger)';
                    $borderColor = 'rgba(239, 68, 68, 0.3)';
                } elseif ($status === 'late') {
                    $bgColor = 'rgba(245, 158, 11, 0.1)';
                    $color = 'var(--warning)';
                    $borderColor = 'rgba(245, 158, 11, 0.3)';
                } elseif ($status === 'leave') {
                    $bgColor = 'rgba(96, 165, 250, 0.1)';
                    $color = '#60A5FA';
                    $borderColor = 'rgba(96, 165, 250, 0.3)';
                }
            @endphp
            <div style="padding: 1.25rem 0.5rem; background-color: {{ $bgColor }}; border: 1px solid {{ $borderColor }}; border-radius: 12px; color: {{ $color }}; position: relative; font-weight: 700;" title="{{ $info['remark'] ?? 'No notes' }}">
                {{ $dayNum }}
                @if($status !== 'none')
                    <div style="width: 6px; height: 6px; border-radius: 50%; background-color: {{ $color }}; position: absolute; bottom: 8px; left: 50%; transform: translateX(-50%);"></div>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
