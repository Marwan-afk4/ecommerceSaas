<div>
    <x-dashboard.page-header title="Order requests" description="Customer requests from every store." />

    <div class="mb-4">
        <x-dashboard.select wire:model.live="status" class="max-w-xs">
            <option value="">All statuses</option>
            @foreach ($statuses as $orderStatus)
                <option value="{{ $orderStatus->value }}">{{ $orderStatus->label() }}</option>
            @endforeach
        </x-dashboard.select>
    </div>

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950">
                <tr>
                    <th class="px-4 py-3 font-medium">Customer</th>
                    <th class="px-4 py-3 font-medium">Store</th>
                    <th class="px-4 py-3 font-medium">Items</th>
                    <th class="px-4 py-3 font-medium">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-b border-zinc-100 dark:border-zinc-800" wire:key="order-{{ $order->id }}">
                        <td class="px-4 py-3">
                            <p class="font-medium">{{ $order->customer_name }}</p>
                            <p class="text-zinc-500">{{ $order->customer_email }}</p>
                        </td>
                        <td class="px-4 py-3">{{ $order->shop->name }}</td>
                        <td class="px-4 py-3">
                            @foreach ($order->items as $item)
                                <p>{{ $item->quantity }} × {{ $item->name }}</p>
                            @endforeach
                        </td>
                        <td class="px-4 py-3">{{ $order->status->label() }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-zinc-500">No order requests yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
</div>
