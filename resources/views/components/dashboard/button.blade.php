@props(['variant' => 'primary'])

<button
    {{ $attributes->class([
        'inline-flex items-center justify-center rounded-lg px-4 py-2 text-sm font-medium transition disabled:pointer-events-none disabled:opacity-60',
        'bg-softora text-white hover:bg-softora/90' => $variant === 'primary',
        'border border-zinc-300 bg-white text-zinc-700 hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800' => $variant === 'secondary',
        'text-red-600 hover:underline' => $variant === 'danger',
    ]) }}
>
    {{ $slot }}
</button>
