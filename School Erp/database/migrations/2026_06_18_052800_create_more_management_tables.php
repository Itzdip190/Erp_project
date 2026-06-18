<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // id_card, bus_pass, admit_card
            $table->string('background_color')->default('#1a1f3c');
            $table->string('text_color')->default('#ffffff');
            $table->string('layout_style')->default('classic');
            $table->timestamps();
        });

        Schema::create('student_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('card_template_id')->constrained('card_templates')->onDelete('cascade');
            $table->string('card_number');
            $table->date('expiry_date');
            $table->string('status')->default('active');
            $table->timestamps();
        });

        Schema::create('digital_diaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->date('diary_date');
            $table->timestamps();
        });

        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_holiday')->default(false);
            $table->timestamps();
        });

        Schema::create('certificate_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // transfer, character, custom
            $table->string('title_text');
            $table->text('body_text');
            $table->string('background_image')->nullable();
            $table->timestamps();
        });

        Schema::create('student_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('certificate_template_id')->constrained('certificate_templates')->onDelete('cascade');
            $table->string('certificate_number');
            $table->date('issue_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_certificates');
        Schema::dropIfExists('certificate_templates');
        Schema::dropIfExists('events');
        Schema::dropIfExists('digital_diaries');
        Schema::dropIfExists('student_cards');
        Schema::dropIfExists('card_templates');
    }
};
