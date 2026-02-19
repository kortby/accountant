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
        Schema::table('income_sources', function (Blueprint $table) {
            $table->string('employer_name')->nullable()->after('source_name');
            $table->string('payer_ein')->nullable()->after('employer_name');
            $table->decimal('federal_tax_withheld', 12, 2)->default(0)->after('amount');
            $table->decimal('state_tax_withheld', 12, 2)->default(0)->after('federal_tax_withheld');
            $table->string('state', 2)->nullable()->after('state_tax_withheld');
            $table->boolean('ai_extracted')->default(false)->after('description');
            $table->decimal('ai_confidence', 5, 2)->nullable()->after('ai_extracted');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('income_sources', function (Blueprint $table) {
            $table->dropColumn([
                'employer_name',
                'payer_ein',
                'federal_tax_withheld',
                'state_tax_withheld',
                'state',
                'ai_extracted',
                'ai_confidence',
            ]);
        });
    }
};
