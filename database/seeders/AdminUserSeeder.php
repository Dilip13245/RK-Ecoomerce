<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'phone' => '1234567890',
            'password' => Hash::make('password'),
            'user_type' => 'customer',
            'status' => 'active',
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);
    }
}
