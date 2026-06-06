<div>
    @if (session('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold">Attendance Tracking</h2>
            <p class="text-sm text-gray-500">Mark daily attendance for active personnel.</p>
        </div>
        <div class="flex items-center gap-3">
            <input type="date" wire:model.live="selectedDate"
                   class="rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
            <button wire:click="saveAll" wire:loading.attr="disabled"
                    class="bg-stormy-600 text-white px-4 py-2 rounded-md hover:bg-stormy-700 text-sm font-medium">
                <span wire:loading.remove>Save All</span>
                <span wire:loading>Saving...</span>
            </button>
        </div>
    </div>

    @if (empty($records))
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-gray-500">No active or endorsed personnel to mark attendance for.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NSS #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check In</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Check Out</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($records as $i => $record)
                        <tr>
                            <td class="px-6 py-3 whitespace-nowrap text-sm font-medium">{{ $record['name'] }}</td>
                            <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $record['nss_number'] }}</td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <select wire:model="records.{{ $i }}.status"
                                        class="text-xs border-gray-300 rounded-md">
                                    <option value="present">Present</option>
                                    <option value="absent">Absent</option>
                                    <option value="late">Late</option>
                                    <option value="half-day">Half Day</option>
                                    <option value="leave">Leave</option>
                                </select>
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <input type="time" wire:model="records.{{ $i }}.check_in"
                                       class="text-sm border-gray-300 rounded-md">
                            </td>
                            <td class="px-6 py-3 whitespace-nowrap">
                                <input type="time" wire:model="records.{{ $i }}.check_out"
                                       class="text-sm border-gray-300 rounded-md">
                            </td>
                            <td class="px-6 py-3">
                                <input type="text" wire:model="records.{{ $i }}.remarks" placeholder="Optional..."
                                       class="text-sm border-gray-300 rounded-md w-full">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
