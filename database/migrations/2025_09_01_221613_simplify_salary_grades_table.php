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
        Schema::table('salary_grades', function (Blueprint $table) {
            // Remove columns that exist
            $table->dropColumn(['base_salary', 'is_active', 'notes', 'standard_hours_per_month', 'overtime_multiplier']);
            
            // Rename hourly rate columns to base salary
            $table->renameColumn('full_time_hourly_rate', 'full_time_base_salary');
            $table->renameColumn('part_time_hourly_rate', 'part_time_base_salary');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_grades', function (Blueprint $table) {
            // Add back the removed columns
            $table->decimal('base_salary', 10, 2)->default(0)->after('grade');
            $table->boolean('is_active')->default(true)->after('part_time_base_salary');
            $table->text('notes')->nullable()->after('is_active');
            $table->integer('standard_hours_per_month')->default(160)->after('notes');
            $table->decimal('overtime_multiplier', 3, 2)->default(1.25)->after('standard_hours_per_month');
            
            // Rename back to hourly rates
            $table->renameColumn('full_time_base_salary', 'full_time_hourly_rate');
            $table->renameColumn('part_time_base_salary', 'part_time_hourly_rate');
        });
    }
};
