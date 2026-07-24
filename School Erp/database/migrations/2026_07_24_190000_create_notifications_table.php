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
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->nullable()->constrained('schools')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
                $table->string('recipient_role')->nullable(); // 'school_admin', 'teacher', 'student', 'parent', etc.
                $table->string('title');
                $table->text('message');
                $table->string('module')->default('general'); // leave, admission, homework, exam, fee, attendance, communication, transport, certificate
                $table->string('type')->nullable(); // e.g. leave_submitted, leave_approved, leave_rejected
                $table->unsignedBigInteger('related_id')->nullable();
                $table->string('priority')->default('normal'); // low, normal, high, urgent
                $table->string('action_url')->nullable();
                $table->string('icon')->default('fa-bell');
                $table->string('color')->default('#8b5cf6');
                $table->boolean('is_read')->default(false);
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->index(['school_id', 'user_id', 'is_read']);
                $table->index(['school_id', 'recipient_role', 'is_read']);
                $table->index(['school_id', 'created_at']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
