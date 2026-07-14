@props([
    'user',
    'size' => 'md',
])

@php
    $photoUrl = $user->profilePhotoUrl();
    $initial = strtoupper(substr($user->name ?? '?', 0, 1));
    $sizeClass = match ($size) {
        'sm' => 'w-7 h-7 text-xs',
        'lg' => 'w-10 h-10 text-base',
        default => 'w-9 h-9 text-sm',
    };
@endphp

<div
    x-data="{ open: false }"
    {{ $attributes->class('shrink-0') }}
>
    @if ($photoUrl)
        <button
            type="button"
            @click="open = true"
            class="{{ $sizeClass }} rounded-full overflow-hidden border border-gray-200 bg-white ring-offset-1 hover:ring-2 hover:ring-stormy-400 transition focus:outline-none focus:ring-2 focus:ring-stormy-500"
            title="Preview photo"
        >
            <img src="{{ $photoUrl }}" alt="{{ $user->name }} photo" class="h-full w-full object-cover">
        </button>

        <template x-teleport="body">
            <div
                x-show="open"
                x-cloak
                class="fixed inset-0 z-[110] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4"
                @keydown.escape.window="open = false"
                @click.self="open = false"
            >
                <div
                    class="relative bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden"
                    @click.stop
                >
                    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
                        <p class="text-sm font-semibold text-gray-900 truncate">{{ $user->name }}</p>
                        <button type="button" @click="open = false" class="p-1 rounded-lg hover:bg-gray-100 text-gray-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="bg-gray-50 p-4 flex items-center justify-center">
                        <img src="{{ $photoUrl }}" alt="{{ $user->name }} photo" class="max-h-[70vh] w-auto max-w-full rounded-lg object-contain">
                    </div>
                </div>
            </div>
        </template>
    @else
        <div class="{{ $sizeClass }} bg-stormy-100 rounded-full flex items-center justify-center">
            <span class="font-bold text-stormy-600">{{ $initial }}</span>
        </div>
    @endif
</div>
