<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class SetShopContext
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $shop = $request->route('shop');

        if ($shop instanceof Shop) {
            Context::add('shop_id', $shop->getKey());
            Context::add('shop_slug', $shop->slug);
        }

        return $next($request);
    }
}
