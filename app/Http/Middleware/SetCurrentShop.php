<?php

namespace App\Http\Middleware;

use App\Models\Shop;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Context;
use Symfony\Component\HttpFoundation\Response;

class SetCurrentShop
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user === null) {
            return $next($request);
        }

        $shop = $this->resolveShop($request);

        if ($shop instanceof Shop) {
            $request->session()->put('current_shop_id', $shop->id);
            Context::add('shop_id', $shop->id);
            Context::add('shop_slug', $shop->slug);
            $request->attributes->set('currentShop', $shop);
        }

        return $next($request);
    }

    private function resolveShop(Request $request): ?Shop
    {
        $user = $request->user();

        if ($user === null) {
            return null;
        }

        $shops = $user->shops()->active()->orderBy('name')->orderBy('id');
        $requestedId = $request->session()->get('current_shop_id');

        if (filled($requestedId)) {
            $current = (clone $shops)->whereKey($requestedId)->first();

            if ($current instanceof Shop) {
                return $current;
            }
        }

        return $shops->first();
    }
}
