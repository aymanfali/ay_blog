<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasTranslations, SoftDeletes;

    protected $translationModel = CategoryTranslation::class;

    protected $fillable = [
        'image',
        'status',
        'published_at',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
