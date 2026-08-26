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
        if (Schema::hasTable('library_transactions')) {
            Schema::table('library_transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('library_transactions', 'school_id')) {
                    $table->unsignedBigInteger('school_id')->nullable()->after('id');
                }
                if (!Schema::hasColumn('library_transactions', 'transaction_code')) {
                    $table->string('transaction_code', 50)->nullable()->after('school_id');
                }
                if (!Schema::hasColumn('library_transactions', 'book_id')) {
                    $table->unsignedBigInteger('book_id')->nullable()->after('transaction_code');
                }
                if (!Schema::hasColumn('library_transactions', 'member_type')) {
                    $table->string('member_type', 30)->default('student')->after('book_id');
                }
                if (!Schema::hasColumn('library_transactions', 'student_id')) {
                    $table->unsignedBigInteger('student_id')->nullable()->after('member_type');
                }
                if (!Schema::hasColumn('library_transactions', 'staff_id')) {
                    $table->unsignedBigInteger('staff_id')->nullable()->after('student_id');
                }
                if (!Schema::hasColumn('library_transactions', 'issue_date')) {
                    $table->date('issue_date')->nullable()->after('staff_id');
                }
                if (!Schema::hasColumn('library_transactions', 'due_date')) {
                    $table->date('due_date')->nullable()->after('issue_date');
                }
                if (!Schema::hasColumn('library_transactions', 'return_date')) {
                    $table->date('return_date')->nullable()->after('due_date');
                }
                if (!Schema::hasColumn('library_transactions', 'renewed_count')) {
                    $table->unsignedInteger('renewed_count')->default(0)->after('return_date');
                }
                if (!Schema::hasColumn('library_transactions', 'status')) {
                    $table->string('status', 30)->default('issued')->after('renewed_count');
                }
                if (!Schema::hasColumn('library_transactions', 'late_days')) {
                    $table->unsignedInteger('late_days')->default(0)->after('status');
                }
                if (!Schema::hasColumn('library_transactions', 'late_fine_amount')) {
                    $table->decimal('late_fine_amount', 10, 2)->default(0.00)->after('late_days');
                }
                if (!Schema::hasColumn('library_transactions', 'damage_lost_fine')) {
                    $table->decimal('damage_lost_fine', 10, 2)->default(0.00)->after('late_fine_amount');
                }
                if (!Schema::hasColumn('library_transactions', 'total_fine')) {
                    $table->decimal('total_fine', 10, 2)->default(0.00)->after('damage_lost_fine');
                }
                if (!Schema::hasColumn('library_transactions', 'fine_status')) {
                    $table->string('fine_status', 30)->default('none')->after('total_fine');
                }
                if (!Schema::hasColumn('library_transactions', 'payment_date')) {
                    $table->date('payment_date')->nullable()->after('fine_status');
                }
                if (!Schema::hasColumn('library_transactions', 'remarks')) {
                    $table->text('remarks')->nullable()->after('payment_date');
                }
                if (!Schema::hasColumn('library_transactions', 'issued_by')) {
                    $table->unsignedBigInteger('issued_by')->nullable()->after('remarks');
                }
                if (!Schema::hasColumn('library_transactions', 'received_by')) {
                    $table->unsignedBigInteger('received_by')->nullable()->after('issued_by');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down needed for safety
    }
};
