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
        // Create scholarships for recipients
        Schema::create('scholarships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('application_id');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('last_updated_by')->nullable();
            $table->string('name');
            $table->date('start_date');
            $table->decimal('amount', 10, 2);
            $table->date('end_date')->nullable();
            $table->enum('status', ['aktif', 'durduruldu', 'sonlandirildi'])->default('aktif');
            $table->text('status_reason')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('program_id')
                  ->references('id')
                  ->on('scholarship_programs')
                  ->onDelete('cascade');
                  
            $table->foreign('application_id')
                  ->references('id')
                  ->on('applications')
                  ->onDelete('cascade');
                  
            $table->foreign('approved_by')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('last_updated_by')
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
        Schema::dropIfExists('scholarships');
    }
};
