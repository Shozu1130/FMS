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
        Schema::create('subject_load_trackers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('faculties')->onDelete('cascade');
            $table->string('subject_code', 20);
            $table->string('subject_name');
            $table->string('section', 10);
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
            $table->softDeletes();
            $table->timestamps();

            // Indexes for performance
            $table->index(['professor_id', 'academic_year', 'semester']);
            $table->index(['subject_code', 'section', 'academic_year', 'semester'], 'subject_load_trackers_idx');
            $table->index(['schedule_day', 'start_time', 'end_time']);
            $table->index('status');

            // Unique constraint to prevent duplicate assignments
            $table->unique(['professor_id', 'subject_code', 'section', 'academic_year', 'semester'], 'unique_subject_assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subject_load_trackers');
    }
};
