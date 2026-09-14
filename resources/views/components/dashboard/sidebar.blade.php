@php
    $isSuperadmin = $area === 'superadmin';
    $shop = $isSuperadmin ? null : $currentShop();
    $pendingOrderCount = $pendingOrderCount();
    $pendingApplicationCount = $pendingApplicationCount();
@endphp

<div
    x-cloak
    x-show="sidebarOpen"
    x-transition.opacity
    class="fixed inset-0 z-30 bg-zinc-950/40 lg:hidden"
    @click="sidebarOpen = false"
></div>

<aside
    class="fixed inset-y-0 left-0 z-40 flex w-72 shrink-0 flex-col border-r border-zinc-200 bg-white transition-transform dark:border-zinc-800 dark:bg-zinc-900 lg:static lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>
    <div class="flex h-16 items-center gap-3 border-b border-zinc-200 px-5 dark:border-zinc-800">
        <x-logo class="h-8" />
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold">{{ config('app.name') }}</p>
            <p class="truncate text-xs text-zinc-500">{{ $isSuperadmin ? 'Superadmin' : ($shop?->name ?? 'Store admin') }}</p>
        </div>
    </div>

    <nav class="flex-1 overflow-y-auto px-3 py-5">
        @if ($isSuperadmin)
            <x-dashboard.nav-group label="Dashboard">
                <x-dashboard.nav-link :href="route('superadmin.dashboard')" :active="request()->routeIs('superadmin.dashboard')" icon="home">Home</x-dashboard.nav-link>
            </x-dashboard.nav-group>

            <x-dashboard.nav-group label="Platform">
                @if ($pendingApplicationCount > 0)
                    <a href="{{ route('superadmin.applications') }}" wire:navigate class="mb-2 flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800 dark:bg-amber-950/40 dark:text-amber-200">
                        {{ $pendingApplicationCount }} {{ \Illuminate\Support\Str::plural('store request', $pendingApplicationCount) }} pending
                    </a>
                @endif
                <x-dashboard.nav-link :href="route('superadmin.shops')" :active="request()->routeIs('superadmin.shops')" icon="store">Stores</x-dashboard.nav-link>
                <x-dashboard.nav-link :href="route('superadmin.applications')" :active="request()->routeIs('superadmin.applications')" icon="clipboard" :badge="$pendingApplicationCount">Store requests</x-dashboard.nav-link>
                <x-dashboard.nav-link :href="route('superadmin.users')" :active="request()->routeIs('superadmin.users')" icon="users">Users</x-dashboard.nav-link>
                <x-dashboard.nav-link :href="route('superadmin.products')" :active="request()->routeIs('superadmin.products')" icon="box">Products</x-dashboard.nav-link>
            </x-dashboard.nav-group>

            <x-dashboard.nav-group label="Sales">
                @if ($pendingOrderCount > 0)
                    <a href="{{ route('superadmin.orders') }}" wire:navigate class="mb-2 flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800 dark:bg-amber-950/40 dark:text-amber-200">
                        {{ $pendingOrderCount }} {{ \Illuminate\Support\Str::plural('order request', $pendingOrderCount) }} pending
                    </a>
                @endif
                <x-dashboard.nav-link :href="route('superadmin.orders')" :active="request()->routeIs('superadmin.orders')" icon="inbox" :badge="$pendingOrderCount">Orders</x-dashboard.nav-link>
            </x-dashboard.nav-group>
        @else
            <x-dashboard.nav-group label="Dashboard">
                <x-dashboard.nav-link :href="route('shop.dashboard')" :active="request()->routeIs('shop.dashboard')" icon="home">Home</x-dashboard.nav-link>
            </x-dashboard.nav-group>

            <x-dashboard.nav-group label="Catalog">
                <x-dashboard.nav-link :href="route('shop.products')" :active="request()->routeIs('shop.products', 'shop.products.edit')" icon="box">Products</x-dashboard.nav-link>
                <x-dashboard.nav-link :href="route('shop.categories')" :active="request()->routeIs('shop.categories')" icon="tag">Categories</x-dashboard.nav-link>
            </x-dashboard.nav-group>

            <x-dashboard.nav-group label="Team">
                <x-dashboard.nav-link :href="route('shop.admins')" :active="request()->routeIs('shop.admins')" icon="users">Admins</x-dashboard.nav-link>
            </x-dashboard.nav-group>

            <x-dashboard.nav-group label="Sales">
                @if ($pendingOrderCount > 0)
                    <a href="{{ route('shop.orders') }}" wire:navigate class="mb-2 flex items-center gap-2 rounded-lg bg-amber-50 px-3 py-2 text-xs font-medium text-amber-800 dark:bg-amber-950/40 dark:text-amber-200">
                        {{ $pendingOrderCount }} {{ \Illuminate\Support\Str::plural('order request', $pendingOrderCount) }} pending
                    </a>
                @endif
                <x-dashboard.nav-link :href="route('shop.orders')" :active="request()->routeIs('shop.orders')" icon="inbox" :badge="$pendingOrderCount">Orders</x-dashboard.nav-link>
            </x-dashboard.nav-group>
        @endif
    </nav>

    <div class="border-t border-zinc-200 p-4 dark:border-zinc-800">
        @if (! $isSuperadmin && $shop)
            <a href="{{ route('shops.show', $shop) }}" class="text-sm font-medium text-softora hover:underline">View public store</a>
        @endif
    </div>
</aside>
