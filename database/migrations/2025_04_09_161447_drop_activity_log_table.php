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
        Schema::dropIfExists('activity_log');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not recreating the table in down() as we're intentionally removing 
        // this functionality completely from the application
    }
};
