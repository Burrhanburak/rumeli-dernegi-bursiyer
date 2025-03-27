<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations using direct SQL to ensure all columns have proper defaults.
     */
    public function up(): void
    {
        // Explicitly update ALL rows to ensure no NULL values in critical fields
        DB::statement("UPDATE applications SET 
            are_documents_approved = IFNULL(are_documents_approved, 0),
            is_interview_completed = IFNULL(is_interview_completed, 0),
            application_date = IFNULL(application_date, CURDATE()),
            status = IFNULL(status, 'awaiting_evaluation'),
            user_id = IFNULL(user_id, (SELECT id FROM users LIMIT 1)),
            application_id = IFNULL(application_id, CONCAT('APP-', UUID_SHORT()))
        ");
        
        // Additional safety: Set default application_id for any row that doesn't have one
        DB::statement("UPDATE applications SET application_id = CONCAT('APP-', UUID_SHORT()) WHERE application_id IS NULL OR application_id = ''");
        
        // For string columns that don't want NULL values, set them to empty string
        $stringColumns = [
            'name', 'surname', 'nationality', 'gender', 'phone', 'email',
            'birth_place', 'image', 'national_id'
        ];
        
        foreach ($stringColumns as $column) {
            DB::statement("UPDATE applications SET {$column} = '' WHERE {$column} IS NULL");
        }
        
        // For debugging purposes, get the column list to make sure we've covered all bases
        $columns = DB::select('SHOW COLUMNS FROM applications');
        $columnNames = array_column($columns, 'Field');
        
        // Log the columns for reference in Laravel's log file instead
        Log::info('Applications columns: ' . implode(", ", $columnNames));
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to revert anything, as we're just ensuring data integrity
    }
};
