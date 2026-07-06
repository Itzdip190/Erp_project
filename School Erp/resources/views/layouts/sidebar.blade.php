@php
    $sch = $currentSchool ?? $school ?? null;
    $sess = $currentSession ?? null;
    $plan = $planName ?? ($sch ? ucfirst($sch->status ?? 'Basic') : 'Basic');
    $u = auth()->user();
    $isTeacherUser = $u && ($u->hasRole('teacher') || $u->hasRole('staff') || $u->hasRole('accountant') || $u->role === 'teacher');
    $logoUrl = $isTeacherUser ? route('teacher.dashboard') : route('school.dashboard');
@endphp
<!-- ══════════ SIDEBAR OVERLAY ══════════ -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<!-- ══════════ SIDEBAR ══════════ -->
<aside class="sidebar" id="appSidebar">
    @if($sch)
        <!-- Unified School Brand Header (Clickable, redirects to Dashboard) -->
        <a href="{{ $logoUrl }}" class="sb-school-header" title="Go to Dashboard">
            <div class="sb-school-header-top">
                <div class="sb-school-logo">
                    @if(!empty($sch->logo) && Storage::disk('public')->exists($sch->logo))
                        <img src="{{ Storage::disk('public')->url($sch->logo) }}" alt="{{ $sch->name }}">
                    @else
                        <i class="fas fa-school"></i>
                    @endif
                </div>
                <div class="sb-school-name-wrapper">
                    <div class="sb-school-name">{{ $sch->name }}</div>
                </div>
            </div>
            <div class="sb-school-meta-row">
                <div class="sb-school-session">
                    <i class="fas fa-calendar-alt" style="font-size: 10px; margin-right: 3px;"></i>
                    <span>Session: {{ $sess?->name ?? '—' }}</span>
                </div>
                <span class="sb-plan-badge">
                    <i class="fas fa-star" style="font-size: 8px; margin-right: 3px;"></i>
                    <span>{{ $plan }}</span>
                </span>
            </div>
            <button type="button" class="sb-close-btn" onclick="closeSidebar()" aria-label="Close sidebar">
                <i class="fas fa-xmark"></i>
            </button>
        </a>
    @else
        <!-- Fallback Default Brand Header (If no school context is active) -->
        <a href="{{ $logoUrl }}" class="sb-logo" title="{{ $isTeacherUser ? 'Go to Teacher Dashboard' : 'Go to Admin Dashboard' }}">
            <div class="sb-logo-icon"><i class="fas fa-shield-halved"></i></div>
            <div class="sb-logo-text">
                <strong>SchoolCloud ERP</strong>
                <span>{{ $isTeacherUser ? 'Teacher Portal' : 'Smart School ERP' }}</span>
            </div>
            <button type="button" class="sb-close-btn" onclick="closeSidebar()" aria-label="Close sidebar">
                <i class="fas fa-xmark"></i>
            </button>
        </a>
    @endif

    @include('layouts.sidebar_nav')

    <div class="sb-bottom" style="position: relative; display: flex; justify-content: center; padding: 10px 12px;">
        <!-- Profile & Settings Dropdown Menu -->
        <div id="sbProfileMenu" class="sb-profile-menu" style="bottom: calc(100% + 8px);">
            <a href="{{ route('school.settings.index') }}">
                <i class="fas fa-user-astronaut" style="color: var(--gold);"></i>
                <span>Profile</span>
            </a>
            <a href="{{ route('school.settings.index') }}">
                <i class="fas fa-sliders" style="color: var(--purple);"></i>
                <span>Settings</span>
            </a>
            <a href="{{ route('logout') }}" style="color: #ef4444; border-top: 1px solid var(--border);">
                <i class="fas fa-right-from-bracket" style="color: #ef4444;"></i>
                <strong>Logout</strong>
            </a>
        </div>

        <button type="button" onclick="toggleSbProfileMenu(event)" class="sb-drawer-btn" title="Menu">
            <i class="fas fa-grip"></i>
        </button>
    </div>
</aside>
