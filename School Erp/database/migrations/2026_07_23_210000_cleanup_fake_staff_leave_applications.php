<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('staff_leave_applications')) {
            DB::table('staff_leave_applications')
                ->whereIn('reason', [
                    'Doctor appointment & fever recovery',
                    'Urgent personal work at hometown',
                    'Attending family medical appointment',
                    'Personal vacation'
                ])
                ->delete();
        }
    }

    public function down(): void
    {
        // No-op
    }
};
