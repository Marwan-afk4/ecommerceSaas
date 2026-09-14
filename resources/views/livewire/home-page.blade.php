<div class="mx-auto flex min-h-[calc(100vh-3.5rem)] max-w-3xl flex-col justify-center px-6 py-16">
    <x-logo class="h-16 sm:h-20" />

    <h1 class="mt-10 text-4xl font-semibold tracking-tight sm:text-5xl">
        Make your own ecommerce for your business
    </h1>
    <p class="mt-4 max-w-xl text-lg text-zinc-600 dark:text-zinc-400">
        Send a store request with your brand and admin. Superadmin will accept or reject it before the store goes live.
    </p>

    <a
        href="{{ route('shops.create') }}"
        wire:navigate
        class="mt-10 inline-flex w-fit items-center rounded-xl bg-softora px-6 py-3 text-base font-semibold text-white hover:bg-indigo-700"
    >
        Make your own ecommerce
    </a>

    <p class="mt-10 text-sm text-zinc-500 dark:text-zinc-400">
        Already have a store?
        <a href="{{ route('login') }}" wire:navigate class="font-medium text-softora hover:underline">Shop admin login</a>
    </p>

    <p class="mt-16 text-xs text-zinc-400">
        <a href="{{ route('superadmin.login') }}" wire:navigate class="hover:text-zinc-600 dark:hover:text-zinc-300">Superadmin</a>
    </p>
</div>
