<div>
    <x-dashboard.page-header title="Store requests" description="People who want to open a store. Accept to create the shop, or reject the request." />

    <div class="mb-4">
        <x-dashboard.select wire:model.live="status" class="max-w-xs">
            <option value="">All statuses</option>
            @foreach ($statuses as $applicationStatus)
                <option value="{{ $applicationStatus->value }}">{{ $applicationStatus->label() }}</option>
            @endforeach
        </x-dashboard.select>
        @error('status') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="space-y-4">
        @forelse ($applications as $application)
            <article class="rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900" wire:key="application-{{ $application->id }}">
                <div class="flex flex-col gap-4 sm:flex-row">
                    @if ($application->logoUrl())
                        <img src="{{ $application->logoUrl() }}" alt="" class="h-20 w-20 rounded-xl object-cover">
                    @endif
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-lg font-semibold">{{ $application->name }}</h2>
                            <span class="rounded-full bg-zinc-100 px-2 py-0.5 text-xs font-medium dark:bg-zinc-800">{{ $application->status->label() }}</span>
                        </div>
                        @if ($application->tagline)
                            <p class="mt-1 text-sm text-zinc-500">{{ $application->tagline }}</p>
                        @endif
                        @if ($application->description)
                            <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">{{ $application->description }}</p>
                        @endif
                        <dl class="mt-4 grid gap-3 text-sm sm:grid-cols-2">
                            <div>
                                <dt class="text-zinc-500">Admin</dt>
                                <dd>{{ $application->admin_name }}</dd>
                                <dd class="text-zinc-500">{{ $application->admin_email }}</dd>
                            </div>
                            <div>
                                <dt class="text-zinc-500">Public contact</dt>
                                <dd>{{ $application->public_owner_name }}</dd>
                                @if ($application->public_email)
                                    <dd class="text-zinc-500">{{ $application->public_email }}</dd>
                                @endif
                                @if ($application->public_phone)
                                    <dd class="text-zinc-500">{{ $application->public_phone }}</dd>
                                @endif
                            </div>
                        </dl>
                        @if ($application->shop)
                            <p class="mt-3 text-sm text-zinc-500">Created store: {{ $application->shop->name }}</p>
                        @endif
                    </div>
                    @if ($application->status === \App\Enums\ShopApplicationStatus::Pending)
                        <div class="flex shrink-0 items-start gap-3">
                            <button type="button" wire:click="accept({{ $application->id }})" wire:loading.attr="disabled" wire:target="accept({{ $application->id }})" class="text-softora hover:underline">Accept</button>
                            <button type="button" wire:click="reject({{ $application->id }})" wire:loading.attr="disabled" wire:target="reject({{ $application->id }})" class="text-red-600 hover:underline">Reject</button>
                        </div>
                    @endif
                </div>
            </article>
        @empty
            <p class="rounded-2xl border border-zinc-200 bg-white px-4 py-8 text-center text-sm text-zinc-500 dark:border-zinc-800 dark:bg-zinc-900">No store requests yet.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $applications->links() }}</div>
</div>
