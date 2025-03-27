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
        // Create scholarship programs
        Schema::create('scholarship_programs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('default_amount', 10, 2);
            $table->date('application_start_date');
            $table->date('application_end_date');
            $table->date('program_start_date');
            $table->date('program_end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('max_recipients')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->text('requirements')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            
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
        Schema::dropIfExists('scholarship_programs');
    }
};
