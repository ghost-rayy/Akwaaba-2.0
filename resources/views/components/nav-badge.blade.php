@props(['count' => 0])

@if ($count > 0)
    <span {{ $attributes->merge(['class' => 'inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-[10px] font-bold leading-none rounded-full bg-rose-500 text-white']) }}>
        {{ $count > 99 ? '99+' : $count }}
    </span>
@endif
