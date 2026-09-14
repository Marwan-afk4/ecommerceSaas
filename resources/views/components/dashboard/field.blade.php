@props(['label', 'error' => null])

<label {{ $attributes->class('block') }}>
    <span class="mb-1.5 block text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ $label }}</span>
    {{ $slot }}
    @if ($error)
        <p class="mt-1.5 text-sm text-red-600">{{ $error }}</p>
    @endif
</label>
