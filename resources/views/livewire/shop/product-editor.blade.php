<div>
    <x-dashboard.page-header :title="$product->name" description="Update details and generate SKUs from the category attributes.">
        <x-slot:actions>
            <a href="{{ route('shop.products') }}" wire:navigate class="text-sm font-medium text-softora hover:underline">Back to products</a>
        </x-slot:actions>
    </x-dashboard.page-header>

    @if ($statusMessage)
        <x-dashboard.notice>{{ $statusMessage }}</x-dashboard.notice>
    @endif

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1.1fr)_minmax(0,0.9fr)]">
        <form wire:submit="saveProduct" class="space-y-5 rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-lg font-semibold">Product details</h2>

            <x-dashboard.field label="Name" :error="$errors->first('name')">
                <x-dashboard.input wire:model="name" type="text" />
            </x-dashboard.field>

            <x-dashboard.field label="Description" :error="$errors->first('description')">
                <x-dashboard.textarea wire:model="description" rows="4" />
            </x-dashboard.field>

            <div class="grid gap-4 sm:grid-cols-2">
                <x-dashboard.field label="Category" :error="$errors->first('category_id')">
                    <x-dashboard.select wire:model="category_id">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </x-dashboard.select>
                </x-dashboard.field>

                <x-dashboard.field label="Status" :error="$errors->first('status')">
                    <x-dashboard.select wire:model="status">
                        @foreach (\App\Enums\ProductStatus::cases() as $productStatus)
                            <option value="{{ $productStatus->value }}">{{ $productStatus->label() }}</option>
                        @endforeach
                    </x-dashboard.select>
                </x-dashboard.field>
            </div>

            <x-dashboard.button type="submit" wire:loading.attr="disabled" wire:target="saveProduct">
                <span wire:loading.remove wire:target="saveProduct">Save product</span>
                <span wire:loading wire:target="saveProduct">Saving...</span>
            </x-dashboard.button>
        </form>

        <section class="rounded-2xl border border-zinc-200 bg-white p-6 shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
            <h2 class="text-lg font-semibold">Generate variants</h2>
            <p class="mt-1 text-sm text-zinc-500">Choose values from this category. If the category has no attributes, a default SKU is created.</p>

            @if ($attributes->isEmpty())
                <p class="mt-4 rounded-xl bg-zinc-50 px-4 py-3 text-sm text-zinc-600 dark:bg-zinc-950 dark:text-zinc-300">No variant types on this category yet. Add Size or Color under Categories, attach them to this category, then generate SKUs.</p>
            @else
                <div class="mt-4 space-y-4">
                    @foreach ($attributes as $attribute)
                        <fieldset class="rounded-xl border border-zinc-200 p-4 dark:border-zinc-800" wire:key="generate-attribute-{{ $attribute->id }}">
                            <legend class="px-1 text-sm font-medium">{{ $attribute->name }}</legend>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach ($attribute->values as $value)
                                    <x-dashboard.option-chip :label="$value->name" wire:key="generate-attribute-{{ $attribute->id }}-value-{{ $value->id }}">
                                        <input type="checkbox" wire:model="selectedValues.{{ $attribute->id }}.{{ $value->id }}" class="rounded border-zinc-300 text-softora focus:ring-softora dark:border-zinc-600 dark:bg-zinc-950">
                                    </x-dashboard.option-chip>
                                @endforeach
                            </div>
                            @error('attributes.'.$attribute->id) <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        </fieldset>
                    @endforeach
                </div>
            @endif

            @error('attributes') <p class="mt-3 text-sm text-red-600">{{ $message }}</p> @enderror
            <div class="mt-5">
                <x-dashboard.button type="button" wire:click="generateVariants" wire:loading.attr="disabled" wire:target="generateVariants">
                    <span wire:loading.remove wire:target="generateVariants">Generate SKUs</span>
                    <span wire:loading wire:target="generateVariants">Generating...</span>
                </x-dashboard.button>
            </div>
        </section>
    </div>

    <section class="mt-6 overflow-hidden rounded-2xl border border-zinc-200 bg-white shadow-sm dark:border-zinc-800 dark:bg-zinc-900">
        <div class="border-b border-zinc-200 px-5 py-4 font-semibold dark:border-zinc-800">SKUs</div>
        <div class="overflow-x-auto">
            <table class="w-full min-w-[40rem] text-left text-sm">
                <thead class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950">
                    <tr>
                        <th class="px-5 py-3 font-medium">SKU</th>
                        <th class="px-5 py-3 font-medium">Options</th>
                        <th class="px-5 py-3 font-medium">Price</th>
                        <th class="px-5 py-3 font-medium">Stock</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($product->variants as $variant)
                        <tr class="border-b border-zinc-100 dark:border-zinc-800" wire:key="variant-{{ $variant->id }}">
                            <td class="px-5 py-4 font-medium">{{ $variant->sku }}</td>
                            <td class="px-5 py-4">{{ $variant->attributeValues->pluck('name')->join(' / ') ?: 'Default' }}</td>
                            <td class="px-5 py-4">
                                <x-dashboard.input type="number" step="0.01" min="0" wire:model="variantPrices.{{ $variant->id }}" />
                                @error('variantPrices.'.$variant->id) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-5 py-4">
                                <x-dashboard.input type="number" min="0" wire:model="variantStocks.{{ $variant->id }}" />
                                @error('variantStocks.'.$variant->id) <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </td>
                            <td class="px-5 py-4 text-right">
                                <x-dashboard.button type="button" variant="secondary" wire:click="saveVariant({{ $variant->id }})" wire:loading.attr="disabled" wire:target="saveVariant({{ $variant->id }})">
                                    Save
                                </x-dashboard.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-zinc-500">No variants yet. Generate SKUs above.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
