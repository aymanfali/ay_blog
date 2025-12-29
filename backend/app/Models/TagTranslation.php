<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Model;

class TagTranslation extends Model
{
    use GeneratesSlug;
    protected $fillable = [
        'tag_id',
        'locale',
        'name',
        'slug',
    ];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    protected static function booted()
    {
        static::saving(function ($translation) {
            if ($translation->isDirty('name')) {
                $translation->slug = $translation->generateSlug(
                    $translation->name,
                    $translation->locale
                );
            }
        });
    }
}
