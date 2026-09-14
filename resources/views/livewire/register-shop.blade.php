<div class="mx-auto max-w-2xl px-6 py-12">
    <a href="{{ route('home') }}" wire:navigate class="inline-block">
        <x-logo class="h-12" />
    </a>

    @if ($submitted)
        <h1 class="mt-8 text-3xl font-semibold tracking-tight">Request sent</h1>
        <p class="mt-2 text-zinc-600 dark:text-zinc-400">
            Superadmin will accept or reject {{ $name }}. You can sign in after the store is approved.
        </p>
        <a href="{{ route('login') }}" wire:navigate class="mt-8 inline-flex rounded-xl bg-softora px-6 py-3 font-semibold text-white hover:bg-indigo-700">
            Go to shop login
        </a>
    @else
        <h1 class="mt-8 text-3xl font-semibold tracking-tight">Request your store</h1>
        <p class="mt-2 text-zinc-600 dark:text-zinc-400">
            Send your business details. Superadmin will review the request before the store is created.
        </p>

        <form wire:submit="save" class="mt-8 space-y-8" enctype="multipart/form-data">
            <section class="space-y-4 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold">Business</h2>

                <label class="block text-sm font-medium">
                    Business name
                    <input wire:model="name" type="text" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950" required>
                </label>
                @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                <label class="block text-sm font-medium">
                    Store image
                    <input wire:model="logo" type="file" accept="image/*" class="mt-1 w-full text-sm">
                </label>
                @error('logo') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
                @if ($logo)
                    <img src="{{ $logo->temporaryUrl() }}" alt="Store image preview" class="h-24 w-24 rounded-lg object-cover">
                @endif

                <label class="block text-sm font-medium">
                    Tagline
                    <input wire:model="tagline" type="text" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950">
                </label>
                @error('tagline') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                <label class="block text-sm font-medium">
                    About the store
                    <textarea wire:model="description" rows="4" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950"></textarea>
                </label>
                @error('description') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </section>

            <section class="space-y-4 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold">Store admin</h2>
                <p class="text-sm text-zinc-500">This person signs in after superadmin accepts the request.</p>

                <label class="block text-sm font-medium">
                    Admin name
                    <input wire:model.blur="admin_name" type="text" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950" required>
                </label>
                @error('admin_name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                <label class="block text-sm font-medium">
                    Admin email
                    <input wire:model="admin_email" type="email" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950" required>
                </label>
                @error('admin_email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                <label class="block text-sm font-medium">
                    Password
                    <input wire:model="admin_password" type="password" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950" required>
                </label>
                @error('admin_password') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                <label class="block text-sm font-medium">
                    Confirm password
                    <input wire:model="admin_password_confirmation" type="password" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950" required>
                </label>
            </section>

            <section class="space-y-4 rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
                <h2 class="text-lg font-semibold">What customers see</h2>
                <p class="text-sm text-zinc-500">Shown on your public store link after approval.</p>

                <label class="block text-sm font-medium">
                    Name shown to customers
                    <input wire:model="public_owner_name" type="text" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950" required>
                </label>
                @error('public_owner_name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                <label class="block text-sm font-medium">
                    Public email
                    <input wire:model="public_email" type="email" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950">
                </label>
                @error('public_email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

                <label class="block text-sm font-medium">
                    Public phone
                    <input wire:model="public_phone" type="text" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950">
                </label>
                @error('public_phone') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
            </section>

            <button type="submit" class="rounded-xl bg-softora px-6 py-3 font-semibold text-white hover:bg-indigo-700" wire:loading.attr="disabled">
                <span wire:loading.remove wire:target="save">Submit store request</span>
                <span wire:loading wire:target="save">Sending request...</span>
            </button>
        </form>
    @endif
</div>
