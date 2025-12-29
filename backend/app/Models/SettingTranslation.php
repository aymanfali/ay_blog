<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingTranslation extends Model
{
    protected $fillable = ['setting_id', 'locale', 'value'];



    public function setting()
    {
        return $this->belongsTo(Setting::class);
    }
}
