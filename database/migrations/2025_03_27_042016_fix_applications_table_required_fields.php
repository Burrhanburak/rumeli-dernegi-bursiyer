<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations using direct SQL to ensure compatibility.
     */
    public function up(): void
    {
        // Direct SQL for maximum compatibility
        // Set default values for potentially problematic fields
        DB::statement('ALTER TABLE applications MODIFY COLUMN are_documents_approved TINYINT(1) NOT NULL DEFAULT 0');
        DB::statement('ALTER TABLE applications MODIFY COLUMN is_interview_completed TINYINT(1) NOT NULL DEFAULT 0');
        
        // For MySQL DATE column, we need to use a specific date format. CURRENT_TIMESTAMP is only
        // valid for TIMESTAMP or DATETIME columns
        DB::statement('ALTER TABLE applications MODIFY COLUMN application_date DATE NOT NULL');
        
        DB::statement('ALTER TABLE applications MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT "awaiting_evaluation"');
        
        // Update any existing NULL values to their defaults
        DB::statement('UPDATE applications SET are_documents_approved = 0 WHERE are_documents_approved IS NULL');
        DB::statement('UPDATE applications SET is_interview_completed = 0 WHERE is_interview_completed IS NULL');
        DB::statement('UPDATE applications SET application_date = CURDATE() WHERE application_date IS NULL');
        DB::statement('UPDATE applications SET status = "awaiting_evaluation" WHERE status IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Allow NULL values again (though not practical to revert)
        DB::statement('ALTER TABLE applications MODIFY COLUMN are_documents_approved TINYINT(1)');
        DB::statement('ALTER TABLE applications MODIFY COLUMN is_interview_completed TINYINT(1)');
        DB::statement('ALTER TABLE applications MODIFY COLUMN application_date DATE');
        DB::statement('ALTER TABLE applications MODIFY COLUMN status VARCHAR(255)');
    }
};
