<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UniqueSlugService
{
    /**
     * @param  class-string<Model>  $modelClass
     */
    public function make(string $modelClass, string $source, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($source) ?: Str::random(8);
        $slug = $baseSlug;
        $counter = 2;

        while ($modelClass::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
