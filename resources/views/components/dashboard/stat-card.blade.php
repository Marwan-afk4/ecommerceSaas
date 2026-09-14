@props([
    'label',
    'value',
    'href' => null,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }} @if ($href) href="{{ $href }}" wire:navigate @endif {{ $attributes->class(['block rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm dark:border-zinc-800 dark:bg-zinc-900', 'transition hover:border-softora/40' => (bool) $href]) }}>
    <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ $label }}</p>
    <p class="mt-2 text-3xl font-semibold tracking-tight">{{ is_numeric($value) ? number_format((int) $value) : $value }}</p>
</{{ $tag }}>
