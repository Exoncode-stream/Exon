<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Video;
use Illuminate\Database\Eloquent\Model;

class PolymorphicResolver
{
    /**
     * Resolves the target model (Article or Video) by type string and ID.
     */
    public static function resolve(string $type, int $id): ?Model
    {
        return match ($type) {
            'articles' => Article::find($id),
            'videos'   => Video::find($id),
            default    => null,
        };
    }
}
