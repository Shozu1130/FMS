<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
protected $signature = 'admin:create {email} {password}';
protected $description = 'Create admin user';

public function handle()
{
    \App\Models\User::create([
        'name' => 'Admin',
        'email' => $this->argument('email'),
        'password' => bcrypt($this->argument('password')),
        'role' => 'admin',
        'email_verified_at' => now(),
    ]);
    $this->info('Admin created successfully!');
}
}
