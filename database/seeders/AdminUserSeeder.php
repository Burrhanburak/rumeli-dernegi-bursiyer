<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'burhanburakozcaan@gmail.com'], // Check for existing user by email
            [
                'name' => 'admin',
                'surname' => 'admin',
                'is_admin' => true,
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(), // Optional: set to current time
                'created_at' => now(),        // Optional: set to current time
                'updated_at' => now(),        // Optional: set to current time
            ]
        );
    }
}