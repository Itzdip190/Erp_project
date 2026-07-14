<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Reset all auto-assigned student fee_schedule_id to NULL so that only explicitly assigned ones are shown,
        // and others show blank (-) as requested.
        \DB::table('students')->update(['fee_schedule_id' => null]);
    }

    public function down(): void
    {
        // No rollback needed
    }
};
