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
        Schema::create('payslips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('faculty')->onDelete('cascade');
            $table->year('year');
            $table->tinyInteger('month');
            $table->enum('employment_type', ['Full-Time', 'Part-Time']);
            $table->decimal('hourly_rate', 8, 2);
            $table->decimal('total_hours', 8, 2);
            $table->decimal('regular_hours', 8, 2);
            $table->decimal('overtime_hours', 8, 2)->default(0);
            $table->decimal('base_salary', 10, 2);
            $table->decimal('overtime_pay', 10, 2)->default(0);
            $table->decimal('allowance', 10, 2)->default(0);
            $table->decimal('gross_salary', 10, 2);
            $table->decimal('late_deductions', 10, 2)->default(0);
            $table->decimal('absence_deductions', 10, 2)->default(0);
            $table->decimal('other_deductions', 10, 2)->default(0);
            $table->decimal('total_deductions', 10, 2)->default(0);
            $table->decimal('net_salary', 10, 2);
            $table->integer('present_days');
            $table->integer('absent_days');
            $table->integer('late_days');
            $table->integer('early_departure_days');
            $table->json('attendance_summary')->nullable();
            $table->enum('status', ['draft', 'finalized', 'paid'])->default('draft');
            $table->timestamp('generated_at');
            $table->timestamp('finalized_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            $table->unique(['faculty_id', 'year', 'month']);
            $table->index(['year', 'month']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payslips');
    }
};
