<div>
    {{-- Row 1: Key Metric Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-gradient-to-br from-stormy-500 to-stormy-700 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-8 h-8 text-stormy-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
            </div>
            <p class="text-3xl font-bold">{{ $totalCompanies }}</p>
            <p class="text-stormy-100 text-sm">Companies</p>
        </div>
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-700 rounded-xl shadow-lg p-5 text-white">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-8 h-8 text-emerald-100" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857"/></svg>
            </div>
            <p class="text-3xl font-bold">{{ $totalPersonnel }}</p>
            <p class="text-emerald-100 text-sm">Total Enrollments</p>
        </div>
        <div class="rounded-xl shadow-lg p-5 text-white" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-8 h-8 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <p class="text-3xl font-bold">{{ $pendingReview + $shortlisted }}</p>
            <p class="text-white/80 text-sm">Awaiting Action</p>
        </div>
        <div class="rounded-xl shadow-lg p-5 text-white" style="background: linear-gradient(135deg, #6366f1, #4f46e5);">
            <div class="flex items-center justify-between mb-2">
                <svg class="w-8 h-8 text-white/80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <p class="text-3xl font-bold">{{ $todayPresent }}<span class="text-lg font-normal text-white/60">/{{ $todayTotal }}</span></p>
            <p class="text-white/80 text-sm">Present Today</p>
        </div>
    </div>

    {{-- Row 2: Status Pipeline + Today's Attendance + Year Distribution --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Status Pipeline --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Personnel Pipeline</h3>
            @php $pipelineMax = max($statusCounts) ?: 1; @endphp
            @foreach ([
                ['key' => 'pending_forms', 'label' => 'Pending Forms', 'color' => 'bg-gray-400'],
                ['key' => 'pending_review', 'label' => 'Pending Review', 'color' => 'bg-blue-500'],
                ['key' => 'shortlisted', 'label' => 'Shortlisted', 'color' => 'bg-sky-500'],
                ['key' => 'endorsed', 'label' => 'Endorsed', 'color' => 'bg-stormy-500'],
                ['key' => 'validated', 'label' => 'Validated', 'color' => 'bg-emerald-500'],
                ['key' => 'active', 'label' => 'Active', 'color' => 'bg-teal-500'],
                ['key' => 'rejected', 'label' => 'Rejected', 'color' => 'bg-rose-500'],
            ] as $item)
                @php $count = $statusCounts[$item['key']] ?? 0; @endphp
                <div class="flex items-center gap-3 py-1.5">
                    <span class="w-28 text-xs font-medium text-gray-600 truncate">{{ $item['label'] }}</span>
                    <div class="flex-1 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full {{ $item['color'] }} rounded-full transition-all" style="width: {{ $totalPersonnel > 0 ? ($count / $totalPersonnel) * 100 : 0 }}%"></div>
                    </div>
                    <span class="w-8 text-xs font-bold text-gray-900 text-right">{{ $count }}</span>
                </div>
            @endforeach
            <div class="mt-4 pt-3 border-t border-gray-100 flex justify-between text-xs font-semibold text-gray-500">
                <span>{{ $totalPersonnel }} total</span>
                <span class="text-emerald-700">{{ $activePersonnel }} active ({{ $totalPersonnel > 0 ? round(($activePersonnel / $totalPersonnel) * 100) : 0 }}%)</span>
            </div>
        </div>

        {{-- Today's Attendance Breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Today's Attendance</h3>
                <span class="text-xs text-gray-400">{{ now()->format('l, d M Y') }}</span>
            </div>
            @if ($todayTotal > 0)
                <div class="flex items-end gap-1 mb-5 h-32">
                    @php
                        $maxStat = max($todayPresent, $todayLate, $todayAbsent) ?: 1;
                        $bars = [
                            ['label' => 'Present', 'count' => $todayPresent, 'color' => 'bg-emerald-500', 'text' => 'text-emerald-700'],
                            ['label' => 'Late', 'count' => $todayLate, 'color' => 'bg-amber-500', 'text' => 'text-amber-700'],
                            ['label' => 'Absent', 'count' => $todayAbsent, 'color' => 'bg-rose-500', 'text' => 'text-rose-700'],
                        ];
                    @endphp
                    @foreach ($bars as $bar)
                        <div class="flex-1 flex flex-col items-center gap-1.5">
                            <span class="text-lg font-bold {{ $bar['text'] }}">{{ $bar['count'] }}</span>
                            <div class="w-full bg-gray-100 rounded-lg overflow-hidden" style="height: 80px;">
                                <div class="h-full {{ $bar['color'] }} rounded-lg transition-all" style="height: {{ ($bar['count'] / $maxStat) * 80 }}px; margin-top: {{ 80 - ($bar['count'] / $maxStat) * 80 }}px"></div>
                            </div>
                            <span class="text-[10px] font-semibold text-gray-500">{{ $bar['label'] }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-400 text-sm">No attendance records today.</div>
            @endif
            <div class="grid grid-cols-3 gap-2 text-center text-xs">
                <div class="bg-emerald-50 rounded-lg py-2"><span class="font-bold text-emerald-700">{{ $todayPresent }}</span> <span class="text-gray-500">Present</span></div>
                <div class="bg-amber-50 rounded-lg py-2"><span class="font-bold text-amber-700">{{ $todayLate }}</span> <span class="text-gray-500">Late</span></div>
                <div class="bg-rose-50 rounded-lg py-2"><span class="font-bold text-rose-700">{{ $todayAbsent }}</span> <span class="text-gray-500">Absent</span></div>
            </div>
        </div>

        {{-- NSS Year Distribution --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">NSS Year Distribution</h3>
            @if ($yearDistribution->isNotEmpty())
                @php $yearMax = $yearDistribution->max('total') ?: 1; @endphp
                @foreach ($yearDistribution as $yd)
                    <div class="flex items-center gap-3 py-1.5">
                        <span class="w-12 text-xs font-semibold text-gray-600">{{ $yd->nss_year }}</span>
                        <div class="flex-1 h-3 bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-gradient-to-r from-stormy-500 to-stormy-600 rounded-full transition-all" style="width: {{ ($yd->total / $yearMax) * 100 }}%"></div>
                        </div>
                        <span class="w-8 text-xs font-bold text-gray-900 text-right">{{ $yd->total }}</span>
                    </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-400 text-sm">No year data available.</div>
            @endif
        </div>
    </div>

    {{-- Row 3: Weekly Trend + Per-Company Breakdown --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Weekly Attendance Trend --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Weekly Attendance Trend</h3>
            <div class="flex items-end gap-2 h-36">
                @php $weekMax = max($weeklyAttendance->max('present'), $weeklyAttendance->max('late'), $weeklyAttendance->max('absent')) ?: 1; @endphp
                @foreach ($weeklyAttendance as $day)
                    <div class="flex-1 flex flex-col items-center gap-1 h-full justify-end">
                        <div class="w-full flex flex-col-reverse gap-0.5" style="height: 100px;">
                            <div class="w-full bg-rose-200 rounded-t-sm" style="height: {{ ($day['absent'] / $weekMax) * 100 }}px"></div>
                            <div class="w-full bg-amber-300 rounded-t-sm" style="height: {{ ($day['late'] / $weekMax) * 100 }}px"></div>
                            <div class="w-full bg-emerald-400 rounded-t-sm" style="height: {{ ($day['present'] / $weekMax) * 100 }}px"></div>
                        </div>
                        <span class="text-[10px] font-semibold text-gray-500">{{ $day['date'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 mt-4 pt-3 border-t border-gray-100 justify-center text-xs">
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-emerald-400"></span> Present</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-amber-300"></span> Late</span>
                <span class="flex items-center gap-1.5"><span class="w-2.5 h-2.5 rounded bg-rose-200"></span> Absent</span>
            </div>
        </div>

        {{-- Per-Company Breakdown --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Company Breakdown</h3>
                <a href="{{ route('admin.companies') }}" wire:navigate class="text-xs font-semibold text-stormy-600 hover:text-stormy-700">All</a>
            </div>
            @if ($companyStats->isNotEmpty())
                @php $compMax = max($companyStats->max('enrollments_count'), 1); @endphp
                @foreach ($companyStats as $c)
                    <div class="mb-3">
                        <div class="flex items-center justify-between text-xs mb-1">
                            <span class="font-medium text-gray-900 truncate">{{ $c->name }}</span>
                            <span class="font-semibold text-gray-600">{{ $c->enrollments_count }} enrolled</span>
                        </div>
                        <div class="flex gap-0.5 h-2.5 bg-gray-100 rounded-full overflow-hidden">
                            @php
                                $activePct = $compMax > 0 ? ($c->active_count / $compMax) * 100 : 0;
                                $pendingPct = $compMax > 0 ? ($c->pending_count / $compMax) * 100 : 0;
                                $otherPct = $compMax > 0 ? (($c->enrollments_count - $c->active_count - $c->pending_count) / $compMax) * 100 : 0;
                            @endphp
                            <div class="h-full bg-emerald-400 transition-all" style="width: {{ $activePct }}%"></div>
                            <div class="h-full bg-amber-400 transition-all" style="width: {{ $pendingPct }}%"></div>
                            <div class="h-full bg-gray-300 transition-all" style="width: {{ $otherPct }}%"></div>
                        </div>
                        <div class="flex gap-3 mt-0.5 text-[10px] text-gray-400">
                            <span>{{ $c->active_count }} active</span>
                            <span>{{ $c->pending_count }} pending</span>
                            <span>{{ $c->departments_count }} depts</span>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="text-center py-8 text-gray-400 text-sm">No companies yet.</div>
            @endif
        </div>
    </div>

    {{-- Row 4: Users by Role + Recent Activity --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Users by Role --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h3 class="text-sm font-bold text-gray-900 mb-4">Users by Role</h3>
            <div class="grid grid-cols-3 gap-4">
                @foreach ([
                    ['role' => 'Company Admins', 'count' => $roleCounts['company_admin'], 'color' => 'bg-stormy-500', 'light' => 'bg-stormy-50'],
                    ['role' => 'HR Staff', 'count' => $roleCounts['hr_staff'], 'color' => 'bg-sky-500', 'light' => 'bg-sky-50'],
                    ['role' => 'NSS Personnel', 'count' => $roleCounts['nss_personnel'], 'color' => 'bg-emerald-500', 'light' => 'bg-emerald-50'],
                ] as $rc)
                    <div class="text-center p-4 {{ $rc['light'] }} rounded-xl">
                        <p class="text-2xl font-bold text-gray-900">{{ $rc['count'] }}</p>
                        <p class="text-xs font-medium text-gray-600 mt-1">{{ $rc['role'] }}</p>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 pt-3 border-t border-gray-100 text-xs text-gray-500 text-center">Total system users: <span class="font-bold text-gray-900">{{ $totalUsers }}</span></div>
        </div>

        {{-- Recent Enrollments --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900">Recent Enrollments</h3>
                <a href="{{ route('admin.personnel') }}" wire:navigate class="text-xs font-semibold text-stormy-600 hover:text-stormy-700">All</a>
            </div>
            @if ($recentEnrollments->isNotEmpty())
                <div class="divide-y divide-gray-50">
                    @foreach ($recentEnrollments as $e)
                        <div class="flex items-center justify-between py-2.5">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-7 h-7 bg-stormy-100 rounded-full flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-stormy-600">{{ substr($e->user->name, 0, 1) }}</span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-medium text-gray-900 truncate">{{ $e->user->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $e->company?->name }} · {{ $e->nss_number }}</p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-500 shrink-0">{{ $e->created_at->diffForHumans() }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8 text-gray-400 text-sm">No enrollments yet.</div>
            @endif
        </div>
    </div>
</div>
