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
        // First check if we need to modify the original notifications table
        if (Schema::hasTable('notifications')) {
            // Check if type column is an ENUM
            $columnType = DB::select("SHOW COLUMNS FROM notifications WHERE Field = 'type'")[0]->Type ?? '';
            
            if (str_starts_with($columnType, 'enum')) {
                Schema::table('notifications', function (Blueprint $table) {
                    // MySQL-specific syntax to modify column type
                    DB::statement("ALTER TABLE notifications MODIFY type VARCHAR(255) NOT NULL");
                });
            }
        }
        
        // Also ensure the laravel_notifications table has a string type column
        if (Schema::hasTable('laravel_notifications')) {
            Schema::table('laravel_notifications', function (Blueprint $table) {
                $table->string('type', 255)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this is complex and risky - would need to validate all data fits in the enum
        // best to leave it as string
    }
};
