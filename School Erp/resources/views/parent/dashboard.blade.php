@extends('layouts.app')

@section('title', 'Parent Portal')

@section('content')
<div class="glass-card">
    <h2 style="font-family: 'Syne', sans-serif; font-size: 1.8rem; margin-bottom: 1rem;">Family Overview</h2>
    <p style="color: var(--text-muted); line-height: 1.6;">Welcome to the SchoolCloud Parent Portal. Below are the academic profiles and quick links for children registered under your email address.</p>
</div>

<h3 style="font-family: 'Syne', sans-serif; margin-bottom: 1.5rem; border-bottom: 1px solid var(--border); padding-bottom: 0.5rem; color: var(--accent);">Your Children</h3>

<div style="display: flex; gap: 2rem; flex-wrap: wrap;">
    @forelse($children as $child)
        <div class="glass-card" style="width: 320px; text-align: center; margin-bottom: 0; padding: 2rem 1.5rem;">
            <!-- Child photo or avatar -->
            <div style="width: 100px; height: 100px; border-radius: 50%; background-image: url('{{ $child->photo_url }}'); background-size: cover; background-position: center; margin: 0 auto 1.5rem; border: 3px solid var(--accent); box-shadow: 0 4px 10px rgba(0,0,0,0.3);">
                @if(!$child->photo)
                    <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background-color: var(--primary-light); color: var(--text-muted); border-radius: 50%;">
                        <i class="fa fa-user" style="font-size: 3rem;"></i>
                    </div>
                @endif
            </div>

            <h4 style="font-family: 'Syne', sans-serif; font-size: 1.25rem; margin-bottom: 0.25rem;">{{ $child->full_name }}</h4>
            <span class="badge badge-success" style="margin-bottom: 1.5rem;">{{ $child->admission_number }}</span>
            
            <div style="text-align: left; display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.9rem; margin-bottom: 1.5rem; border-top: 1px solid var(--border); padding-top: 1rem;">
                <div><strong style="color: var(--text-muted);">Class:</strong> {{ $child->class?->name }} - {{ $child->section?->name }}</div>
                <div><strong style="color: var(--text-muted);">Roll Number:</strong> {{ $child->roll_number ?? 'N/A' }}</div>
                <div><strong style="color: var(--text-muted);">Guardian:</strong> {{ $child->guardian_name }}</div>
            </div>

            <div style="display: flex; gap: 1rem; width: 100%;">
                <a href="{{ route('parent.children.show', $child->id) }}" class="btn-accent" style="flex: 1; justify-content: center; padding: 0.5rem; font-size: 0.85rem; background-color: #4B5563;">
                    <i class="fa fa-user-circle"></i> Profile
                </a>
                <a href="{{ route('parent.attendance.index', ['student_id' => $child->id]) }}" class="btn-accent" style="flex: 1; justify-content: center; padding: 0.5rem; font-size: 0.85rem;">
                    <i class="fa fa-calendar-check"></i> Attendance
                </a>
            </div>
        </div>
    @empty
        <div class="glass-card" style="width: 100%; text-align: center; color: var(--text-muted); padding: 4rem;">
            <i class="fa fa-info-circle" style="font-size: 3rem; margin-bottom: 1rem; color: var(--accent);"></i>
            <p>No children records found linked to your email address: <strong>{{ auth()->user()->email }}</strong>.</p>
        </div>
    @endforelse
</div>
@endsection
