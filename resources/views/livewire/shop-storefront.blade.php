<div class="mx-auto max-w-5xl px-6 py-12">
    <header class="flex flex-col gap-6 sm:flex-row sm:items-center">
        @if ($shop->logoUrl())
            <img src="{{ $shop->logoUrl() }}" alt="{{ $shop->name }}" class="h-24 w-24 rounded-2xl object-cover">
        @endif
        <div>
            <p class="text-sm font-medium tracking-wide text-softora uppercase">{{ $shop->tagline }}</p>
            <h1 class="mt-1 text-4xl font-semibold tracking-tight">{{ $shop->name }}</h1>
            @if ($shop->description)
                <p class="mt-3 max-w-2xl text-zinc-600 dark:text-zinc-400">{{ $shop->description }}</p>
            @endif
        </div>
    </header>

    <section class="mt-8 rounded-2xl border border-zinc-200 bg-white p-6 text-sm dark:border-zinc-800 dark:bg-zinc-900">
        <h2 class="font-semibold">Store owner</h2>
        <p class="mt-2">{{ $shop->public_owner_name }}</p>
        @if ($shop->public_email)
            <p class="mt-1">{{ $shop->public_email }}</p>
        @endif
        @if ($shop->public_phone)
            <p class="mt-1">{{ $shop->public_phone }}</p>
        @endif
    </section>

    <section class="mt-10">
        <h2 class="text-2xl font-semibold">Products</h2>

        @if ($shop->products->isEmpty())
            <p class="mt-4 text-zinc-500">This store has not added products yet.</p>
        @else
            <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($shop->products as $product)
                    <article class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-800 dark:bg-zinc-900">
                        @if ($product->category)
                            <p class="text-xs font-medium tracking-wide text-softora uppercase">{{ $product->category->name }}</p>
                        @endif
                        <h3 class="mt-1 font-semibold">{{ $product->name }}</h3>
                        @if ($product->description)
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $product->description }}</p>
                        @endif
                        @if ($product->variants->isNotEmpty())
                            <ul class="mt-3 space-y-1 text-sm text-zinc-600 dark:text-zinc-400">
                                @foreach ($product->variants as $variant)
                                    <li>
                                        {{ $variant->attributeValues->pluck('name')->join(' / ') ?: $variant->sku }}
                                        · {{ number_format((float) $variant->price, 2) }}
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    @if ($shop->products->isNotEmpty())
        <section class="mt-12 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-xl font-semibold">Request an order</h2>
            <p class="mt-1 text-sm text-zinc-500">The store admin will receive this request in their dashboard.</p>

            @if ($orderSubmitted)
                <p class="mt-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200">{{ $orderSubmitted }}</p>
            @endif

            <form wire:submit="requestOrder" class="mt-6 grid gap-4 sm:grid-cols-2">
                <x-dashboard.input wire:model="customer_name" type="text" placeholder="Your name" />
                <x-dashboard.input wire:model="customer_email" type="email" placeholder="Email" />
                <x-dashboard.input wire:model="customer_phone" type="text" placeholder="Phone (optional)" />
                <x-dashboard.input wire:model="quantity" type="number" min="1" max="99" />
                <x-dashboard.select wire:model.live="product_id" class="sm:col-span-2">
                    <option value="">Choose a product</option>
                    @foreach ($shop->products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </x-dashboard.select>
                @if ($selectedProduct && $selectedProduct->variants->isNotEmpty())
                    <x-dashboard.select wire:model="product_variant_id" class="sm:col-span-2">
                        <option value="">Choose a variant</option>
                        @foreach ($selectedProduct->variants as $variant)
                            <option value="{{ $variant->id }}">
                                {{ $variant->attributeValues->pluck('name')->join(' / ') ?: $variant->sku }}
                            </option>
                        @endforeach
                    </x-dashboard.select>
                @endif
                <div class="sm:col-span-2">
                    <x-dashboard.button type="submit">Send order request</x-dashboard.button>
                </div>
                @error('customer_name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('customer_email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('product_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('product_variant_id') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @error('quantity') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </form>
        </section>
    @endif
</div>
