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
        if (!Schema::hasTable('late_fine_audit_logs')) {
            Schema::create('late_fine_audit_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->integer('installment_no');
                $table->string('action'); // 'applied' or 'removed'
                $table->decimal('old_fine', 10, 2)->default(0.00);
                $table->decimal('new_fine', 10, 2)->default(0.00);
                $table->text('reason')->nullable();
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('late_fine_audit_logs');
    }
};
