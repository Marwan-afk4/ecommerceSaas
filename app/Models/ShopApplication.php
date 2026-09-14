<?php

namespace App\Models;

use App\Enums\ShopApplicationStatus;
use Database\Factories\ShopApplicationFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Fillable([
    'name',
    'tagline',
    'description',
    'public_owner_name',
    'public_email',
    'public_phone',
    'logo_path',
    'admin_name',
    'admin_email',
    'admin_password',
    'status',
    'shop_id',
    'reviewed_at',
])]
#[Hidden(['admin_password'])]
class ShopApplication extends Model
{
    /** @use HasFactory<ShopApplicationFactory> */
    use HasFactory;

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => ShopApplicationStatus::Pending->value,
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'status' => ShopApplicationStatus::class,
            'admin_password' => 'encrypted',
            'reviewed_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Shop, $this>
     */
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function logoUrl(): ?string
    {
        if (! filled($this->logo_path)) {
            return null;
        }

        return Storage::disk('public')->url($this->logo_path);
    }

    #[Scope]
    protected function pending(Builder $query): Builder
    {
        return $query->where('status', ShopApplicationStatus::Pending);
    }
}
