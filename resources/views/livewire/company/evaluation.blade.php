<div>
    @if (session('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Evaluation Form --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">New Evaluation</h2>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Personnel</label>
                    <select wire:model="selectedPersonnelId"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                        <option value="">Select Personnel</option>
                        @foreach ($personnelList as $e)
                            <option value="{{ $e->id }}">{{ $e->user->name }} ({{ $e->nss_number }})</option>
                        @endforeach
                    </select>
                    @error('selectedPersonnelId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Period Start</label>
                        <input type="date" wire:model="periodStart"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                        @error('periodStart') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Period End</label>
                        <input type="date" wire:model="periodEnd"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                        @error('periodEnd') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Punctuality (1-5)</label>
                        <select wire:model="punctualityScore"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} - {{ match($i) { 1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Very Good', 5 => 'Excellent' } }}</option>
                            @endfor
                        </select>
                        @error('punctualityScore') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Performance (1-5)</label>
                        <select wire:model="performanceScore"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} - {{ match($i) { 1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Very Good', 5 => 'Excellent' } }}</option>
                            @endfor
                        </select>
                        @error('performanceScore') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Attitude (1-5)</label>
                        <select wire:model="attitudeScore"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} - {{ match($i) { 1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Very Good', 5 => 'Excellent' } }}</option>
                            @endfor
                        </select>
                        @error('attitudeScore') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Teamwork (1-5)</label>
                        <select wire:model="teamworkScore"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                            @for ($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}">{{ $i }} - {{ match($i) { 1 => 'Poor', 2 => 'Fair', 3 => 'Good', 4 => 'Very Good', 5 => 'Excellent' } }}</option>
                            @endfor
                        </select>
                        @error('teamworkScore') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Comments</label>
                    <textarea wire:model="comments" rows="3"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500"
                              placeholder="Optional comments..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Recommendation</label>
                    <textarea wire:model="recommendation" rows="2"
                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500"
                              placeholder="e.g., Continue, Needs improvement, etc."></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="bg-stormy-600 text-white px-6 py-2 rounded-md hover:bg-stormy-700 text-sm font-medium">
                        Save Evaluation
                    </button>
                </div>
            </form>
        </div>

        {{-- Recent Evaluations --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Recent Evaluations</h2>
            @if ($recentEvaluations->isEmpty())
                <p class="text-gray-500 text-sm">No evaluations yet.</p>
            @else
                <div class="space-y-3 max-h-[600px] overflow-y-auto">
                    @foreach ($recentEvaluations as $ev)
                        <div class="border rounded-lg p-3 text-sm">
                            <div class="flex items-center justify-between mb-1">
                                <span class="font-medium">{{ $ev->user->name }}</span>
                                <span class="text-xs text-gray-500">{{ $ev->created_at->format('d M Y') }}</span>
                            </div>
                            <div class="text-xs text-gray-500 mb-2">
                                Evaluated by {{ $ev->evaluator->name }}
                                &middot; {{ $ev->period_start->format('d M') }} - {{ $ev->period_end->format('d M') }}
                            </div>
                            <div class="grid grid-cols-4 gap-2 text-center mb-2">
                                <div>
                                    <div class="text-lg font-bold {{ $ev->punctuality_score >= 4 ? 'text-green-600' : ($ev->punctuality_score >= 3 ? 'text-yellow-600' : 'text-red-600') }}">{{ $ev->punctuality_score }}</div>
                                    <div class="text-xs text-gray-500">Punctual</div>
                                </div>
                                <div>
                                    <div class="text-lg font-bold {{ $ev->performance_score >= 4 ? 'text-green-600' : ($ev->performance_score >= 3 ? 'text-yellow-600' : 'text-red-600') }}">{{ $ev->performance_score }}</div>
                                    <div class="text-xs text-gray-500">Perf.</div>
                                </div>
                                <div>
                                    <div class="text-lg font-bold {{ $ev->attitude_score >= 4 ? 'text-green-600' : ($ev->attitude_score >= 3 ? 'text-yellow-600' : 'text-red-600') }}">{{ $ev->attitude_score }}</div>
                                    <div class="text-xs text-gray-500">Attitude</div>
                                </div>
                                <div>
                                    <div class="text-lg font-bold {{ $ev->teamwork_score >= 4 ? 'text-green-600' : ($ev->teamwork_score >= 3 ? 'text-yellow-600' : 'text-red-600') }}">{{ $ev->teamwork_score }}</div>
                                    <div class="text-xs text-gray-500">Team</div>
                                </div>
                            </div>
                            @if ($ev->comments)
                                <p class="text-xs text-gray-600 italic">"{{ $ev->comments }}"</p>
                            @endif
                            @if ($ev->overall_score)
                                <div class="mt-1 text-xs">
                                    <strong>Overall:</strong> {{ $ev->overall_score }}/5
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
