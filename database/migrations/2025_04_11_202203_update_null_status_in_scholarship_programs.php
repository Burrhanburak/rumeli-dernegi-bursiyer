<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update any null or empty status values to 'aktif'
        DB::table('scholarship_programs')
            ->whereNull('status')
            ->orWhere('status', '')
            ->update(['status' => 'aktif']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need for down migration as we are just setting default values
    }
};
