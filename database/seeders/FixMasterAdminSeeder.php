<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FixMasterAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Delete existing accounts and recreate with proper passwords
        User::where('email', 'master@bestlink.edu.ph')->delete();
        User::where('email', 'admin1@bestlink.edu.ph')->delete();

        // Create Master Admin account with proper password
        User::create([
            'name' => 'Master Administrator',
            'email' => 'master@bestlink.edu.ph',
            'password' => Hash::make('master123'),
            'role' => 'master_admin',
            'department' => null,
        ]);

        // Create BSCRIM Admin account
        User::create([
            'name' => 'BSCRIM Administrator',
            'email' => 'admin1@bestlink.edu.ph',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'department' => 'BSCRIM',
        ]);

        $this->command->info('Master Admin accounts fixed successfully!');
        $this->command->info('Master Admin: master@bestlink.edu.ph / master123');
        $this->command->info('BSCRIM Admin: admin1@bestlink.edu.ph / admin123');
    }
}
