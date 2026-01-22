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
        Schema::create('availabilities', function (Blueprint $table) {
            $table->id();
            
            // Assuming this is for a specific user/coach (essential for any dashboard)
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Day of the week (e.g., 'Monday', 'Tuesday', or '['Monday', 'Tuesday']' as JSON/string)
            // Given the Vue component stores it as an array, using a JSON column is best practice.
            $table->json('days'); 
            
            // Time string (e.g., '09:00', '17:00')
            $table->time('start_time');
            $table->time('end_time');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availabilities');
    }
};
