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
        // Fix foreign key constraints that reference 'faculty' instead of 'faculties'
        // We'll drop and recreate the foreign keys with correct table references
        
        // Fix payslips table
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
        });
        
        // Fix clearances table
        Schema::table('clearances', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
        });
        
        // Fix evaluations table
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
        });
        
        // Fix teaching_histories table
        Schema::table('teaching_histories', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
        });
        
        // Fix leave_requests table
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->foreign('faculty_id')->references('id')->on('faculties')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original (incorrect) foreign key constraints
        // This is just for rollback purposes - we don't actually want to use these
        
        Schema::table('payslips', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->foreign('faculty_id')->references('id')->on('faculty')->onDelete('cascade');
        });
        
        Schema::table('clearances', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->foreign('faculty_id')->references('id')->on('faculty')->onDelete('cascade');
        });
        
        Schema::table('evaluations', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->foreign('faculty_id')->references('id')->on('faculty')->onDelete('cascade');
        });
        
        Schema::table('teaching_histories', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->foreign('faculty_id')->references('id')->on('faculty')->onDelete('cascade');
        });
        
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['faculty_id']);
            $table->foreign('faculty_id')->references('id')->on('faculty')->onDelete('cascade');
        });
    }
};
