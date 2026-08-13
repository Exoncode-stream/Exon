<?php

namespace App\Models\Traits;

use App\Models\Comment;
use App\Models\Like;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasCommentsAndLikes
{
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }
}
