<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogRevision extends Model
{
    protected $fillable = [
        'post_id', 'user_id', 'content', 'changes',
        'revision_type'
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    // Belongs to one post
    public function post()
    {
        return $this->belongsTo(BlogPost::class, 'post_id');
    }

    // Belongs to one user (who made the revision)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
