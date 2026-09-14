@props(['label'])

<div class="mb-6">
    <p class="px-3 text-[11px] font-semibold tracking-[0.16em] text-zinc-400 uppercase">{{ $label }}</p>
    <div class="mt-2 space-y-1">
        {{ $slot }}
    </div>
</div>
