<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FixMasterAdminPasswordSeeder extends Seeder
{
    public function run(): void
    {
        // Delete existing master admin
        DB::table('users')->where('email', 'master@bestlink.edu.ph')->delete();
        
        // Create new master admin with known working password hash
        DB::table('users')->insert([
            'name' => 'Master Administrator',
            'email' => 'master@bestlink.edu.ph',
            'password' => '$2y$12$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'role' => 'master_admin',
            'department' => null,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Master Admin password fixed!');
        $this->command->info('Login: master@bestlink.edu.ph / password');
    }
}
