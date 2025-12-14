<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasTranslations;

    protected $fillable = ['group', 'key'];
    protected $translationModel = SettingTranslation::class;
}
