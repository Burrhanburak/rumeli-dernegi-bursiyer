<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'burrakozcaaan@gmail.com'], // Check for existing user by email
            [
                'name' => 'Burhan',
                'surname' => 'ozcaan',
                'is_admin' => false,
                'password' => Hash::make('user123'),
                'email_verified_at' => Carbon::now(), // Use current date instead of future date
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );
    }
}