<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teaching_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('faculty')->onDelete('cascade');
            $table->string('course_code');
            $table->string('course_title');
            $table->string('semester');
            $table->year('academic_year');
            $table->integer('units')->default(3);
            $table->enum('schedule', ['MWF', 'TTH', 'MW', 'TTHS', 'F', 'S'])->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('room')->nullable();
            $table->integer('number_of_students')->default(0);
            $table->decimal('rating', 3, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->index(['faculty_id', 'academic_year', 'semester']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_histories');
    }
};
