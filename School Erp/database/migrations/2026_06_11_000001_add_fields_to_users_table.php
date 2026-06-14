<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // school_id = null means superadmin
            $table->unsignedBigInteger('school_id')->nullable()->after('id');
            $table->foreign('school_id')->references('id')->on('schools')->nullOnDelete();

            $table->boolean('is_active')->default(true)->after('school_id');  // false = login blocked
            $table->string('phone')->nullable()->after('email');               // OTP login
            $table->string('photo')->nullable()->after('phone');               // S3 path
            $table->dateTime('last_login_at')->nullable()->after('remember_token');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn(['school_id', 'is_active', 'phone', 'photo', 'last_login_at']);
        });
    }
};
