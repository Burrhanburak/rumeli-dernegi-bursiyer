<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User; // User modelini dahil edin
use Illuminate\Support\Facades\Hash;
use Database\Seeders\AdminUserSeeder;
use Database\Seeders\ActivityLogTypeSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ScholarshipProgramSeeder;
use App\Models\Applications; // Applications modelini dahil edin

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(AdminUserSeeder::class);
     
        $this->call(UserSeeder::class);
        $this->call(ScholarshipProgramSeeder::class);
        
        // // Create 10 sample applications
        // Applications::factory()->count(10)->create();
    }
}
