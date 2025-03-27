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
        Schema::create('activity_log_types', function (Blueprint $table) {
            $table->id(); // Primary key referenced by activity_logs
            $table->string('name'); // Example column, adjust as needed
            $table->string('description');
            $table->timestamps(); // Optional: created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_log_types');
    }
};
