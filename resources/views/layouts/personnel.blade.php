<x-app-layout>
    @php
        $user = auth()->user();
        $isProfileComplete = !$user->must_change_password && $user->form_step >= 3;
        
        $tabs = [
            [
                'route' => 'personnel.dashboard',
                'label' => 'Dashboard',
                'icon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6',
                'locked' => false
            ],
            [
                'route' => 'personnel.attendance',
                'label' => 'Attendance',
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4',
                'locked' => !$isProfileComplete
            ],
            [
                'route' => 'personnel.documents',
                'label' => 'Documents',
                'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z',
                'locked' => !$isProfileComplete
            ],
        ];
    @endphp

    <div class="bg-white/80 backdrop-blur-md border-b border-gray-100 shadow-sm sticky top-0 z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex space-x-2 overflow-x-auto py-2.5" aria-label="Tabs">
                @foreach ($tabs as $tab)
                    @if ($tab['locked'])
                        <div class="group relative">
                            <span class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold text-gray-300 bg-gray-50/50 border border-gray-100 rounded-xl cursor-not-allowed select-none transition-all">
                                <svg class="w-4 h-4 shrink-0 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                                </svg>
                                <span>{{ $tab['label'] }}</span>
                                <svg class="w-3.5 h-3.5 ml-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <div class="pointer-events-none absolute bottom-full left-1/2 -translate-x-1/2 mb-2 w-48 bg-gray-900 text-white text-xs text-center py-1.5 px-3 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 z-20">
                                Complete profile to unlock
                                <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
                            </div>
                        </div>
                    @else
                        <a href="{{ route($tab['route']) }}"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm font-semibold rounded-xl whitespace-nowrap transition-all duration-200 border
                                  {{ request()->routeIs($tab['route']) 
                                     ? 'bg-gradient-to-r from-stormy-600 to-stormy-700 text-white shadow-md shadow-stormy-600/10 border-transparent' 
                                     : 'text-gray-600 border-transparent hover:text-gray-900 hover:bg-gray-50 hover:border-gray-200/60' }}"
                           wire:navigate>
                            <svg class="w-4 h-4 shrink-0 {{ request()->routeIs($tab['route']) ? 'text-white' : 'text-gray-500' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tab['icon'] }}"/>
                            </svg>
                            <span>{{ $tab['label'] }}</span>
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>
    </div>

    <div class="py-8 bg-gray-50/50 min-h-[calc(100vh-120px)]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </div>
</x-app-layout>
