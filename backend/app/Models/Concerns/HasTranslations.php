<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasTranslations
{
    public function translations(): HasMany
    {
        return $this->hasMany($this->translationModel);
    }

    public function translation(?string $locale = null)
    {
        $locale ??= app()->getLocale();

        return $this->translations
            ->firstWhere('locale', $locale)
            ??  $this->translations
            ->firstWhere('locale', config('app.fallback_locale'));
    }

    public function scopeWithTranslation($query, ?string $locale = null)
    {
        $locale ??= app()->getLocale();

        return $query->with([
            'translations' => function ($q) use ($locale) {
                $q->where('locale', $locale)
                    ->orWhere('locale', config('app.fallback_locale'));
            },
        ]);
    }
}
