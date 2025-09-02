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
            $table->decimal('full_time_hourly_rate', 8, 2)->nullable()->after('allowance');
            $table->decimal('part_time_hourly_rate', 8, 2)->nullable()->after('full_time_hourly_rate');
            $table->integer('standard_hours_per_month')->default(160)->after('part_time_hourly_rate');
            $table->decimal('overtime_multiplier', 3, 2)->default(1.25)->after('standard_hours_per_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_grades', function (Blueprint $table) {
            $table->dropColumn([
                'full_time_hourly_rate',
                'part_time_hourly_rate', 
                'standard_hours_per_month',
                'overtime_multiplier'
            ]);
        });
    }
};
