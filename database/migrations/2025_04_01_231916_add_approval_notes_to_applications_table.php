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
            $table->text('approval_notes')->nullable()->after('approval_date');
            $table->decimal('scholarship_amount', 10, 2)->nullable()->after('approval_notes');
            $table->date('scholarship_start_date')->nullable()->after('scholarship_amount');
            $table->date('scholarship_end_date')->nullable()->after('scholarship_start_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('approval_notes');
            $table->dropColumn('scholarship_amount');
            $table->dropColumn('scholarship_start_date');
            $table->dropColumn('scholarship_end_date');
        });
    }
};
