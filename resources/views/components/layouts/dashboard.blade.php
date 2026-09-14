<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title ?? config('app.name') }}</title>
        <link rel="icon" href="{{ asset('images/softora-favicon.png') }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('images/softora-favicon.png') }}">
        <x-theme-boot />
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="min-h-screen bg-zinc-100 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100" x-data="{ sidebarOpen: false }">
        <div class="min-h-screen lg:flex">
            <x-dashboard.sidebar :area="request()->routeIs('superadmin.*') ? 'superadmin' : 'shop'" />

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-zinc-200 bg-white/90 px-4 backdrop-blur dark:border-zinc-800 dark:bg-zinc-900/90 lg:px-8">
                    <div class="flex items-center gap-3">
                        <button
                            type="button"
                            class="inline-flex h-10 w-10 items-center justify-center rounded-lg border border-zinc-200 text-zinc-600 hover:bg-zinc-100 lg:hidden dark:border-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-800"
                            @click="sidebarOpen = true"
                            aria-label="Open menu"
                        >
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16" />
                            </svg>
                        </button>
                        <p class="hidden text-sm text-zinc-500 sm:block">{{ now()->translatedFormat('l, d M Y') }}</p>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <x-theme-toggle />
                        <span class="font-medium">{{ auth()->user()?->name }}</span>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-zinc-500 hover:text-zinc-800 dark:hover:text-zinc-200">Log out</button>
                        </form>
                    </div>
                </header>

                <main class="flex-1 px-4 py-6 lg:px-8 lg:py-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
        @livewireScripts
    </body>
</html>
