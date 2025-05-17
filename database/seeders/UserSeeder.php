<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@dilogif.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ])->assignRole('admin');

        // Buat user staff
        User::create([
            'name' => 'Staff',
            'email' => 'staff@dilogif.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ])->assignRole('staff');
    }
} 