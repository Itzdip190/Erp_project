<aside class="sidebar" id="sidebar">
    <a href="{{ route('parent.dashboard') }}" class="sb-logo">
        <div class="sb-logo-icon"><i class="fas fa-shield-halved"></i></div>
        <div class="sb-logo-text">
            <strong>SchoolCloud ERP</strong>
            <span>Smart School ERP</span>
        </div>
    </a>

    <!-- Student Profile Card -->
    <div class="sb-student">
        <div class="sb-stu-avatar">
            @if($student?->photo)
                <img src="{{ $student->photo_url }}" alt="">
            @else
                {{ $stuInitials }}
            @endif
        </div>
        <div class="sb-stu-name">{{ $stuName }}</div>
        <div class="sb-stu-class">{{ $classDisplay }} – Sec {{ $sectionDisplay }}</div>
        @if($student)
            <span class="sb-admit">
                <i class="fas fa-id-card" style="font-size:8px;"></i>
                {{ $student->admission_number }}
            </span>
        @endif
    </div>

    <div class="sb-nav">
        <!-- 1. Overview -->
        <div class="sb-group">
            <div class="sb-hdr {{ request()->routeIs('parent.dashboard') ? 'open' : '' }}">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-house"></i></div>
                    <span class="sb-hdr-title">1. Overview</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu {{ request()->routeIs('parent.dashboard') ? 'open' : '' }}">
                <li class="{{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('parent.dashboard') }}">
                        <span class="sb-submenu-label">Dashboard</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 2. Attendance & Academics -->
        <div class="sb-group">
            <div class="sb-hdr {{ request()->routeIs('parent.attendance.*') || request()->routeIs('parent.diary.*') || request()->routeIs('parent.cards.*') || request()->routeIs('parent.exams.*') || request()->routeIs('parent.leaves.*') ? 'open' : '' }}">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-graduation-cap"></i></div>
                    <span class="sb-hdr-title">2. Attendance & Academics</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu {{ request()->routeIs('parent.attendance.*') || request()->routeIs('parent.diary.*') || request()->routeIs('parent.cards.*') || request()->routeIs('parent.exams.*') || request()->routeIs('parent.leaves.*') ? 'open' : '' }}">
                <li class="{{ request()->routeIs('parent.attendance.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.attendance.index') }}">
                        <span class="sb-submenu-label">Attendance Logs</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.diary.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.diary.index') }}">
                        <span class="sb-submenu-label">Digital Diary</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ route('parent.dashboard') }}#timetable">
                        <span class="sb-submenu-label">Timetable</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.cards.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.cards.index') }}">
                        <span class="sb-submenu-label">ID Cards & Passes</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.exams.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.exams.index') }}">
                        <span class="sb-submenu-label">Exams & Report Cards</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.leaves.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.leaves.index') }}">
                        <span class="sb-submenu-label">Apply Leave</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 3. Documents & Reports -->
        <div class="sb-group">
            <div class="sb-hdr {{ request()->routeIs('parent.documents.*') || request()->routeIs('parent.certificates.*') ? 'open' : '' }}">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-file-pdf"></i></div>
                    <span class="sb-hdr-title">3. Documents & Reports</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu {{ request()->routeIs('parent.documents.*') || request()->routeIs('parent.certificates.*') ? 'open' : '' }}">
                <li class="{{ request()->routeIs('parent.documents.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.documents.index') }}">
                        <span class="sb-submenu-label">My Documents</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.certificates.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.certificates.index') }}">
                        <span class="sb-submenu-label">My Certificates</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 4. Finance & Fees -->
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-indian-rupee-sign"></i></div>
                    <span class="sb-hdr-title">4. Finance & Fees</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li>
                    <a href="#">
                        <span class="sb-submenu-label">Fee Details</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 5. Communication -->
        <div class="sb-group">
            <div class="sb-hdr {{ request()->routeIs('parent.events.*') || request()->routeIs('parent.notices.*') || request()->routeIs('parent.surveys.*') || request()->routeIs('parent.chat.*') ? 'open' : '' }}">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-comment-dots"></i></div>
                    <span class="sb-hdr-title">5. Communication</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu {{ request()->routeIs('parent.events.*') || request()->routeIs('parent.notices.*') || request()->routeIs('parent.surveys.*') || request()->routeIs('parent.chat.*') ? 'open' : '' }}">
                <li class="{{ request()->routeIs('parent.events.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.events.index') }}">
                        <span class="sb-submenu-label">Events & Calendar</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.notices.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.notices.index') }}">
                        <span class="sb-submenu-label">Notice Board</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.surveys.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.surveys.index') }}">
                        <span class="sb-submenu-label">Surveys & Polls</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.chat.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.chat.index') }}">
                        <span class="sb-submenu-label">Chat Messenger</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="sb-bottom">
        <a href="{{ route('logout') }}" class="sb-logout">
            <i class="fas fa-right-from-bracket"></i><span>Logout</span>
        </a>
    </div>
</aside>
