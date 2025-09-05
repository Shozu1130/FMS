<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FixLoginAndDepartmentSeeder extends Seeder
{
    public function run(): void
    {
        // Delete and recreate Master Admin with proper password
        DB::table('users')->where('email', 'master@bestlink.edu.ph')->delete();
        
        User::create([
            'name' => 'Master Administrator',
            'email' => 'master@bestlink.edu.ph',
            'password' => Hash::make('master123'),
            'role' => 'master_admin',
            'department' => null,
            'email_verified_at' => now(),
        ]);

        // Verify BSCRIM admin exists
        $bscrimAdmin = User::where('email', 'admin1@bestlink.edu.ph')->first();
        if (!$bscrimAdmin) {
            User::create([
                'name' => 'BSCRIM Administrator',
                'email' => 'admin1@bestlink.edu.ph',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'department' => 'BSCRIM',
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Master Admin login fixed!');
        $this->command->info('Master Admin: master@bestlink.edu.ph / master123');
        $this->command->info('BSCRIM Admin: admin1@bestlink.edu.ph / admin123');
    }
}
