<?php

namespace App\Enums;

enum ShopApplicationStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending',
            self::Accepted => 'Accepted',
            self::Rejected => 'Rejected',
        };
    }

    public function isPending(): bool
    {
        return $this === self::Pending;
    }
}
