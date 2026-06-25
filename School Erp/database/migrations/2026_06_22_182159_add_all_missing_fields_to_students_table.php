<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * FIX: Changed all `string()` (varchar 255) columns to narrower widths
     * to avoid MySQL error 1118 "Row size too large" (max 65535 bytes).
     * Only genuinely long free-text fields are kept as `text()`.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // 1. Personal Details
            $table->string('first_name_local', 100)->nullable()->after('first_name');
            $table->string('last_name_local', 100)->nullable()->after('last_name');
            $table->string('place_of_birth', 100)->nullable()->after('gender');
            $table->string('birth_certificate_no', 80)->nullable()->after('place_of_birth');
            $table->string('usn_srn_number', 80)->nullable()->after('birth_certificate_no');
            $table->string('house_role', 50)->nullable()->after('house_id');

            // 2. Academic & Fee Schedule Details
            $table->string('biometric_id', 80)->nullable()->after('admission_date');
            $table->string('pen_number', 50)->nullable()->after('biometric_id');
            $table->string('apaar_id', 80)->nullable()->after('pen_number');
            $table->string('samagra_id', 80)->nullable()->after('apaar_id');
            $table->string('class_at_admission', 80)->nullable()->after('samagra_id');
            $table->string('enrollment_number', 80)->nullable()->after('class_at_admission');
            $table->string('tc_number', 80)->nullable()->after('enrollment_number');

            // 3. Transport Details
            $table->string('transport_month', 50)->nullable()->after('tc_number');
            $table->string('transport_route', 100)->nullable()->after('transport_month');
            $table->string('transport_vehicle_code', 50)->nullable()->after('transport_route');
            $table->string('transport_stop', 100)->nullable()->after('transport_vehicle_code');
            $table->string('transport_drop_vehicle_code', 50)->nullable()->after('transport_stop');

            // 4. Previous School Details
            $table->string('prev_school', 150)->nullable()->after('transport_drop_vehicle_code');
            $table->string('prev_city_country', 100)->nullable()->after('prev_school');
            $table->string('prev_year_attended', 30)->nullable()->after('prev_city_country');
            $table->string('prev_board', 80)->nullable()->after('prev_year_attended');
            $table->string('prev_reg_no', 80)->nullable()->after('prev_board');
            $table->string('prev_pcm_marks', 50)->nullable()->after('prev_reg_no');
            $table->string('prev_pcm_percentage', 30)->nullable()->after('prev_pcm_marks');
            $table->string('prev_total_marks', 50)->nullable()->after('prev_pcm_percentage');
            $table->string('prev_average', 30)->nullable()->after('prev_total_marks');
            $table->string('entrance_exam_name', 100)->nullable()->after('prev_average');
            $table->string('entrance_exam_rank', 50)->nullable()->after('entrance_exam_name');
            $table->text('entrance_exam_remarks')->nullable()->after('entrance_exam_rank');

            $table->boolean('disciplinary_action')->default(false)->after('entrance_exam_remarks');
            $table->text('disciplinary_action_reason')->nullable()->after('disciplinary_action');
            $table->boolean('asked_to_leave')->default(false)->after('disciplinary_action_reason');
            $table->text('asked_to_leave_reason')->nullable()->after('asked_to_leave');
            $table->boolean('special_needs')->default(false)->after('asked_to_leave_reason');
            $table->text('special_needs_reason')->nullable()->after('special_needs');
            $table->boolean('interests_talents')->default(false)->after('special_needs_reason');
            $table->text('interests_talents_reason')->nullable()->after('interests_talents');
            $table->boolean('represented_school')->default(false)->after('interests_talents_reason');
            $table->text('represented_school_reason')->nullable()->after('represented_school');
            $table->boolean('other_info')->default(false)->after('represented_school_reason');
            $table->text('other_info_reason')->nullable()->after('other_info');

            // 5. Family Details
            $table->string('father_alternate_phone', 20)->nullable()->after('father_phone');
            $table->string('father_email', 100)->nullable()->after('father_alternate_phone');
            $table->string('father_id', 80)->nullable()->after('father_occupation');
            $table->string('father_aadhar', 20)->nullable()->after('father_id');
            $table->string('father_income', 50)->nullable()->after('father_aadhar');
            $table->string('father_qualification', 100)->nullable()->after('father_income');
            $table->string('father_passport', 50)->nullable()->after('father_qualification');
            $table->text('father_address')->nullable()->after('father_passport');

            $table->string('mother_alternate_phone', 20)->nullable()->after('mother_phone');
            $table->string('mother_email', 100)->nullable()->after('mother_alternate_phone');
            $table->string('mother_id', 80)->nullable()->after('mother_occupation');
            $table->string('mother_aadhar', 20)->nullable()->after('mother_id');
            $table->string('mother_income', 50)->nullable()->after('mother_aadhar');
            $table->string('mother_qualification', 100)->nullable()->after('mother_income');
            $table->string('mother_passport', 50)->nullable()->after('mother_qualification');
            $table->text('mother_address')->nullable()->after('mother_passport');
            $table->text('mother_office_address')->nullable()->after('mother_address');

            $table->string('whatsapp_number', 20)->nullable()->after('guardian_phone');
            $table->string('sub_caste', 80)->nullable()->after('caste');
            $table->string('family_id', 80)->nullable()->after('sub_caste');

            // 6. Guardian Details
            $table->string('guardian_passport', 50)->nullable()->after('guardian_relationship');
            $table->string('guardian_name_local', 100)->nullable()->after('guardian_name');
            $table->text('guardian_address')->nullable()->after('guardian_photo');

            // 7. Emergency Details
            $table->text('emergency_address')->nullable()->after('guardian_address');

            // 8. Communication
            $table->string('contact_priority', 50)->nullable()->after('emergency_address');

            // 9. Address Details
            $table->string('address_line_2', 150)->nullable()->after('address');
            $table->string('country', 80)->nullable()->after('state');
            $table->string('region', 80)->nullable()->after('country');
            $table->string('permanent_address_line_2', 150)->nullable()->after('permanent_address');
            $table->string('permanent_country', 80)->nullable()->after('permanent_state');
            $table->string('permanent_region', 80)->nullable()->after('permanent_country');

            // 10. Bank Details
            $table->string('bank_account_holder', 100)->nullable()->after('bank_account_no');
            $table->string('bank_branch', 100)->nullable()->after('bank_name');
            $table->string('bank_micr', 20)->nullable()->after('ifsc_code');

            // 11. Medical Health Record
            $table->string('medical_height', 20)->nullable()->after('opening_due_balance');
            $table->string('medical_weight', 20)->nullable()->after('medical_height');
            $table->string('medical_vision_left', 30)->nullable()->after('medical_weight');
            $table->string('medical_vision_right', 30)->nullable()->after('medical_vision_left');
            $table->string('medical_dental', 50)->nullable()->after('medical_vision_right');
            $table->string('medical_illness', 100)->nullable()->after('medical_dental');
            $table->text('medical_history')->nullable()->after('medical_illness');
            $table->text('medical_allergies')->nullable()->after('medical_history');
            $table->text('medical_disabilities')->nullable()->after('medical_allergies');
            $table->string('medical_doctor_name', 100)->nullable()->after('medical_disabilities');
            $table->string('medical_doctor_phone', 20)->nullable()->after('medical_doctor_name');
            $table->text('medical_doctor_address')->nullable()->after('medical_doctor_phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'first_name_local', 'last_name_local', 'place_of_birth', 'birth_certificate_no', 'usn_srn_number', 'house_role',
                'biometric_id', 'pen_number', 'apaar_id', 'samagra_id', 'class_at_admission', 'enrollment_number', 'tc_number',
                'transport_month', 'transport_route', 'transport_vehicle_code', 'transport_stop', 'transport_drop_vehicle_code',
                'prev_school', 'prev_city_country', 'prev_year_attended', 'prev_board', 'prev_reg_no', 'prev_pcm_marks',
                'prev_pcm_percentage', 'prev_total_marks', 'prev_average', 'entrance_exam_name', 'entrance_exam_rank', 'entrance_exam_remarks',
                'disciplinary_action', 'disciplinary_action_reason', 'asked_to_leave', 'asked_to_leave_reason', 'special_needs', 'special_needs_reason',
                'interests_talents', 'interests_talents_reason', 'represented_school', 'represented_school_reason', 'other_info', 'other_info_reason',
                'father_alternate_phone', 'father_email', 'father_id', 'father_aadhar', 'father_income', 'father_qualification', 'father_passport', 'father_address',
                'mother_alternate_phone', 'mother_email', 'mother_id', 'mother_aadhar', 'mother_income', 'mother_qualification', 'mother_passport', 'mother_address', 'mother_office_address',
                'whatsapp_number', 'sub_caste', 'family_id',
                'guardian_passport', 'guardian_name_local', 'guardian_address',
                'emergency_address', 'contact_priority',
                'address_line_2', 'country', 'region', 'permanent_address_line_2', 'permanent_country', 'permanent_region',
                'bank_account_holder', 'bank_branch', 'bank_micr',
                'medical_height', 'medical_weight', 'medical_vision_left', 'medical_vision_right', 'medical_dental', 'medical_illness',
                'medical_history', 'medical_allergies', 'medical_disabilities', 'medical_doctor_name', 'medical_doctor_phone', 'medical_doctor_address',
            ]);
        });
    }
};
