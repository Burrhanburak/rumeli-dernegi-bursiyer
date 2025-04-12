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
        Schema::table('scholarship_programs', function (Blueprint $table) {
            $table->enum('status', ['active', 'suspended', 'terminated'])->default('active')->after('is_active');
            $table->text('status_reason')->nullable()->after('status');
            $table->unsignedBigInteger('last_updated_by')->nullable()->after('status_reason');
            $table->foreign('last_updated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scholarship_programs', function (Blueprint $table) {
            $table->dropForeign(['last_updated_by']);
            $table->dropColumn(['status', 'status_reason', 'last_updated_by']);
        });
    }
};
