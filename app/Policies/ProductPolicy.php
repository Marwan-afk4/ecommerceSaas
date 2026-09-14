<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesShopCatalog;

class ProductPolicy
{
    use AuthorizesShopCatalog;
}
