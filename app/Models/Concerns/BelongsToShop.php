<?php

namespace App\Models\Concerns;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToShop
{
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    protected static function bootBelongsToShop(): void
    {
        static::creating(function (Model $model): void {
            if (filled($model->shop_id)) {
                return;
            }

            $shopId = Shop::currentId();

            if (filled($shopId)) {
                $model->shop_id = $shopId;
            }
        });
    }
}
