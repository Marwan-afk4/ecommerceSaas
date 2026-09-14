@props(['tone' => 'success'])

<div
    {{ $attributes->class([
        'mb-6 rounded-xl px-4 py-3 text-sm',
        'bg-emerald-50 text-emerald-800 dark:bg-emerald-950/40 dark:text-emerald-200' => $tone === 'success',
        'bg-red-50 text-red-800 dark:bg-red-950/40 dark:text-red-200' => $tone === 'danger',
    ]) }}
>
    {{ $slot }}
</div>
