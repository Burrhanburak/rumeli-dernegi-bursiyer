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
        // Add interview_date column if it doesn't exist
        if (!Schema::hasColumn('interviews', 'interview_date')) {
            Schema::table('interviews', function (Blueprint $table) {
                $table->dateTime('interview_date')->nullable()->after('application_id');
            });
        }
        
        // First, change the status column to varchar to accept more values
        DB::statement('ALTER TABLE interviews MODIFY COLUMN status VARCHAR(255) NULL');
        
        // Also, change the interview_result column to VARCHAR to accept more values like 'passed', 'failed', etc
        DB::statement('ALTER TABLE interviews MODIFY COLUMN interview_result VARCHAR(255) DEFAULT "beklemede"');
        
        // Make interview_date nullable so it doesn't cause NOT NULL constraint errors
        DB::statement('ALTER TABLE interviews MODIFY COLUMN interview_date DATETIME NULL');
        
        // Then add default data for interview_date if missing
        DB::statement("UPDATE interviews SET interview_date = NOW() WHERE interview_date IS NULL");
        
        // Update any existing 'awaiting_schedule' records to a valid status if needed
        DB::statement("UPDATE interviews SET status = 'awaiting_schedule' WHERE status = ''");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // In the down method, we'll revert to the original settings
        DB::statement("ALTER TABLE interviews MODIFY COLUMN status ENUM('planlandı', 'tamamlandı', 'iptal_edildi') DEFAULT 'planlandı'");
        DB::statement("ALTER TABLE interviews MODIFY COLUMN interview_result ENUM('kabul', 'red', 'beklemede') DEFAULT 'beklemede'");
        DB::statement("ALTER TABLE interviews MODIFY COLUMN interview_date DATETIME NOT NULL");
    }
};
