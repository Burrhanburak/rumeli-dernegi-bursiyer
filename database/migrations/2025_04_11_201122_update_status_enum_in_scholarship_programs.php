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
        // First change the enum to a varchar to allow any value temporarily
        DB::statement("ALTER TABLE scholarship_programs MODIFY status VARCHAR(20) DEFAULT 'aktif'");

        // Now update the values
        DB::table('scholarship_programs')
            ->where('status', 'active')
            ->update(['status' => 'aktif']);

        DB::table('scholarship_programs')
            ->where('status', 'suspended')
            ->update(['status' => 'askıya_alındı']);

        DB::table('scholarship_programs')
            ->where('status', 'terminated')
            ->update(['status' => 'sonlandırıldı']);

        // Update any null or empty values to aktif
        DB::table('scholarship_programs')
            ->whereNull('status')
            ->orWhere('status', '')
            ->update(['status' => 'aktif']);

        // Finally change back to enum with the new values
        DB::statement("ALTER TABLE scholarship_programs MODIFY status ENUM('aktif', 'askıya_alındı', 'sonlandırıldı') DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // First change to varchar to allow any value
        DB::statement("ALTER TABLE scholarship_programs MODIFY status VARCHAR(20) DEFAULT 'active'");

        // Update the values back
        DB::table('scholarship_programs')
            ->where('status', 'aktif')
            ->update(['status' => 'active']);

        DB::table('scholarship_programs')
            ->where('status', 'askıya_alındı')
            ->update(['status' => 'suspended']);

        DB::table('scholarship_programs')
            ->where('status', 'sonlandırıldı')
            ->update(['status' => 'terminated']);

        // Finally change back to the original enum
        DB::statement("ALTER TABLE scholarship_programs MODIFY status ENUM('active', 'suspended', 'terminated') DEFAULT 'active'");
    }
};
