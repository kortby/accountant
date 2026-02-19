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
        Schema::table('tax_returns', function (Blueprint $table) {
            $table->string('ai_processing_status')->default('none')->after('accountant_notes'); // none, pending, processing, completed, failed
            $table->timestamp('ai_processed_at')->nullable()->after('ai_processing_status');
            $table->decimal('federal_tax_withheld', 12, 2)->default(0)->after('ai_processed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tax_returns', function (Blueprint $table) {
            $table->dropColumn([
                'ai_processing_status',
                'ai_processed_at',
                'federal_tax_withheld',
            ]);
        });
    }
};
