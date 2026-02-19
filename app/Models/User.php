<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'password',
        'google_id',
        'email_verified_at',
        'ai_enabled',
    ];

    /**
     * Accessor for the `name` attribute for backward compatibility.
     */
    public function getNameAttribute(): string
    {
        return trim((string) ($this->first_name.' '.($this->last_name ?? '')));
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'ai_enabled' => 'boolean',
        ];
    }

    // Relationships
    public function clientProfile()
    {
        return $this->hasOne(ClientProfile::class);
    }

    public function taxReturns()
    {
        return $this->hasMany(TaxReturn::class);
    }

    public function sentMessages()
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    public function testimonial()
    {
        return $this->hasOne(Testimonial::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function accountantBookings()
    {
        return $this->hasMany(Booking::class, 'accountant_id');
    }

    // One-to-Many relationship with posts (as author)
    public function blogPosts()
    {
        return $this->hasMany(BlogPost::class, 'user_id');
    }

    // One-to-Many relationship with comments
    public function blogComments()
    {
        return $this->hasMany(BlogComment::class, 'user_id');
    }

    // One-to-Many relationship with revisions
    public function blogRevisions()
    {
        return $this->hasMany(BlogRevision::class, 'user_id');
    }
}
