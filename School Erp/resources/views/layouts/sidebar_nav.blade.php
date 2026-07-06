@use('App\Support\StaffAccessHelper')
@use('Illuminate\Support\Facades\Auth')

<div class="sb-nav">
        <!-- Module Search Bar -->
        <div class="sb-search-wrapper" style="padding: 4px 10px 12px 10px;">
            <div class="sb-search-box" style="position: relative; display: flex; align-items: center;">
                <i class="fas fa-search" style="position: absolute; left: 10px; font-size: 11.5px; pointer-events: none;"></i>
                <input type="text" id="sbModuleSearch" placeholder="Search modules..." style="width: 100%; height: 34px; padding: 6px 12px 6px 28px; border: 1px solid var(--sidebar-stitch); border-radius: 8px; font-size: 12.5px; font-weight: 500; font-family: inherit; transition: all 0.2s ease; outline: none;">
            </div>
        </div>

        @if(Auth::check() && (Auth::user()->hasRole('teacher') || Auth::user()->hasRole('staff') || Auth::user()->hasRole('accountant') || Auth::user()->role === 'teacher'))
            <!-- Teacher / Staff Dedicated Dashboard Return Link -->
            <div style="margin: 4px 10px 14px 10px;">
                <a href="{{ route('teacher.dashboard') }}" style="display:flex; align-items:center; justify-content:center; gap:8px; padding:10px 12px; background:linear-gradient(135deg, #2563eb, #1d4ed8); color:#fff; text-decoration:none; border-radius:10px; font-weight:700; font-size:13px; box-shadow:0 4px 12px rgba(37,99,235,0.3); transition:all .2s;">
                    <i class="fas fa-arrow-left" style="font-size:12px;"></i>
                    <span>Teacher Dashboard</span>
                </a>
            </div>
        @endif

        <!-- 1. Overview -->
        @if(StaffAccessHelper::hasAccess('overview'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-house"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('overview', 'Overview') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('overview', 'mis_report'))
                <li class="{{ request()->is('school/dashboard/mis-report') ? 'active' : '' }}">
                    <a href="{{ route('school.dashboard.mis-report') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('overview', 'mis_report', 'Daily MIS Report') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('overview', 'admin_dashboard'))
                <li class="{{ request()->is('school/dashboard') ? 'active' : '' }}">
                    <a href="{{ route('school.dashboard') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('overview', 'admin_dashboard', 'Admin Dashboard') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 2. Institute Info -->
        @if(StaffAccessHelper::hasAccess('institute_info'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-building"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('institute_info', 'Institute Info') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('institute_info', 'basic_info'))
                <li class="{{ request()->is('school/settings/institute-info') ? 'active' : '' }}">
                    <a href="{{ route('school.settings.institute-info') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('institute_info', 'basic_info', 'Basic Institute Info') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('institute_info', 'udise'))
                <li class="{{ request()->is('school/settings/udise') ? 'active' : '' }}">
                    <a href="{{ route('school.settings.udise') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('institute_info', 'udise', 'UDISE') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 3. Admin Role Management -->
        @if(StaffAccessHelper::hasAccess('admin_role_management'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-users"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('admin_role_management', 'Admin Role Management') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('admin_role_management', 'role_category'))
                <li class="{{ request()->is('school/role-management/roles') ? 'active' : '' }}">
                    <a href="{{ route('school.roles.index') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('admin_role_management', 'role_category', 'Role Category') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('admin_role_management', 'staff_access'))
                <li class="{{ request()->is('school/role-management/staff-access') ? 'active' : '' }}">
                    <a href="{{ route('school.roles.staff-access') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('admin_role_management', 'staff_access', 'Staff Access Control') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 4. Password Management -->
        @if(StaffAccessHelper::hasAccess('password_management'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-lock"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('password_management', 'Password Management') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('password_management', 'reset_password'))
                <li class="{{ request()->is('school/settings/reset-password') ? 'active' : '' }}">
                    <a href="{{ route('school.settings.reset-password') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('password_management', 'reset_password', 'Reset Password') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 5. Staff Management -->
        @if(StaffAccessHelper::hasAccess('staff_management'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-user-cog"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('staff_management', 'Staff Management') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('staff_management', 'staff_directory'))
                <li class="{{ request()->is('school/staff') && !request()->is('school/staff/create') && !request()->is('school/staff/import') && !request()->is('school/staff/bulk-photo') && !request()->is('school/staff/bulk-attendance') ? 'active' : '' }}">
                    <a href="{{ route('school.staff.index') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('staff_management', 'staff_directory', 'Staff Directory') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('staff_management', 'add_staff'))
                <li class="{{ request()->is('school/staff/create') ? 'active' : '' }}">
                    <a href="{{ route('school.staff.create') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('staff_management', 'add_staff', 'Add Staff') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('staff_management', 'bulk_import'))
                <li class="{{ request()->is('school/staff/import') ? 'active' : '' }}">
                    <a href="{{ route('school.staff.import') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('staff_management', 'bulk_import', 'Bulk Staff Import') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('staff_management', 'bulk_photo'))
                <li class="{{ request()->is('school/staff/bulk-photo') ? 'active' : '' }}">
                    <a href="{{ route('school.staff.bulk-photo') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('staff_management', 'bulk_photo', 'Bulk Photo Upload') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 6. Class, Subject & Teacher Assignment -->
        @if(StaffAccessHelper::hasAccess('class_subject_teacher'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-book"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('class_subject_teacher', 'Class, Subject & Teacher Assignment') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('class_subject_teacher', 'class_overview'))
                <li class="{{ request()->is('school/assignments/class-overview') ? 'active' : '' }}">
                    <a href="{{ route('school.assignments.class-overview') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('class_subject_teacher', 'class_overview', 'Class Overview') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('class_subject_teacher', 'add_class'))
                <li class="{{ request()->is('school/assignments/classes') ? 'active' : '' }}">
                    <a href="{{ route('school.assignments.classes') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('class_subject_teacher', 'add_class', 'Add/modify class') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('class_subject_teacher', 'add_subject'))
                <li class="{{ request()->is('school/assignments/subjects') ? 'active' : '' }}">
                    <a href="{{ route('school.assignments.subjects') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('class_subject_teacher', 'add_subject', 'Add/modify subjects') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('class_subject_teacher', 'assign_teacher'))
                <li class="{{ request()->is('school/assignments/teachers') ? 'active' : '' }}">
                    <a href="{{ route('school.assignments.teachers') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('class_subject_teacher', 'assign_teacher', 'Assign teachers') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 7. Time Table -->
        @if(StaffAccessHelper::hasAccess('timetable'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-calendar-days"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('timetable', 'Time Table') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('timetable', 'class_timetable'))
                <li class="{{ request()->is('school/timetable/class*') ? 'active' : '' }}">
                    <a href="{{ route('school.timetable.class') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('timetable', 'class_timetable', 'Class Timetable') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('timetable', 'group_timetable'))
                <li class="{{ request()->is('school/timetable/group*') ? 'active' : '' }}">
                    <a href="{{ route('school.timetable.group') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('timetable', 'group_timetable', 'Group Timetable') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('timetable', 'teacher_timetable'))
                <li class="{{ request()->is('school/timetable/teacher*') ? 'active' : '' }}">
                    <a href="{{ route('school.timetable.teacher') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('timetable', 'teacher_timetable', 'Teacher Timetable') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('timetable', 'teacher_substitution'))
                <li class="{{ request()->is('school/timetable/substitution*') ? 'active' : '' }}">
                    <a href="{{ route('school.timetable.substitution') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('timetable', 'teacher_substitution', 'Teacher Substitution') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 8. Student Management -->
        @if(StaffAccessHelper::hasAccess('student_management'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-graduation-cap"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('student_management', 'Student Management') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('student_management', 'add_student'))
                <li class="{{ request()->is('school/students/create') ? 'active' : '' }}">
                    <a href="{{ route('school.students.create') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('student_management', 'add_student', 'Add Student') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('student_management', 'bulk_student_import'))
                <li class="{{ request()->is('school/student-mgmt/import*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.import') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('student_management', 'bulk_student_import', 'Bulk Student Import') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('student_management', 'bulk_photo_doc'))
                <li class="{{ request()->is('school/student-mgmt/bulk-photo*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.bulk-photo') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('student_management', 'bulk_photo_doc', 'Bulk Photo/Document Upload') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('student_management', 'optional_subject'))
                <li class="{{ request()->is('school/student-mgmt/optional-subject*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.optional-subject') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('student_management', 'optional_subject', 'Student Optional Subject Allocation') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('student_management', 'student_directory'))
                <li class="{{ request()->is('school/students') && !request()->is('school/students/create') ? 'active' : '' }}">
                    <a href="{{ route('school.students.index') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('student_management', 'student_directory', 'Student Directory') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('student_management', 'admission_report'))
                <li class="{{ request()->is('school/student-mgmt/admission-report*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.admission-report') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('student_management', 'admission_report', 'New Admission Report') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('student_management', 'siblings'))
                <li class="{{ request()->is('school/student-mgmt/siblings*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.siblings') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('student_management', 'siblings', 'Siblings List') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('student_management', 'student_attendance'))
                <li class="{{ request()->is('school/attendance/students') && !request()->is('school/attendance/students/report') && !request()->is('school/attendance/students/daily') && !request()->is('school/attendance/students/stats') ? 'active' : '' }}">
                    <a href="{{ route('school.attendance.students.index') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('student_management', 'student_attendance', 'Student Attendance') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('student_management', 'bulk_attendance'))
                <li class="{{ request()->is('school/student-mgmt/bulk-attendance*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.bulk-attendance') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('student_management', 'bulk_attendance', 'Student Mark Bulk Attendance') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('student_management', 'student_report'))
                <li class="{{ request()->is('school/student-mgmt/report*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.report') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('student_management', 'student_report', 'Student Report') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 9. Attendance -->
        @if(StaffAccessHelper::hasAccess('attendance'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-calendar-check"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('attendance', 'Attendance') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('attendance', 'student_attendance'))
                <li class="{{ (request()->is('school/attendance/students') && !request()->is('school/attendance/students/report') && !request()->is('school/attendance/students/daily') && !request()->is('school/attendance/students/stats') && !request()->is('school/attendance/students/marking-report')) ? 'active' : '' }}">
                    <a href="{{ route('school.attendance.students.index') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('attendance', 'student_attendance', 'Student Attendance') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('attendance', 'student_bulk_attendance'))
                <li class="{{ request()->is('school/student-mgmt/bulk-attendance*') ? 'active' : '' }}">
                    <a href="{{ route('school.student-mgmt.bulk-attendance') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('attendance', 'student_bulk_attendance', 'Student Mark Bulk Attendance') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('attendance', 'staff_attendance'))
                <li class="{{ request()->is('school/attendance/staff') ? 'active' : '' }}">
                    <a href="{{ route('school.attendance.staff.index') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('attendance', 'staff_attendance', 'Staff Attendance') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('attendance', 'staff_bulk_attendance'))
                <li class="{{ request()->is('school/staff/bulk-attendance') ? 'active' : '' }}">
                    <a href="{{ route('school.staff.bulk-attendance') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('attendance', 'staff_bulk_attendance', 'Staff Mark Bulk Attendance') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('attendance', 'student_att_report'))
                <li class="{{ request()->is('school/attendance/students/marking-report') ? 'active' : '' }}">
                    <a href="{{ route('school.attendance.students.marking-report') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('attendance', 'student_att_report', 'Student Attendance Marking Report') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 9. Download Statistics -->
        @if(StaffAccessHelper::hasAccess('download_statistics'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-chart-pie"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('download_statistics', 'Download Statistics') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('download_statistics', 'student_download'))
                <li class="{{ request()->is('school/downloads/student-status*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.student-status') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('download_statistics', 'student_download', 'Student Download Status') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('download_statistics', 'staff_download'))
                <li class="{{ request()->is('school/downloads/staff-status*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.staff-status') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('download_statistics', 'staff_download', 'Staff Download Status') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('download_statistics', 'parent_download'))
                <li class="{{ request()->is('school/downloads/parent-status*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.parent-status') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('download_statistics', 'parent_download', 'Parent Download Status') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('download_statistics', 'student_activity'))
                <li class="{{ request()->is('school/downloads/student-activity*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.student-activity') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('download_statistics', 'student_activity', 'Student Activity') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('download_statistics', 'staff_activity'))
                <li class="{{ request()->is('school/downloads/staff-activity*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.staff-activity') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('download_statistics', 'staff_activity', 'Staff Activity') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('download_statistics', 'parent_activity'))
                <li class="{{ request()->is('school/downloads/parent-activity*') ? 'active' : '' }}">
                    <a href="{{ route('school.downloads.parent-activity') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('download_statistics', 'parent_activity', 'Parent Activity') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 10. Fee Management -->
        @if(StaffAccessHelper::hasAccess('fee_management'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-indian-rupee-sign"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('fee_management', 'Fee Management') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                {{--
                @if(StaffAccessHelper::hasAccess('fee_management', 'fee_configuration'))
                <li class="{{ request()->is('school/fees/configuration*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.configuration') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('fee_management', 'fee_configuration', 'Fee Configuration') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                --}}
                @if(StaffAccessHelper::hasAccess('fee_management', 'fee_basics'))
                <li class="{{ request()->is('school/fees/basics*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.basics') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('fee_management', 'fee_basics', 'Fee Basics') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('fee_management', 'class_wise_fee'))
                <li class="{{ request()->is('school/fees/class-wise*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.class-wise') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('fee_management', 'class_wise_fee', 'Class-wise Fee') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('fee_management', 'student_wise_fee'))
                <li class="{{ request()->is('school/fees/student-wise*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.student-wise') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('fee_management', 'student_wise_fee', 'Student-wise Fee') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('fee_management', 'schedule_mapper'))
                <li class="{{ request()->is('school/fees/schedule-mapper*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.schedule-mapper') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('fee_management', 'schedule_mapper', 'Student Class & Fee Schedule Mapper') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('fee_management', 'fee_receipts'))
                <li class="{{ request()->is('school/fees/receipts*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.receipts') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('fee_management', 'fee_receipts', 'Fee Receipts') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('fee_management', 'pending_cheques'))
                <li class="{{ request()->is('school/fees/pending-cheques*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.pending-cheques') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('fee_management', 'pending_cheques', 'Pending Cheques') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('fee_management', 'fee_reports'))
                <li class="{{ request()->is('school/fees/reports*') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.reports') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('fee_management', 'fee_reports', 'Fee Reports') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('fee_management', 'fee_invoice'))
                <li class="{{ request()->is('school/fees/invoice') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.invoice') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('fee_management', 'fee_invoice', 'Fee Invoice') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('fee_management', 'fee_invoice1'))
                <li class="{{ request()->is('school/fees/invoice1') ? 'active' : '' }}">
                    <a href="{{ route('school.fees.invoice1') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('fee_management', 'fee_invoice1', 'Fee Invoice 1') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 11. I Card/ Bus Pass/ Admit Card -->
        @if(StaffAccessHelper::hasAccess('icard_buspass'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-address-card"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('icard_buspass', 'I Card/ Bus Pass/ Admit Card') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('icard_buspass', 'template_creator'))
                <li class="{{ request()->is('school/cards/template-creator*') ? 'active' : '' }}">
                    <a href="{{ route('school.cards.template-creator') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('icard_buspass', 'template_creator', 'Template Creator') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('icard_buspass', 'generate_card'))
                <li class="{{ request()->is('school/cards/generate-card*') ? 'active' : '' }}">
                    <a href="{{ route('school.cards.generate-card') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('icard_buspass', 'generate_card', 'Generate Card') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 12. Transport Management -->
        @if(StaffAccessHelper::hasAccess('transport_management'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-bus"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('transport_management', 'Transport Management') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/transport/basics*') ? 'active' : '' }}">
                    <a href="{{ route('school.transport.basics') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('transport_management', 'transport_basics', 'Transport Basics') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/transport/vehicles*') ? 'active' : '' }}">
                    <a href="{{ route('school.transport.vehicles') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('transport_management', 'vehicles', 'Vehicles') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/transport/stops*') ? 'active' : '' }}">
                    <a href="{{ route('school.transport.stops') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('transport_management', 'stops', 'Stops') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/transport/routes*') ? 'active' : '' }}">
                    <a href="{{ route('school.transport.routes') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('transport_management', 'routes', 'Routes') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/transport/vehicle-trip-mapping*') ? 'active' : '' }}">
                    <a href="{{ route('school.transport.trip-mapping') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('transport_management', 'vehicle_trip_mapping', 'Vehicle trip mapping') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/transport/student-route-mapping*') ? 'active' : '' }}">
                    <a href="{{ route('school.transport.student-mapping') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('transport_management', 'student_route_mapping', 'Student route mapping') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/transport/bus-attendance*') ? 'active' : '' }}">
                    <a href="{{ route('school.transport.bus-attendance') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('transport_management', 'bus_attendance', 'Bus Attendance') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/transport/vehicle-expenses*') ? 'active' : '' }}">
                    <a href="{{ route('school.transport.expenses') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('transport_management', 'vehicle_expenses', 'Vehicle Expenses') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>
        @endif

        <!-- 13. Digital Diary -->
        @if(StaffAccessHelper::hasAccess('digital_diary'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-book-open"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('digital_diary', 'Digital Diary') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('digital_diary', 'create_diary'))
                <li class="{{ request()->is('school/diary/create*') ? 'active' : '' }}">
                    <a href="{{ route('school.diary.create') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('digital_diary', 'create_diary', 'Create Diary') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('digital_diary', 'diary_report'))
                <li class="{{ request()->is('school/diary/report*') ? 'active' : '' }}">
                    <a href="{{ route('school.diary.report') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('digital_diary', 'diary_report', 'Daily Diary Report') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 14. Event & Holiday Management -->
        @if(StaffAccessHelper::hasAccess('event_holiday'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-calendar-check"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('event_holiday', 'Event & Holiday Management') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('event_holiday', 'event_holiday'))
                <li class="{{ request()->is('school/events*') ? 'active' : '' }}">
                    <a href="{{ route('school.events.index') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('event_holiday', 'event_holiday', 'Event & Holiday Management') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 15. Certificate Management -->
        @if(StaffAccessHelper::hasAccess('certificate_management'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-certificate"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('certificate_management', 'Certificate Management') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('certificate_management', 'template_creator'))
                <li class="{{ request()->is('school/certificates/template-creator*') ? 'active' : '' }}">
                    <a href="{{ route('school.certificates.template-creator') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('certificate_management', 'template_creator', 'Certificate Template Creator') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('certificate_management', 'manage_certs'))
                <li class="{{ request()->is('school/certificates/manage*') ? 'active' : '' }}">
                    <a href="{{ route('school.certificates.manage') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('certificate_management', 'manage_certs', 'Manage Certificates') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('certificate_management', 'class_wise_cert'))
                <li class="{{ request()->is('school/certificates/class-wise*') ? 'active' : '' }}">
                    <a href="{{ route('school.certificates.class-wise') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('certificate_management', 'class_wise_cert', 'Class-wise Student Certificate') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('certificate_management', 'cert_report'))
                <li class="{{ request()->is('school/certificates/report*') ? 'active' : '' }}">
                    <a href="{{ route('school.certificates.report') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('certificate_management', 'cert_report', 'Certificates Report') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 16. Leave Management -->
        @if(StaffAccessHelper::hasAccess('leave_management'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-sign-out-alt"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('leave_management', 'Leave Management') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('leave_management', 'leave_basics'))
                <li class="{{ request()->is('school/leave/basics*') ? 'active' : '' }}">
                    <a href="{{ route('school.leave.basics') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('leave_management', 'leave_basics', 'Leave Basics') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('leave_management', 'staff_leave'))
                <li class="{{ request()->is('school/leave/staff*') ? 'active' : '' }}">
                    <a href="{{ route('school.leave.staff') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('leave_management', 'staff_leave', 'Staff Leave') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('leave_management', 'student_leave'))
                <li class="{{ request()->is('school/leave/student*') ? 'active' : '' }}">
                    <a href="{{ route('school.leave.student') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('leave_management', 'student_leave', 'Student Leave') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 17. Communication -->
        @if(StaffAccessHelper::hasAccess('communication'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-comments"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('communication', 'Communication') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('communication', 'notification_settings'))
                <li class="{{ request()->is('school/communication/settings*') ? 'active' : '' }}">
                    <a href="{{ route('school.communication.settings') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('communication', 'notification_settings', 'Notification settings') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('communication', 'notice_circular'))
                <li class="{{ request()->is('school/communication/notice*') ? 'active' : '' }}">
                    <a href="{{ route('school.communication.notice') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('communication', 'notice_circular', 'Notice / Circular') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('communication', 'survey'))
                <li class="{{ request()->is('school/communication/survey*') ? 'active' : '' }}">
                    <a href="{{ route('school.communication.survey') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('communication', 'survey', 'Survey') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('communication', 'sms'))
                <li class="{{ request()->is('school/communication/sms*') ? 'active' : '' }}">
                    <a href="{{ route('school.communication.sms') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('communication', 'sms', 'SMS') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('communication', 'sms_template'))
                <li class="{{ request()->is('school/communication/sms-template*') ? 'active' : '' }}">
                    <a href="{{ route('school.communication.sms-template') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('communication', 'sms_template', 'SMS Template') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('communication', 'whatsapp'))
                <li class="{{ request()->is('school/communication/whatsapp*') ? 'active' : '' }}">
                    <a href="{{ route('school.communication.whatsapp') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('communication', 'whatsapp', 'WhatsApp') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('communication', 'email'))
                <li class="{{ request()->is('school/communication/email*') ? 'active' : '' }}">
                    <a href="{{ route('school.communication.email') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('communication', 'email', 'E-Mail') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('communication', 'chat'))
                <li class="{{ request()->is('school/communication/chat*') ? 'active' : '' }}">
                    <a href="{{ route('school.communication.chat') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('communication', 'chat', 'Chat') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 18. Examination -->
        @if(StaffAccessHelper::hasAccess('examination'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-graduation-cap"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('examination', 'Examination') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('examination', 'grade_scale'))
                <li class="{{ request()->is('school/examination/grade-scale*') ? 'active' : '' }}">
                    <a href="{{ route('school.examination.grade-scale') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('examination', 'grade_scale', 'Grade Scale') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('examination', 'marks_entry'))
                <li class="{{ request()->is('school/examination/marks-entry*') ? 'active' : '' }}">
                    <a href="{{ route('school.examination.marks-entry') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('examination', 'marks_entry', 'Marks Entry') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('examination', 'offline_tests'))
                <li class="{{ request()->is('school/examination/offline-tests*') ? 'active' : '' }}">
                    <a href="{{ route('school.examination.offline-tests') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('examination', 'offline_tests', 'Offline Tests') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('examination', 'report_card_template'))
                <li class="{{ request()->is('school/examination/report-card-template*') ? 'active' : '' }}">
                    <a href="{{ route('school.examination.report-card-template') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('examination', 'report_card_template', 'Report Card Template Creator') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('examination', 'report_card'))
                <li class="{{ request()->is('school/examination/report-card') ? 'active' : '' }}">
                    <a href="{{ route('school.examination.report-card') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('examination', 'report_card', 'Report Card') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('examination', 'report_card_v2'))
                <li class="{{ request()->is('school/examination/report-card-v2*') ? 'active' : '' }}">
                    <a href="{{ route('school.examination.report-card-v2') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('examination', 'report_card_v2', 'Report Card v2') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 19. Admissions -->
        @if(StaffAccessHelper::hasAccess('admissions'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon"><i class="fas fa-user-plus"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('admissions', 'Admissions') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('admissions', 'admission_process'))
                <li class="{{ request()->is('school/admissions/process*') ? 'active' : '' }}">
                    <a href="{{ route('school.admissions.process') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('admissions', 'admission_process', 'Admission Process') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('admissions', 'admission_settings'))
                <li class="{{ request()->is('school/admissions/settings*') ? 'active' : '' }}">
                    <a href="{{ route('school.admissions.settings') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('admissions', 'admission_settings', 'Admission Settings') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('admissions', 'enquiry_leads'))
                <li class="{{ request()->is('school/admissions/enquiry-leads*') ? 'active' : '' }}">
                    <a href="{{ route('school.admissions.enquiry-leads') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('admissions', 'enquiry_leads', 'Enquiry Leads') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('admissions', 'application_payment'))
                <li class="{{ request()->is('school/admissions/application-payment*') ? 'active' : '' }}">
                    <a href="{{ route('school.admissions.application-payment') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('admissions', 'application_payment', 'Application & Payment') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('admissions', 'pending_documents'))
                <li class="{{ request()->is('school/admissions/pending-documents*') ? 'active' : '' }}">
                    <a href="{{ route('school.admissions.pending-documents') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('admissions', 'pending_documents', 'Pending Documents') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('admissions', 'interaction_evaluation'))
                <li class="{{ request()->is('school/admissions/interaction-evaluation*') ? 'active' : '' }}">
                    <a href="{{ route('school.admissions.interaction-evaluation') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('admissions', 'interaction_evaluation', 'Interaction and Evaluation') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('admissions', 'admission'))
                <li class="{{ request()->is('school/admissions/admission*') ? 'active' : '' }}">
                    <a href="{{ route('school.admissions.admission') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('admissions', 'admission', 'Admission') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('admissions', 'new_admission_report'))
                <li class="{{ request()->is('school/admissions/new-admission-report*') ? 'active' : '' }}">
                    <a href="{{ route('school.admissions.new-admission-report') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('admissions', 'new_admission_report', 'New Admission Report') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('admissions', 'daily_planner'))
                <li class="{{ request()->is('school/admissions/daily-planner*') ? 'active' : '' }}">
                    <a href="{{ route('school.admissions.daily-planner') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('admissions', 'daily_planner', 'Daily Planner') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
                @if(StaffAccessHelper::hasAccess('admissions', 'admission_dashboard'))
                <li class="{{ request()->is('school/admissions/dashboard*') ? 'active' : '' }}">
                    <a href="{{ route('school.admissions.dashboard') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('admissions', 'admission_dashboard', 'Admission Dashboard') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 20. Gallery -->
        @if(StaffAccessHelper::hasAccess('gallery'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 100%); color: #fff; border-radius: 50%;"><i class="fas fa-image"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('gallery', 'Gallery') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('gallery', 'post_event'))
                <li class="{{ request()->is('school/gallery*') ? 'active' : '' }}">
                    <a href="{{ route('school.gallery.events') }}">
                        <span class="sb-submenu-label" style="color: #f97316; font-weight: 700;">{{ App\Support\ModuleRegistry::getFeatureLabel('gallery', 'post_event', 'Post An Event') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon" style="color: #f97316;"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

        <!-- 21. AI Assistant -->
        @if(StaffAccessHelper::hasAccess('ai_assistant'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #fff; border-radius: 50%; display:flex;align-items:center;justify-content:center;">
                        <img src="{{ asset('images/ai-assistant.png') }}" alt="AI" style="width:20px;height:20px;object-fit:contain;border-radius:50%;">
                    </div>
                    <span class="sb-hdr-title" style="background: linear-gradient(90deg,#818cf8,#a78bfa); -webkit-background-clip:text; -webkit-text-fill-color:transparent; font-weight:800;">{{ App\Support\ModuleRegistry::getLabel('ai_assistant', 'AI Assistant') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                <li class="{{ request()->is('school/ai/settings*') ? 'active' : '' }}">
                    <a href="{{ route('school.ai.settings') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('ai_assistant', 'ai_settings', 'AI Settings') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/ai/chat*') ? 'active' : '' }}">
                    <a href="{{ route('school.ai.chat') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('ai_assistant', 'ai_chat', 'AI Chat') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
            </ul>
        </div>
        @endif

        <!-- Expenses Control -->
        @if(StaffAccessHelper::hasAccess('expenses_control'))
        <div class="sb-group">
            <div class="sb-hdr">
                <div class="sb-hdr-left">
                    <div class="sb-hdr-icon" style="background: linear-gradient(135deg, #1d4ed8 0%, #3b82f6 100%); color: #fff; border-radius: 50%;"><i class="fas fa-wallet"></i></div>
                    <span class="sb-hdr-title">{{ App\Support\ModuleRegistry::getLabel('expenses_control', 'Expenses Control') }}</span>
                </div>
                <i class="fas fa-chevron-down sb-hdr-arrow"></i>
            </div>
            <ul class="sb-submenu">
                @if(StaffAccessHelper::hasAccess('expenses_control', 'manage_expenses'))
                <li class="{{ (request()->is('school/expenses') && !request()->is('school/expenses/reports')) ? 'active' : '' }}">
                    <a href="{{ route('school.expenses.index') }}">
                        <span class="sb-submenu-label">{{ App\Support\ModuleRegistry::getFeatureLabel('expenses_control', 'manage_expenses', 'Manage Expenses') }}</span>
                        <i class="fas fa-arrow-up-right-from-square sb-submenu-icon"></i>
                    </a>
                </li>
                <li class="{{ request()->is('school/expenses/reports') ? 'active' : '' }}">
                    <a href="{{ route('school.expenses.reports') }}">
                        <span class="sb-submenu-label">Expense Reports</span>
                        <i class="fas fa-chart-pie sb-submenu-icon"></i>
                    </a>
                </li>
                @endif
            </ul>
        </div>
        @endif

</div>

