<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin';
protected $description = 'Create admin user';

public function handle() {
    \App\Models\User::create([
        'name' => 'Admin',
        'email' => 'Admin@gmail.com',
        'password' => bcrypt('password'),
        'role' => 'admin'
    ]);
    $this->info('Admin created!');
}
}
