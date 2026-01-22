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
        Schema::create('testimonials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('author_name');
            $table->string('author_title')->nullable(); // e.g., "Business Owner", "Homeowner"
            $table->string('author_company')->nullable();
            $table->string('author_location')->nullable();
            $table->text('content');
            $table->integer('rating')->nullable(); // 1-5 star rating
            $table->string('project_type')->nullable(); // e.g., "Commercial Installation", "Emergency Repair"
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('image_path')->nullable(); // For author's photo
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_approved')->default(false);
            $table->boolean('is_displayed')->default(true);
            $table->integer('display_order')->nullable();
            $table->date('project_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('testimonials');
    }
};
