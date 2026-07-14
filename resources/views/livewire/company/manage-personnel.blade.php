<div x-data="{ photoModal: null }">
    <div class="mb-6">
        <h2 class="text-xl font-semibold">Manage Personnel</h2>
        <p class="text-sm text-gray-500">View, filter, and assign departments to all personnel.</p>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <input type="text" wire:model.live="search" placeholder="Search name, NSS#, email..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
            </div>
            <div>
                <select wire:model.live="filterDepartment"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                    <option value="">All Departments</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="filterStatus"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status }}">{{ str_replace('_', ' ', ucfirst($status)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-sm text-gray-500 flex items-center">
                {{ $enrollments->total() }} total personnel
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">NSS #</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Department</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse ($enrollments as $e)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                @php $photoUrl = $e->user->profilePhotoUrl(); @endphp
                                @if ($photoUrl)
                                    <img src="{{ $photoUrl }}" alt="" @click="photoModal = '{{ $photoUrl }}'" class="w-9 h-9 rounded-full object-cover ring-2 ring-gray-100 cursor-pointer hover:ring-stormy-300 transition-all">
                                @else
                                    <div class="w-9 h-9 rounded-full bg-stormy-100 flex items-center justify-center text-xs font-bold text-stormy-700 ring-2 ring-gray-100">
                                        {{ strtoupper(substr($e->user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="font-medium">{{ $e->user->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $e->nss_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div>{{ $e->user->email }}</div>
                            <div class="text-gray-500">{{ $e->user->phone }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            @if ($e->department)
                                <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs">{{ $e->department->name }}</span>
                            @else
                                <span class="text-gray-400 italic">Not set</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $badge = match($e->status) {
                                    'pending_forms' => 'bg-yellow-100 text-yellow-800',
                                    'pending_review' => 'bg-blue-100 text-blue-800',
                                    'shortlisted' => 'bg-green-100 text-green-800',
                                    'rejected' => 'bg-red-100 text-red-800',
                                    'endorsed' => 'bg-stormy-100 text-stormy-800',
                                    'validated' => 'bg-emerald-100 text-emerald-800',
                                    'active' => 'bg-teal-100 text-teal-800',
                                    'completed' => 'bg-gray-100 text-gray-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="inline-block px-2 py-0.5 rounded text-xs font-medium {{ $badge }}">
                                {{ str_replace('_', ' ', ucfirst($e->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <button wire:click="startAssign({{ $e->id }})"
                                    class="text-stormy-600 hover:text-stormy-800 text-xs font-medium">
                                {{ $e->department ? 'Change Dept' : 'Assign Dept' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            No personnel found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $enrollments->links() }}
    </div>

    {{-- Assign Department Modal --}}
    @if ($assigningPersonnelId)
        <div class="fixed inset-0 bg-gray-500/75 flex items-center justify-center z-50">
            <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-sm mx-4">
                <h3 class="text-lg font-semibold mb-4">Assign Department</h3>
                <select wire:model="assignDepartmentId"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                    <option value="">Select Department</option>
                    @foreach ($departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                @error('assignDepartmentId') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                <div class="flex justify-end space-x-3 mt-4">
                    <button wire:click="$set('assigningPersonnelId', null)"
                            class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                        Cancel
                    </button>
                    <x-loading-button type="button" target="saveDepartment" loading="Saving..." wire:click="saveDepartment"
                            class="px-4 py-2 text-sm text-white bg-stormy-600 rounded-md hover:bg-stormy-700">
                        Save
                    </x-loading-button>
                </div>
            </div>
        </div>
    @endif

    {{-- Photo Modal --}}
    <template x-teleport="body">
        <div x-show="photoModal" x-cloak
             @click="photoModal = null"
             @keydown.escape.window="photoModal = null"
             class="fixed inset-0 z-[100] flex items-center justify-center bg-black/60 backdrop-blur-sm"
             x-transition:enter="transition duration-200 ease-out"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition duration-150 ease-in"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.stop
                 class="relative mx-4"
                 x-transition:enter="transition duration-200 ease-out"
                 x-transition:enter-start="scale-75 opacity-0"
                 x-transition:enter-end="scale-100 opacity-100"
                 x-transition:leave="transition duration-150 ease-in"
                 x-transition:leave-start="scale-100 opacity-100"
                 x-transition:leave-end="scale-75 opacity-0">
                <img :src="photoModal" alt=""
                     class="w-48 h-48 rounded-full object-cover ring-4 ring-white shadow-2xl">
                <button @click="photoModal = null"
                        class="absolute -top-3 -right-3 w-8 h-8 rounded-full bg-white shadow-md flex items-center justify-center text-gray-500 hover:text-gray-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </template>
</div>
