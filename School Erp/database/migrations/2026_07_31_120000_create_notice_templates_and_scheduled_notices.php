<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('notices', 'publish_at')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->timestamp('publish_at')->nullable()->after('target_audience');
            });
        }

        if (!Schema::hasTable('notice_templates')) {
            Schema::create('notice_templates', function (Blueprint $table) {
                $table->id();
                $table->foreignId('school_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->string('target_audience')->default('all');
                $table->string('category')->nullable();
                $table->text('content');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('notice_templates');
        if (Schema::hasColumn('notices', 'publish_at')) {
            Schema::table('notices', function (Blueprint $table) {
                $table->dropColumn('publish_at');
            });
        }
    }
};
