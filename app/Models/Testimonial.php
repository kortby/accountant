<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Testimonial extends Model
{
    protected $fillable = [
        'user_id',
        'author_name',
        'author_title',
        'author_company',
        'author_location',
        'content',
        'rating',
        'project_type',
        'service_id',
        'image_path',
        'is_featured',
        'is_approved',
        'is_displayed',
        'display_order',
        'project_date',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_approved' => 'boolean',
        'is_displayed' => 'boolean',
        'project_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
