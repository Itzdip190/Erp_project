<?php

namespace App\Services;

use App\Models\SchoolSetting;
use App\Models\FeeConfiguration;
use App\Models\SchoolAiSetting;
use App\Models\StudentLeaveSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    /**
     * Default values for all ERP settings across 20 groups.
     */
    public static function defaults(): array
    {
        $baseDefaults = [
            // 1. School Configuration
            'school_name' => '',
            'school_code' => '',
            'school_phone' => '',
            'school_email' => '',
            'school_address' => '',
            'academic_session_name' => '',
            'language' => 'en',
            'theme' => 'light',
            'timezone' => 'Asia/Kolkata',
            'date_format' => 'Y-m-d',
            'prospectus_issue' => '1',
            'active_session' => '',
            'student_id_prefix' => '',
            'staff_id_prefix' => '',

            // 2. User Controls
            'allow_student_login' => '1',
            'allow_parent_login' => '1',
            'allow_staff_login' => '1',
            'allow_password_reset' => '1',
            'allow_online_admission' => '1',
            'allow_student_profile_edit' => '1',
            'show_fee_to_parents' => '1',

            // 3. Attendance Controls
            'attendance_mode' => 'daily_section',
            'auto_notify_absent' => 'sms_app',
            'auto_notify_present' => 'off',
            'skip_hostel_student_absent' => '0',
            'absent_staff_notification' => 'sms_app',
            'custom_absent_message' => 'Dear parents, your ward ::name:: is absent on ::today::. Kindly consider regularity of the student. Regards SCHOOL NAME',

            // 4. Leave Management Controls
            'allow_student_leave' => '1',
            'allow_staff_leave' => '1',
            'auto_approve_student_leave' => '0',
            'staff_leave_carry_forward' => '0',

            // 5. Fee Management & Invoicing Controls
            'reset_invoice_no' => 'session',
            'reset_challan_no' => 'session',
            'reset_sr_no' => 'session',
            'sync_sr_no_all_session' => '0',
            'auto_receipt_online_payment' => '1',
            'auto_generate_payment_challan' => '1',
            'fees_receipt_template_design' => '0',
            'enable_fee_reminder' => '1',
            'enable_late_fine' => '1',
            'allow_partial_fee_payment' => '1',
            'block_expense_voucher_back_date' => '0',

            // 6. Notification Controls
            'student_fees_notification' => 'sms_app',
            'auto_notify_holidays' => 'sms_app',
            'enable_sms_gateway' => '1',
            'enable_push_notification' => '1',
            'enable_email_notification' => '1',

            // 7. Director & Executive Settings
            'director_daily_report' => '1',
            'director_report_sms' => '0',
            'director_report_email' => '1',
            'director_report_time' => '17:00',
            'director_emails' => '',
            'permanent_slip_delete_contacts' => '',
            'edit_stock_otp_verification' => '1',

            // 8. Transport Controls
            'auto_transport_fee_allocation' => '0',
            'transport_invoice_title' => 'Transport Fee Receipt',
            'enable_gps_tracking' => '1',
            'show_driver_contact_to_parents' => '1',
            'show_school_name_transport_invoice' => '1',
            'quarterly_transport_payment' => '0',
            'show_school_logo_transport_invoice' => '1',
            'show_route_vehicle_on_transport_invoice' => '1',
            'auto_transport_absent_deduction' => '1',
            'allow_advance_transport_payment' => '1',
            'lock_partial_transport_payment' => '0',
            'transport_receipt_prefix' => 'TRN-',
            'enable_transport_parent_notifications' => '1',

            // 9. Timetable Controls
            'enable_auto_substitution' => '1',
            'max_periods_per_teacher' => '6',
            'period_duration_minutes' => '40',

            // 10. Examination & Marks
            'allow_teacher_marks_entry' => '1',
            'lock_published_marks' => '1',
            'show_class_rank' => '1',
            'show_grades_with_marks' => '1',

            // 11. Homework & Assignments
            'enable_homework_module' => '1',
            'enable_assignment_module' => '1',
            'allow_student_file_uploads' => '1',
            'max_attachment_size_mb' => '10',

            // 12. Communication Controls
            'auto_push_circulars' => '1',
            'allow_parents_view_notices' => '1',

            // 13. AI Assistant
            'enable_ai_assistant' => '1',
            'ai_provider' => 'gemini',
            'ai_model' => 'gemini-1.5-flash',
            'chatbot_name' => 'EduBot',
            'max_daily_ai_queries' => '50',

            // 14. Mobile App Controls
            'enable_mobile_api' => '1',
            'mobile_app_maintenance' => '0',
            'min_mobile_app_version' => '1.0.0',

            // 15. Security Controls
            'enforce_otp_login' => '0',
            'session_timeout_minutes' => '120',
            'audit_logging_level' => 'full',

            // 16. Finance Controls
            'require_voucher_approval' => '0',

            // 17. Export Controls
            'default_export_format' => 'pdf',
            'watermark_pdf_exports' => '0',

            // 18. Backup Controls
            'auto_daily_backup' => '1',
            'backup_retention_days' => '30',

            // 19. Automation Rules
            'auto_archive_graduated_students' => '1',
            'auto_carry_forward_balances' => '0',

            // 20. System Controls
            'maintenance_mode' => '0',

            // 21. Additional Operational Controls
            'delete_concession_otp_approval' => '1',
            'delete_fine_otp_approval' => '1',
            'delete_account_transfer_otp_approval' => '',
            'show_timetable_using_documents' => '0',
            'auto_select_installment_via_amount' => '1',
            'fees_challan_receipt_issue' => '1',
            'show_exams_v2_on_management_app' => '1',
            'enable_transport_v2' => '1',
            'enable_enquiry_v2' => '1',
            'show_sessionwise_previous_due_without_fees_applied' => '0',
            'sync_user_avatars_in_biometric_device' => '1',
            'payment_confirmation_modes' => 'CHEQUE',
            'show_inventory_previous_sessions_vouchers_due' => '0',
            'auto_approve_expense_voucher' => '0',
            'disable_previous_session_fees_payment' => '0',
            'show_report_card_v2_in_student_profile' => '0',
            'show_payment_service_charges' => '0',
        ];

        // Populate dynamic Feature Visibility defaults for registered features
        $features = \App\Support\FeatureVisibilityHelper::registeredFeatures();
        $scopes = ['master', 'web', 'mobile', 'student', 'parent', 'teacher', 'staff', 'admin'];
        foreach ($features as $fKey => $fMeta) {
            foreach ($scopes as $scope) {
                $k = "feat_{$fKey}_{$scope}";
                $baseDefaults[$k] = '1';
            }
        }

        return $baseDefaults;
    }

    /**
     * Get value for a setting key.
     */
    public static function get(string $key, mixed $default = null, ?int $schoolId = null): mixed
    {
        $defaults = static::defaults();
        $fallback = array_key_exists($key, $defaults) ? $defaults[$key] : $default;

        return SchoolSetting::getValue($key, $fallback, $schoolId);
    }

    /**
     * Set a setting value.
     */
    public static function set(string $key, mixed $value, string $group = 'system', string $type = 'string', ?int $schoolId = null): void
    {
        SchoolSetting::setValue($key, $value, $group, $type, $schoolId);
    }

    /**
     * Save bulk settings from request data and sync with legacy models.
     */
    public static function saveBulk(array $settingsData, ?int $schoolId = null): void
    {
        $schoolId = $schoolId ?: (Auth::check() ? Auth::user()->school_id : null);
        if (!$schoolId) {
            return;
        }

        $defaults = static::defaults();

        foreach ($settingsData as $key => $value) {
            // Determine type automatically
            $type = 'string';
            if (is_bool($value) || in_array($value, ['0', '1', true, false], true)) {
                if ($key !== 'reset_invoice_no' && $key !== 'reset_challan_no' && $key !== 'reset_sr_no' && $key !== 'auto_notify_absent' && $key !== 'auto_notify_present' && $key !== 'absent_staff_notification' && $key !== 'student_fees_notification' && $key !== 'auto_notify_holidays') {
                    $type = 'boolean';
                }
            }

            SchoolSetting::setValue($key, $value, static::getGroupForKey($key), $type, $schoolId);
        }

        // Synchronize with existing core models for complete backwards compatibility
        static::syncLegacyModels($settingsData, $schoolId);

        // Clear cache
        Cache::forget("school_settings_{$schoolId}");
    }

    /**
     * Map setting key to group.
     */
    public static function getGroupForKey(string $key): string
    {
        if (str_contains($key, 'school_') || in_array($key, ['language', 'theme', 'timezone', 'date_format', 'prospectus_issue', 'active_session'])) {
            return 'school_config';
        }
        if (str_starts_with($key, 'allow_') || str_contains($key, 'login') || str_contains($key, 'admission')) {
            return 'user_control';
        }
        if (str_contains($key, 'attendance') || str_contains($key, 'absent') || str_contains($key, 'present')) {
            return 'attendance';
        }
        if (str_contains($key, 'leave')) {
            return 'leave';
        }
        if (str_contains($key, 'fee') || str_contains($key, 'receipt') || str_contains($key, 'challan') || str_contains($key, 'sr_no') || str_contains($key, 'fine') || str_contains($key, 'discount')) {
            return 'fee';
        }
        if (str_contains($key, 'notify') || str_contains($key, 'sms') || str_contains($key, 'notification')) {
            return 'notification';
        }
        if (str_contains($key, 'director') || str_contains($key, 'permanent_slip') || str_contains($key, 'stock_otp')) {
            return 'director';
        }
        if (str_contains($key, 'transport')) {
            return 'transport';
        }
        if (str_contains($key, 'timetable') || str_contains($key, 'substitution') || str_contains($key, 'period')) {
            return 'timetable';
        }
        if (str_contains($key, 'marks') || str_contains($key, 'rank') || str_contains($key, 'grade')) {
            return 'exam';
        }
        if (str_contains($key, 'homework') || str_contains($key, 'assignment')) {
            return 'homework';
        }
        if (str_contains($key, 'ai_')) {
            return 'ai_assistant';
        }
        if (str_contains($key, 'mobile_')) {
            return 'mobile_app';
        }
        if (str_contains($key, 'otp') || str_contains($key, 'session_timeout') || str_contains($key, 'audit')) {
            return 'security';
        }
        return 'system';
    }

    /**
     * Synchronize setting values with legacy models.
     */
    protected static function syncLegacyModels(array $data, int $schoolId): void
    {
        // 1. FeeConfiguration
        $feeConfig = FeeConfiguration::firstOrCreate(['school_id' => $schoolId]);
        $feeUpdates = [];
        if (isset($data['transport_invoice_title'])) {
            $feeUpdates['transport_invoice_title'] = $data['transport_invoice_title'];
        }
        if (isset($data['fees_receipt_template_design'])) {
            $feeUpdates['receipt_template'] = $data['fees_receipt_template_design'] ? 'custom' : 'standard';
        }
        if (!empty($feeUpdates)) {
            $feeConfig->update($feeUpdates);
        }

        // 2. SchoolAiSetting
        $aiSetting = SchoolAiSetting::firstOrCreate(['school_id' => $schoolId]);
        $aiUpdates = [];
        if (isset($data['enable_ai_assistant'])) {
            $aiUpdates['enabled'] = (bool) $data['enable_ai_assistant'];
        }
        if (isset($data['ai_provider'])) {
            $aiUpdates['ai_provider'] = $data['ai_provider'];
        }
        if (isset($data['ai_model'])) {
            $aiUpdates['ai_model'] = $data['ai_model'];
        }
        if (isset($data['chatbot_name'])) {
            $aiUpdates['chatbot_name'] = $data['chatbot_name'];
        }
        if (!empty($aiUpdates)) {
            $aiSetting->update($aiUpdates);
        }

        // 3. StudentLeaveSetting
        $leaveSetting = StudentLeaveSetting::firstOrCreate(['school_id' => $schoolId]);
        if (isset($data['allow_student_leave'])) {
            $leaveSetting->update(['use_acknowledgement' => (bool) $data['allow_student_leave']]);
        }
    }
}
