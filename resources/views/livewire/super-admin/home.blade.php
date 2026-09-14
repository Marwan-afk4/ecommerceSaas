<div>
    <div class="relative overflow-hidden rounded-3xl bg-linear-to-br from-softora to-indigo-400 p-6 text-white shadow-sm sm:p-8">
        <div class="relative z-10 max-w-2xl">
            <p class="text-sm text-white/80">{{ now()->translatedFormat('l, d M Y') }}</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight">Welcome back, {{ auth()->user()?->name }}</h1>
            <p class="mt-2 text-white/80">Here is what is happening across every Softora store today.</p>
        </div>
        <img src="{{ asset('images/softora-logo.png') }}" alt="" class="pointer-events-none absolute right-4 bottom-[-20px] h-28 opacity-20 sm:h-36">
    </div>

    <div class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-5">
        <x-dashboard.stat-card label="Stores" :value="$shopCount" :href="route('superadmin.shops')" />
        <x-dashboard.stat-card label="Store requests" :value="$pendingApplicationCount" :href="route('superadmin.applications')" />
        <x-dashboard.stat-card label="Users" :value="$userCount" :href="route('superadmin.users')" />
        <x-dashboard.stat-card label="Products" :value="$productCount" :href="route('superadmin.products')" />
        <x-dashboard.stat-card label="Pending orders" :value="$pendingOrderCount" :href="route('superadmin.orders')" />
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">Recent stores</h2>
                <a href="{{ route('superadmin.shops') }}" wire:navigate class="text-sm text-softora hover:underline">View all</a>
            </div>
            @forelse ($recentShops as $shop)
                <div class="flex items-center justify-between border-t border-zinc-100 py-3 text-sm first:border-t-0 dark:border-zinc-800" wire:key="shop-{{ $shop->id }}">
                    <div>
                        <p class="font-medium">{{ $shop->name }}</p>
                        <p class="text-zinc-500">{{ $shop->users_count }} admins · {{ $shop->products_count }} products</p>
                    </div>
                    <span class="text-zinc-500">{{ $shop->is_active ? 'Active' : 'Off' }}</span>
                </div>
            @empty
                <p class="text-sm text-zinc-500">No stores yet.</p>
            @endforelse
        </section>

        <section class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="font-semibold">Latest store requests</h2>
                <a href="{{ route('superadmin.applications') }}" wire:navigate class="text-sm text-softora hover:underline">View all</a>
            </div>
            @forelse ($recentApplications as $application)
                <div class="flex items-center justify-between border-t border-zinc-100 py-3 text-sm first:border-t-0 dark:border-zinc-800" wire:key="application-{{ $application->id }}">
                    <div>
                        <p class="font-medium">{{ $application->name }}</p>
                        <p class="text-zinc-500">{{ $application->admin_email }}</p>
                    </div>
                    <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium dark:bg-zinc-800">{{ $application->status->label() }}</span>
                </div>
            @empty
                <p class="text-sm text-zinc-500">No store requests yet.</p>
            @endforelse
        </section>
    </div>

    <section class="mt-6 rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="mb-4 flex items-center justify-between">
            <h2 class="font-semibold">Latest order requests</h2>
            <a href="{{ route('superadmin.orders') }}" wire:navigate class="text-sm text-softora hover:underline">View all</a>
        </div>
        @forelse ($recentOrders as $order)
            <div class="flex items-center justify-between border-t border-zinc-100 py-3 text-sm first:border-t-0 dark:border-zinc-800" wire:key="order-{{ $order->id }}">
                <div>
                    <p class="font-medium">{{ $order->customer_name }}</p>
                    <p class="text-zinc-500">{{ $order->shop->name }}</p>
                </div>
                <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium dark:bg-zinc-800">{{ $order->status->label() }}</span>
            </div>
        @empty
            <p class="text-sm text-zinc-500">No order requests yet.</p>
        @endforelse
    </section>
</div>
