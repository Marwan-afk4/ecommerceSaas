<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Fulfilled = 'fulfilled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
            self::Fulfilled => 'Fulfilled',
        };
    }

    public function isPending(): bool
    {
        return $this === self::Pending;
    }
}
