<div>
    {{-- Stats Row --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total</span>
                <span class="p-1.5 bg-stormy-50 rounded-lg text-stormy-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalPersonnel }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Personnel</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Active</span>
                <span class="p-1.5 bg-emerald-50 rounded-lg text-emerald-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $activePersonnel }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Validated</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Review</span>
                <span class="p-1.5 bg-amber-50 rounded-lg text-amber-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $pendingReview }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Pending Review</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Shortlisted</span>
                <span class="p-1.5 bg-sky-50 rounded-lg text-sky-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $shortlisted }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Shortlisted</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Today</span>
                <span class="p-1.5 bg-indigo-50 rounded-lg text-indigo-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $todayPresent }}<span class="text-sm font-normal text-gray-400">/{{ $todayTotal }}</span></p>
            <p class="text-xs text-gray-500 mt-0.5">Present Today</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Depts</span>
                <span class="p-1.5 bg-purple-50 rounded-lg text-purple-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                </span>
            </div>
            <p class="text-2xl font-bold text-gray-900">{{ $totalDepartments }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Departments</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-8">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-8 gap-3">
            <a href="{{ route('company.onboard') }}" wire:navigate
               class="flex flex-col items-center gap-1.5 p-3 bg-stormy-50 rounded-xl hover:bg-stormy-100 transition-colors group">
                <span class="p-2 bg-stormy-600 rounded-lg text-white group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </span>
                <span class="text-[10px] font-semibold text-gray-600 text-center leading-tight">Onboard</span>
            </a>
            <a href="{{ route('company.personnel') }}" wire:navigate
               class="flex flex-col items-center gap-1.5 p-3 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-colors group">
                <span class="p-2 bg-emerald-600 rounded-lg text-white group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857"/></svg>
                </span>
                <span class="text-[10px] font-semibold text-gray-600 text-center leading-tight">Personnel</span>
            </a>
            <a href="{{ route('company.shortlist') }}" wire:navigate
               class="flex flex-col items-center gap-1.5 p-3 bg-rose-50 rounded-xl hover:bg-rose-100 transition-colors group">
                <span class="p-2 bg-rose-600 rounded-lg text-white group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <span class="text-[10px] font-semibold text-gray-600 text-center leading-tight">Shortlist</span>
            </a>
            <a href="{{ route('company.endorse') }}" wire:navigate
               class="flex flex-col items-center gap-1.5 p-3 bg-amber-50 rounded-xl hover:bg-amber-100 transition-colors group">
                <span class="p-2 bg-amber-600 rounded-lg text-white group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </span>
                <span class="text-[10px] font-semibold text-gray-600 text-center leading-tight">Endorse</span>
            </a>
            <a href="{{ route('company.attendance') }}" wire:navigate
               class="flex flex-col items-center gap-1.5 p-3 bg-stormy-50 rounded-xl hover:bg-stormy-100 transition-colors group">
                <span class="p-2 bg-stormy-600 rounded-lg text-white group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </span>
                <span class="text-[10px] font-semibold text-gray-600 text-center leading-tight">Attendance</span>
            </a>
            <a href="{{ route('company.evaluations') }}" wire:navigate
               class="flex flex-col items-center gap-1.5 p-3 bg-rose-50 rounded-xl hover:bg-rose-100 transition-colors group">
                <span class="p-2 bg-rose-600 rounded-lg text-white group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </span>
                <span class="text-[10px] font-semibold text-gray-600 text-center leading-tight">Evaluate</span>
            </a>
            <a href="{{ route('company.reports') }}" wire:navigate
               class="flex flex-col items-center gap-1.5 p-3 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition-colors group">
                <span class="p-2 bg-emerald-600 rounded-lg text-white group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </span>
                <span class="text-[10px] font-semibold text-gray-600 text-center leading-tight">Reports</span>
            </a>
            <a href="{{ route('company.departments') }}" wire:navigate
               class="flex flex-col items-center gap-1.5 p-3 bg-amber-50 rounded-xl hover:bg-amber-100 transition-colors group">
                <span class="p-2 bg-amber-600 rounded-lg text-white group-hover:scale-110 transition-transform">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                </span>
                <span class="text-[10px] font-semibold text-gray-600 text-center leading-tight">Depts</span>
            </a>
        </div>

    {{-- Two-column layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Recent Onboardings --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Recent Onboardings</h3>
                <a href="{{ route('company.onboard') }}" wire:navigate class="text-xs font-semibold text-stormy-600 hover:text-stormy-700">View All</a>
            </div>
            @if ($recentOnboardings->isEmpty())
                <div class="text-center py-8 text-gray-400 text-sm">No personnel onboarded yet.</div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach ($recentOnboardings as $enr)
                        <div class="flex items-center justify-between py-2.5">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-7 h-7 bg-stormy-100 rounded-full flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-stormy-600">{{ substr($enr->user->name, 0, 1) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $enr->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $enr->nss_number }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 shrink-0">{{ $enr->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Pending Validations --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Pending Validations</h3>
                <a href="{{ route('company.endorsed-letters') }}" wire:navigate class="text-xs font-semibold text-stormy-600 hover:text-stormy-700">Review All</a>
            </div>
            @if ($pendingValidations->isEmpty())
                <div class="text-center py-8 text-gray-400 text-sm">No pending validations.</div>
            @else
                <div class="divide-y divide-gray-50">
                    @foreach ($pendingValidations as $letter)
                        <div class="flex items-center justify-between py-2.5">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-7 h-7 bg-amber-100 rounded-full flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-amber-600">{{ substr($letter->enrollment->user->name, 0, 1) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $letter->enrollment->user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $letter->enrollment->nss_number }}</p>
                                </div>
                            </div>
                            <a href="{{ route('company.endorsed-letters') }}" wire:navigate
                               class="text-xs font-semibold text-amber-600 hover:text-amber-700 shrink-0">Validate</a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Department Distribution Chart --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <h3 class="text-sm font-bold text-gray-900 mb-4">Department Distribution</h3>
        @if ($departmentDistribution->isEmpty())
            <div class="text-center py-12 text-gray-400 text-sm">No departments created yet.</div>
        @else
            @php
                $sorted = $departmentDistribution->sortByDesc('enrollments_count');
                $maxCount = $sorted->max('enrollments_count') ?: 1;
            @endphp
            {{-- grid-template-columns: max-content 1fr auto
                 The first column stretches to the widest label automatically,
                 so every bar starts at exactly the same x position. --}}
            <div style="display: grid; grid-template-columns: max-content 1fr auto; row-gap: 1rem; column-gap: 0.75rem; align-items: center;">
                @foreach ($sorted as $dept)
                    @php
                        $pct  = ($dept->enrollments_count / $maxCount) * 100;
                        $width = $dept->enrollments_count > 0 ? max($pct, 3) : 2;
                    @endphp

                    {{-- Label --}}
                    <span class="text-[11px] font-medium text-gray-500 whitespace-nowrap">{{ $dept->name }}</span>

                    {{-- Bar track --}}
                    <div class="relative h-5 rounded-full bg-gray-100 overflow-hidden">
                        <div class="absolute inset-y-0 left-0 rounded-full bg-gradient-to-r from-stormy-600 to-stormy-400 transition-all duration-500 ease-out"
                             style="width: {{ $width }}%"></div>
                    </div>

                    {{-- Count --}}
                    <span class="text-right text-xs font-bold text-gray-700 tabular-nums">{{ $dept->enrollments_count }}</span>
                @endforeach
            </div>
        @endif
    </div>
</div>
