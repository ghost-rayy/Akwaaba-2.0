@props([
    'target' => null,
    'loading' => __('Processing...'),
])

@php
    $wireTarget = $target ?? $attributes->wire('click')->value();
@endphp

<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex flex-row items-center justify-center gap-2 px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-stormy-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-70 disabled:cursor-not-allowed']) }}
    @if($wireTarget) wire:loading.attr="disabled" wire:target="{{ $wireTarget }}" @endif
>
    @if($wireTarget)
        <span wire:loading.remove wire:target="{{ $wireTarget }}" class="inline-flex items-center">{{ $slot }}</span>
        <span wire:loading wire:target="{{ $wireTarget }}" class="inline-flex flex-row items-center gap-2 whitespace-nowrap">
            <x-loading-spinner />
            <span>{{ $loading }}</span>
        </span>
    @else
        {{ $slot }}
    @endif
</button>
