<?php

namespace App\Livewire\Concerns;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Model;

trait InteractsWithCurrentShop
{
    protected function currentShop(): Shop
    {
        $shopId = Shop::currentId();
        $shop = $shopId !== null ? Shop::query()->find($shopId) : null;

        abort_unless($shop instanceof Shop, 404);

        $this->authorize('view', $shop);

        return $shop;
    }

    /**
     * @template T of Model
     *
     * @param  class-string<T>  $class
     * @return T
     */
    protected function shopRecord(string $class, int $id): Model
    {
        $record = $class::query()
            ->where('shop_id', $this->currentShop()->id)
            ->whereKey($id)
            ->first();

        abort_unless($record instanceof $class, 404);

        return $record;
    }
}
