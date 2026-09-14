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
    <body class="min-h-screen bg-zinc-50 text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <header class="sticky top-0 z-20 flex h-14 items-center justify-end border-b border-zinc-200/80 bg-zinc-50/90 px-4 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90 sm:px-6">
            <x-theme-toggle />
        </header>
        {{ $slot }}
        @livewireScripts
    </body>
</html>
