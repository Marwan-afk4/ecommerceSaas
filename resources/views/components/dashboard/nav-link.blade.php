@props([
    'href',
    'active' => false,
    'icon' => 'home',
    'badge' => 0,
])

<a
    href="{{ $href }}"
    wire:navigate
    {{ $attributes->class([
        'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium transition',
        'bg-softora/10 text-softora' => $active,
        'text-zinc-600 hover:bg-zinc-100 hover:text-zinc-900 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-white' => ! $active,
    ]) }}
>
    <span class="inline-flex h-5 w-5 shrink-0 items-center justify-center">
        <x-dashboard.icon :name="$icon" class="h-4.5 w-4.5" />
    </span>
    <span class="flex-1">{{ $slot }}</span>
    @if ((int) $badge > 0)
        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[11px] font-semibold text-amber-800 dark:bg-amber-900 dark:text-amber-100">{{ $badge }}</span>
    @endif
</a>
