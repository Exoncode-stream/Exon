<?php

namespace App\Models;

use App\Models\Traits\HasCommentsAndLikes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Video extends Model
{
    use HasFactory, HasCommentsAndLikes;

    protected $fillable = [
        'title',
        'youtube_id',
        'category',
    ];
}
