<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateFilamentUser extends Command
{
    protected $signature = 'make:admin';
    protected $description = 'Create a new Filament admin user';

    public function handle()
    {
        $name = $this->ask('Name');
        $email = $this->ask('Email address');
        $phone = $this->ask('Phone number');
        $password = $this->secret('Password');

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'password' => Hash::make($password),
            'user_type' => 'customer',
            'status' => 'active',
            'is_verified' => true,
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        $this->info('Admin user created successfully!');
        $this->info('Email: ' . $user->email);
        $this->info('Phone: ' . $user->phone);

        return Command::SUCCESS;
    }
}
