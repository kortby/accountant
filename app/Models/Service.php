<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Service extends Model
{
    use HasFactory, SoftDeletes, HasSlug;

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $fillable = [
        'name',
        'slug',
        'description',
        'short_description',
        'base_price',
        'duration',
        'image_path',
        'icon',
        'is_featured',
        'is_emergency',
        'is_active',
        'display_order',
        'included_services',
        'excluded_services',
        'category',
        'requirements',
        'warning'
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_emergency' => 'boolean',
        'is_active' => 'boolean',
        'included_services' => 'array',
        'excluded_services' => 'array'
    ];



    // Relationships
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function testimonials()
    {
        return $this->hasMany(Testimonial::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeEmergency($query)
    {
        return $query->where('is_emergency', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    public function scopeCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Accessor for formatted price
    public function getFormattedPriceAttribute()
    {
        return $this->base_price ? '$' . number_format($this->base_price, 2) : 'Contact for Price';
    }
}
