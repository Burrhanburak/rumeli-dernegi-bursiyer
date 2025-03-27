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
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Kullanıcı ID
            $table->foreignId('log_type_id')->constrained('activity_log_types')->onDelete('cascade');
            $table->unsignedBigInteger('document_id')->nullable();
            $table->unsignedBigInteger('document_type_id')->nullable();
            $table->unsignedBigInteger('interview_id')->nullable();
            $table->unsignedBigInteger('scholarship_id')->nullable();
            $table->unsignedBigInteger('program_id')->nullable();
            $table->unsignedBigInteger('application_id')->nullable(); // "aplication" typo fixed
            $table->string('action');
            $table->text('description');
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();
            $table->timestamps();
            $table->foreign('document_id')->references('id')->on('documents')->onDelete('set null');
            $table->foreign('document_type_id')->references('id')->on('document_types')->onDelete('set null');
            $table->foreign('interview_id')->references('id')->on('interviews')->onDelete('set null');
            $table->foreign('scholarship_id')->references('id')->on('scholarships')->onDelete('set null');
            $table->foreign('program_id')->references('id')->on('scholarship_programs')->onDelete('set null');
            $table->foreign('application_id')->references('id')->on('applications')->onDelete('set null'); // Typo fixed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
     
    }
};
