<div class="space-y-6">
    <div class="border-b border-gray-100 pb-4">
        <h2 class="text-xl font-bold text-gray-900">Official Placement Documents</h2>
        <p class="text-sm text-gray-500 mt-0.5">Access and download your endorsed letters and onboarding credentials.</p>
    </div>

    @if ($endorsedLetters->isEmpty())
        <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center shadow-sm">
            <span class="text-gray-300 block mb-3">
                <svg class="w-12 h-12 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </span>
            <p class="text-gray-500 text-sm max-w-sm mx-auto">No endorsed letters yet. Your letter will appear here once endorsed by your company admin.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($endorsedLetters as $letter)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between">
                    <div>
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex items-center gap-3">
                                <div class="p-2.5 bg-stormy-50 rounded-xl text-stormy-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <h3 class="font-bold text-gray-900 text-base">Endorsed Posting Letter</h3>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        Updated {{ $letter->updated_at->format('d M Y, h:i A') }}
                                    </p>
                                </div>
                            </div>
                            @php
                                $statusBadge = match(true) {
                                    $letter->validated_by => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                    $letter->validated_file_path => 'bg-amber-100 text-amber-800 border-amber-200',
                                    default => 'bg-stormy-100 text-stormy-800 border-stormy-200',
                                };
                                $statusLabel = match(true) {
                                    $letter->validated_by => 'Validated',
                                    $letter->validated_file_path => 'Pending Validation',
                                    default => 'Endorsed',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusBadge }}">
                                {{ $statusLabel }}
                            </span>
                        </div>

                        @if ($letter->enrollment?->department)
                            <div class="bg-gray-50 rounded-xl px-4 py-3 mb-6 flex items-center justify-between text-xs font-semibold text-gray-500 border border-gray-100">
                                <span>Assigned Department:</span>
                                <span class="text-gray-800">{{ $letter->enrollment->department->name }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-4">
                        <a href="{{ Storage::url($letter->generated_file_path) }}?v={{ $letter->updated_at->timestamp }}" target="_blank"
                           class="inline-flex items-center justify-center gap-2 w-full bg-gradient-to-r from-stormy-600 to-stormy-700 hover:from-stormy-700 hover:to-stormy-800 text-white font-bold py-2.5 px-4 rounded-xl shadow-sm transition-all text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download Endorsed PDF
                        </a>

                        <div class="border-t border-gray-100 pt-4">
                            @if ($letter->validated_file_path)
                                <div class="flex items-center justify-between text-xs rounded-xl px-4 py-3 {{ $letter->validated_by ? 'bg-emerald-50 border border-emerald-100' : 'bg-amber-50 border border-amber-100' }}">
                                    <span class="font-semibold flex items-center gap-1.5 {{ $letter->validated_by ? 'text-emerald-800' : 'text-amber-800' }}">
                                        @if ($letter->validated_by)
                                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Validated
                                        @else
                                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            Pending Validation
                                        @endif
                                    </span>
                                    <a href="{{ Storage::url($letter->validated_file_path) }}" target="_blank" class="text-stormy-600 hover:text-stormy-800 font-bold underline">
                                        View File
                                    </a>
                                </div>
                            @else
                                <form wire:submit.prevent="uploadValidatedLetter({{ $letter->id }})" class="space-y-2">
                                    <label class="block text-xs font-semibold text-gray-600">Upload Validated Endorsed Posting Letter (PDF)</label>
                                    <div class="flex items-center gap-2">
                                        <input type="file" wire:model="validatedFile" accept=".pdf" class="block w-full text-xs text-gray-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-stormy-50 file:text-stormy-700 hover:file:bg-stormy-100">
                                        <x-loading-button target="uploadValidatedLetter({{ $letter->id }})" loading="Uploading..."
                                                class="px-3.5 py-2 bg-stormy-600 hover:bg-stormy-700 text-white rounded-lg text-xs font-bold transition-all whitespace-nowrap shadow-sm">
                                            Upload
                                        </x-loading-button>
                                    </div>
                                    <div wire:loading wire:target="validatedFile" class="text-xs text-gray-400">Uploading file...</div>
                                    @error('validatedFile') <p class="text-red-500 text-[10px] mt-0.5">{{ $message }}</p> @enderror
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
        <h3 class="font-bold text-gray-900 mb-3 text-base">About Your Documents</h3>
        <ul class="space-y-3">
            <li class="flex items-start gap-2.5 text-sm text-gray-500">
                <svg class="w-5 h-5 text-stormy-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                <span>Your endorsed posting letter is available for download once the company admin has endorsed it.</span>
            </li>
            <li class="flex items-start gap-2.5 text-sm text-gray-500">
                <svg class="w-5 h-5 text-stormy-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                <span>The letter includes your verified personal details, education background, and assigned corporate department.</span>
            </li>
            <li class="flex items-start gap-2.5 text-sm text-gray-500">
                <svg class="w-5 h-5 text-stormy-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                <span>Please keep a downloaded backup for your records and present it to NSS coordinators whenever requested.</span>
            </li>
        </ul>
    </div>
</div>
