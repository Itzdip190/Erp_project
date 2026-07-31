<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Extend surveys table
        Schema::table('surveys', function (Blueprint $table) {
            if (!Schema::hasColumn('surveys', 'title')) {
                $table->string('title')->nullable()->after('school_id');
            }
            if (!Schema::hasColumn('surveys', 'target_audience')) {
                $table->string('target_audience')->default('all')->after('question');
            }
            if (!Schema::hasColumn('surveys', 'class_id')) {
                $table->foreignId('class_id')->nullable()->constrained('school_classes')->onDelete('set null')->after('target_audience');
            }
            if (!Schema::hasColumn('surveys', 'section_id')) {
                $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('set null')->after('class_id');
            }
            if (!Schema::hasColumn('surveys', 'scheduled_at')) {
                $table->timestamp('scheduled_at')->nullable()->after('is_active');
            }
            if (!Schema::hasColumn('surveys', 'published_at')) {
                $table->timestamp('published_at')->nullable()->after('scheduled_at');
            }
            // Make question nullable for new surveys that use title + survey_questions
            $table->string('question')->nullable()->change();
        });

        // 2. Create survey_questions table
        if (!Schema::hasTable('survey_questions')) {
            Schema::create('survey_questions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('survey_id')->constrained('surveys')->onDelete('cascade');
                $table->text('question_text');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        // 3. Extend survey_options table
        Schema::table('survey_options', function (Blueprint $table) {
            if (!Schema::hasColumn('survey_options', 'survey_question_id')) {
                $table->foreignId('survey_question_id')->nullable()->constrained('survey_questions')->onDelete('cascade')->after('survey_id');
            }
            if (!Schema::hasColumn('survey_options', 'votes')) {
                $table->integer('votes')->default(0)->after('option_text');
            }
        });

        // 4. Extend survey_responses table
        Schema::table('survey_responses', function (Blueprint $table) {
            if (!Schema::hasColumn('survey_responses', 'survey_question_id')) {
                $table->foreignId('survey_question_id')->nullable()->constrained('survey_questions')->onDelete('cascade')->after('survey_id');
            }
        });

        // 5. Data Migration for Existing Single-Question Surveys
        try {
            $surveys = DB::table('surveys')->get();
            foreach ($surveys as $survey) {
                // Set title if null
                $titleVal = $survey->title ?: ($survey->question ?: 'Opinion Survey #' . $survey->id);
                DB::table('surveys')->where('id', $survey->id)->update([
                    'title' => $titleVal,
                    'published_at' => $survey->created_at,
                ]);

                if (!empty($survey->question)) {
                    // Check if question already exists for this survey
                    $existingQ = DB::table('survey_questions')->where('survey_id', $survey->id)->first();
                    if (!$existingQ) {
                        $qId = DB::table('survey_questions')->insertGetId([
                            'survey_id' => $survey->id,
                            'question_text' => $survey->question,
                            'sort_order' => 1,
                            'created_at' => $survey->created_at,
                            'updated_at' => $survey->updated_at,
                        ]);

                        // Link existing survey_options to this survey_question_id
                        DB::table('survey_options')
                            ->where('survey_id', $survey->id)
                            ->whereNull('survey_question_id')
                            ->update(['survey_question_id' => $qId]);

                        // Link existing survey_responses to this survey_question_id
                        DB::table('survey_responses')
                            ->where('survey_id', $survey->id)
                            ->whereNull('survey_question_id')
                            ->update(['survey_question_id' => $qId]);
                    }
                }
            }
        } catch (\Throwable $e) {
            // Ignore if empty
        }
    }

    public function down(): void
    {
        Schema::table('survey_responses', function (Blueprint $table) {
            if (Schema::hasColumn('survey_responses', 'survey_question_id')) {
                $table->dropForeign(['survey_question_id']);
                $table->dropColumn('survey_question_id');
            }
        });

        Schema::table('survey_options', function (Blueprint $table) {
            if (Schema::hasColumn('survey_options', 'survey_question_id')) {
                $table->dropForeign(['survey_question_id']);
                $table->dropColumn('survey_question_id');
            }
            if (Schema::hasColumn('survey_options', 'votes')) {
                $table->dropColumn('votes');
            }
        });

        Schema::dropIfExists('survey_questions');

        Schema::table('surveys', function (Blueprint $table) {
            if (Schema::hasColumn('surveys', 'title')) $table->dropColumn('title');
            if (Schema::hasColumn('surveys', 'target_audience')) $table->dropColumn('target_audience');
            if (Schema::hasColumn('surveys', 'class_id')) {
                $table->dropForeign(['class_id']);
                $table->dropColumn('class_id');
            }
            if (Schema::hasColumn('surveys', 'section_id')) {
                $table->dropForeign(['section_id']);
                $table->dropColumn('section_id');
            }
            if (Schema::hasColumn('surveys', 'scheduled_at')) $table->dropColumn('scheduled_at');
            if (Schema::hasColumn('surveys', 'published_at')) $table->dropColumn('published_at');
        });
    }
};
