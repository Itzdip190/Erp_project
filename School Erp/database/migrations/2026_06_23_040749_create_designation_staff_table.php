<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('designation_staff');

        Schema::create('designation_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff')->cascadeOnDelete();
            $table->foreignId('designation_id')->constrained('designations')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['staff_id', 'designation_id']);
        });

        // Copy existing single designations to the pivot table
        $staffMembers = DB::table('staff')->whereNotNull('designation_id')->get();
        foreach ($staffMembers as $staff) {
            DB::table('designation_staff')->insertOrIgnore([
                'staff_id' => $staff->id,
                'designation_id' => $staff->designation_id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('designation_staff');
    }
};
