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
        Schema::table('notifications', function (Blueprint $table) {
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('set null');
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('set null');
            $table->foreign('interview_id')->references('id')->on('interviews')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
            $table->dropForeign(['document_id']);
            $table->dropForeign(['interview_id']);
        });
    }
};
