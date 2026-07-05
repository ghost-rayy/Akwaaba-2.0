@props([
    'target',
    'loading' => __('Processing...'),
    'type' => 'submit',
    'disabled' => false,
])

<button
    type="{{ $type }}"
    wire:loading.attr="disabled"
    wire:target="{{ $target }}"
    @disabled($disabled)
    {{ $attributes->merge(['class' => 'inline-flex flex-row items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed']) }}
>
    <span wire:loading.remove wire:target="{{ $target }}" class="inline-flex items-center">{{ $slot }}</span>
    <span wire:loading wire:target="{{ $target }}" style="display: none;" class="inline-flex flex-row items-center gap-2 whitespace-nowrap">
        <x-loading-spinner />
        <span>{{ $loading }}</span>
    </span>
</button>
