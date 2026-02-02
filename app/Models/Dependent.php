<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dependent extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_profile_id',
        'first_name',
        'middle_name',
        'last_name',
        'date_of_birth',
        'relationship',
        'social_security_number',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'social_security_number' => 'encrypted',
    ];

    public function clientProfile()
    {
        return $this->belongsTo(ClientProfile::class);
    }

    /**
     * Get masked SSN (e.g., ***-**-1234)
     */
    public function getMaskedSsnAttribute(): string
    {
        if (! $this->social_security_number) {
            return '***-**-****';
        }

        $ssn = str_replace('-', '', $this->social_security_number);
        $lastFour = substr($ssn, -4);

        return "***-**-{$lastFour}";
    }
}
