<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

trait GeneratesSlug
{
    /**
     * Generate a unique slug for the model.
     */
    public function generateSlug(
        string $title,
        ?string $locale = null,
        string $column = 'slug'
    ): string {
        $slug = Str::slug($title);
        $baseSlug = $slug;
        $counter = 1;

        $query = static::query()
            ->where($column, $slug);

        if ($locale && $this->hasColumn('locale')) {
            $query->where('locale', $locale);
        }

        if ($this->exists) {
            $query->whereKeyNot($this->getKey());
        }

        while ($query->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $query->where($column, $slug);
            $counter++;
        }

        return $slug;
    }

    /**
     * Check if model table has a given column.
     */
    protected function hasColumn(string $column): bool
    {
        return Schema::hasColumn($this->getTable(), $column);
    }
}
