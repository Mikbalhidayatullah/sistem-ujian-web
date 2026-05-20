<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_print_settings', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('teacher_id')->unique()->constrained('users')->cascadeOnDelete();
            $table->string('school_name')->nullable();
            $table->string('school_department')->nullable();
            $table->string('school_address')->nullable();
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_print_settings');
    }
};
