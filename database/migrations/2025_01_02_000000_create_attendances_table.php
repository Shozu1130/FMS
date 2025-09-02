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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade');

            $table->foreignId('faculty_id')->constrained('faculty')->onDelete('cascade');

            $table->date('date');
            $table->datetime('time_in')->nullable();
            $table->datetime('time_out')->nullable();
            $table->string('time_in_photo')->nullable(); // Path to stored photo
            $table->string('time_out_photo')->nullable(); // Path to stored photo
            $table->string('time_in_location')->nullable(); // GPS coordinates or location name
            $table->string('time_out_location')->nullable(); // GPS coordinates or location name
            $table->decimal('total_hours', 5, 2)->default(0.00); // Total hours worked
            $table->enum('status', ['present', 'absent', 'late', 'early_departure', 'half_day'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['faculty_id', 'date']);
            $table->index(['date']);
            $table->index(['status']);
            
            // Ensure one attendance record per faculty per day
            $table->unique(['faculty_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
