@props(['message' => 'Nothing here yet.'])

<div class="rounded-2xl border border-dashed border-zinc-300 bg-white px-6 py-12 text-center text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900">
    {{ $slot->isEmpty() ? $message : $slot }}
</div>
