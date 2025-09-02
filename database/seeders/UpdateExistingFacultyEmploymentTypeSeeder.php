<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UpdateExistingFacultyEmploymentTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Faculty::whereNull('employment_type')
            ->orWhere('employment_type', '')
            ->orWhere('employment_type', 'Full-Time')
            ->update(['employment_type' => 'Full-Time']);
    }
}
