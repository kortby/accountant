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
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            // Link to the user/coach who owns this booking (the 'user' table)
            $table->foreignId('coach_id')->constrained('users')->onDelete('cascade');
            
            // Link to the specific recurring availability slot the client chose
            // Note: This assumes the client books a slot tied to a specific Availability ID.
            $table->foreignId('availability_id')->nullable()->constrained('availabilities')->onDelete('set null'); 

            // Client Information (If they are not a registered user)
            $table->string('client_name');
            $table->string('client_email');
            
            // Booking details (when the specific appointment is)
            $table->date('booking_date'); 
            $table->time('start_time'); 
            $table->time('end_time');

            // Type of service (e.g., 'Coaching', 'Etiquette: Dining')
            $table->string('service_type');

            // Location preference: 'online', 'in-person'
            $table->enum('location_type', ['online', 'in-person']);
            
            // Status of the booking: 'pending', 'confirmed', 'canceled'
            $table->enum('status', ['pending', 'confirmed', 'canceled'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
