<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_ai_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id')->unique();
            $table->boolean('enabled')->default(false);
            $table->text('api_key')->nullable();
            $table->string('ai_model', 100)->default('gemini-1.5-flash');
            $table->string('chatbot_name', 100)->default('AI Assistant');
            $table->string('ai_provider', 50)->default('gemini');
            $table->integer('max_tokens')->default(1024);
            $table->timestamps();

            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_ai_settings');
    }
};
