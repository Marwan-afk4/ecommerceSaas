<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesShopCatalog;

class ProductVariantPolicy
{
    use AuthorizesShopCatalog;
}
