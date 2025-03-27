<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('document_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('is_required')->default(true);
            $table->string('allowed_file_types')->default('pdf,jpg,png,jpeg');
            $table->integer('max_file_size')->default(2048); // Size in KB
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
        
        // Add standard document types
        DB::table('document_types')->insert([
            ['name' => 'Kimlik Belgesi', 'description' => 'TC Kimlik kartı veya pasaport', 'is_required' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Öğrenci Belgesi', 'description' => 'Güncel dönem öğrenci belgesi', 'is_required' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Transkript', 'description' => 'Not döküm belgesi', 'is_required' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'İkametgah Belgesi', 'description' => 'Güncel ikametgah belgesi', 'is_required' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gelir Belgesi', 'description' => 'Aile gelir durumunu gösteren belge', 'is_required' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sağlık Raporu', 'description' => 'Sağlık durumu belgesi (gerekli durumlarda)', 'is_required' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Referans Mektubu', 'description' => 'Öğretim üyesinden referans mektubu', 'is_required' => false, 'created_at' => now(), 'updated_at' => now()],
        ]);
        
        // Create program document requirements table
        Schema::create('program_document_requirements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('program_id');
            $table->unsignedBigInteger('document_type_id');
            $table->boolean('is_required')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
            
            $table->foreign('program_id')
                  ->references('id')
                  ->on('scholarship_programs')
                  ->onDelete('cascade');
                  
            $table->foreign('document_type_id')
                  ->references('id')
                  ->on('document_types')
                  ->onDelete('cascade');
                  
            $table->foreign('created_by')
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
        Schema::dropIfExists('program_document_requirements');
        Schema::dropIfExists('document_types');
    }
};
