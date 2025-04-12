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
        // First convert to varchar to allow any value
        DB::statement("ALTER TABLE scholarship_programs MODIFY status VARCHAR(20)");

        // Update the values to simpler ones without special characters
        DB::table('scholarship_programs')
            ->where('status', 'aktif')
            ->orWhere('status', 'active')
            ->orWhereNull('status')
            ->orWhere('status', '')
            ->update(['status' => 'aktif']);

        DB::table('scholarship_programs')
            ->where('status', 'askıya_alındı')
            ->orWhere('status', 'suspended')
            ->update(['status' => 'askida']);

        DB::table('scholarship_programs')
            ->where('status', 'sonlandırıldı')
            ->orWhere('status', 'terminated')
            ->update(['status' => 'sonlandirildi']);

        // Convert back to enum with new simpler values
        DB::statement("ALTER TABLE scholarship_programs MODIFY status ENUM('aktif', 'askida', 'sonlandirildi') DEFAULT 'aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Convert back to varchar
        DB::statement("ALTER TABLE scholarship_programs MODIFY status VARCHAR(20)");

        // Revert to previous values
        DB::table('scholarship_programs')
            ->where('status', 'aktif')
            ->update(['status' => 'aktif']);

        DB::table('scholarship_programs')
            ->where('status', 'askida')
            ->update(['status' => 'askıya_alındı']);

        DB::table('scholarship_programs')
            ->where('status', 'sonlandirildi')
            ->update(['status' => 'sonlandırıldı']);

        // Convert back to previous enum
        DB::statement("ALTER TABLE scholarship_programs MODIFY status ENUM('aktif', 'askıya_alındı', 'sonlandırıldı') DEFAULT 'aktif'");
    }
};
