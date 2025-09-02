<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clearances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('faculty_id')->constrained('faculties')->onDelete('cascade');

            $table->string('clearance_type');
            $table->date('issued_date');
            $table->date('expiration_date')->nullable();
            $table->boolean('is_cleared')->default(false);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clearances');
    }
};
