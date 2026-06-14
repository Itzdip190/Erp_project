@extends('layouts.app')

@section('title', 'School Dashboard')

@section('content')
<div class="glass-card">
    <h2 style="font-family: 'Syne', sans-serif; font-size: 1.8rem; margin-bottom: 1rem;">Welcome to Yash International School Portal</h2>
    <p style="color: var(--text-muted); line-height: 1.6;">You are logged in as a school administrator. You can manage student admissions, promote students, mark manual/bulk student and staff attendance, and view comprehensive analytics reports from the sidebar.</p>
</div>

<div class="grid-3">
    <!-- Stat Card 1 -->
    <div class="glass-card" style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 0;">
        <div style="width: 50px; height: 50px; border-radius: 12px; background-color: rgba(59,130,246,0.1); display: flex; align-items: center; justify-content: center; color: var(--accent); font-size: 1.5rem;">
            <i class="fa fa-graduation-cap"></i>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ $studentsCount }}</div>
            <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Total Students</div>
        </div>
    </div>

    <!-- Stat Card 2 -->
    <div class="glass-card" style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 0;">
        <div style="width: 50px; height: 50px; border-radius: 12px; background-color: rgba(16,185,129,0.1); display: flex; align-items: center; justify-content: center; color: var(--success); font-size: 1.5rem;">
            <i class="fa fa-user-tie"></i>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ $staffCount }}</div>
            <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Active Staff</div>
        </div>
    </div>

    <!-- Stat Card 3 -->
    <div class="glass-card" style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 0;">
        <div style="width: 50px; height: 50px; border-radius: 12px; background-color: rgba(245,158,11,0.1); display: flex; align-items: center; justify-content: center; color: var(--warning); font-size: 1.5rem;">
            <i class="fa fa-chart-bar"></i>
        </div>
        <div>
            <div style="font-size: 1.8rem; font-weight: 800;">{{ $attendanceRate }}%</div>
            <div style="color: var(--text-muted); font-size: 0.85rem; font-weight: 700; text-transform: uppercase;">Daily Attendance</div>
        </div>
    </div>
</div>
@endsection
