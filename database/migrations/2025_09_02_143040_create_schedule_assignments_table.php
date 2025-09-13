<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedule_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('faculties')->onDelete('cascade');
            $table->string('subject_code', 20);
            $table->string('subject_name');
            $table->string('section', 10);
            $table->enum('year_level', ['1st Year', '2nd Year', '3rd Year', '4th Year', '5th Year']);
            $table->integer('units');
            $table->integer('hours_per_week');
            $table->enum('schedule_day', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room', 50)->nullable();
            $table->integer('academic_year');
            $table->enum('semester', ['1st Semester', '2nd Semester', 'Summer']);
            $table->enum('status', ['active', 'inactive', 'completed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['professor_id', 'academic_year', 'semester']);
            $table->index(['schedule_day', 'start_time', 'end_time']);
            $table->index(['subject_code', 'section']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_assignments');
    }
};
