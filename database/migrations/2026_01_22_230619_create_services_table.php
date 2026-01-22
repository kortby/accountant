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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->text('short_description');
            $table->decimal('base_price', 10, 2)->nullable();
            $table->string('duration')->nullable(); // e.g., "2-3 hours", "1 day"
            $table->string('image_path')->nullable();
            $table->string('icon')->nullable(); // For UI icons
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_emergency')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->nullable();
            $table->json('included_services')->nullable(); // List of what's included
            $table->json('excluded_services')->nullable(); // List of what's not included
            $table->string('category')->nullable(); // e.g., "Residential", "Commercial"
            $table->text('requirements')->nullable(); // Any special requirements
            $table->text('warning')->nullable(); // Any warnings or important notices
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
