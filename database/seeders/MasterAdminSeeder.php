<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Faculty;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class MasterAdminSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // First, let's try to add the department columns if they don't exist
        try {
            // Check if department column exists in users table
            if (!DB::getSchemaBuilder()->hasColumn('users', 'department')) {
                DB::statement('ALTER TABLE users ADD COLUMN department VARCHAR(255) NULL AFTER role');
            }
        } catch (\Exception $e) {
            // Column might already exist or there might be a connection issue
        }

        try {
            // Check if department column exists in faculties table
            if (!DB::getSchemaBuilder()->hasColumn('faculties', 'department')) {
                DB::statement('ALTER TABLE faculties ADD COLUMN department VARCHAR(255) DEFAULT "BSIT" AFTER employment_type');
            }
        } catch (\Exception $e) {
            // Column might already exist or there might be a connection issue
        }

        // Create Master Admin account
        $masterAdmin = User::updateOrCreate(
            ['email' => 'master@bestlink.edu.ph'],
            [
                'name' => 'Master Administrator',
                'email' => 'master@bestlink.edu.ph',
                'password' => Hash::make('master123'),
                'role' => 'master_admin',
                'department' => null, // Master admin doesn't belong to a specific department
            ]
        );

        // Update existing admin to BSIT department
        User::where('role', 'admin')
            ->whereNull('department')
            ->update(['department' => 'BSIT']);

        // Create BSCRIM Admin account
        $bscrimAdmin = User::updateOrCreate(
            ['email' => 'admin1@bestlink.edu.ph'],
            [
                'name' => 'BSCRIM Administrator',
                'email' => 'admin1@bestlink.edu.ph',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'department' => 'BSCRIM',
            ]
        );

        // Update all existing faculty to BSIT department
        try {
            Faculty::whereNull('department')
                   ->orWhere('department', '')
                   ->update(['department' => 'BSIT']);
        } catch (\Exception $e) {
            // Handle case where column doesn't exist yet
        }

        $this->command->info('Master Admin and department assignments created successfully!');
        $this->command->info('Master Admin: master@bestlink.edu.ph / master123');
        $this->command->info('BSCRIM Admin: admin1@bestlink.edu.ph / admin123');
    }
}
