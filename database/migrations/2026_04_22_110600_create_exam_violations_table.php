<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_violations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attempt_id')->constrained('exam_attempts')->cascadeOnDelete();
            $table->string('violation_type');
            $table->string('detail')->nullable();
            $table->timestamp('happened_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_violations');
    }
};
