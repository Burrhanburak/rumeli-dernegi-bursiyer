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
        if (Schema::hasTable('laravel_notifications')) {
            // For laravel_notifications, we need to ensure the ID field accepts UUIDs
            // First check if the column is already correctly set up
            $idColumn = DB::select("SHOW COLUMNS FROM laravel_notifications WHERE Field = 'id'")[0] ?? null;
            
            if ($idColumn && $idColumn->Type !== 'char(36)') {
                // If the table already has a primary key, we need to handle it differently
                try {
                    DB::statement('ALTER TABLE laravel_notifications MODIFY id CHAR(36) NOT NULL');
                } catch (\Exception $e) {
                    // If that fails, we need a more complex approach
                    // This could involve dropping and recreating the table
                    // For now, log the error
                    \Log::error('Failed to modify laravel_notifications id column: ' . $e->getMessage());
                }
            }
            
            // Create the UUID trigger regardless
            $notificationCount = DB::table('laravel_notifications')->count();
            if ($notificationCount == 0) {
                // Drop the trigger if it exists (to avoid errors)
                DB::unprepared('DROP TRIGGER IF EXISTS laravel_notifications_uuid_trigger');
                
                // Create trigger to automatically generate UUIDs
                DB::unprepared('
                CREATE TRIGGER laravel_notifications_uuid_trigger BEFORE INSERT ON laravel_notifications
                FOR EACH ROW
                BEGIN
                    IF NEW.id IS NULL OR NEW.id = "" THEN
                        SET NEW.id = UUID();
                    END IF;
                END
                ');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('laravel_notifications')) {
            DB::unprepared('DROP TRIGGER IF EXISTS laravel_notifications_uuid_trigger');
        }
    }
};
