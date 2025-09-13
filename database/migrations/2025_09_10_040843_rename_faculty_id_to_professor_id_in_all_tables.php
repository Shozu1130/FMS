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
        // Rename faculty_id to professor_id in all tables
        $tables = [
            'attendances',
            'payslips', 
            'clearance_requests',
            'schedule_assignments',
            'subject_load_trackers',
            'teaching_histories',
            'evaluations',
            'leave_requests',
            'faculty_salary_grade'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'faculty_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->renameColumn('faculty_id', 'professor_id');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rename professor_id back to faculty_id in all tables
        $tables = [
            'attendances',
            'payslips',
            'clearance_requests', 
            'schedule_assignments',
            'subject_load_trackers',
            'teaching_histories',
            'evaluations',
            'leave_requests',
            'faculty_salary_grade'
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasColumn($tableName, 'professor_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->renameColumn('professor_id', 'faculty_id');
                });
            }
        }
    }
};
