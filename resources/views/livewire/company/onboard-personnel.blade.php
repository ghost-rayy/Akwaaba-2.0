<div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-stormy-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-stormy-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Onboard New Personnel</h3>
                    <p class="text-sm text-gray-500">Create an account and send login credentials via email</p>
                </div>
            </div>

            <form wire:submit="onboard" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NSS Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0"/></svg>
                        </div>
                        <input wire:model="nss_number" type="text" placeholder="e.g. NSS2026001"
                               class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm">
                    </div>
                    @error('nss_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                        </div>
                        <input wire:model="email" type="email" placeholder="personnel@company.com"
                               class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm">
                    </div>
                    @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        </div>
                        <input wire:model="phone" type="text" placeholder="0244000000"
                               class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm">
                    </div>
                    @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">NSS Year</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <select wire:model="nss_year"
                                class="w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm bg-white">
                            <option value="">Select Year</option>
                            @foreach ($years as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('nss_year') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <x-loading-button
                    target="onboard"
                    loading="Onboarding personnel and sending email..."
                    class="w-full bg-gradient-to-r from-stormy-600 to-stormy-700 text-white py-2.5 rounded-lg hover:from-stormy-700 hover:to-stormy-700 font-medium text-sm transition-all shadow-sm">
                    Onboard Personnel
                </x-loading-button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Recent Onboardings</h3>
                    <p class="text-sm text-gray-500">Latest personnel added to the system</p>
                </div>
            </div>

            @if($recentOnboardings->isEmpty())
                <div class="text-center py-12">
                    <svg class="w-12 h-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <p class="text-gray-400 text-sm">No personnel onboarded yet.</p>
                </div>
            @else
                <div class="overflow-hidden">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100">
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase pb-3">NSS #</th>
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase pb-3">Email</th>
                                <th class="text-left text-xs font-semibold text-gray-500 uppercase pb-3">Status</th>
                                <th class="text-right text-xs font-semibold text-gray-500 uppercase pb-3">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($recentOnboardings as $enrollment)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-3 pr-4 text-sm font-medium text-gray-900">{{ $enrollment->nss_number }}</td>
                                    <td class="py-3 pr-4 text-sm text-gray-600">{{ $enrollment->user->email }}</td>
                                    <td class="py-3 pr-4">
                                        @php
                                            $badge = match($enrollment->status) {
                                                'pending_forms' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                                'pending_review' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                                'active' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
                                                default => 'bg-gray-50 text-gray-700 ring-gray-600/20',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium ring-1 ring-inset {{ $badge }}">
                                            {{ str_replace('_', ' ', ucfirst($enrollment->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-right text-sm text-gray-500">{{ $enrollment->created_at->format('d M Y') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
