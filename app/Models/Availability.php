<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $user_id
 * @property array<string> $days
 * @property string $start_time
 * @property string $end_time
 */
class Availability extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'days',
        'start_time',
        'end_time',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * We cast 'days' to an array so Laravel automatically serializes/deserializes it to/from JSON.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'days' => 'array',
    ];

    /**
     * Get the user that owns the availability slot.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
