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
        Schema::create('document_extractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tax_return_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('media_id');
            $table->string('status')->default('pending'); // pending, processing, completed, failed
            $table->string('document_type')->nullable(); // w2, 1099_nec, 1099_int, 1099_div, 1099_k, 1098, receipt, other
            $table->json('extracted_data')->nullable();
            $table->decimal('confidence_score', 5, 2)->nullable();
            $table->text('error_message')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['tax_return_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_extractions');
    }
};
