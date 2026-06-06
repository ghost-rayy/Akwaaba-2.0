<div>
    @if (session('message'))
        <div class="mb-4 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl font-semibold text-sm">
            {{ session('message') }}
        </div>
    @endif

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Companies</h2>
            <p class="text-sm text-gray-500 mt-1">Manage all registered companies.</p>
        </div>
        <button wire:click="create"
                class="bg-gradient-to-r from-stormy-600 to-stormy-700 hover:from-stormy-700 hover:to-stormy-800 text-white font-semibold py-2 px-4 rounded-xl text-sm shadow-sm transition-all">
            + New Company
        </button>
    </div>

    <div class="mb-4">
        <input type="text" wire:model.live="search" placeholder="Search companies..."
               class="w-full max-w-xs rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Personnel</th>
                    <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Depts</th>
                    <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Active</th>
                    <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($companies as $company)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $company->name }}</div>
                            <div class="text-xs text-gray-400">{{ $company->registration_number ? 'Reg: ' . $company->registration_number : '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div>{{ $company->email ?? '-' }}</div>
                            <div class="text-gray-500">{{ $company->phone ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-semibold">{{ $company->enrollments_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-center">{{ $company->departments_count }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @if ($company->is_active)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700">Active</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <button wire:click="edit({{ $company->id }})"
                                    class="text-stormy-600 hover:text-stormy-800 text-sm font-semibold">Edit</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">No companies found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $companies->links() }}</div>

    {{-- Create/Edit Modal --}}
    @if ($editingId !== null || old('name'))
        <div class="fixed inset-0 bg-gray-500/75 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl shadow-xl p-6 w-full max-w-lg mx-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">{{ $editingId ? 'Edit Company' : 'New Company' }}</h3>
                    <button wire:click="$set('editingId', null)" class="p-1 hover:bg-gray-100 rounded-lg">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                            <input type="text" wire:model="name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                            @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" wire:model="email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                            @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                            <input type="text" wire:model="phone" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                            <input type="text" wire:model="location" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                            <input type="text" wire:model="registration_number" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                            @error('registration_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                            <input type="text" wire:model="contact_person" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" wire:model="is_active" id="is_active" class="rounded border-gray-300 text-stormy-600 focus:ring-stormy-500">
                            <label for="is_active" class="text-sm font-medium text-gray-700">Active</label>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-2">
                        <button type="button" wire:click="$set('editingId', null)" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                        <button type="submit" class="px-4 py-2 text-sm text-white bg-stormy-600 rounded-lg hover:bg-stormy-700">Save</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
