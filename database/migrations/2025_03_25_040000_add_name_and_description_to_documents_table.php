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
        Schema::table('documents', function (Blueprint $table) {
            $table->string('name')->nullable()->after('application_id');
            $table->text('description')->nullable()->after('file_path');
            $table->boolean('is_verified')->default(false)->after('admin_comment');
            $table->dateTime('verification_date')->nullable()->after('is_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn(['name', 'description', 'is_verified', 'verification_date']);
        });
    }
}; 