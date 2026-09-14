<div>
    <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-softora to-indigo-400 p-6 text-white shadow-sm sm:p-8">
        <div class="relative z-10 max-w-2xl">
            <p class="text-sm text-white/80">{{ now()->translatedFormat('l, d M Y') }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Welcome back, {{ auth()->user()?->name }}</h1>
            <p class="mt-2 text-white/80">Here is what is happening in {{ $shop->name }} today.</p>
        </div>
        <img src="{{ asset('images/softora-logo.png') }}" alt="" class="pointer-events-none absolute right-4 bottom-[-20px] h-28 opacity-20 sm:h-36">
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-dashboard.stat-card label="Products" :value="$productCount" :href="route('shop.products')" />
        <x-dashboard.stat-card label="Categories" :value="$categoryCount" :href="route('shop.categories')" />
        <x-dashboard.stat-card label="Pending orders" :value="$pendingOrderCount" :href="route('shop.orders')" />
        <x-dashboard.stat-card label="Admins" :value="$adminCount" :href="route('shop.admins')" />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">Recent order requests</h2>
                <a href="{{ route('shop.orders') }}" wire:navigate class="text-sm text-softora hover:underline">View all</a>
            </div>
            @forelse ($recentOrders as $order)
                <div class="flex items-center justify-between border-t border-zinc-100 py-3 text-sm first:border-t-0 dark:border-zinc-800" wire:key="order-{{ $order->id }}">
                    <div>
                        <p class="font-medium">{{ $order->customer_name }}</p>
                        <p class="text-zinc-500">{{ $order->items->pluck('name')->join(', ') }}</p>
                    </div>
                    <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium dark:bg-zinc-800">{{ $order->status->label() }}</span>
                </div>
            @empty
                <p class="text-sm text-zinc-500">No order requests yet.</p>
            @endforelse
        </section>

        <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">Latest products</h2>
                <a href="{{ route('shop.products') }}" wire:navigate class="text-sm text-softora hover:underline">Manage</a>
            </div>
            @forelse ($recentProducts as $product)
                <div class="flex items-center justify-between border-t border-zinc-100 py-3 text-sm first:border-t-0 dark:border-zinc-800" wire:key="home-product-{{ $product->id }}">
                    <div>
                        <p class="font-medium">{{ $product->name }}</p>
                        <p class="text-zinc-500">{{ $product->category->name }}</p>
                    </div>
                    <span class="capitalize text-zinc-500">{{ $product->status->value }}</span>
                </div>
            @empty
                <p class="text-sm text-zinc-500">Add your first product from Catalog.</p>
            @endforelse
        </section>
    </div>
</div>
