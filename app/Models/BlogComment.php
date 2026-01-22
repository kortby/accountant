<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogComment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'post_id',
        'user_id',
        'parent_id',
        'author_name',
        'author_email',
        'author_website',
        'content',
        'is_approved',
        'ip_address',
        'user_agent'
    ];

    protected $casts = [
        'is_approved' => 'boolean',
    ];

    // Belongs to one post
    public function post()
    {
        return $this->belongsTo(BlogPost::class, 'post_id');
    }

    // Optional relationship to registered user
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Self-referential relationships for nested comments
    public function parent()
    {
        return $this->belongsTo(BlogComment::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(BlogComment::class, 'parent_id');
    }
}
