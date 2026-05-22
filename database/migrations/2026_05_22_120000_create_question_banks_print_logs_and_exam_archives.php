<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table): void {
            $table->timestamp('archived_at')->nullable()->after('shuffle_questions_per_student');
        });

        Schema::create('question_banks', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('subject_id')->constrained()->cascadeOnDelete();
            $table->longText('prompt');
            $table->string('media_type')->nullable();
            $table->string('media_path')->nullable();
            $table->unsignedInteger('points')->default(10);
            $table->string('source_exam_title')->nullable();
            $table->unsignedInteger('source_question_position')->nullable();
            $table->timestamps();
        });

        Schema::create('question_bank_options', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('question_bank_id')->constrained()->cascadeOnDelete();
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->unsignedTinyInteger('position');
            $table->timestamps();
        });

        Schema::create('exam_print_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->timestamp('printed_at');
            $table->string('channel')->default('print');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_print_logs');
        Schema::dropIfExists('question_bank_options');
        Schema::dropIfExists('question_banks');

        Schema::table('exams', function (Blueprint $table): void {
            $table->dropColumn('archived_at');
        });
    }
};
