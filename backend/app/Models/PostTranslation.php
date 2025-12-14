<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Model;

class PostTranslation extends Model
{
    use GeneratesSlug;
    protected $fillable = [
        'post_id',
        'locale',
        'title',
        'slug',
        'excerpt',
        'content',
    ];

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
    protected static function booted()
    {
        static::saving(function ($translation) {
            if ($translation->isDirty('title')) {
                $translation->slug = $translation->generateSlug(
                    $translation->title,
                    $translation->locale
                );
            }
        });
    }
}
