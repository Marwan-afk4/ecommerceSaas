@props(['label'])

<label {{ $attributes->class('inline-flex cursor-pointer items-center gap-2 rounded-full border border-zinc-200 bg-white px-3 py-1.5 text-sm text-zinc-700 shadow-sm transition has-[:checked]:border-softora has-[:checked]:bg-softora/10 has-[:checked]:text-softora has-[:focus-visible]:ring-2 has-[:focus-visible]:ring-softora/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-200') }}>
    {{ $slot }}
    <span>{{ $label }}</span>
</label>
