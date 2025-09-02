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
        Schema::table('payslips', function (Blueprint $table) {
            // Remove complex salary calculation columns
            $table->dropColumn([
                'hourly_rate',
                'regular_hours',
                'overtime_hours',
                'overtime_pay',
                'allowance',
                'gross_salary',
                'late_deductions',
                'absence_deductions',
                'other_deductions',
                'early_departure_days'
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payslips', function (Blueprint $table) {
            // Add back the removed columns
            $table->decimal('hourly_rate', 8, 2)->after('employment_type');
            $table->decimal('regular_hours', 8, 2)->after('total_hours');
            $table->decimal('overtime_hours', 8, 2)->after('regular_hours');
            $table->decimal('overtime_pay', 10, 2)->after('base_salary');
            $table->decimal('allowance', 10, 2)->default(0)->after('overtime_pay');
            $table->decimal('gross_salary', 10, 2)->after('allowance');
            $table->decimal('late_deductions', 10, 2)->default(0)->after('gross_salary');
            $table->decimal('absence_deductions', 10, 2)->default(0)->after('late_deductions');
            $table->decimal('other_deductions', 10, 2)->default(0)->after('absence_deductions');
            $table->integer('early_departure_days')->default(0)->after('late_days');
        });
    }
};
