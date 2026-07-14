<div>
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Attendance Validation</h2>
            <p class="text-sm text-gray-500 mt-1">Personnel appear here after they check in, check out, or mark absent.</p>
        </div>
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by name or NSS #..."
                   class="rounded-xl border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
            <input type="date" wire:model.live="selectedDate"
                   class="rounded-xl border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
        </div>
    </div>

    @if ($pendingCount > 0)
        <div class="mb-4 bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl text-sm font-medium">
            {{ $pendingCount }} attendance submission{{ $pendingCount === 1 ? '' : 's' }} awaiting validation.
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900">Submissions for {{ \Carbon\Carbon::parse($selectedDate)->format('d M Y') }}</h3>
            <span class="text-xs text-gray-500">{{ $records->total() }} record(s)</span>
        </div>

        @if ($records->isEmpty())
            <div class="p-12 text-center text-gray-500 text-sm">
                No personnel submissions for this date yet.
            </div>
        @else
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                <button type="button" wire:click="sortBy('name')" class="hover:text-stormy-600">Name</button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                <button type="button" wire:click="sortBy('nss_number')" class="hover:text-stormy-600">NSS #</button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                <button type="button" wire:click="sortBy('status')" class="hover:text-stormy-600">Status</button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                <button type="button" wire:click="sortBy('check_in')" class="hover:text-stormy-600">Check In</button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                <button type="button" wire:click="sortBy('check_out')" class="hover:text-stormy-600">Check Out</button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Reason</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($records as $record)
                            @include('livewire.company.partials.attendance-row', ['record' => $record, 'mobile' => false])
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="lg:hidden divide-y divide-gray-100">
                @foreach ($records as $record)
                    @include('livewire.company.partials.attendance-row', ['record' => $record, 'mobile' => true])
                @endforeach
            </div>

            <div class="px-6 py-4 border-t border-gray-100">
                {{ $records->links() }}
            </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="font-semibold text-gray-900">All Attendance History</h3>
            <p class="text-xs text-gray-500 mt-1">All attendance records. Use column headers above to sort.</p>
        </div>

        @if ($history->isEmpty())
            <div class="p-12 text-center text-gray-500 text-sm">No attendance history yet.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                <button type="button" wire:click="sortBy('date')" class="hover:text-stormy-600">Date</button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                <button type="button" wire:click="sortBy('name')" class="hover:text-stormy-600">Name</button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">
                                <button type="button" wire:click="sortBy('nss_number')" class="hover:text-stormy-600">NSS #</button>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Check In</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Check Out</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($history as $record)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm">{{ $record->date->format('d M Y') }}</td>
                                <td class="px-6 py-3 text-sm font-medium">{{ $record->user->name }}</td>
                                <td class="px-6 py-3 text-sm font-mono">{{ $record->user->enrollment?->nss_number ?? '—' }}</td>
                                <td class="px-6 py-3 text-sm">
                                    @if ($record->check_in)
                                        {{ substr($record->check_in, 0, 5) }}
                                        <span class="text-xs {{ $record->check_in_validated_at ? 'text-emerald-600' : 'text-amber-600' }}">
                                            ({{ $record->check_in_validated_at ? 'validated' : 'pending' }})
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm">
                                    @if ($record->check_out)
                                        {{ substr($record->check_out, 0, 5) }}
                                        <span class="text-xs {{ $record->check_out_validated_at ? 'text-emerald-600' : 'text-amber-600' }}">
                                            ({{ $record->check_out_validated_at ? 'validated' : 'pending' }})
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-sm capitalize">{{ $record->status }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $history->links(data: ['pageName' => 'historyPage']) }}
            </div>
        @endif
    </div>
</div>
