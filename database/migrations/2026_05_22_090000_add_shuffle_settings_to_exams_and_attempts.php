<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exams', function (Blueprint $table): void {
            $table->boolean('shuffle_questions_per_student')
                ->default(false)
                ->after('violations_enabled');
        });

        Schema::table('exam_attempts', function (Blueprint $table): void {
            $table->json('question_order')
                ->nullable()
                ->after('student_identifier');
        });
    }

    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table): void {
            $table->dropColumn('question_order');
        });

        Schema::table('exams', function (Blueprint $table): void {
            $table->dropColumn('shuffle_questions_per_student');
        });
    }
};
