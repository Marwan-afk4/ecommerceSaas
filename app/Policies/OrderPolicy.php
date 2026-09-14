<?php

namespace App\Policies;

use App\Policies\Concerns\AuthorizesShopCatalog;

class OrderPolicy
{
    use AuthorizesShopCatalog;
}
