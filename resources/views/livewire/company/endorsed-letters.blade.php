<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Endorsed Posting Letters</h2>
            <p class="text-sm text-gray-500 mt-1">Preview endorsed posting letters and validate uploaded letters.</p>
        </div>
        <span class="bg-stormy-50 text-stormy-700 text-sm font-medium px-3 py-1.5 rounded-lg">{{ $letters->count() }} endorsed</span>
    </div>

    @if ($letters->isEmpty())
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-16 text-center">
            <p class="text-gray-500 font-medium">No endorsed letters yet.</p>
            <p class="text-gray-400 text-sm mt-1">Endorsed letters will appear here once generated.</p>
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Personnel</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">NSS #</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Department</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Endorsed By</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Validated Letter</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($letters as $letter)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-stormy-100 rounded-full flex items-center justify-center">
                                        <span class="text-sm font-bold text-stormy-600">{{ substr($letter->enrollment->user->name, 0, 1) }}</span>
                                    </div>
                                    <div class="font-medium text-gray-900">{{ $letter->enrollment->user->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">{{ $letter->enrollment->nss_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($letter->enrollment->department)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-700">{{ $letter->enrollment->department->name }}</span>
                                @else
                                    <span class="text-gray-400 italic">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $letter->endorsedBy?->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if ($letter->validated_file_path)
                                    <a href="{{ Storage::url($letter->validated_file_path) }}" target="_blank" class="text-stormy-600 hover:text-stormy-800 font-medium underline">
                                        View Uploaded
                                    </a>
                                @else
                                    <span class="text-gray-400 italic">Not uploaded</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusBadge = match(true) {
                                        $letter->validated_by => 'bg-emerald-100 text-emerald-800',
                                        $letter->validated_file_path => 'bg-amber-100 text-amber-800',
                                        default => 'bg-gray-100 text-gray-600',
                                    };
                                    $statusLabel = match(true) {
                                        $letter->validated_by => 'Validated',
                                        $letter->validated_file_path => 'Pending Validation',
                                        default => 'Awaiting Upload',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusBadge }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm space-x-2">
                                <button wire:click="preview({{ $letter->id }})"
                                        class="inline-flex items-center gap-1 text-stormy-600 hover:text-stormy-800 font-semibold hover:underline">
                                    Preview
                                </button>
                                @if ($letter->validated_file_path && !$letter->validated_by)
                                    <button wire:click="confirmValidate({{ $letter->id }})"
                                            class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 font-semibold hover:underline">
                                        Validate
                                    </button>
                                    <button wire:click="confirmReject({{ $letter->id }})"
                                            class="inline-flex items-center gap-1 text-red-600 hover:text-red-800 font-semibold hover:underline">
                                        Reject
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- Preview Modal --}}
    @if ($previewUrl)
        <div class="fixed inset-0 bg-gray-500/50 backdrop-blur-sm flex items-center justify-center z-50" wire:click.self="closePreview">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-4xl mx-4 max-h-[90vh] flex flex-col">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Endorsed Posting Letter</h3>
                        <p class="text-sm text-gray-500">{{ $previewName }}</p>
                    </div>
                    <button wire:click="closePreview" class="p-1 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <div class="flex-1 p-4 min-h-0">
                    <iframe src="{{ $previewUrl }}" class="w-full h-[70vh] rounded-lg border border-gray-200" frameborder="0"></iframe>
                </div>
                <div class="px-6 py-3 border-t border-gray-200 flex justify-end">
                    <a href="{{ $previewUrl }}" target="_blank"
                       class="inline-flex items-center gap-2 text-sm text-stormy-600 hover:text-stormy-800 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download
                    </a>
                </div>
            </div>
        </div>
    @endif

    {{-- Reject Modal --}}
    @if ($rejectingId)
        <div class="fixed inset-0 bg-gray-500/75 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Reject Personnel</h3>
                <p class="text-sm text-gray-500 mb-4">Provide a reason for rejecting this personnel's validated letter.</p>
                <textarea wire:model="rejectReason" rows="3"
                          class="w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                          placeholder="Reason for rejection..."></textarea>
                @error('rejectReason') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div class="flex justify-end space-x-3 mt-4">
                    <button wire:click="$set('rejectingId', null)"
                            class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">
                        Cancel
                    </button>
                    <x-loading-button type="button" target="reject" loading="Rejecting..." wire:click="reject"
                            class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700">
                        Reject
                    </x-loading-button>
                </div>
            </div>
        </div>
    @endif
</div>
