<div>
    <x-dashboard.page-header title="Products" description="Add products, assign a category, then open a product to generate variants and SKUs." />

    @if ($statusMessage)
        <x-dashboard.notice>{{ $statusMessage }}</x-dashboard.notice>
    @endif

    <form wire:submit="createProduct" class="mb-8 space-y-5 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div>
            <h2 class="text-lg font-semibold">Add a product</h2>
            <p class="mt-1 text-sm text-zinc-500">Customers see this name and description on your public store.</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <x-dashboard.field label="Product name" :error="$errors->first('product_name')" class="sm:col-span-2">
                <x-dashboard.input wire:model="product_name" type="text" placeholder="Pixel 15" />
            </x-dashboard.field>

            <x-dashboard.field label="Category" :error="$errors->first('category_id')">
                <x-dashboard.select wire:model="category_id">
                    <option value="">General (default)</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </x-dashboard.select>
            </x-dashboard.field>

            <x-dashboard.field label="Short description" :error="$errors->first('product_description')" class="sm:col-span-2">
                <x-dashboard.textarea wire:model="product_description" rows="3" placeholder="What this product is and who it is for." />
            </x-dashboard.field>
        </div>

        @if ($categories->isEmpty())
            <p class="rounded-xl bg-zinc-50 px-4 py-3 text-sm text-zinc-600 dark:bg-zinc-950 dark:text-zinc-300">No categories yet. We will put this product in a default category, or you can <a href="{{ route('shop.categories') }}" wire:navigate class="font-medium text-softora hover:underline">create one first</a>.</p>
        @endif

        <x-dashboard.button type="submit" wire:loading.attr="disabled" wire:target="createProduct">
            <span wire:loading.remove wire:target="createProduct">Add product</span>
            <span wire:loading wire:target="createProduct">Adding...</span>
        </x-dashboard.button>
    </form>

    <div class="overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-5 py-4 dark:border-zinc-800">
            <h2 class="font-semibold">Catalog</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[40rem] text-left text-sm">
                <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950">
                    <tr>
                        <th class="px-5 py-3 font-medium">Name</th>
                        <th class="px-5 py-3 font-medium">Category</th>
                        <th class="px-5 py-3 font-medium">Variants</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($products as $product)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800" wire:key="product-{{ $product->id }}">
                            <td class="px-5 py-4">
                                <p class="font-medium">{{ $product->name }}</p>
                                @if ($product->description)
                                    <p class="mt-0.5 line-clamp-1 text-zinc-500">{{ $product->description }}</p>
                                @endif
                            </td>
                            <td class="px-5 py-4">{{ $product->category->name }}</td>
                            <td class="px-5 py-4">{{ $product->variants_count }}</td>
                            <td class="px-5 py-4">
                                <span class="rounded-full bg-zinc-100 px-2.5 py-0.5 text-xs font-medium dark:bg-zinc-800">{{ $product->status->label() }}</span>
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap">
                                <a href="{{ route('shop.products.edit', $product) }}" wire:navigate class="mr-3 font-medium text-softora hover:underline">Variants</a>
                                <button type="button" wire:click="deleteProduct({{ $product->id }})" wire:confirm="Delete this product?" class="text-red-600 hover:underline">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-zinc-500">No products yet. Add one above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>
</div>
