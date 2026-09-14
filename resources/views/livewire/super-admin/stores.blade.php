<div>
    <x-dashboard.page-header title="Stores" description="Every shop on the platform." />

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950">
                <tr>
                    <th class="px-4 py-3 font-medium">Store</th>
                    <th class="px-4 py-3 font-medium">Admins</th>
                    <th class="px-4 py-3 font-medium">Products</th>
                    <th class="px-4 py-3 font-medium">Orders</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($shops as $shop)
                    <tr class="border-b border-zinc-100 dark:border-zinc-800" wire:key="shop-{{ $shop->id }}">
                        <td class="px-4 py-3">
                            <a href="{{ route('shops.show', $shop) }}" class="font-medium text-softora hover:underline">{{ $shop->name }}</a>
                        </td>
                        <td class="px-4 py-3">{{ $shop->users_count }}</td>
                        <td class="px-4 py-3">{{ $shop->products_count }}</td>
                        <td class="px-4 py-3">{{ $shop->orders_count }}</td>
                        <td class="px-4 py-3">{{ $shop->is_active ? 'Active' : 'Off' }}</td>
                        <td class="px-4 py-3 text-right">
                            <button type="button" wire:click="toggleShop({{ $shop->id }})" class="mr-3 text-softora hover:underline">
                                {{ $shop->is_active ? 'Turn off' : 'Turn on' }}
                            </button>
                            <button type="button" wire:click="deleteShop({{ $shop->id }})" wire:confirm="Delete this store and all of its data?" class="text-red-600 hover:underline">
                                Delete
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-zinc-500">No stores yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $shops->links() }}</div>
</div>
