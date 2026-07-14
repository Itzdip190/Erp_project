<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pending_cheques', function (Blueprint $table) {
            // 'cleared' status add karo (existing: pending, bounced)
            // status column already string hai, sirf allowed values me 'cleared' add ho jayega
            // We add tracking timestamps and who changed the status
            if (!Schema::hasColumn('pending_cheques', 'status_changed_at')) {
                $table->timestamp('status_changed_at')->nullable()->after('status');
            }
            if (!Schema::hasColumn('pending_cheques', 'status_changed_by')) {
                $table->unsignedBigInteger('status_changed_by')->nullable()->after('status_changed_at');
            }
            if (!Schema::hasColumn('pending_cheques', 'status_remarks')) {
                $table->string('status_remarks')->nullable()->after('status_changed_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('pending_cheques', function (Blueprint $table) {
            $table->dropColumnIfExists('status_changed_at');
            $table->dropColumnIfExists('status_changed_by');
            $table->dropColumnIfExists('status_remarks');
        });
    }
};
