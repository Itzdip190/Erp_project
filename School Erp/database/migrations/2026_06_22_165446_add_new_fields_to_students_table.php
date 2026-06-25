<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('email')->nullable()->after('last_name');
            $table->string('phone')->nullable()->after('email');
            $table->string('group')->nullable()->after('category_id');
            
            $table->string('father_name')->nullable()->after('photo');
            $table->string('father_phone')->nullable()->after('father_name');
            $table->string('father_occupation')->nullable()->after('father_phone');
            $table->string('father_photo')->nullable()->after('father_occupation');
            
            $table->string('mother_name')->nullable()->after('father_photo');
            $table->string('mother_phone')->nullable()->after('mother_name');
            $table->string('mother_occupation')->nullable()->after('mother_phone');
            $table->string('mother_photo')->nullable()->after('mother_occupation');
            
            $table->string('guardian_occupation')->nullable()->after('guardian_relationship');
            $table->string('guardian_photo')->nullable()->after('guardian_occupation');
            
            $table->text('permanent_address')->nullable()->after('pincode');
            $table->string('permanent_city')->nullable()->after('permanent_address');
            $table->string('permanent_state')->nullable()->after('permanent_city');
            $table->string('permanent_pincode')->nullable()->after('permanent_state');
            
            $table->string('national_id')->nullable()->after('opening_due_balance');
            $table->string('local_id')->nullable()->after('national_id');
            $table->string('bank_account_no')->nullable()->after('local_id');
            $table->string('bank_name')->nullable()->after('bank_account_no');
            $table->string('ifsc_code')->nullable()->after('bank_name');
            $table->text('note')->nullable()->after('ifsc_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'email', 'phone', 'group',
                'father_name', 'father_phone', 'father_occupation', 'father_photo',
                'mother_name', 'mother_phone', 'mother_occupation', 'mother_photo',
                'guardian_occupation', 'guardian_photo',
                'permanent_address', 'permanent_city', 'permanent_state', 'permanent_pincode',
                'national_id', 'local_id', 'bank_account_no', 'bank_name', 'ifsc_code', 'note'
            ]);
        });
    }
};
