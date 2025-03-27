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
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('program_id')->nullable();
            $table->date('application_date');
            $table->enum('status', [
                'scholarship_pool',      // burs_havuzu
                'pre_approved',          // on_kabul
                'rejected',              // red_edildi
                'awaiting_documents',    // evrak_bekleniyor
                'documents_under_review', // evrak_incelemede
                'interview_pool',        // mulakat_havuzu
                'awaiting_evaluation',   // degerlendirme_bekleniyor
                'interview_scheduled',   // mulakat_planlandi
                'interview_completed',   // mulakat_tamamlandi
                'accepted',              // kabul_edildi
                'final_acceptance',      // kesin_kabul
                'previous_scholar'       // onceki_burslu
            ])->default('awaiting_evaluation');
            $table->unsignedBigInteger('pre_approved_by')->nullable(); // on_kabul_by (Admin who pre-approved)
            $table->dateTime('pre_approved_at')->nullable(); // on_kabul_at
            $table->unsignedBigInteger('rejected_by')->nullable(); // red_by (Admin who rejected)
            $table->dateTime('rejected_at')->nullable(); // red_at
            $table->unsignedBigInteger('document_reviewed_by')->nullable(); // evrak_inceleme_by (Admin who reviewed documents)
            $table->dateTime('document_reviewed_at')->nullable(); // evrak_inceleme_at
            $table->unsignedBigInteger('interview_pool_by')->nullable(); // mulakat_havuzu_by (Admin who moved to interview pool)
            $table->dateTime('interview_pool_at')->nullable(); // mulakat_havuzu_at
            $table->unsignedBigInteger('accepted_by')->nullable(); // kabul_by (Admin who accepted)
            $table->dateTime('accepted_at')->nullable(); // kabul_at
            $table->unsignedBigInteger('final_acceptance_by')->nullable(); // kesin_kabul_by (Admin who gave final acceptance)
            $table->dateTime('final_acceptance_at')->nullable(); // kesin_kabul_at
            $table->text('notes')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->boolean('are_documents_approved')->default(false);
            $table->boolean('is_interview_completed')->default(false);
            $table->date('approval_date')->nullable();
            $table->timestamps();
            
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
                  
            $table->foreign('program_id')
                  ->references('id')
                  ->on('scholarship_programs')
                  ->onDelete('cascade');
                  
            $table->foreign('pre_approved_by') // on_kabul_by
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('rejected_by') // red_by
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('document_reviewed_by') // evrak_inceleme_by
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('interview_pool_by') // mulakat_havuzu_by
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('accepted_by') // kabul_by
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
                  
            $table->foreign('final_acceptance_by') // kesin_kabul_by
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
        Schema::dropIfExists('applications');
    }
};
