<div>
    @if (session('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc pl-4 text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- No shortlisted personnel message --}}
    @if ($shortlistedPersonnel->isEmpty())
        <div class="bg-white rounded-lg shadow p-12 text-center mb-6">
            <p class="text-gray-500">No shortlisted personnel awaiting endorsement. Shortlist personnel first.</p>
        </div>
    @endif

    {{-- Endorsement Form --}}
    @if ($shortlistedPersonnel->isNotEmpty())
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Endorse Letters</h2>
            <form wire:submit="endorse" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Template</label>
                    <select wire:model="selectedTemplateId"
                            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">-- Choose Template --</option>
                        @foreach ($templates as $t)
                            <option value="{{ $t->id }}">
                                {{ $t->name }} ({{ $t->fieldMappings->count() }} fields)
                            </option>
                        @endforeach
                    </select>
                    @if ($templates->isEmpty())
                        <p class="text-xs text-amber-600 mt-1">No active templates. Upload and configure one in the Letters tab first.</p>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Personnel</label>
                    <div class="border rounded-md divide-y max-h-64 overflow-y-auto">
                        @foreach ($shortlistedPersonnel as $e)
                            <label class="flex items-center px-4 py-3 hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" wire:model="selectedPersonnel" value="{{ $e->id }}"
                                       class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                <span class="ml-3 text-sm">
                                    <span class="font-medium">{{ $e->user->name }}</span>
                                    <span class="text-gray-500 ml-2">({{ $e->nss_number }})</span>
                                    <span class="text-gray-400 text-xs ml-2">{{ $e->department?->name ?? 'No dept' }}</span>
                                </span>
                            </label>
                        @endforeach
                    </div>
                    @if ($shortlistedPersonnel->isNotEmpty())
                        <button type="button" wire:click="$set('selectedPersonnel', {{ $shortlistedPersonnel->pluck('id') }})"
                                class="text-xs text-indigo-600 hover:text-indigo-800 mt-1">
                            Select All
                        </button>
                    @endif
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Signature (optional)</label>
                        <input type="file" wire:model="signature" accept=".png,.jpg,.jpeg"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Stamp (optional)</label>
                        <input type="file" wire:model="stamp" accept=".png,.jpg,.jpeg"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium"
                            wire:loading.attr="disabled">
                        <span wire:loading.remove>Generate Endorsed Letters</span>
                        <span wire:loading>Generating...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Endorsed Letters History --}}
    @if ($endorsedLetters->isNotEmpty())
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b">
                <h3 class="font-semibold">Endorsed Letters History</h3>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Personnel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NSS #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Download</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($endorsedLetters as $letter)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                {{ $letter->enrollment->user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $letter->enrollment->nss_number }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $letter->created_at->format('d M Y, h:i A') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ Storage::url($letter->generated_file_path) }}" target="_blank"
                                   class="text-indigo-600 hover:text-indigo-800 font-medium">
                                    Download PDF
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
