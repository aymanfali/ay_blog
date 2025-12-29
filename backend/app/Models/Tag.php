<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasTranslations;

    protected $translationModel = TagTranslation::class;

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
