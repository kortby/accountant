<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'coach_id',
        'availability_id',
        'client_name',
        'client_email',
        'booking_date',
        'start_time',
        'end_time',
        'service_type',
        'location_type',
        'status',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booking_date' => 'date',
    ];

    /**
     * Get the coach associated with the booking.
     */
    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    /**
     * Get the recurring availability slot the booking was based on.
     */
    public function availabilitySlot()
    {
        return $this->belongsTo(Availability::class, 'availability_id');
    }
}
