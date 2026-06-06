<div>
    <div class="mb-6">
        <h2 class="text-xl font-semibold text-gray-900">All Personnel</h2>
        <p class="text-sm text-gray-500 mt-1">View every NSS personnel across all companies.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
            <div>
                <input type="text" wire:model.live="search" placeholder="Search name, NSS#, email..."
                       class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
            </div>
            <div>
                <select wire:model.live="filterCompany"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                    <option value="">All Companies</option>
                    @foreach ($companies as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <select wire:model.live="filterStatus"
                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                    <option value="">All Statuses</option>
                    @foreach ($statuses as $s)
                        <option value="{{ $s }}">{{ str_replace('_', ' ', ucfirst($s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="text-sm text-gray-500 flex items-center">
                {{ $enrollments->total() }} total
            </div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">NSS #</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Company</th>
                    <th class="px-6 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase">Department</th>
                    <th class="px-6 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase">Enrolled</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($enrollments as $e)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $e->user->name }}</div>
                            <div class="text-xs text-gray-400">{{ $e->user->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-600">{{ $e->nss_number }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $e->company?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $e->department?->name ?? '-' }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-center">
                            @php
                                $badge = match($e->status) {
                                    'pending_forms' => 'bg-yellow-50 text-yellow-700',
                                    'pending_review' => 'bg-blue-50 text-blue-700',
                                    'shortlisted' => 'bg-green-50 text-green-700',
                                    'rejected' => 'bg-red-50 text-red-700',
                                    'endorsed' => 'bg-stormy-50 text-stormy-700',
                                    'validated' => 'bg-emerald-50 text-emerald-700',
                                    'active' => 'bg-teal-50 text-teal-700',
                                    'completed' => 'bg-gray-100 text-gray-700',
                                    default => 'bg-gray-50 text-gray-600',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge }}">
                                {{ str_replace('_', ' ', ucfirst($e->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ $e->created_at->format('d M Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500 text-sm">No personnel found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $enrollments->links() }}</div>
</div>
