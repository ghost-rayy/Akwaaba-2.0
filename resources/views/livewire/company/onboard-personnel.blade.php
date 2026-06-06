<div>
    @if($successMessage)
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ $successMessage }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Onboard New NSS Personnel</h3>
        <form wire:submit="onboard" class="space-y-4">
            <div>
                <label for="nss_number" class="block text-sm font-medium text-gray-700">NSS Number</label>
                <input wire:model="nss_number" id="nss_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('nss_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input wire:model="email" id="email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                <input wire:model="phone" id="phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            <div>
                <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                    Onboard Personnel
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Onboardings</h3>
        @if($recentOnboardings->isEmpty())
            <p class="text-gray-500">No personnel onboarded yet.</p>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NSS #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($recentOnboardings as $enrollment)
                        <tr>
                            <td class="px-6 py-4">{{ $enrollment->nss_number }}</td>
                            <td class="px-6 py-4">{{ $enrollment->user->email }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full 
                                    {{ $enrollment->status === 'pending_forms' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $enrollment->status === 'active' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $enrollment->status === 'completed' ? 'bg-blue-100 text-blue-800' : '' }}">
                                    {{ str_replace('_', ' ', ucfirst($enrollment->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">{{ $enrollment->created_at->format('M d, Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
