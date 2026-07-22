<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('report_card_template_mappings');
        Schema::dropIfExists('report_card_templates');

        Schema::create('report_card_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->onDelete('set null');
            $table->string('name');
            $table->longText('content')->nullable();
            $table->string('background_image')->nullable();
            $table->json('design_settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('report_card_template_mappings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->onDelete('cascade');
            $table->foreignId('report_card_template_id')->constrained('report_card_templates')->onDelete('cascade');
            $table->foreignId('academic_session_id')->nullable()->constrained('academic_sessions')->onDelete('set null');
            $table->foreignId('class_id')->constrained('school_classes')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
            $table->timestamps();

            $table->index(['school_id', 'class_id', 'section_id'], 'rc_tmpl_map_sch_cls_sec_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('report_card_template_mappings');
        Schema::dropIfExists('report_card_templates');
    }
};
