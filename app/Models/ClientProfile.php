<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'social_security_number',
        'date_of_birth',
        'occupation',
        'marital_status',
        'address',
        'city',
        'state',
        'zip_code',
        'has_dependents',
        'spouse_first_name',
        'spouse_middle_name',
        'spouse_last_name',
        'spouse_social_security_number',
        'spouse_date_of_birth',
        'spouse_occupation',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'spouse_date_of_birth' => 'date',
        'has_dependents' => 'boolean',
        'social_security_number' => 'encrypted',
        'spouse_social_security_number' => 'encrypted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dependents()
    {
        return $this->hasMany(Dependent::class);
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
