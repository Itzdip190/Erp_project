<?php

namespace App\Support;

/**
 * Central registry of all ERP modules and their features.
 * Add a new entry here and it will automatically appear in Role Category
 * and Staff Access Control pages without any further changes.
 */
class ModuleRegistry
{
    /**
     * Returns the full module list.
     * Format: [ 'module_key' => [ 'label' => '...', 'icon' => 'fa-...', 'features' => [ 'feature_key' => 'Feature Label' ] ] ]
     */
    public static function all(): array
    {
        return [
            'overview' => [
                'label'   => '1. Overview',
                'icon'    => 'fa-house',
                'features' => [
                    'mis_report'       => 'Daily MIS Report',
                    'admin_dashboard'  => 'Admin Dashboard',
                ],
            ],
            'institute_info' => [
                'label'   => '2. Institute Info',
                'icon'    => 'fa-building',
                'features' => [
                    'basic_info'            => 'Basic Institute Info',
                    'implementation_process'=> 'Implementation Process',
                    'udise'                 => 'UDISE',
                ],
            ],
            'admin_role_management' => [
                'label'   => '3. Admin Role Management',
                'icon'    => 'fa-users',
                'features' => [
                    'role_category'    => 'Role Category',
                    'staff_access'     => 'Staff Access Control',
                ],
            ],
            'password_management' => [
                'label'   => '4. Password Management',
                'icon'    => 'fa-lock',
                'features' => [
                    'reset_password'   => 'Reset Password',
                ],
            ],
            'staff_management' => [
                'label'   => '6. Staff Management',
                'icon'    => 'fa-user-cog',
                'features' => [
                    'staff_directory'       => 'Staff Directory',
                    'add_staff'             => 'Add Staff',
                    'bulk_import'           => 'Bulk Staff Import',
                    'bulk_photo'            => 'Bulk Photo Upload',
                    'staff_attendance'      => 'Staff Attendance',
                    'bulk_attendance'       => 'Staff Mark Bulk Attendance',
                    'student_att_report'    => 'Student Attendance Marking Report',
                ],
            ],
            'class_subject_teacher' => [
                'label'   => '7. Class, Subject & Teacher Assignment',
                'icon'    => 'fa-book',
                'features' => [
                    'class_overview'   => 'Class Overview',
                    'add_class'        => 'Add/Modify Class',
                    'add_subject'      => 'Add/Modify Subjects',
                    'assign_teacher'   => 'Assign Teachers',
                ],
            ],
            'timetable' => [
                'label'   => '8. Time Table',
                'icon'    => 'fa-calendar-days',
                'features' => [
                    'class_timetable'      => 'Class Timetable',
                    'group_timetable'      => 'Group Timetable',
                    'teacher_timetable'    => 'Teacher Timetable',
                    'teacher_substitution' => 'Teacher Substitution',
                    'teacher_workload'     => 'Teacher Workload',
                ],
            ],
            'student_management' => [
                'label'   => '9. Student Management',
                'icon'    => 'fa-graduation-cap',
                'features' => [
                    'add_student'           => 'Add Student',
                    'bulk_student_import'   => 'Bulk Student Import',
                    'bulk_photo_doc'        => 'Bulk Photo/Document Upload',
                    'optional_subject'      => 'Student Optional Subject Allocation',
                    'student_directory'     => 'Student Directory',
                    'admission_report'      => 'New Admission Report',
                    'siblings'              => 'Siblings List',
                    'student_attendance'    => 'Student Attendance',
                    'bulk_attendance'       => 'Student Mark Bulk Attendance',
                    'student_report'        => 'Student Report',
                    'app_settings'          => 'Student Info Update Settings on App',
                    'bulk_admission_no'     => 'Bulk Admission Number Change',
                    'attendance_report'     => 'Attendance Report',
                    'discipline'            => 'Discipline Management',
                    'bulk_operation'        => 'Bulk Student Operation',
                    'ptm_attendance'        => 'PTM Attendance',
                    'cca_module'            => 'CCA Module',
                ],
            ],
            'download_statistics' => [
                'label'   => '10. Download Statistics',
                'icon'    => 'fa-chart-pie',
                'features' => [
                    'student_download'  => 'Student Download Status',
                    'staff_download'    => 'Staff Download Status',
                    'parent_download'   => 'Parent Download Status',
                    'student_activity'  => 'Student Activity',
                    'staff_activity'    => 'Staff Activity',
                    'parent_activity'   => 'Parent Activity',
                ],
            ],
            'fee_management' => [
                'label'   => '11. Fee Management',
                'icon'    => 'fa-indian-rupee-sign',
                'features' => [
                    'fee_configuration'  => 'Fee Configuration',
                    'fee_basics'         => 'Fee Basics',
                    'class_wise_fee'     => 'Class-wise Fee',
                    'student_wise_fee'   => 'Student-wise Fee',
                    'optional_fee'       => 'Optional Fee Mapping',
                    'payment_links'      => 'Payment Links',
                    'collection_followup'=> 'Collection Follow-Up',
                    'schedule_mapper'    => 'Student Class & Fee Schedule Mapper',
                    'refund_fee'         => 'Refund Fee',
                    'fee_receipts'       => 'Fee Receipts',
                    'pending_cheques'    => 'Pending Cheques',
                    'fee_reports'        => 'Fee Reports',
                    'fee_invoice'        => 'Fee Invoice',
                    'fee_invoice1'       => 'Fee Invoice 1',
                    'bulk_upload'        => 'Fee Bulk Upload',
                    'statement_account'  => 'Statement of Account',
                    'xero_integration'   => 'Xero Integration',
                ],
            ],
            'icard_buspass' => [
                'label'   => '13. I Card / Bus Pass / Admit Card',
                'icon'    => 'fa-address-card',
                'features' => [
                    'template_creator'  => 'Template Creator',
                    'generate_card'     => 'Generate Card',
                ],
            ],
            'digital_diary' => [
                'label'   => '14. Digital Diary',
                'icon'    => 'fa-book-open',
                'features' => [
                    'create_diary'      => 'Create Diary',
                    'diary_report'      => 'Daily Diary Report',
                ],
            ],
            'event_holiday' => [
                'label'   => '15. Event & Holiday Management',
                'icon'    => 'fa-calendar-check',
                'features' => [
                    'event_holiday'     => 'Event & Holiday Management',
                ],
            ],
            'certificate_management' => [
                'label'   => '16. Certificate Management',
                'icon'    => 'fa-certificate',
                'features' => [
                    'template_creator'  => 'Certificate Template Creator',
                    'manage_certs'      => 'Manage Certificates',
                    'class_wise_cert'   => 'Class-wise Student Certificate',
                    'cert_report'       => 'Certificates Report',
                ],
            ],
            'leave_management' => [
                'label'   => '17. Leave Management',
                'icon'    => 'fa-sign-out-alt',
                'features' => [
                    'leave_basics'      => 'Leave Basics',
                    'staff_leave'       => 'Staff Leave',
                    'student_leave'     => 'Student Leave',
                ],
            ],
            'communication' => [
                'label'   => '18. Communication',
                'icon'    => 'fa-comments',
                'features' => [
                    'notification_settings' => 'Notification Settings',
                    'notice_circular'       => 'Notice / Circular',
                    'survey'                => 'Survey',
                    'sms'                   => 'SMS',
                    'sms_template'          => 'SMS Template',
                    'whatsapp'              => 'WhatsApp',
                    'email'                 => 'E-Mail',
                    'chat'                  => 'Chat',
                ],
            ],
            'examination' => [
                'label'   => '19. Examination',
                'icon'    => 'fa-graduation-cap',
                'features' => [
                    'grade_scale'           => 'Grade Scale',
                    'marks_entry'           => 'Marks Entry',
                    'offline_tests'         => 'Offline Tests',
                    'lms_tests'             => 'LMS Linked Tests',
                    'report_card_template'  => 'Report Card Template Creator',
                    'report_card'           => 'Report Card',
                    'report_card_v2'        => 'Report Card v2',
                    'marksheets_report'     => 'Marksheets and ORSS Report',
                    'reports'               => 'Reports',
                ],
            ],
            'admissions' => [
                'label'   => '20. Admissions',
                'icon'    => 'fa-user-plus',
                'features' => [
                    'admission_process'         => 'Admission Process',
                    'admission_settings'        => 'Admission Settings',
                    'enquiry_leads'             => 'Enquiry Leads',
                    'application_payment'       => 'Application & Payment',
                    'pending_documents'         => 'Pending Documents',
                    'interaction_evaluation'    => 'Interaction and Evaluation',
                    'admission'                 => 'Admission',
                    'new_admission_report'      => 'New Admission Report',
                    'daily_planner'             => 'Daily Planner',
                    'admission_dashboard'       => 'Admission Dashboard',
                ],
            ],
        ];
    }
}
