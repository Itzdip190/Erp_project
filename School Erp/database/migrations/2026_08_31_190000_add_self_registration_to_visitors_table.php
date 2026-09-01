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
        Schema::table('visitors', function (Blueprint $table) {
            if (!Schema::hasColumn('visitors', 'is_self_registered')) {
                $table->boolean('is_self_registered')->default(false)->after('status')->index();
            }
            if (!Schema::hasColumn('visitors', 'rejection_reason')) {
                $table->text('rejection_reason')->nullable()->after('is_self_registered');
            }
            if (!Schema::hasColumn('visitors', 'approved_at')) {
                $table->dateTime('approved_at')->nullable()->after('rejection_reason')->index();
            }
            if (!Schema::hasColumn('visitors', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            if (Schema::hasColumn('visitors', 'approved_by')) {
                $table->dropForeign(['approved_by']);
                $table->dropColumn('approved_by');
            }
            $columnsToDrop = [];
            if (Schema::hasColumn('visitors', 'approved_at')) {
                $columnsToDrop[] = 'approved_at';
            }
            if (Schema::hasColumn('visitors', 'rejection_reason')) {
                $columnsToDrop[] = 'rejection_reason';
            }
            if (Schema::hasColumn('visitors', 'is_self_registered')) {
                $columnsToDrop[] = 'is_self_registered';
            }
            if (!empty($columnsToDrop)) {
                $table->dropColumn($columnsToDrop);
            }
        });
    }
};
