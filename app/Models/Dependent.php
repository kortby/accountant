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
        'social_security_number'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function clientProfile()
    {
        return $this->belongsTo(ClientProfile::class);
    }
}
