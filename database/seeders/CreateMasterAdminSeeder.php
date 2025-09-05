<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CreateMasterAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Delete existing master admin
        DB::table('users')->where('email', 'master@bestlink.edu.ph')->delete();
        
        // Create new master admin using Laravel's User model for proper password hashing
        User::create([
            'name' => 'Master Administrator',
            'email' => 'master@bestlink.edu.ph',
            'password' => 'password', // This will be automatically hashed by the User model
            'role' => 'master_admin',
            'department' => 'MASTER ADMIN',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Master Admin created successfully!');
        $this->command->info('Login: master@bestlink.edu.ph / password');
    }
}
