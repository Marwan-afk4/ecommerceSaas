<div>
    <x-dashboard.page-header title="Categories" description="Group products, then attach variant types such as Size or Color so you can generate SKUs." />

    @if ($statusMessage)
        <x-dashboard.notice>{{ $statusMessage }}</x-dashboard.notice>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <form wire:submit="createCategory" class="space-y-5 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div>
                <p class="text-xs font-semibold tracking-wide text-softora uppercase">Step 1</p>
                <h2 class="mt-1 text-lg font-semibold">New category</h2>
                <p class="mt-1 text-sm text-zinc-500">Each product belongs to one category.</p>
            </div>
            <x-dashboard.field label="Category name" :error="$errors->first('category_name')">
                <x-dashboard.input wire:model="category_name" type="text" placeholder="Phones" />
            </x-dashboard.field>
            <x-dashboard.field label="Description" :error="$errors->first('category_description')">
                <x-dashboard.textarea wire:model="category_description" rows="3" placeholder="Optional details for your team." />
            </x-dashboard.field>
            <x-dashboard.button type="submit" wire:loading.attr="disabled" wire:target="createCategory">
                <span wire:loading.remove wire:target="createCategory">Add category</span>
                <span wire:loading wire:target="createCategory">Adding...</span>
            </x-dashboard.button>
        </form>

        <form wire:submit="createAttribute" class="space-y-5 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <div>
                <p class="text-xs font-semibold tracking-wide text-softora uppercase">Step 2</p>
                <h2 class="mt-1 text-lg font-semibold">Variant type</h2>
                <p class="mt-1 text-sm text-zinc-500">These options are used to generate SKUs on each product.</p>
            </div>
            <x-dashboard.field label="Type name" :error="$errors->first('attribute_name')">
                <x-dashboard.input wire:model="attribute_name" type="text" placeholder="Size, Color, Storage..." />
            </x-dashboard.field>
            <x-dashboard.field label="Values" :error="$errors->first('attribute_values')">
                <x-dashboard.input wire:model="attribute_values" type="text" placeholder="S, M, L" />
            </x-dashboard.field>
            <p class="-mt-2 text-xs text-zinc-500">Separate values with commas.</p>
            <x-dashboard.button type="submit" wire:loading.attr="disabled" wire:target="createAttribute">
                <span wire:loading.remove wire:target="createAttribute">Add variant type</span>
                <span wire:loading wire:target="createAttribute">Adding...</span>
            </x-dashboard.button>
        </form>
    </div>

    @if ($attributes->isNotEmpty())
        <section class="mt-6 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="font-semibold">Variant types in this store</h2>
            <div class="mt-4 flex flex-wrap gap-2">
                @foreach ($attributes as $attribute)
                    <span class="rounded-full bg-zinc-100 px-3 py-1.5 text-sm dark:bg-zinc-800" wire:key="attribute-chip-{{ $attribute->id }}">
                        <span class="font-medium">{{ $attribute->name }}</span>
                        <span class="text-zinc-500">{{ $attribute->values->pluck('name')->join(', ') }}</span>
                    </span>
                @endforeach
            </div>
        </section>
    @endif

    <div class="mt-6 space-y-4">
        @forelse ($categories as $category)
            <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900" wire:key="category-{{ $category->id }}">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $category->name }}</h3>
                        @if ($category->description)
                            <p class="mt-1 text-sm text-zinc-500">{{ $category->description }}</p>
                        @endif
                        <p class="mt-1 text-sm text-zinc-500">{{ $category->products_count }} {{ \Illuminate\Support\Str::plural('product', $category->products_count) }}</p>
                    </div>
                    <button type="button" wire:click="deleteCategory({{ $category->id }})" wire:confirm="Delete this category?" class="text-sm text-red-600 hover:underline">
                        Delete
                    </button>
                </div>

                @if ($category->products->isNotEmpty())
                    <ul class="mt-4 divide-y divide-zinc-100 rounded-xl border border-zinc-100 dark:divide-zinc-800 dark:border-zinc-800">
                        @foreach ($category->products as $product)
                            <li class="flex items-center justify-between gap-3 px-4 py-2.5 text-sm" wire:key="category-{{ $category->id }}-product-{{ $product->id }}">
                                <span>{{ $product->name }}</span>
                                <a href="{{ route('shop.products.edit', $product) }}" wire:navigate class="font-medium text-softora hover:underline">Variants</a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                @if ($attributes->isNotEmpty())
                    <div class="mt-5 space-y-3">
                        <p class="text-sm font-medium">Variant types for this category</p>
                        <p class="text-xs text-zinc-500">Check the types that products in this category can use, then save.</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($attributes as $attribute)
                                <x-dashboard.option-chip :label="$attribute->name" wire:key="category-{{ $category->id }}-attribute-{{ $attribute->id }}">
                                    <input type="checkbox" wire:model="categoryAttributeIds.{{ $category->id }}.{{ $attribute->id }}" class="rounded border-zinc-300 text-softora focus:ring-softora dark:border-zinc-600 dark:bg-zinc-950">
                                </x-dashboard.option-chip>
                            @endforeach
                        </div>
                        <x-dashboard.button type="button" variant="secondary" wire:click="saveCategoryAttributes({{ $category->id }})" wire:loading.attr="disabled" wire:target="saveCategoryAttributes({{ $category->id }})">
                            <span wire:loading.remove wire:target="saveCategoryAttributes({{ $category->id }})">Save attributes</span>
                            <span wire:loading wire:target="saveCategoryAttributes({{ $category->id }})">Saving...</span>
                        </x-dashboard.button>
                    </div>
                @endif
            </section>
        @empty
            <x-dashboard.empty-state message="Create a category to group products." />
        @endforelse
    </div>
</div>
