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
        'has_dependents'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'has_dependents' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function dependents()
    {
        return $this->hasMany(Dependent::class);
    }
}
