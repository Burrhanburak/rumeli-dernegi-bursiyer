<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Add reviewed_by and reviewed_at columns
            $table->unsignedBigInteger('reviewed_by')->nullable()->after('final_acceptance_at');
            $table->dateTime('reviewed_at')->nullable()->after('reviewed_by');
            
            // Add foreign key constraint for reviewed_by
            $table->foreign('reviewed_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['reviewed_by']);
            
            // Then drop columns
            $table->dropColumn(['reviewed_by', 'reviewed_at']);
        });
    }
};
