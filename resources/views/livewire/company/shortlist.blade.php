<div>
    @if (session('message'))
        <div class="alert-dismiss mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Shortlist Personnel</h2>
            <p class="text-sm text-gray-500 mt-1">Review and shortlist personnel who have completed their profiles.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-500 bg-gray-100 px-3 py-1.5 rounded-lg">{{ $processedCount }} already processed</span>
            <span class="bg-stormy-50 text-stormy-700 text-sm font-medium px-3 py-1.5 rounded-lg">{{ $pendingPersonnel->count() }} pending</span>
        </div>
    </div>

    @if ($pendingPersonnel->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-16 text-center">
            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <p class="text-gray-500 font-medium">No personnel pending review.</p>
            <p class="text-gray-400 text-sm mt-1">Personnel who complete their profiles will appear here.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NSS #</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Contact</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Posting Letter</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($pendingPersonnel as $enrollment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-stormy-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-stormy-600">{{ substr($enrollment->user->name, 0, 1) }}</span>
                                    </div>
                                    <div class="font-medium text-gray-900">{{ $enrollment->user->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">{{ $enrollment->nss_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <div class="text-gray-900">{{ $enrollment->user->email }}</div>
                                <div class="text-gray-400">{{ $enrollment->user->phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($enrollment->department)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ $enrollment->department->name }}</span>
                                @else
                                    <span class="text-gray-400 italic">Not assigned</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @php
                                    $postingLetter = $enrollment->user->documents()->where('type', 'posting_letter')->first();
                                @endphp
                                @if ($postingLetter)
                                    <button wire:click="viewLetter({{ $enrollment->id }})"
                                       class="inline-flex items-center gap-1.5 text-stormy-600 hover:text-stormy-800 font-semibold hover:underline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        View Letter
                                    </button>
                                @else
                                    <span class="text-gray-400 italic">Not uploaded</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                <button wire:click="shortlist({{ $enrollment->id }})"
                                        class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 px-3 py-1.5 rounded-lg hover:bg-emerald-100 text-xs font-medium ring-1 ring-emerald-600/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Shortlist
                                </button>
                                <button wire:click="confirmReject({{ $enrollment->id }})"
                                        class="inline-flex items-center gap-1 bg-red-50 text-red-700 px-3 py-1.5 rounded-lg hover:bg-red-100 text-xs font-medium ring-1 ring-red-600/20 transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    Reject
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Letter Viewer Modal --}}
    @if ($viewingLetterId && $viewingLetterBase64)
        <div class="fixed inset-0 bg-gray-500/50 backdrop-blur-sm flex items-center justify-center z-50" wire:click.self="closeViewer">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Posting Letter</h3>
                    <button wire:click="closeViewer" class="p-1 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex-1 p-4 min-h-0" x-data x-init="initPdfViewer('pdf-viewer-container', '{{ $viewingLetterBase64 }}')">
                    <div id="pdf-viewer-container" class="w-full h-[70vh] overflow-y-auto rounded-lg border border-gray-200 bg-gray-50 p-2"></div>
                </div>
            </div>
        </div>
    @endif

    @if ($confirmingRejection)
        <div class="fixed inset-0 bg-gray-500/50 backdrop-blur-sm flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md mx-4">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Reject Personnel</h3>
                        <p class="text-sm text-gray-500">Provide a reason for rejection</p>
                    </div>
                </div>
                <textarea wire:model="rejectionReason" rows="4" placeholder="Reason for rejection..."
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:ring-2 focus:ring-red-500 focus:border-red-500 text-sm"></textarea>
                @error('rejectionReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div class="flex justify-end gap-3 mt-4">
                    <button wire:click="$set('confirmingRejection', false)"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button wire:click="reject"
                            class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors">
                        Confirm Rejection
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
