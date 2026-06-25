<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only create if it does NOT already exist (idempotent)
        if (!Schema::hasTable('staff_module_access')) {
            Schema::create('staff_module_access', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->unsignedBigInteger('user_id');
                $table->string('module_key');
                $table->string('feature_key');
                $table->boolean('view_access')->default(false);
                $table->boolean('edit_access')->default(false);
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->unique(['school_id', 'user_id', 'module_key', 'feature_key'], 'sma_school_user_mod_feat_unique');
                $table->index(['school_id', 'module_key', 'feature_key']);
            });
        }

        // Also ensure module_permissions exists (same idempotent guard)
        if (!Schema::hasTable('module_permissions')) {
            Schema::create('module_permissions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('school_id');
                $table->string('module_key');
                $table->string('feature_key');
                $table->boolean('view_access')->default(false);
                $table->boolean('edit_access')->default(false);
                $table->timestamps();

                $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
                $table->unique(['school_id', 'module_key', 'feature_key']);
                $table->index(['school_id', 'module_key']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_module_access');
    }
};
