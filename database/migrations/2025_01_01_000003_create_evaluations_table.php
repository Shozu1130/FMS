<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('faculties')->onDelete('cascade');

            $table->foreignId('teaching_history_id')->nullable()->constrained('teaching_histories')->onDelete('set null');
            $table->string('evaluation_period');
            $table->year('academic_year');
            $table->string('semester');
            $table->decimal('teaching_effectiveness', 5, 2)->default(0);
            $table->decimal('subject_matter_knowledge', 5, 2)->default(0);
            $table->decimal('classroom_management', 5, 2)->default(0);
            $table->decimal('communication_skills', 5, 2)->default(0);
            $table->decimal('student_engagement', 5, 2)->default(0);
            $table->decimal('overall_rating', 5, 2)->default(0);
            $table->text('strengths')->nullable();
            $table->text('areas_for_improvement')->nullable();
            $table->text('recommendations')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();
            
            $table->index(['professor_id', 'academic_year', 'semester']);
            $table->unique(['professor_id', 'teaching_history_id', 'evaluation_period'], 'evaluations_unique_constraint');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
