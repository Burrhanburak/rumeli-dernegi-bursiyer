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
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('application_id')->nullable();
            $table->unsignedBigInteger('interviewer_admin_id')->nullable();
            $table->dateTime('interview_date');
            $table->string('location')->nullable();
            $table->text('notes')->nullable();
            $table->text('interview_questions')->nullable();
            $table->text('interview_answers')->nullable();
            $table->integer('interview_score')->nullable();
            $table->enum('interview_result', ['kabul', 'red', 'beklemede'])->default('beklemede');
            $table->enum('status', ['planlandı', 'tamamlandı', 'iptal_edildi'])->default('planlandı');
            $table->boolean('is_online')->default(false);
            $table->string('meeting_link')->nullable();
            $table->dateTime('notification_sent_at')->nullable();
            $table->dateTime('reminder_sent_at')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('application_id')
                  ->references('id')
                  ->on('applications')
                  ->onDelete('cascade');
                  
            $table->foreign('interviewer_admin_id')
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
        Schema::dropIfExists('interviews');
    }
};
