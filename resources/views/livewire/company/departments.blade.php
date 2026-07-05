<div>
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-medium text-gray-900">Departments</h3>
        <button wire:click="create" class="bg-stormy-600 text-white px-4 py-2 rounded-md hover:bg-stormy-700 text-sm">
            + Add Department
        </button>
    </div>

    @if($editing)
        <div class="bg-white rounded-lg shadow p-6 mb-6">
            <h4 class="font-medium text-gray-900 mb-4">{{ $departmentId ? 'Edit' : 'Add' }} Department</h4>
            <form wire:submit="save" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Department Name</label>
                    <input wire:model="name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Department Head</label>
                    <select wire:model="head_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                        <option value="">-- Select Head --</option>
                        @foreach($companyUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst(str_replace('_', ' ', $user->role)) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Supervisor <span class="text-gray-400">(optional)</span></label>
                    <select wire:model="supervisor_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                        <option value="">-- Select Supervisor --</option>
                        @foreach($companyUsers as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst(str_replace('_', ' ', $user->role)) }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex space-x-3">
                    <x-loading-button target="save" loading="{{ $departmentId ? 'Updating...' : 'Saving...' }}"
                            class="bg-stormy-600 text-white px-4 py-2 rounded-md hover:bg-stormy-700 text-sm">
                        {{ $departmentId ? 'Update' : 'Save' }}
                    </x-loading-button>
                    <button type="button" wire:click="cancel" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @endif

    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($departments->isEmpty())
            <div class="p-6 text-gray-500 text-center">No departments yet. Click "Add Department" to create one.</div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Head</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supervisor</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($departments as $dept)
                        <tr>
                            <td class="px-6 py-4">{{ $dept->name }}</td>
                            <td class="px-6 py-4">{{ $dept->head?->name ?? '—' }}</td>
                            <td class="px-6 py-4">{{ $dept->supervisor?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button wire:click="edit({{ $dept->id }})" class="text-stormy-600 hover:text-stormy-900 text-sm">Edit</button>
                                <button wire:click="confirmDelete({{ $dept->id }})" class="text-red-600 hover:text-red-900 text-sm">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>

    @if($showDeleteModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center">
            <div class="bg-white rounded-lg p-6 w-96">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Confirm Delete</h4>
                <p class="text-gray-500 mb-6">Are you sure you want to delete this department? This action cannot be undone.</p>
                <div class="flex justify-end space-x-3">
                    <button wire:click="$set('showDeleteModal', false)" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm">Cancel</button>
                    <x-loading-button type="button" target="delete" loading="Deleting..." wire:click="delete"
                            class="bg-red-600 text-white px-4 py-2 rounded-md text-sm">
                        Delete
                    </x-loading-button>
                </div>
            </div>
        </div>
    @endif
</div>
