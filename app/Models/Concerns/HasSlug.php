<?php

namespace App\Models\Concerns;

use Illuminate\Support\Str;

/**
 * Generates a unique, URL-friendly slug from the model's "name" attribute
 * and uses it as the route key ({category} in a URL resolves by slug).
 */
trait HasSlug
{
    /**
     * Laravel calls boot{TraitName}() automatically when the model boots.
     */
    protected static function bootHasSlug(): void
    {
        static::saving(function ($model) {
            // Only rebuild the slug when it's missing or the name changed.
            if (blank($model->slug) || $model->isDirty('name')) {
                $model->slug = $model->uniqueSlug(Str::slug($model->name));
            }
        });
    }

    /**
     * Append -2, -3, ... until no other record uses the slug.
     */
    protected function uniqueSlug(string $base): string
    {
        $base = $base !== '' ? $base : 'item'; // name had no sluggable characters
        $slug = $base;
        $counter = 2;

        while ($this->slugExists($slug)) {
            $slug = "{$base}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    /**
     * Is the slug taken by a record other than this one?
     */
    protected function slugExists(string $slug): bool
    {
        return static::where('slug', $slug)
            ->when($this->exists, fn ($query) => $query->whereKeyNot($this->getKey()))
            ->exists();
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
