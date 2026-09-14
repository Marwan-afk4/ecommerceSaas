<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait GeneratesSlugFromName
{
    protected static function bootGeneratesSlugFromName(): void
    {
        static::saving(function (Model $model): void {
            if (filled($model->slug)) {
                return;
            }

            $model->slug = Str::slug((string) $model->name);
        });
    }
}
