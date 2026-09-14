<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Models\Concerns\BelongsToShop;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'shop_id',
    'customer_name',
    'customer_email',
    'customer_phone',
    'status',
    'notes',
])]
class Order extends Model
{
    /** @use HasFactory<OrderFactory> */
    use BelongsToShop, HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => OrderStatus::Pending->value,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
        ];
    }

    /**
     * @return HasMany<OrderItem, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->orderBy('id');
    }

    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->where('status', OrderStatus::Pending);
    }
}
