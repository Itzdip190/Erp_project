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
        if (!Schema::hasTable('branch_groups')) {
            Schema::create('branch_groups', function (Blueprint $table) {
                $table->id();
                $table->string('name')->nullable();
                $table->timestamps();
            });
        }

        Schema::table('schools', function (Blueprint $table) {
            if (!Schema::hasColumn('schools', 'branch_group_id')) {
                $table->foreignId('branch_group_id')
                    ->nullable()
                    ->after('status')
                    ->constrained('branch_groups')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('schools', 'branch_access_enabled')) {
                $table->boolean('branch_access_enabled')
                    ->default(false)
                    ->after('branch_group_id')
                    ->index();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schools', function (Blueprint $table) {
            if (Schema::hasColumn('schools', 'branch_group_id')) {
                $table->dropForeign(['branch_group_id']);
                $table->dropColumn('branch_group_id');
            }

            if (Schema::hasColumn('schools', 'branch_access_enabled')) {
                $table->dropColumn('branch_access_enabled');
            }
        });

        Schema::dropIfExists('branch_groups');
    }
};
