<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gallery_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('posted_by')->default('Admin');
            $table->string('type')->default('event'); // 'event' or 'achievement'
            $table->string('academic_year')->nullable();
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('attachments')->nullable();
            $table->string('recipients')->default('all_staff_students');
            $table->boolean('is_scheduled')->default(false);
            $table->timestamp('scheduled_at')->nullable();
            $table->boolean('show_popup')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gallery_posts');
    }
};
