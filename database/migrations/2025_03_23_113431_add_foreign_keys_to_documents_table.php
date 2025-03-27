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
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->foreign('document_type_id')
                  ->references('id')
                  ->on('document_types')
                  ->onDelete('cascade');

            $table->foreign('application_id')
                  ->references('id')
                  ->on('applications')
                  ->onDelete('set null');

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
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['document_type_id']);
            $table->dropForeign(['application_id']);
            $table->dropForeign(['reviewed_by']);
        });
    }
};

