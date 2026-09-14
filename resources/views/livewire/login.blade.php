<div class="mx-auto flex min-h-[calc(100vh-3.5rem)] max-w-md flex-col justify-center px-6 py-16">
    <a href="{{ route('home') }}" wire:navigate class="inline-block">
        <x-logo class="h-12" />
    </a>

    <h1 class="mt-8 text-3xl font-semibold tracking-tight">
        {{ $superadmin ? 'Superadmin' : 'Shop admin' }}
    </h1>
    <p class="mt-2 text-zinc-600 dark:text-zinc-400">
        {{ $superadmin ? 'Sign in to control every store on the platform.' : 'Sign in to manage your store.' }}
    </p>

    <form wire:submit="authenticate" class="mt-8 space-y-4">
        <label class="block text-sm font-medium">
            Email
            <input wire:model="email" type="email" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950" required>
        </label>
        @error('email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

        <label class="block text-sm font-medium">
            Password
            <input wire:model="password" type="password" class="mt-1 w-full rounded-lg border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-950" required>
        </label>
        @error('password') <p class="text-sm text-red-600">{{ $message }}</p> @enderror

        <label class="flex items-center gap-2 text-sm">
            <input wire:model="remember" type="checkbox" class="rounded border-zinc-300 text-softora focus:ring-softora dark:border-zinc-600 dark:bg-zinc-950">
            Remember me
        </label>

        <button type="submit" class="w-full rounded-xl bg-softora px-6 py-3 font-semibold text-white hover:bg-indigo-700">
            Sign in
        </button>
    </form>
</div>
