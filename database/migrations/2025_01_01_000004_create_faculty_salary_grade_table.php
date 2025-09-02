<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('faculty_salary_grade', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade');
            $table->foreignId('salary_grade_id')->constrained('salary_grades')->onDelete('cascade');
            $table->date('effective_date');
            $table->date('end_date')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_current')->default(true);
            $table->timestamps();
            
            $table->unique(['faculty_id', 'salary_grade_id', 'effective_date']);
            $table->index(['faculty_id', 'is_current']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faculty_salary_grade');
    }
};
