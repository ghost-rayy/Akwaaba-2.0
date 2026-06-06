<div>
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Reports</h2>
        <p class="text-sm text-gray-500">Summary statistics and data exports.</p>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Total Personnel</p>
            <p class="text-3xl font-bold">{{ $totalPersonnel }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Endorsed Letters</p>
            <p class="text-3xl font-bold">{{ $endorsedCount }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Attendance (Period)</p>
            <p class="text-3xl font-bold">{{ $totalAttendance }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm text-gray-500">Evaluations</p>
            <p class="text-3xl font-bold">{{ $totalEvaluations }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Personnel by Status --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold mb-4">Personnel by Status</h3>
            @if ($personnelByStatus->isEmpty())
                <p class="text-sm text-gray-400">No data.</p>
            @else
                <div class="space-y-3">
                    @foreach ($personnelByStatus as $status => $count)
                        @php
                            $colors = [
                                'pending_forms' => 'bg-yellow-400',
                                'pending_review' => 'bg-blue-400',
                                'shortlisted' => 'bg-green-400',
                                'rejected' => 'bg-red-400',
                                'endorsed' => 'bg-stormy-400',
                                'active' => 'bg-emerald-400',
                                'completed' => 'bg-gray-400',
                            ];
                            $pct = $totalPersonnel > 0 ? round($count / $totalPersonnel * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ str_replace('_', ' ', ucfirst($status)) }}</span>
                                <span class="font-medium">{{ $count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $colors[$status] ?? 'bg-stormy-400' }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Attendance Summary --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="font-semibold mb-4">Attendance Summary</h3>
            <div class="flex items-center gap-2 mb-4">
                <input type="date" wire:model.live="startDate"
                       class="text-sm rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                <span class="text-gray-400">to</span>
                <input type="date" wire:model.live="endDate"
                       class="text-sm rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
            </div>
            @if ($recentAttendance->isEmpty())
                <p class="text-sm text-gray-400">No attendance records for this period.</p>
            @else
                <div class="space-y-3">
                    @foreach ($recentAttendance as $status => $count)
                        @php
                            $colors = [
                                'present' => 'bg-green-400',
                                'absent' => 'bg-red-400',
                                'late' => 'bg-yellow-400',
                                'half-day' => 'bg-orange-400',
                                'leave' => 'bg-blue-400',
                            ];
                            $pct = $totalAttendance > 0 ? round($count / $totalAttendance * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span>{{ ucfirst($status) }}</span>
                                <span class="font-medium">{{ $count }}</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="{{ $colors[$status] ?? 'bg-stormy-400' }} h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Average Scores --}}
    @if ($avgScores && $avgScores->overall)
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h3 class="font-semibold mb-4">Average Evaluation Scores</h3>
            <div class="grid grid-cols-5 gap-4">
                <div class="text-center">
                    <div class="text-3xl font-bold text-stormy-600">{{ number_format($avgScores->punctuality, 1) }}</div>
                    <div class="text-xs text-gray-500">Punctuality</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-green-600">{{ number_format($avgScores->performance, 1) }}</div>
                    <div class="text-xs text-gray-500">Performance</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-blue-600">{{ number_format($avgScores->attitude, 1) }}</div>
                    <div class="text-xs text-gray-500">Attitude</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-stormy-600">{{ number_format($avgScores->teamwork, 1) }}</div>
                    <div class="text-xs text-gray-500">Teamwork</div>
                </div>
                <div class="text-center">
                    <div class="text-3xl font-bold text-amber-600">{{ number_format($avgScores->overall, 1) }}</div>
                    <div class="text-xs text-gray-500">Overall</div>
                </div>
            </div>
        </div>
    @endif

    {{-- Export Buttons --}}
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="font-semibold mb-4">Download Reports</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <button wire:click="downloadPersonnel"
                    class="flex items-center justify-center gap-2 bg-stormy-600 text-white px-4 py-3 rounded-md hover:bg-stormy-700 text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Personnel List
            </button>
            <button wire:click="downloadAttendance"
                    class="flex items-center justify-center gap-2 bg-green-600 text-white px-4 py-3 rounded-md hover:bg-green-700 text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Attendance Record
            </button>
            <button wire:click="downloadEvaluations"
                    class="flex items-center justify-center gap-2 bg-stormy-500 text-white px-4 py-3 rounded-md hover:bg-stormy-600 text-sm font-medium">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                Evaluations
            </button>
        </div>
    </div>
</div>
