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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('document_type_id');
            $table->unsignedBigInteger('application_id')->nullable();
            $table->string('file_path');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->dateTime('reviewed_at')->nullable();
            $table->unsignedBigInteger('reviewed_by')->nullable(); // Admin ID who reviewed
            $table->string('reason')->nullable();
            $table->string('admin_comment')->nullable();
            $table->timestamps();
            
            // Foreign key constraints have been removed and will be added in a separate migration
            // after all related tables are created to avoid migration order issues.
            // The following constraints were removed:
            // - user_id references users(id) on delete cascade
            // - document_type_id references document_types(id) on delete cascade
            // - aplication_id references aplications(id) on delete set null
            // - reviewed_by references users(id) on delete set null
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
