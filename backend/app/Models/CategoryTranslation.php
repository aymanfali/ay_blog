<?php

namespace App\Models;

use App\Models\Concerns\GeneratesSlug;
use Illuminate\Database\Eloquent\Model;

class CategoryTranslation extends Model
{
    use GeneratesSlug;
    protected $fillable = [
        'category_id',
        'locale',
        'name',
        'slug',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
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
