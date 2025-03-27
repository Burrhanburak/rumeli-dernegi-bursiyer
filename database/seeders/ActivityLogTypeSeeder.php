<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivityLogTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('activity_log_types')->insert([
            ['name' => 'giris', 'description' => 'Sisteme giriş', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'basvuru', 'description' => 'Başvuru işlemleri', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'evrak', 'description' => 'Evrak işlemleri', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'mulakat', 'description' => 'Mülakat işlemleri', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'burs', 'description' => 'Burs işlemleri', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'kullanici', 'description' => 'Kullanıcı işlemleri', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'sistem', 'description' => 'Sistem işlemleri', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
