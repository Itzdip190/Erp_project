@extends('layouts.app')

@section('page-title', 'Teacher & Staff Dashboard')

@section('content')
<style>
    .teacher-hero {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        border-radius: 16px;
        padding: 28px 32px;
        color: #ffffff;
        margin-bottom: 24px;
        box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.25);
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }
    .teacher-hero::after {
        content: '';
        position: absolute;
        right: -50px;
        top: -50px;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50%;
        pointer-events: none;
    }
    .hero-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 6px;
        letter-spacing: -0.02em;
    }
    .hero-subtitle {
        color: #94a3b8;
        font-size: 0.95rem;
    }
    .badge-designation {
        background: rgba(255, 255, 255, 0.12);
        backdrop-filter: blur(8px);
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 0.85rem;
        font-weight: 600;
        color: #e2e8f0;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }
    .info-notice-card {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        border-left: 5px solid #22c55e;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-start;
        gap: 14px;
    }
    .info-notice-card i {
        color: #16a34a;
        font-size: 1.4rem;
        margin-top: 2px;
    }
    .info-notice-content h4 {
        margin: 0 0 4px 0;
        color: #14532d;
        font-size: 1rem;
        font-weight: 600;
    }
    .info-notice-content p {
        margin: 0;
        color: #166534;
        font-size: 0.9rem;
        line-height: 1.45;
    }
    .section-heading {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .module-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    .module-card {
        background: #ffffff;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        padding: 20px;
        transition: all 0.25s ease;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .module-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 20px -5px rgba(0,0,0,0.08);
        border-color: #cbd5e1;
    }
    .mod-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 14px;
    }
    .mod-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #3b82f6;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .mod-title {
        font-weight: 700;
        font-size: 1.05rem;
        color: #0f172a;
    }
    .feat-list {
        list-style: none;
        padding: 0;
        margin: 0 0 16px 0;
    }
    .feat-item {
        padding: 6px 0;
        border-bottom: 1px dashed #f1f5f9;
        font-size: 0.88rem;
        color: #475569;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .feat-item:last-child {
        border-bottom: none;
    }
    .empty-state {
        background: #ffffff;
        border-radius: 14px;
        border: 2px dashed #cbd5e1;
        padding: 40px 20px;
        text-align: center;
        color: #64748b;
    }
    .empty-state i {
        font-size: 2.5rem;
        color: #94a3b8;
        margin-bottom: 12px;
    }
</style>

@if(session('error'))
    <div class="alert alert-danger mb-4" style="border-radius:10px; padding:12px 18px;">
        <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
    </div>
@endif

<!-- Teacher Hero Banner -->
<div class="teacher-hero">
    <div>
        <div class="hero-title">Welcome back, {{ $user->name }}! 👋</div>
        <div class="hero-subtitle">
            {{ $school->name ?? 'School Dashboard' }} &bull; {{ $currentSession->name ?? 'Academic Session' }}
        </div>
    </div>
    @if($staff && $staff->designation)
        <span class="badge-designation">
            <i class="fas fa-id-badge me-1"></i> {{ $staff->designation->name }}
        </span>
    @else
        <span class="badge-designation">
            <i class="fas fa-chalkboard-user me-1"></i> Staff Member
        </span>
    @endif
</div>

<!-- Access Control Status Info -->
<div class="info-notice-card">
    <i class="fas fa-shield-halved"></i>
    <div class="info-notice-content">
        <h4>Staff Access Control Activated</h4>
        <p>You are logged into your dedicated Staff Portal. By default, access is restricted to this dashboard. Additional school management modules will dynamically appear below as soon as access is granted to you by your School Administrator.</p>
    </div>
</div>

<!-- Authorized Modules Grid -->
<div class="section-heading">
    <i class="fas fa-cubes" style="color: #3b82f6;"></i>
    <span>Your Granted Modules & Features</span>
</div>

@if(count($accessibleModules) > 0)
    <div class="module-grid">
        @foreach($accessibleModules as $modKey => $mod)
            <div class="module-card">
                <div>
                    <div class="mod-header">
                        <div class="mod-icon">
                            <i class="fas {{ $mod['icon'] ?? 'fa-cube' }}"></i>
                        </div>
                        <div class="mod-title">{{ $mod['label'] }}</div>
                    </div>
                    <ul class="feat-list">
                        @foreach($mod['features'] as $featKey => $featLabel)
                            <li class="feat-item">
                                <span><i class="fas fa-check-circle text-success me-2" style="font-size:0.8rem;"></i>{{ $featLabel }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="empty-state">
        <i class="fas fa-user-lock"></i>
        <h4 style="font-weight:700; color:#334155; margin-bottom:6px;">No Additional Modules Assigned Yet</h4>
        <p style="margin:0; font-size:0.92rem;">Your School Administrator has not enabled additional module permissions for your account yet. Contact your school admin to enable specific features.</p>
    </div>
@endif

@if(count($todaySubstitutions) > 0)
    <div class="section-heading mt-4">
        <i class="fas fa-calendar-check" style="color: #eab308;"></i>
        <span>Today's Substitution Classes</span>
    </div>
    <div class="card mb-4" style="border-radius:12px; border:1px solid #e2e8f0; padding:16px;">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reason</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($todaySubstitutions as $sub)
                    <tr>
                        <td>{{ $sub->date }}</td>
                        <td>{{ $sub->reason ?? 'Substitution Class' }}</td>
                        <td><span class="badge bg-warning text-dark">Assigned Today</span></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif

@endsection
