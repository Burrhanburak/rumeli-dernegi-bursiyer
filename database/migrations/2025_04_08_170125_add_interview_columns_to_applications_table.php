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
        Schema::table('applications', function (Blueprint $table) {
            // Sadece eksik olan sütun
            if (!Schema::hasColumn('applications', 'is_interview_scheduled')) {
                $table->boolean('is_interview_scheduled')->default(false)->after('are_documents_approved');
            }
            
            // Diğer sütunları kontrol edelim ve eksikse ekleyelim
            if (!Schema::hasColumn('applications', 'is_interview_completed')) {
                $table->boolean('is_interview_completed')->default(false)->after('is_interview_scheduled');
            }
            
            if (!Schema::hasColumn('applications', 'interview_result')) {
                $table->string('interview_result')->nullable()->after('is_interview_completed');
            }
            
            if (!Schema::hasColumn('applications', 'interview_score')) {
                $table->integer('interview_score')->nullable()->after('interview_result');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            if (Schema::hasColumn('applications', 'is_interview_scheduled')) {
                $table->dropColumn('is_interview_scheduled');
            }
            
            // Diğer sütunları bu migration'da eklediyse sil
            // Eski sütunları silmeyelim, hata alırız
        });
    }
};
