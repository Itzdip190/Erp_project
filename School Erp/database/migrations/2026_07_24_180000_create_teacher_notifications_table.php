<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('teacher_notifications')) {
            Schema::create('teacher_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->nullable()->constrained()->onDelete('cascade');
                $table->foreignId('staff_id')->nullable()->constrained('staff')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('message');
                $table->string('type')->default('leave'); // e.g. leave_submitted, leave_approved, leave_rejected
                $table->unsignedBigInteger('leave_application_id')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['school_id', 'staff_id']);
                $table->index(['school_id', 'user_id']);
                $table->index(['is_read', 'created_at']);
            });
        }

        if (Schema::hasTable('staff_leave_applications')) {
            if (!Schema::hasColumn('staff_leave_applications', 'admin_remark')) {
                Schema::table('staff_leave_applications', function (Blueprint $table) {
                    $table->text('admin_remark')->nullable()->after('rejection_reason');
                });
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_notifications');

        if (Schema::hasTable('staff_leave_applications') && Schema::hasColumn('staff_leave_applications', 'admin_remark')) {
            Schema::table('staff_leave_applications', function (Blueprint $table) {
                $table->dropColumn('admin_remark');
            });
        }
    }
};
