@php
    $student = $student ?? (new \App\Http\Controllers\Parent\ParentDashboardController())->resolveStudent(auth()->user());
    $resolvedSchoolId = $user->school_id ?? $student?->school_id ?? auth()->user()?->school_id ?? 1;
    $currentSession = \App\Models\AcademicSession::resolveCurrentSessionForUser(auth()->user(), $resolvedSchoolId)
        ?? ($student?->academicSession ?? \App\Models\AcademicSession::where('school_id', $resolvedSchoolId)->first());
    $activeStudentSession = ($student && $currentSession) ? $student->studentSessionFor($currentSession->id) : null;
    $classDisplay = optional($activeStudentSession?->schoolClass ?? $student?->class)->name ?? ($classDisplay ?? 'Class');
    $sectionDisplay = optional($activeStudentSession?->section ?? $student?->section)->name ?? ($sectionDisplay ?? 'A');
    $sessionDisplay = optional($currentSession ?? $student?->academicSession)->name ?? ($sessionDisplay ?? '2026 – 2027');
    $stuName = $student ? $student->full_name : ($stuName ?? auth()->user()?->name ?? 'Student');
    $stuInitials = $student ? strtoupper(substr($stuName,0,1).(str_contains($stuName,' ') ? substr($stuName,strrpos($stuName,' ')+1,1) : '')) : ($stuInitials ?? 'ST');
    $children = $children ?? (new \App\Http\Controllers\Parent\ParentDashboardController())->getParentChildren(auth()->user());
@endphp

<!-- SIDEBAR OVERLAY -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<aside class="sidebar" id="sidebar">
    <a href="{{ route('parent.dashboard') }}" class="sb-logo">
        <div class="sb-logo-left" style="display:flex;align-items:center;gap:10px;text-decoration:none;min-width:0;flex:1;">
            <div class="sb-logo-icon">
                @if(isset($school) && $school?->logo_url)
                    <img src="{{ $school->logo_url }}" alt="School Logo" style="width:100%;height:100%;object-fit:cover;border-radius:8px;">
                @else
                    <i class="fas fa-school"></i>
                @endif
            </div>
            <div class="sb-logo-text" style="min-width:0;">
                <strong style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; display: block;" title="{{ $school?->name ?? ($student?->school?->name ?? 'School Portal') }}">
                    {{ $school?->name ?? ($student?->school?->name ?? 'School Portal') }}
                </strong>
                <span>Student Portal</span>
            </div>
        </div>
        <button type="button" class="sb-close-btn" onclick="closeSidebar()" aria-label="Close sidebar">
            <i class="fas fa-xmark"></i>
        </button>
    </a>

    <!-- Student Profile Card (Dynamic with Active Academic Year Class) -->
    <div class="sb-student">
        <a href="{{ route('parent.profile') }}" style="text-decoration:none; display:block;" title="View Student Profile">
            <div class="sb-stu-avatar">
                @if(isset($student) && $student?->photo)
                    <img src="{{ $student->photo_url }}" alt="">
                @else
                    {{ $stuInitials ?? 'ST' }}
                @endif
            </div>
            <div class="sb-stu-name">{{ $stuName ?? 'Student' }}</div>
            <div class="sb-stu-class">{{ $classDisplay }} – Sec {{ $sectionDisplay }}</div>
            @if(isset($student) && ($student?->admission_number || $student?->admission_id))
                <span class="sb-admit">
                    <i class="fas fa-id-card" style="font-size:8px;"></i>
                    {{ $student->admission_number ?? $student->admission_id }}
                </span>
            @endif
        </a>

        @if(isset($children) && $children->count() > 1)
            <div style="margin-top: 10px; width: 100%;">
                <select onchange="window.location.href='{{ route('parent.dashboard') }}?switch_student_id='+this.value" 
                        style="width: 100%; background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.18); color: #fff; font-size: 11px; font-weight: 600; border-radius: 6px; padding: 5px 8px; outline: none; cursor: pointer;">
                    @foreach($children as $child)
                        <option value="{{ $child->id }}" {{ $child->id === ($student?->id) ? 'selected' : '' }} style="background: #1e1e2d; color: #fff;">
                            👤 {{ $child->full_name }} ({{ $child->class_name }})
                        </option>
                    @endforeach
                </select>
            </div>
        @endif
    </div>

    <!-- ══ NAVIGATION APPS GRID / ACCORDION ══ -->
    <div class="sb-nav">
        <!-- 1. Overview / Dashboard -->
        <div class="sb-group {{ request()->routeIs('parent.dashboard') ? 'active-group' : '' }}" data-direct-link="{{ route('parent.dashboard') }}">
            <div class="sb-hdr {{ request()->routeIs('parent.dashboard') ? 'open' : '' }}" onclick="handleSbHdrClick(this, event)">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="m3d-icon">
                          <defs>
                            <linearGradient id="stu-ov-bg" x1="0" y1="0" x2="64" y2="64"><stop offset="0%" stop-color="#3b82f6"/><stop offset="100%" stop-color="#1d4ed8"/></linearGradient>
                            <filter id="stu-ov-sh" x1="0" y1="0" width="64" height="64" filterUnits="userSpaceOnUse"><feDropShadow dx="0" dy="4" stdDeviation="3" flood-color="#1e3a8a" flood-opacity="0.35"/></filter>
                          </defs>
                          <rect x="4" y="4" width="56" height="56" rx="16" fill="url(#stu-ov-bg)" filter="url(#stu-ov-sh)"/>
                          <path d="M14 32L32 16L50 32V48C50 50.2 48.2 52 46 52H18C15.8 52 14 50.2 14 48V32Z" fill="#ffffff"/>
                          <path d="M10 32L32 14L54 32" stroke="#93c5fd" stroke-width="4.5" stroke-linecap="round" stroke-linejoin="round"/>
                          <rect x="26" y="36" width="12" height="16" rx="3" fill="#1d4ed8"/>
                          <circle cx="32" cy="25" r="3.5" fill="#60a5fa"/>
                        </svg>
                    </div>
                    <span class="sb-hdr-title">Dashboard</span>
                </div>
                <i class="fas fa-arrow-up-right-from-square sb-hdr-arrow" style="font-size:10px; opacity:0.6;"></i>
            </div>
            <ul class="sb-submenu {{ request()->routeIs('parent.dashboard') ? 'open' : '' }}">
                <li class="{{ request()->routeIs('parent.dashboard') ? 'active' : '' }}">
                    <a href="{{ route('parent.dashboard') }}">
                        <span class="sb-submenu-label">Dashboard Overview</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 2. Attendance & Diary -->
        <div class="sb-group {{ request()->routeIs('parent.attendance.*') || request()->routeIs('parent.diary.*') || request()->routeIs('parent.leaves.*') ? 'active-group' : '' }}">
            <div class="sb-hdr {{ request()->routeIs('parent.attendance.*') || request()->routeIs('parent.diary.*') || request()->routeIs('parent.leaves.*') ? 'open' : '' }}" onclick="handleSbHdrClick(this, event)">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="m3d-icon">
                          <defs>
                            <linearGradient id="stu-att-bg" x1="0" y1="0" x2="64" y2="64"><stop offset="0%" stop-color="#10b981"/><stop offset="100%" stop-color="#047857"/></linearGradient>
                            <filter id="stu-att-sh" x1="0" y1="0" width="64" height="64" filterUnits="userSpaceOnUse"><feDropShadow dx="0" dy="4" stdDeviation="3" flood-color="#064e3b" flood-opacity="0.35"/></filter>
                          </defs>
                          <rect x="4" y="4" width="56" height="56" rx="16" fill="url(#stu-att-bg)" filter="url(#stu-att-sh)"/>
                          <rect x="16" y="16" width="32" height="34" rx="6" fill="#ffffff"/>
                          <rect x="16" y="16" width="32" height="10" rx="4" fill="#6ee7b7"/>
                          <rect x="22" y="12" width="4" height="7" rx="2" fill="#ffffff"/>
                          <rect x="38" y="12" width="4" height="7" rx="2" fill="#ffffff"/>
                          <path d="M23 37L29 43L41 31" stroke="#059669" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="sb-hdr-title">Attendance & Diary</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu {{ request()->routeIs('parent.attendance.*') || request()->routeIs('parent.diary.*') || request()->routeIs('parent.leaves.*') ? 'open' : '' }}">
                <li class="{{ request()->routeIs('parent.attendance.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.attendance.index') }}">
                        <span class="sb-submenu-label">Attendance Logs</span>
                        <i class="fas fa-calendar-check sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.diary.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.diary.index') }}">
                        <span class="sb-submenu-label">Digital Diary</span>
                        <i class="fas fa-book-open sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.leaves.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.leaves.index') }}">
                        <span class="sb-submenu-label">Apply Leave</span>
                        <i class="fas fa-person-walking-arrow-right sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 3. Academics & Timetable -->
        <div class="sb-group {{ request()->routeIs('parent.timetable.*') || request()->routeIs('parent.assignments.*') || request()->routeIs('parent.study-materials.*') ? 'active-group' : '' }}">
            <div class="sb-hdr {{ request()->routeIs('parent.timetable.*') || request()->routeIs('parent.assignments.*') || request()->routeIs('parent.study-materials.*') ? 'open' : '' }}" onclick="handleSbHdrClick(this, event)">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="m3d-icon">
                          <defs>
                            <linearGradient id="stu-acd-bg" x1="0" y1="0" x2="64" y2="64"><stop offset="0%" stop-color="#8b5cf6"/><stop offset="100%" stop-color="#6d28d9"/></linearGradient>
                            <filter id="stu-acd-sh" x1="0" y1="0" width="64" height="64" filterUnits="userSpaceOnUse"><feDropShadow dx="0" dy="4" stdDeviation="3" flood-color="#4c1d95" flood-opacity="0.35"/></filter>
                          </defs>
                          <rect x="4" y="4" width="56" height="56" rx="16" fill="url(#stu-acd-bg)" filter="url(#stu-acd-sh)"/>
                          <path d="M32 16L14 26L32 36L50 26L32 16Z" fill="#ffffff"/>
                          <path d="M20 31.5V42C20 45.5 25.4 49 32 49C38.6 49 44 45.5 44 42V31.5" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round"/>
                          <path d="M47 28.5V44" stroke="#fcd34d" stroke-width="3" stroke-linecap="round"/>
                          <circle cx="47" cy="46" r="2.5" fill="#f59e0b"/>
                        </svg>
                    </div>
                    <span class="sb-hdr-title">Academics</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu {{ request()->routeIs('parent.timetable.*') || request()->routeIs('parent.assignments.*') || request()->routeIs('parent.study-materials.*') ? 'open' : '' }}">
                <li class="{{ request()->routeIs('parent.timetable.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.timetable.index') }}">
                        <span class="sb-submenu-label">Class Timetable</span>
                        <i class="fas fa-clock sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.assignments.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.assignments.index') }}">
                        <span class="sb-submenu-label">Assignments</span>
                        <i class="fas fa-pencil sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.study-materials.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.study-materials.index') }}">
                        <span class="sb-submenu-label">Study Materials</span>
                        <i class="fas fa-book sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 4. Examinations & Results (DEDICATED MODULE) -->
        <div class="sb-group {{ request()->routeIs('parent.exams.*') ? 'active-group' : '' }}" data-direct-link="{{ route('parent.exams.index') }}">
            <div class="sb-hdr {{ request()->routeIs('parent.exams.*') ? 'open' : '' }}" onclick="handleSbHdrClick(this, event)">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="m3d-icon">
                          <defs>
                            <linearGradient id="stu-exam-bg" x1="0" y1="0" x2="64" y2="64"><stop offset="0%" stop-color="#ec4899"/><stop offset="100%" stop-color="#be185d"/></linearGradient>
                            <filter id="stu-exam-sh" x1="0" y1="0" width="64" height="64" filterUnits="userSpaceOnUse"><feDropShadow dx="0" dy="4" stdDeviation="3" flood-color="#831843" flood-opacity="0.35"/></filter>
                          </defs>
                          <rect x="4" y="4" width="56" height="56" rx="16" fill="url(#stu-exam-bg)" filter="url(#stu-exam-sh)"/>
                          <rect x="18" y="14" width="28" height="36" rx="4" fill="#ffffff"/>
                          <path d="M24 24H40M24 30H40M24 36H34" stroke="#db2777" stroke-width="3" stroke-linecap="round"/>
                          <circle cx="38" cy="38" r="7" fill="#f59e0b"/>
                          <path d="M35 38L37.5 40.5L41.5 35.5" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="sb-hdr-title">Examinations</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu {{ request()->routeIs('parent.exams.*') ? 'open' : '' }}">
                <li class="{{ request()->routeIs('parent.exams.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.exams.index') }}">
                        <span class="sb-submenu-label">Exams & Report Cards</span>
                        <i class="fas fa-file-signature sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 5. Documents & ID -->
        <div class="sb-group {{ request()->routeIs('parent.cards.*') || request()->routeIs('parent.documents.*') || request()->routeIs('parent.certificates.*') ? 'active-group' : '' }}">
            <div class="sb-hdr {{ request()->routeIs('parent.cards.*') || request()->routeIs('parent.documents.*') || request()->routeIs('parent.certificates.*') ? 'open' : '' }}" onclick="handleSbHdrClick(this, event)">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="m3d-icon">
                          <defs>
                            <linearGradient id="stu-doc-bg" x1="0" y1="0" x2="64" y2="64"><stop offset="0%" stop-color="#f59e0b"/><stop offset="100%" stop-color="#b45309"/></linearGradient>
                            <filter id="stu-doc-sh" x1="0" y1="0" width="64" height="64" filterUnits="userSpaceOnUse"><feDropShadow dx="0" dy="4" stdDeviation="3" flood-color="#78350f" flood-opacity="0.35"/></filter>
                          </defs>
                          <rect x="4" y="4" width="56" height="56" rx="16" fill="url(#stu-doc-bg)" filter="url(#stu-doc-sh)"/>
                          <rect x="15" y="18" width="34" height="28" rx="5" fill="#ffffff"/>
                          <circle cx="25" cy="30" r="5" fill="#f59e0b"/>
                          <rect x="33" y="26" width="12" height="3.5" rx="1.5" fill="#d97706"/>
                          <rect x="33" y="32" width="8" height="3" rx="1.5" fill="#fcd34d"/>
                          <rect x="25" y="14" width="14" height="6" rx="2" fill="#fed7aa"/>
                        </svg>
                    </div>
                    <span class="sb-hdr-title">Documents & ID</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu {{ request()->routeIs('parent.cards.*') || request()->routeIs('parent.documents.*') || request()->routeIs('parent.certificates.*') ? 'open' : '' }}">
                <li class="{{ request()->routeIs('parent.cards.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.cards.index') }}">
                        <span class="sb-submenu-label">ID Cards & Passes</span>
                        <i class="fas fa-id-card sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.documents.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.documents.index') }}">
                        <span class="sb-submenu-label">My Documents</span>
                        <i class="fas fa-folder-open sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.certificates.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.certificates.index') }}">
                        <span class="sb-submenu-label">My Certificates</span>
                        <i class="fas fa-award sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 6. Finance & Fees -->
        <div class="sb-group {{ request()->routeIs('parent.fees.*') ? 'active-group' : '' }}" data-direct-link="{{ route('parent.fees.index') }}">
            <div class="sb-hdr {{ request()->routeIs('parent.fees.*') ? 'open' : '' }}" onclick="handleSbHdrClick(this, event)">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="m3d-icon">
                          <defs>
                            <linearGradient id="stu-fee-bg" x1="0" y1="0" x2="64" y2="64"><stop offset="0%" stop-color="#06b6d4"/><stop offset="100%" stop-color="#0e7490"/></linearGradient>
                            <filter id="stu-fee-sh" x1="0" y1="0" width="64" height="64" filterUnits="userSpaceOnUse"><feDropShadow dx="0" dy="4" stdDeviation="3" flood-color="#164e63" flood-opacity="0.35"/></filter>
                          </defs>
                          <rect x="4" y="4" width="56" height="56" rx="16" fill="url(#stu-fee-bg)" filter="url(#stu-fee-sh)"/>
                          <circle cx="32" cy="32" r="18" fill="#ffffff"/>
                          <path d="M26 23H38M26 28H38M26 23C29 23 34 23.5 34 28C34 32.5 29 33 26 33H31L37 42" stroke="#0891b2" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <span class="sb-hdr-title">Finance & Fees</span>
                </div>
                <i class="fas fa-arrow-up-right-from-square sb-hdr-arrow" style="font-size:10px; opacity:0.6;"></i>
            </div>
            <ul class="sb-submenu {{ request()->routeIs('parent.fees.*') ? 'open' : '' }}">
                <li class="{{ request()->routeIs('parent.fees.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.fees.index') }}">
                        <span class="sb-submenu-label">Fee Details & Dues</span>
                        <i class="fas fa-indian-rupee-sign sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 7. Communication -->
        <div class="sb-group {{ request()->routeIs('parent.events.*') || request()->routeIs('parent.notices.*') || request()->routeIs('parent.surveys.*') || request()->routeIs('parent.chat.*') ? 'active-group' : '' }}">
            <div class="sb-hdr {{ request()->routeIs('parent.events.*') || request()->routeIs('parent.notices.*') || request()->routeIs('parent.surveys.*') || request()->routeIs('parent.chat.*') ? 'open' : '' }}" onclick="handleSbHdrClick(this, event)">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="m3d-icon">
                          <defs>
                            <linearGradient id="stu-com-bg" x1="0" y1="0" x2="64" y2="64"><stop offset="0%" stop-color="#f97316"/><stop offset="100%" stop-color="#c2410c"/></linearGradient>
                            <filter id="stu-com-sh" x1="0" y1="0" width="64" height="64" filterUnits="userSpaceOnUse"><feDropShadow dx="0" dy="4" stdDeviation="3" flood-color="#7c2d12" flood-opacity="0.35"/></filter>
                          </defs>
                          <rect x="4" y="4" width="56" height="56" rx="16" fill="url(#stu-com-bg)" filter="url(#stu-com-sh)"/>
                          <path d="M16 23C16 18.5 20 15 25 15H39C44 15 48 18.5 48 23V35C48 39.5 44 43 39 43H28L18 49V43C16.8 41.5 16 38.5 16 35V23Z" fill="#ffffff"/>
                          <circle cx="26" cy="29" r="3" fill="#ea580c"/>
                          <circle cx="33" cy="29" r="3" fill="#ea580c"/>
                          <circle cx="40" cy="29" r="3" fill="#ea580c"/>
                        </svg>
                    </div>
                    <span class="sb-hdr-title">Communication</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu {{ request()->routeIs('parent.events.*') || request()->routeIs('parent.notices.*') || request()->routeIs('parent.surveys.*') || request()->routeIs('parent.chat.*') ? 'open' : '' }}">
                <li class="{{ request()->routeIs('parent.notices.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.notices.index') }}">
                        <span class="sb-submenu-label">Notice Board</span>
                        <i class="fas fa-bullhorn sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.events.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.events.index') }}">
                        <span class="sb-submenu-label">Events & Calendar</span>
                        <i class="fas fa-calendar-days sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.chat.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.chat.index') }}">
                        <span class="sb-submenu-label">Chat Messenger</span>
                        <i class="fas fa-comment-dots sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.surveys.index') ? 'active' : '' }}">
                    <a href="{{ route('parent.surveys.index') }}">
                        <span class="sb-submenu-label">Surveys & Polls</span>
                        <i class="fas fa-poll sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>

        <!-- 8. Profile & Settings -->
        <div class="sb-group {{ request()->routeIs('parent.profile') || request()->routeIs('parent.settings') ? 'active-group' : '' }}">
            <div class="sb-hdr {{ request()->routeIs('parent.profile') || request()->routeIs('parent.settings') ? 'open' : '' }}" onclick="handleSbHdrClick(this, event)">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon">
                        <svg viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" class="m3d-icon">
                          <defs>
                            <linearGradient id="stu-set-bg" x1="0" y1="0" x2="64" y2="64"><stop offset="0%" stop-color="#6366f1"/><stop offset="100%" stop-color="#4338ca"/></linearGradient>
                            <filter id="stu-set-sh" x1="0" y1="0" width="64" height="64" filterUnits="userSpaceOnUse"><feDropShadow dx="0" dy="4" stdDeviation="3" flood-color="#312e81" flood-opacity="0.35"/></filter>
                          </defs>
                          <rect x="4" y="4" width="56" height="56" rx="16" fill="url(#stu-set-bg)" filter="url(#stu-set-sh)"/>
                          <circle cx="32" cy="32" r="7" fill="#ffffff"/>
                          <path d="M32 17V21M32 43V47M47 32H43M21 32H17M42.6 21.4L39.8 24.2M24.2 39.8L21.4 42.6M42.6 42.6L39.8 39.8M24.2 24.2L21.4 21.4" stroke="#ffffff" stroke-width="3.5" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <span class="sb-hdr-title">My Account</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu {{ request()->routeIs('parent.profile') || request()->routeIs('parent.settings') ? 'open' : '' }}">
                <li class="{{ request()->routeIs('parent.profile') ? 'active' : '' }}">
                    <a href="{{ route('parent.profile') }}">
                        <span class="sb-submenu-label">Student Profile</span>
                        <i class="fas fa-user-circle sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->routeIs('parent.settings') ? 'active' : '' }}">
                    <a href="{{ route('parent.settings') }}">
                        <span class="sb-submenu-label">Account Settings</span>
                        <i class="fas fa-gear sb-submenu-icon"></i>
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

<!-- ══ MOBILE MODULE SUB-PAGES BOTTOM SHEET POPUP MODAL ══ -->
<div id="sbModulePopupModal" class="sb-module-popup-modal" onclick="closeModulePopupModal(event)">
    <div class="sb-mpm-card" onclick="event.stopPropagation()">
        <div class="sb-mpm-header">
            <div class="sb-mpm-header-left">
                <div class="sb-mpm-icon" id="sbMpmIcon">
                    <i class="fas fa-cubes"></i>
                </div>
                <div class="sb-mpm-title" id="sbMpmTitle">Module Features</div>
            </div>
            <button type="button" class="sb-mpm-close" onclick="closeModulePopupModal(event)" aria-label="Close">
                <i class="fas fa-xmark"></i>
            </button>
        </div>
        <div class="sb-mpm-body" id="sbMpmBody">
            <!-- Dynamically populated sub-module items -->
        </div>
    </div>
</div>

<style>
/* ─── BASE & DESKTOP SIDEBAR STYLING ─────────────────────── */
.m3d-icon {
    width: 24px;
    height: 24px;
    display: block;
}

@media (min-width: 1025px) {
    .sidebar {
        width: 230px !important;
        min-width: 230px !important;
        background: #111827 !important;
        height: 100vh !important;
        position: fixed !important;
        left: 0 !important;
        top: 0 !important;
        display: flex !important;
        flex-direction: column !important;
        z-index: 200 !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        box-shadow: 4px 0 20px rgba(0, 0, 0, 0.15) !important;
    }
    .sidebar::-webkit-scrollbar {
        width: 4px;
    }
    .sidebar::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 4px;
    }
    .main {
        margin-left: 230px !important;
        min-width: 0 !important;
    }
    .sb-logo {
        padding: 16px 14px 12px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
        text-decoration: none !important;
        flex-shrink: 0 !important;
    }
    .sb-close-btn {
        display: none !important;
    }
    .sb-student {
        margin: 10px 10px !important;
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 12px !important;
        padding: 10px !important;
        flex-shrink: 0 !important;
        text-align: center !important;
    }
    .sb-stu-avatar {
        width: 42px !important;
        height: 42px !important;
        border-radius: 50% !important;
        background: linear-gradient(135deg, #f59e0b, #ea580c) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #fff !important;
        font-size: 15px !important;
        font-weight: 800 !important;
        margin: 0 auto 5px !important;
        overflow: hidden !important;
    }
    .sb-stu-avatar img {
        width: 100% !important;
        height: 100% !important;
        object-fit: cover !important;
    }
    .sb-stu-name {
        color: #fff !important;
        font-size: 12px !important;
        font-weight: 700 !important;
        margin-bottom: 1px !important;
    }
    .sb-stu-class {
        color: rgba(255, 255, 255, 0.6) !important;
        font-size: 10.5px !important;
    }
    .sb-admit {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px !important;
        background: rgba(245, 158, 11, 0.15) !important;
        color: #f59e0b !important;
        font-size: 9.5px !important;
        font-weight: 700 !important;
        border-radius: 20px !important;
        padding: 1px 7px !important;
        margin-top: 4px !important;
    }

    /* Desktop Navigation List */
    .sb-nav {
        list-style: none !important;
        padding: 6px 8px !important;
        flex: 1 !important;
        overflow-y: auto !important;
        overflow-x: hidden !important;
        display: block !important;
    }
    .sb-group {
        margin-bottom: 3px !important;
        display: block !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
        border-radius: 0 !important;
    }
    .sb-hdr {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        flex-direction: row !important;
        padding: 8px 10px !important;
        cursor: pointer !important;
        user-select: none !important;
        color: rgba(255, 255, 255, 0.78) !important;
        transition: all 0.2s ease !important;
        border-radius: 8px !important;
        margin: 1px 0 !important;
        width: 100% !important;
    }
    .sb-hdr:hover {
        background: rgba(255, 255, 255, 0.08) !important;
        color: #ffffff !important;
    }
    .sb-hdr.open {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.06) !important;
    }
    .sb-hdr-left {
        display: flex !important;
        align-items: center !important;
        flex-direction: row !important;
        gap: 9px !important;
        min-width: 0 !important;
        flex: 1 !important;
    }
    .sb-hdr-icon {
        width: 24px !important;
        height: 24px !important;
        min-width: 24px !important;
        min-height: 24px !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        filter: none !important;
    }
    .sb-hdr-icon svg, .sb-hdr-icon .m3d-icon {
        width: 24px !important;
        height: 24px !important;
        display: block !important;
    }
    .sb-hdr-title {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif !important;
        color: inherit !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        margin-top: 0 !important;
        white-space: nowrap !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: block !important;
    }
    .sb-hdr-arrow {
        display: inline-block !important;
        font-size: 10px !important;
        color: rgba(255, 255, 255, 0.4) !important;
        transition: transform 0.2s ease !important;
        flex-shrink: 0 !important;
    }
    .sb-hdr.open .sb-hdr-arrow {
        transform: rotate(180deg) !important;
        color: #f59e0b !important;
    }

    /* Submenu on Desktop */
    .sb-submenu {
        list-style: none !important;
        padding: 2px 0 4px 12px !important;
        display: none !important;
        height: auto !important;
        max-height: none !important;
        opacity: 1 !important;
        visibility: visible !important;
        pointer-events: auto !important;
        overflow: visible !important;
    }
    .sb-submenu.open {
        display: block !important;
    }
    .sb-submenu li {
        margin-bottom: 1px !important;
    }
    .sb-submenu a {
        display: flex !important;
        align-items: center !important;
        justify-content: space-between !important;
        padding: 6px 10px !important;
        border-radius: 6px !important;
        color: rgba(255, 255, 255, 0.65) !important;
        font-size: 11px !important;
        font-weight: 500 !important;
        text-decoration: none !important;
        transition: all 0.18s ease !important;
    }
    .sb-submenu a:hover {
        color: #ffffff !important;
        background: rgba(255, 255, 255, 0.08) !important;
    }
    .sb-submenu li.active a {
        color: #f59e0b !important;
        font-weight: 700 !important;
        background: rgba(245, 158, 11, 0.12) !important;
    }
    .sb-submenu-label {
        display: flex !important;
        align-items: center !important;
        gap: 6px !important;
    }
    .sb-submenu-icon {
        font-size: 9.5px !important;
        color: #f59e0b !important;
        flex-shrink: 0 !important;
        opacity: 0.8 !important;
    }

    .sb-bottom {
        padding: 10px 12px !important;
        border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
        background: transparent !important;
        flex-shrink: 0 !important;
    }
    .sb-logout {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 8px !important;
        color: #f87171 !important;
        background: rgba(239, 68, 68, 0.1) !important;
        border: 1px solid rgba(239, 68, 68, 0.2) !important;
        font-size: 11.5px !important;
        font-weight: 700 !important;
        padding: 7px 10px !important;
        border-radius: 8px !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
    }
    .sb-logout:hover {
        background: #ef4444 !important;
        color: #ffffff !important;
    }
}

/* ─── MOBILE ICON-BASED APP GRID SIDEBAR (<= 1024px) ───────── */
@media (max-width: 1024px) {
    .sidebar {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        bottom: 0 !important;
        width: 100vw !important;
        max-width: 100vw !important;
        height: 100vh !important;
        height: 100dvh !important;
        z-index: 1005 !important;
        transform: translateX(-100%) !important;
        display: flex !important;
        flex-direction: column !important;
        background: linear-gradient(180deg, #0f172a 0%, #1e1b4b 60%, #0f172a 100%) !important;
        box-shadow: none !important;
        transition: transform 0.35s cubic-bezier(0.16, 1, 0.3, 1) !important;
        overflow: hidden !important;
    }
    .sidebar.open {
        transform: translateX(0) !important;
    }

    .sb-logo {
        padding: 14px 16px !important;
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-bottom-left-radius: 20px !important;
        border-bottom-right-radius: 20px !important;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3) !important;
        flex-shrink: 0 !important;
    }
    .sb-close-btn {
        display: flex !important;
        width: 32px !important;
        height: 32px !important;
        border-radius: 50% !important;
        background: rgba(255, 255, 255, 0.12) !important;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        color: #ffffff !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        font-size: 13px !important;
        transition: all 0.2s ease !important;
        flex-shrink: 0 !important;
    }
    .sb-close-btn:active {
        transform: scale(0.92) !important;
        background: rgba(255, 255, 255, 0.25) !important;
    }

    .sb-student {
        margin: 10px 12px 6px 12px !important;
        padding: 10px 12px !important;
        background: rgba(255, 255, 255, 0.06) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 14px !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        text-align: left !important;
    }
    .sb-stu-avatar {
        width: 38px !important;
        height: 38px !important;
        border-radius: 10px !important;
        font-size: 14px !important;
        margin: 0 !important;
        flex-shrink: 0 !important;
    }
    .sb-stu-name {
        font-size: 12.5px !important;
        margin-bottom: 1px !important;
    }
    .sb-stu-class {
        font-size: 10.5px !important;
    }
    .sb-admit {
        font-size: 9px !important;
        margin-top: 3px !important;
        padding: 1px 6px !important;
    }

    /* 3-Column Modern App Grid */
    .sb-nav {
        padding: 10px 12px calc(24px + env(safe-area-inset-bottom, 16px)) 12px !important;
        flex: 1 !important;
        overflow-y: auto !important;
        -webkit-overflow-scrolling: touch !important;
        display: grid !important;
        grid-template-columns: repeat(3, 1fr) !important;
        gap: 10px 8px !important;
        align-content: start !important;
    }
    .sb-group {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: flex-start !important;
        text-align: center !important;
        margin: 0 !important;
        padding: 10px 4px !important;
        position: relative !important;
        background: rgba(255, 255, 255, 0.05) !important;
        border-radius: 16px !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.18) !important;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), background 0.2s ease, border-color 0.2s ease !important;
        cursor: pointer !important;
        -webkit-tap-highlight-color: transparent !important;
    }
    .sb-group:active {
        transform: translateY(-2px) scale(0.96) !important;
        background: rgba(255, 255, 255, 0.12) !important;
        border-color: rgba(245, 158, 11, 0.5) !important;
    }
    .sb-hdr {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        text-align: center !important;
        padding: 0 !important;
        margin: 0 !important;
        min-height: unset !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        width: 100% !important;
        cursor: pointer !important;
    }
    .sb-hdr-left {
        display: flex !important;
        flex-direction: column !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 6px !important;
        width: 100% !important;
    }
    .sb-hdr-icon {
        width: 52px !important;
        height: 52px !important;
        min-width: 52px !important;
        min-height: 52px !important;
        border-radius: 14px !important;
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-shrink: 0 !important;
        transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1) !important;
        filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.3)) !important;
    }
    .sb-hdr-icon svg, .sb-hdr-icon .m3d-icon {
        width: 52px !important;
        height: 52px !important;
        display: block !important;
    }
    .sb-group:hover .sb-hdr-icon, .sb-group:active .sb-hdr-icon {
        transform: translateY(-2px) scale(1.06) !important;
    }
    .sb-hdr-title {
        font-family: 'Plus Jakarta Sans', 'Inter', sans-serif !important;
        font-size: 11px !important;
        font-weight: 700 !important;
        color: #f1f5f9 !important;
        text-align: center !important;
        line-height: 1.25 !important;
        margin-top: 4px !important;
        max-width: 100% !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 2 !important;
        -webkit-box-orient: vertical !important;
    }
    .sb-hdr-arrow {
        display: none !important;
    }
    .sb-submenu {
        display: none !important;
        height: 0 !important;
        max-height: 0 !important;
        opacity: 0 !important;
        visibility: hidden !important;
        pointer-events: none !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
    }

    .sb-group.active-group {
        border-color: rgba(245, 158, 11, 0.8) !important;
        background: linear-gradient(180deg, rgba(245, 158, 11, 0.15) 0%, rgba(30, 27, 75, 0.6) 100%) !important;
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.25) !important;
    }
    .sb-group.active-group::after {
        content: '';
        position: absolute;
        top: 6px;
        right: 6px;
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 6px #10b981;
    }
    .sb-group.active-group .sb-hdr-title {
        color: #fef08a !important;
        font-weight: 800 !important;
    }

    .sb-bottom {
        padding: 10px 14px !important;
        background: rgba(15, 23, 42, 0.8) !important;
        border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
        flex-shrink: 0 !important;
    }
}

@media (max-width: 360px) {
    .sb-nav {
        grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
        gap: 6px 4px !important;
    }
    .sb-hdr-icon {
        width: 44px !important;
        height: 44px !important;
        min-width: 44px !important;
        min-height: 44px !important;
    }
    .sb-hdr-icon svg, .sb-hdr-icon .m3d-icon {
        width: 44px !important;
        height: 44px !important;
    }
    .sb-hdr-title {
        font-size: 9.5px !important;
    }
}

/* ─── MODULE SUB-PAGES BOTTOM SHEET POPUP MODAL ───────────── */
.sb-module-popup-modal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    bottom: 0 !important;
    width: 100vw !important;
    height: 100vh !important;
    height: 100dvh !important;
    background: rgba(15, 23, 42, 0.72) !important;
    -webkit-backdrop-filter: blur(8px) !important;
    backdrop-filter: blur(8px) !important;
    z-index: 100050 !important;
    display: flex !important;
    align-items: flex-end !important;
    justify-content: center !important;
    opacity: 0 !important;
    visibility: hidden !important;
    pointer-events: none !important;
    transition: opacity 0.28s cubic-bezier(0.16, 1, 0.3, 1), visibility 0.28s ease !important;
}
.sb-module-popup-modal.active {
    opacity: 1 !important;
    visibility: visible !important;
    pointer-events: auto !important;
}
.sb-mpm-card {
    width: 100% !important;
    max-width: 460px !important;
    background: linear-gradient(180deg, #1e1b4b 0%, #0f172a 100%) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    border-top-left-radius: 24px !important;
    border-top-right-radius: 24px !important;
    box-shadow: 0 -10px 40px rgba(0, 0, 0, 0.5) !important;
    padding: 20px 18px 24px 18px !important;
    transform: translateY(100%) !important;
    transition: transform 0.32s cubic-bezier(0.16, 1, 0.3, 1) !important;
    max-height: calc(100vh - 80px) !important;
    max-height: calc(100dvh - 80px) !important;
    margin-bottom: max(6px, env(safe-area-inset-bottom)) !important;
    display: flex !important;
    flex-direction: column !important;
}
.sb-module-popup-modal.active .sb-mpm-card {
    transform: translateY(0) !important;
}
.sb-mpm-header {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding-bottom: 14px !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    margin-bottom: 14px !important;
}
.sb-mpm-header-left {
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
}
.sb-mpm-icon {
    width: 42px !important;
    height: 42px !important;
    border-radius: 12px !important;
    background: linear-gradient(135deg, #f59e0b, #d97706) !important;
    color: #ffffff !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 18px !important;
    box-shadow: 0 4px 12px rgba(245, 158, 11, 0.4) !important;
    overflow: hidden !important;
}
.sb-mpm-icon svg, .sb-mpm-icon .m3d-icon {
    width: 42px !important;
    height: 42px !important;
}
.sb-mpm-title {
    font-size: 16px !important;
    font-weight: 800 !important;
    color: #ffffff !important;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}
.sb-mpm-close {
    width: 32px !important;
    height: 32px !important;
    border-radius: 50% !important;
    background: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    font-size: 13px !important;
    transition: all 0.2s ease !important;
}
.sb-mpm-close:active {
    transform: scale(0.92) !important;
    background: rgba(255, 255, 255, 0.2) !important;
}
.sb-mpm-body {
    overflow-y: auto !important;
    -webkit-overflow-scrolling: touch !important;
    display: flex !important;
    flex-direction: column !important;
    gap: 8px !important;
    padding-bottom: 12px !important;
}
.sb-mpm-item {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    padding: 12px 14px !important;
    border-radius: 12px !important;
    background: rgba(255, 255, 255, 0.06) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    color: #ffffff !important;
    font-size: 13px !important;
    font-weight: 700 !important;
    text-decoration: none !important;
    transition: all 0.18s ease !important;
}
.sb-mpm-item:hover, .sb-mpm-item:active {
    background: linear-gradient(135deg, #f59e0b, #d97706) !important;
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
    transform: translateX(3px) !important;
}
.sb-mpm-item.active {
    background: linear-gradient(135deg, #f59e0b, #b45309) !important;
    color: #ffffff !important;
    border-color: #fde047 !important;
}
.sb-mpm-item i {
    font-size: 12px !important;
    color: #fde047 !important;
}
.sb-mpm-item:hover i, .sb-mpm-item:active i {
    color: #ffffff !important;
}
</style>

<script>
// ── STUDENT SIDEBAR (DESKTOP ACCORDION & MOBILE POPUP) SCRIPT ──
function openModulePopupModal(iconHtml, titleText, itemsHtml) {
    const modal = document.getElementById('sbModulePopupModal');
    const iconEl = document.getElementById('sbMpmIcon');
    const titleEl = document.getElementById('sbMpmTitle');
    const bodyEl = document.getElementById('sbMpmBody');
    if (!modal) return;
    if (iconEl) iconEl.innerHTML = iconHtml;
    if (titleEl) titleEl.textContent = titleText;
    if (bodyEl) bodyEl.innerHTML = itemsHtml;
    modal.classList.add('active');
}

function closeModulePopupModal(e) {
    if (e && e.preventDefault) { e.preventDefault(); e.stopPropagation(); }
    const modal = document.getElementById('sbModulePopupModal');
    if (modal) modal.classList.remove('active');
}

function handleSbHdrClick(hdr, event) {
    if (!hdr) return;
    if (event) {
        event.stopPropagation();
    }

    const group = hdr.closest('.sb-group');
    if (!group) return;
    const directLink = group.dataset.directLink;
    const submenu = group.querySelector('.sb-submenu');

    if (window.innerWidth <= 1024) {
        // Mobile App Grid Mode
        if (directLink && (!submenu || submenu.querySelectorAll('li a').length <= 1)) {
            window.location.href = directLink;
            return;
        }

        if (submenu && submenu.classList.contains('sb-submenu')) {
            const links = submenu.querySelectorAll('li a');
            if (links.length > 0) {
                const iconEl = hdr.querySelector('.sb-hdr-icon');
                const titleEl = hdr.querySelector('.sb-hdr-title');
                const iconHtml = iconEl ? iconEl.innerHTML : '<i class="fas fa-cubes"></i>';
                const titleText = titleEl ? titleEl.textContent.trim() : 'Features';

                let itemsHtml = '';
                links.forEach(link => {
                    const href = link.getAttribute('href');
                    const labelEl = link.querySelector('.sb-submenu-label');
                    const label = labelEl ? labelEl.textContent.trim() : link.textContent.trim();
                    const iconElSub = link.querySelector('.sb-submenu-icon');
                    const icon = iconElSub ? iconElSub.outerHTML : '<i class="fas fa-arrow-up-right-from-square"></i>';
                    const isActive = link.closest('li')?.classList.contains('active') ? 'active' : '';
                    itemsHtml += `
                        <a href="${href}" class="sb-mpm-item ${isActive}" onclick="closeSidebar(); closeModulePopupModal();">
                            <span>${label}</span>
                            ${icon}
                        </a>
                    `;
                });
                openModulePopupModal(iconHtml, titleText, itemsHtml);
                return;
            }
        }
    } else {
        // Desktop Accordion Mode
        if (directLink && (!submenu || submenu.querySelectorAll('li a').length <= 1)) {
            window.location.href = directLink;
            return;
        }

        if (submenu && submenu.classList.contains('sb-submenu')) {
            const isCurrentlyOpen = submenu.classList.contains('open');
            if (isCurrentlyOpen) {
                submenu.classList.remove('open');
                hdr.classList.remove('open');
            } else {
                submenu.classList.add('open');
                hdr.classList.add('open');
            }
        }
    }
}
</script>
