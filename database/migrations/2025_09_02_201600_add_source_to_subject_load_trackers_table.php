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
        Schema::table('subject_load_trackers', function (Blueprint $table) {
            $table->enum('source', ['direct', 'subject_load_tracker'])->default('subject_load_tracker')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subject_load_trackers', function (Blueprint $table) {
            $table->dropColumn('source');
        });
    }
};
