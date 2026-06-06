<div class="space-y-6">
    @if (session('message'))
        <div class="alert-dismiss bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm animate-fade-in">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-emerald-800">{{ session('message') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert-dismiss bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl shadow-sm animate-fade-in">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-rose-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow text-center">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Present Days</span>
            <p class="text-4xl font-extrabold text-emerald-600 mt-2">{{ $stats['present'] }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow text-center">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Late Days</span>
            <p class="text-4xl font-extrabold text-amber-500 mt-2">{{ $stats['late'] }}</p>
        </div>
        <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow text-center">
            <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Absent Days</span>
            <p class="text-4xl font-extrabold text-rose-500 mt-2">{{ $stats['absent'] }}</p>
        </div>
    </div>

    {{-- Today's Check In/Out Section --}}
    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-100 pb-4 mb-6">
            <div>
                <h2 class="text-xl font-bold text-gray-900">Today's Attendance</h2>
                <p class="text-sm text-gray-500 mt-0.5">{{ now()->format('l, d F Y') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Live Check-in Portal</span>
            </div>
        </div>

        @if (!$canCheckIn)
            <div class="bg-amber-50 border border-amber-200 rounded-xl px-5 py-4 flex items-start gap-3">
                <svg class="w-5 h-5 text-amber-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-amber-900">Attendance Restricted</p>
                    <p class="text-sm text-amber-800 mt-0.5">{{ $statusMessage }}</p>
                </div>
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-4 mt-4">
            @if (!$checkedIn)
                <button wire:click="checkIn" {{ !$canCheckIn ? 'disabled' : '' }}
                        class="bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-bold px-8 py-3.5 rounded-xl shadow-lg shadow-emerald-500/10 transition-all transform active:scale-95 text-base {{ !$canCheckIn ? 'opacity-50 cursor-not-allowed' : '' }}">
                    Check In
                </button>
            @else
                <div class="inline-flex items-center gap-2.5 bg-emerald-50 text-emerald-800 border border-emerald-100 px-5 py-3 rounded-xl font-semibold text-sm">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Checked in at {{ substr($todayRecord->check_in, 0, 5) }}</span>
                </div>
            @endif

            @if ($checkedIn && !$checkedOut)
                <button wire:click="checkOut" {{ !$canCheckIn ? 'disabled' : '' }}
                        class="bg-gradient-to-r from-rose-500 to-rose-600 hover:from-rose-600 hover:to-rose-700 text-white font-bold px-8 py-3.5 rounded-xl shadow-lg shadow-rose-500/10 transition-all transform active:scale-95 text-base {{ !$canCheckIn ? 'opacity-50 cursor-not-allowed' : '' }}">
                    Check Out
                </button>
            @elseif ($checkedOut)
                <div class="inline-flex items-center gap-2.5 bg-gray-50 text-gray-700 border border-gray-200/60 px-5 py-3 rounded-xl font-semibold text-sm">
                    <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Checked out at {{ substr($todayRecord->check_out, 0, 5) }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- History Table --}}
    @if ($history->isNotEmpty())
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900 text-lg">Attendance Log</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-400">Date</th>
                            <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-400">Status</th>
                            <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-400">Check In</th>
                            <th class="px-6 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-400">Check Out</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($history as $att)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-700">
                                    {{ $att->date->format('l, d M Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                        {{ match($att->status) { 'present' => 'bg-emerald-50 text-emerald-700 border border-emerald-100', 'late' => 'bg-amber-50 text-amber-700 border border-amber-100', 'absent' => 'bg-rose-50 text-rose-700 border border-rose-100', default => 'bg-gray-50 text-gray-700' } }}">
                                        {{ ucfirst($att->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $att->check_in ? $att->date->setTimeFromTimeString($att->check_in)->format('h:i A') : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $att->check_out ? $att->date->setTimeFromTimeString($att->check_out)->format('h:i A') : '-' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
