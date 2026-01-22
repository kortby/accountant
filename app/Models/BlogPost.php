<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class BlogPost extends Model
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
        'user_id',
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'featured_image_alt',
        'featured_image_caption',
        'meta_title',
        'meta_description',
        'canonical_url',
        'structured_data',
        'social_image',
        'social_title',
        'social_description',
        'status',
        'published_at',
        'scheduled_for',
        'is_featured',
        'allow_comments',
        'view_count',
        'share_count',
        'comment_count',
        'reading_time',
        'table_of_contents',
        'template',
        'custom_fields'
    ];

    protected $casts = [
        'structured_data' => 'array',
        'custom_fields' => 'array',
        'table_of_contents' => 'array',
        'is_featured' => 'boolean',
        'allow_comments' => 'boolean',
        'published_at' => 'datetime',
        'scheduled_for' => 'datetime'
    ];

    // Belongs to one author (User)
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Belongs to one category
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    // Many-to-Many relationship with tags
    public function tags()
    {
        return $this->belongsToMany(BlogTag::class, 'blog_post_tag', 'post_id', 'tag_id')
            ->withTimestamps();
    }

    // Has many comments
    public function comments()
    {
        return $this->hasMany(BlogComment::class, 'post_id');
    }

    // Has many revisions
    public function revisions()
    {
        return $this->hasMany(BlogRevision::class, 'post_id');
    }

    // Many-to-Many self-referential relationship for related posts
    public function relatedPosts()
    {
        return $this->belongsToMany(BlogPost::class, 'blog_related_posts', 'post_id', 'related_post_id')
            ->withPivot('order')
            ->withTimestamps();
    }

    // Inverse of related posts
    public function relatedToPosts()
    {
        return $this->belongsToMany(BlogPost::class, 'blog_related_posts', 'related_post_id', 'post_id')
            ->withPivot('order')
            ->withTimestamps();
    }
}
