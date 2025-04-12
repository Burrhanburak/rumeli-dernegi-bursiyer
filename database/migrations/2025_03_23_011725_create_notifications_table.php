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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->morphs('notifiable'); // Can be either admin or user
            $table->string('title');
            $table->text('message');
            $table->enum('type', [
                'document_required',       // User needs to upload documents
                'document_approved',       // Document was approved
                'document_rejected',       // Document was rejected
                'interview_scheduled',     // Interview was scheduled
                'interview_reminder',      // Reminder about upcoming interview
                'application_status',      // Application status change
                'scholarship_awarded',     // Scholarship was awarded
                'scholarship_changed',     // Scholarship details changed
                'system'                   // General system notification
            ]);
            $table->json('data')->nullable();
            $table->unsignedBigInteger('application_id')->nullable();
            $table->unsignedBigInteger('document_id')->nullable();
            $table->unsignedBigInteger('interview_id')->nullable();
            $table->boolean('is_read')->default(false);
            $table->dateTime('read_at')->nullable();
            $table->boolean('email_sent')->default(false);
            $table->dateTime('email_sent_at')->nullable();
            $table->timestamps();
            
            // Foreign key constraints have been moved to a separate migration
            // to ensure they're created after all referenced tables exist
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropColumn('data');
        });
    }
};
