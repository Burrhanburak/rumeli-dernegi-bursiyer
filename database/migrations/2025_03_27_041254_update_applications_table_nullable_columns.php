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
        Schema::table('applications', function (Blueprint $table) {
            // Update the non-nullable columns to have default values instead
            // This is safer than changing them to nullable and will prevent future errors
            $table->boolean('are_documents_approved')->default(false)->change();
            $table->boolean('is_interview_completed')->default(false)->change();
            
            // Make sure application_date is not nullable and has a default
            $table->date('application_date')->default(now())->change();
            
            // Check if the unique constraint already exists
            $uniqueKeyExists = collect(DB::select("SHOW KEYS FROM applications WHERE Key_name = 'applications_application_id_unique'"))->isNotEmpty();
            
            if (!$uniqueKeyExists) {
                // Make sure the application_id has a unique constraint
                $table->string('application_id')->unique()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // If we need to reverse these changes, we'd remove the defaults
            // but keep the columns non-nullable
            $table->boolean('are_documents_approved')->change();
            $table->boolean('is_interview_completed')->change();
            $table->date('application_date')->change();
        });
    }
};
