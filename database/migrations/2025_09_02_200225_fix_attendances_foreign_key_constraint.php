<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For SQLite, we need to recreate the table with the correct foreign key
        // First, create a temporary table with the correct structure
        Schema::create('attendances_temp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('professor_id')->constrained('faculties')->onDelete('cascade');
            $table->date('date');
            $table->datetime('time_in')->nullable();
            $table->datetime('time_out')->nullable();
            $table->string('time_in_photo')->nullable();
            $table->string('time_out_photo')->nullable();
            $table->string('time_in_location')->nullable();
            $table->string('time_out_location')->nullable();
            $table->decimal('total_hours', 5, 2)->default(0.00);
            $table->enum('status', ['present', 'absent', 'late', 'early_departure', 'half_day'])->default('present');
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes for better performance
            $table->index(['professor_id', 'date']);
            $table->index(['date']);
            $table->index(['status']);
            
            // Ensure one attendance record per faculty per day
            $table->unique(['professor_id', 'date']);
        });

        // Copy data from old table to new table (if any exists)
        if (Schema::hasTable('attendances')) {
            DB::statement('INSERT INTO attendances_temp SELECT * FROM attendances');
            
            // Drop the old table
            Schema::dropIfExists('attendances');
        }

        // Rename the temporary table to the original name
        Schema::rename('attendances_temp', 'attendances');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This migration fixes the foreign key constraint, so we don't reverse it
        // as it would break the system again
    }
};
