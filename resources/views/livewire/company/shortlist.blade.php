<div>
    @if (session('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold">Shortlist Personnel</h2>
            <p class="text-sm text-gray-500">Review and shortlist personnel who have completed their profiles.</p>
        </div>
        <span class="text-sm text-gray-500">{{ $processedCount }} already processed</span>
    </div>

    @if ($pendingPersonnel->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center">
            <p class="text-gray-500">No personnel pending review.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NSS #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($pendingPersonnel as $enrollment)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="font-medium">{{ $enrollment->user->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $enrollment->nss_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div>{{ $enrollment->user->email }}</div>
                                <div class="text-gray-500">{{ $enrollment->user->phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                {{ $enrollment->department?->name ?? 'Not assigned' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                <button wire:click="shortlist({{ $enrollment->id }})"
                                        class="bg-green-600 text-white px-4 py-1.5 rounded-md hover:bg-green-700 text-xs font-medium">
                                    Shortlist
                                </button>
                                <button wire:click="confirmReject({{ $enrollment->id }})"
                                        class="bg-red-100 text-red-700 px-4 py-1.5 rounded-md hover:bg-red-200 text-xs font-medium">
                                    Reject
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Rejection Modal --}}
    @if ($confirmingRejection)
        <div class="fixed inset-0 bg-gray-500/75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold mb-4">Reject Personnel</h3>
                <p class="text-sm text-gray-500 mb-4">Please provide a reason for rejecting this personnel.</p>
                <textarea wire:model="rejectionReason"
                          rows="4"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                          placeholder="Reason for rejection..."></textarea>
                @error('rejectionReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div class="flex justify-end space-x-3 mt-4">
                    <button wire:click="$set('confirmingRejection', false)"
                            class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <button wire:click="reject"
                            class="px-4 py-2 text-sm text-white bg-red-600 rounded-md hover:bg-red-700">
                        Confirm Reject
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
