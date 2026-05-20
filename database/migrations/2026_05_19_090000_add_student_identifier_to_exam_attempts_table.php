<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->string('student_identifier', 30)->nullable()->after('student_name');
            $table->index(['exam_id', 'student_identifier'], 'exam_attempts_exam_identifier_index');
        });
    }

    public function down(): void
    {
        Schema::table('exam_attempts', function (Blueprint $table) {
            $table->dropIndex('exam_attempts_exam_identifier_index');
            $table->dropColumn('student_identifier');
        });
    }
};
