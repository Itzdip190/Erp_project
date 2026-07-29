@php
    $u = auth()->user();
    $uName = $u?->name ?? 'User';
    $uInitials = strtoupper(substr($uName, 0, 1) . (str_contains($uName, ' ') ? substr($uName, strrpos($uName, ' ') + 1, 1) : ''));
    if (empty($uInitials)) { $uInitials = 'YA'; }
    $isTeacherUser = $u && ($u->hasRole('teacher') || $u->hasRole('staff') || $u->hasRole('accountant') || ($u->role ?? '') === 'teacher');
    
    $dashUrl = route('school.dashboard');
    if ($isTeacherUser && Route::has('teacher.dashboard')) {
        $dashUrl = route('teacher.dashboard');
    } elseif (request()->routeIs('superadmin.*') && Route::has('superadmin.dashboard')) {
        $dashUrl = route('superadmin.dashboard');
    }

    $studentsUrl = Route::has('school.students.index') ? route('school.students.index') : '#';
    $attendanceUrl = Route::has('school.attendance.students.index') ? route('school.attendance.students.index') : '#';

    // Primary Bottom Nav Tab Route Matching
    $isDashRoute = request()->routeIs('school.dashboard') 
        || request()->routeIs('teacher.dashboard') 
        || request()->routeIs('superadmin.dashboard')
        || request()->is('school') || request()->is('school/dashboard') 
        || request()->is('teacher') || request()->is('teacher/dashboard') 
        || request()->is('superadmin') || request()->is('superadmin/dashboard');

    if (request()->is('*/mis-report*')) {
        $isDashRoute = false;
    }

    $isStudentsRoute = request()->routeIs('school.students.index') || request()->is('school/students') || request()->is('school/students/index');
    if (request()->is('school/students/*') && !request()->is('school/students/index')) {
        $isStudentsRoute = false;
    }

    $isAttendanceRoute = request()->routeIs('school.attendance.students.index') || request()->is('school/attendance/students') || request()->is('school/attendance/students/index');

    $isDashActive = $isDashRoute;
    $isStudentsActive = $isStudentsRoute;
    $isAttendanceActive = $isAttendanceRoute;

    // Bottom Navigation visible ONLY for primary tabs (Dashboard, Students, Attendance, Alerts)
    // Hidden completely for all Side Drawer modules
    $showBottomNav = $isDashRoute || $isStudentsRoute || $isAttendanceRoute;

    $notifCount = isset($navNotifications) ? $navNotifications->count() : 0;
    
    $isAndroidDevice = isset($_SERVER['HTTP_USER_AGENT']) && preg_match('/android/i', $_SERVER['HTTP_USER_AGENT']);
@endphp

@if($showBottomNav)
<!-- Android Dedicated Premium Bottom Navigation Bar -->
<div class="android-bottom-nav" id="androidBottomNav">
    <a href="{{ $dashUrl }}" class="abn-item {{ $isDashActive ? 'active' : '' }}" title="Home">
        <div class="abn-icon-wrap">
            <i class="fas fa-house"></i>
        </div>
        <span>Home</span>
        @if($isDashActive)
            <div class="abn-active-line"></div>
        @endif
    </a>

    <button type="button" class="abn-item" onclick="toggleAndroidSidebar(event)" title="Dashboard">
        <div class="abn-icon-wrap">
            <i class="fas fa-sliders"></i>
        </div>
        <span>Dashboard</span>
    </button>

    <a href="{{ Route::has('school.communication.notice') ? route('school.communication.notice') : '#' }}" class="abn-item" title="Messages">
        <div class="abn-icon-wrap">
            <i class="far fa-comment-dots"></i>
            @if($notifCount > 0)
                <span class="abn-badge">{{ $notifCount }}</span>
            @endif
        </div>
        <span>Messages</span>
    </a>

    <a href="{{ route('school.settings.index') }}" class="abn-item" title="Profile">
        <div class="abn-icon-wrap">
            <i class="far fa-user"></i>
        </div>
        <span>Profile</span>
    </a>
</div>
@endif

<style>
/* Default state: hidden on non-Android devices or side drawer pages */
.android-bottom-nav {
    display: none !important;
    position: fixed !important;
    bottom: 0 !important;
    left: 0 !important;
    right: 0 !important;
    width: 100% !important;
    height: 64px !important;
    background: #ffffff !important;
    border-top-left-radius: 20px !important;
    border-top-right-radius: 20px !important;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.08), 0 -1px 4px rgba(0, 0, 0, 0.04) !important;
    border-top: 1px solid rgba(0, 0, 0, 0.05) !important;
    z-index: 99999 !important;
    align-items: center !important;
    justify-content: space-around !important;
    padding: 4px 8px 6px 8px !important;
    box-sizing: border-box !important;
}

body.dark-mode .android-bottom-nav {
    background: #1e293b !important;
    border-top-color: rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.4) !important;
}

/* Display strictly on Android devices AND when on a primary bottom nav tab */
body.is-android.has-bottom-nav .android-bottom-nav {
    display: flex !important;
}

/* Safe area padding & scroll range for Android devices when bottom nav is active */
body.is-android.has-bottom-nav {
    padding-bottom: 80px !important;
}

body.is-android.has-bottom-nav .main,
body.is-android.has-bottom-nav .pg,
body.is-android.has-bottom-nav .main-wrapper,
body.is-android.has-bottom-nav .content-wrapper,
body.is-android.has-bottom-nav .app-main,
body.is-android.has-bottom-nav .page-wrapper,
body.is-android.has-bottom-nav .wrapper {
    padding-bottom: 80px !important;
}

/* Ensure the last content/list/button element has safe scrolling space */
body.is-android.has-bottom-nav .main::after,
body.is-android.has-bottom-nav .pg::after,
body.is-android.has-bottom-nav .main-wrapper::after {
    content: '';
    display: block;
    height: 32px;
    width: 100%;
}

/* Position floating action bars, chatbot, and fixed elements above bottom nav */
body.is-android.has-bottom-nav .fixed-bottom,
body.is-android.has-bottom-nav .bottom-action-bar,
body.is-android.has-bottom-nav .sticky-bottom,
body.is-android.has-bottom-nav #robot-assistant,
body.is-android.has-bottom-nav .robot-body,
body.is-android.has-bottom-nav #custom-lang-switcher,
body.is-android.has-bottom-nav .lang-switch-wrap {
    bottom: 80px !important;
}

/* Modals and SweetAlert safe spacing on Android when bottom nav is active */
body.is-android.has-bottom-nav .modal-dialog {
    margin-bottom: 80px !important;
}

body.is-android.has-bottom-nav .swal2-container {
    padding-bottom: 80px !important;
}

.abn-item {
    display: flex !important;
    flex-direction: column !important;
    align-items: center !important;
    justify-content: center !important;
    flex: 1 !important;
    text-decoration: none !important;
    color: #64748b !important;
    font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif !important;
    font-size: 11px !important;
    font-weight: 600 !important;
    background: transparent !important;
    border: none !important;
    outline: none !important;
    cursor: pointer !important;
    padding: 3px 0 !important;
    gap: 3px !important;
    transition: all 0.15s ease !important;
    -webkit-tap-highlight-color: transparent !important;
}

body.dark-mode .abn-item {
    color: #94a3b8 !important;
}

.abn-item.active,
.abn-item:active {
    color: #2563eb !important;
    font-weight: 700 !important;
}

body.dark-mode .abn-item.active {
    color: #3b82f6 !important;
}

.abn-icon-wrap {
    position: relative !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    height: 24px !important;
    font-size: 19px !important;
    color: inherit !important;
}

.abn-item.active .abn-icon-wrap {
    color: #2563eb !important;
}

body.dark-mode .abn-item.active .abn-icon-wrap {
    color: #3b82f6 !important;
}

.abn-badge {
    position: absolute !important;
    top: -4px !important;
    right: -8px !important;
    background: #ef4444 !important;
    color: #ffffff !important;
    font-size: 9.5px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    min-width: 16px !important;
    height: 16px !important;
    border-radius: 10px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 3px !important;
    border: 1.5px solid #ffffff !important;
    box-shadow: 0 2px 4px rgba(239, 68, 68, 0.35) !important;
}

body.dark-mode .abn-badge {
    border-color: #1e293b !important;
}

.abn-avatar {
    width: 26px !important;
    height: 26px !important;
    border-radius: 50% !important;
    background: #2563eb !important;
    color: #ffffff !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    letter-spacing: -0.3px !important;
    box-shadow: 0 2px 6px rgba(37, 99, 235, 0.3) !important;
}
</style>

<script>
(function() {
    // Detect Android User Agent (Server-side + Client-side verification)
    var isAndroid = {{ $isAndroidDevice ? 'true' : 'false' }} || /android/i.test(navigator.userAgent);
    var showBottomNav = {{ $showBottomNav ? 'true' : 'false' }};
    if (isAndroid) {
        document.body.classList.add('is-android');
        if (showBottomNav) {
            document.body.classList.add('has-bottom-nav');
        } else {
            document.body.classList.remove('has-bottom-nav');
        }
    }
})();

function toggleAndroidSidebar(e) {
    if (e && e.preventDefault) { e.preventDefault(); e.stopPropagation(); }
    const sidebar = document.getElementById('appSidebar') || document.getElementById('sidebar') || document.querySelector('.main-sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) {
        const isOpen = sidebar.classList.toggle('open');
        if (overlay) overlay.classList.toggle('active', isOpen);
        document.body.style.overflow = isOpen ? 'hidden' : '';
    } else if (typeof toggleSbProfileMenu === 'function') {
        toggleSbProfileMenu(e);
    }
}
</script>

