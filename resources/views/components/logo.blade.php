@props([
    'alt' => 'Softora',
])

<img
    src="{{ asset('images/softora-logo.png') }}"
    alt="{{ $alt }}"
    {{ $attributes->class(['w-auto', 'object-contain', 'h-12' => ! $attributes->has('class')]) }}
>
