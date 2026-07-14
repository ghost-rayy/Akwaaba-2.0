<div class="space-y-6">
    {{-- Header --}}
    <div class="border-b border-gray-100 pb-4">
        <h2 class="text-xl font-bold text-gray-900">Document Management System</h2>
        <p class="text-sm text-gray-500 mt-0.5">Browse personnel documents organised by NSS year and department.</p>
    </div>

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-1.5 text-sm">
        @if ($level === 'years')
            <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 font-medium text-stormy-800 bg-stormy-50">
        @else
            <button wire:click="backToYears" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 font-medium text-stormy-600 hover:bg-stormy-50 transition-colors">
        @endif
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
            All Years
        @if ($level === 'years')
            </span>
        @else
            </button>
        @endif
        @if ($selectedYear)
            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <button wire:click="backToDepartments" class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 font-medium text-stormy-600 hover:bg-stormy-50 transition-colors {{ $level === 'departments' ? 'bg-stormy-50 text-stormy-800' : '' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                NSS {{ $selectedYear }}
            </button>
        @endif
        @if ($selectedDepartment)
            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1.5 font-medium text-gray-500">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                {{ $departmentName }}
            </span>
        @endif
    </nav>

    {{-- Level 1: NSS Years --}}
    @if ($level === 'years')
        @if ($years->isEmpty())
            <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center shadow-sm">
                <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <p class="text-gray-500 text-sm">No documents found for your company yet.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach ($years as $yearData)
                    <button wire:click="selectYear('{{ $yearData->nss_year }}')" class="group relative flex flex-col items-center gap-2 rounded-2xl border-2 border-transparent bg-white p-5 shadow-sm transition-all hover:border-stormy-200 hover:shadow-md hover:-translate-y-0.5">
                        <svg class="w-16 h-16 text-amber-400 drop-shadow-sm" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 7C3 5.89543 3.89543 5 5 5H9L11 7H19C20.1046 7 21 7.89543 21 9V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V7Z" fill="#FBBF24"/>
                            <path d="M3 8C3 6.89543 3.89543 6 5 6H9L11 8H19C20.1046 8 21 8.89543 21 10V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V8Z" fill="#FCD34D"/>
                            <rect x="3" y="8" width="18" height="1.5" rx="0.5" fill="#FDE68A"/>
                        </svg>
                        <span class="text-sm font-bold text-gray-800">NSS {{ $yearData->nss_year }}</span>
                        <span class="text-xs font-medium text-gray-400">{{ $yearData->total }} document{{ $yearData->total !== 1 ? 's' : '' }}</span>
                    </button>
                @endforeach
            </div>
        @endif
    @endif

    {{-- Level 2: Departments --}}
    @if ($level === 'departments')
        @php
            $hasDepartments = $departments->isNotEmpty() || $unassignedCount > 0;
        @endphp
        @if (!$hasDepartments)
            <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center shadow-sm">
                <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <p class="text-gray-500 text-sm">No departments found for NSS {{ $selectedYear }}.</p>
            </div>
        @else
            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
                @foreach ($departments as $dept)
                    @php $count = $departmentCounts[$dept->id] ?? 0; @endphp
                    <button wire:click="selectDepartment({{ $dept->id }})" class="group relative flex flex-col items-center gap-2 rounded-2xl border-2 border-transparent bg-white p-5 shadow-sm transition-all hover:border-stormy-200 hover:shadow-md hover:-translate-y-0.5">
                        <svg class="w-16 h-16 text-amber-400 drop-shadow-sm" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 7C3 5.89543 3.89543 5 5 5H9L11 7H19C20.1046 7 21 7.89543 21 9V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V7Z" fill="#FBBF24"/>
                            <path d="M3 8C3 6.89543 3.89543 6 5 6H9L11 8H19C20.1046 8 21 8.89543 21 10V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V8Z" fill="#FCD34D"/>
                            <rect x="3" y="8" width="18" height="1.5" rx="0.5" fill="#FDE68A"/>
                        </svg>
                        <span class="text-sm font-bold text-gray-800 text-center">{{ $dept->name }}</span>
                        <span class="text-xs font-medium text-gray-400">{{ $count }} document{{ $count !== 1 ? 's' : '' }}</span>
                    </button>
                @endforeach
                @if ($unassignedCount > 0)
                    <button wire:click="selectDepartment(0)" class="group relative flex flex-col items-center gap-2 rounded-2xl border-2 border-dashed border-gray-200 bg-white p-5 shadow-sm transition-all hover:border-gray-300 hover:shadow-md hover:-translate-y-0.5">
                        <svg class="w-16 h-16 text-gray-300" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M3 7C3 5.89543 3.89543 5 5 5H9L11 7H19C20.1046 7 21 7.89543 21 9V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V7Z" fill="#D1D5DB"/>
                            <path d="M3 8C3 6.89543 3.89543 6 5 6H9L11 8H19C20.1046 8 21 8.89543 21 10V18C21 19.1046 20.1046 20 19 20H5C3.89543 20 3 19.1046 3 18V8Z" fill="#E5E7EB"/>
                            <rect x="3" y="8" width="18" height="1.5" rx="0.5" fill="#F3F4F6"/>
                        </svg>
                        <span class="text-sm font-bold text-gray-500 text-center">Unassigned</span>
                        <span class="text-xs font-medium text-gray-400">{{ $unassignedCount }} document{{ $unassignedCount !== 1 ? 's' : '' }}</span>
                    </button>
                @endif
            </div>
        @endif
    @endif

    {{-- Level 3: Documents --}}
    @if ($level === 'documents')
        {{-- Search & Filter --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <div class="relative flex-1">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search by file name, type, or personnel name..." class="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-10 pr-4 text-sm text-gray-900 placeholder-gray-400 focus:border-stormy-300 focus:outline-none focus:ring-2 focus:ring-stormy-100">
            </div>
            <select wire:model.live="filterType" class="rounded-xl border border-gray-200 bg-white py-2.5 px-3 text-sm text-gray-700 focus:border-stormy-300 focus:outline-none focus:ring-2 focus:ring-stormy-100">
                <option value="">All Types</option>
                @foreach ($documentTypes as $type)
                    <option value="{{ $type }}">{{ ucwords(str_replace('_', ' ', $type)) }}</option>
                @endforeach
            </select>
        </div>

        {{-- Documents List --}}
        @if ($documents->isEmpty())
            <div class="bg-white border border-gray-100 rounded-2xl p-12 text-center shadow-sm">
                <svg class="w-16 h-16 mx-auto text-gray-200 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <p class="text-gray-500 text-sm">No documents match your criteria.</p>
                @if ($search || $filterType)
                    <button wire:click="clearFilters" class="mt-3 text-sm font-semibold text-stormy-600 hover:text-stormy-800">Clear filters</button>
                @endif
            </div>
        @else
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">File</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Personnel</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Type</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Uploaded</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Size</th>
                                <th scope="col" class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wider text-gray-500">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach ($documents as $doc)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="shrink-0 w-9 h-9 flex items-center justify-center rounded-lg bg-stormy-50 text-stormy-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-medium text-gray-900 truncate max-w-[200px]">{{ $doc->original_name ?? $doc->type }}</p>
                                                <p class="text-xs text-gray-400">{{ $doc->mime_type ?? 'Unknown type' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-stormy-100 flex items-center justify-center text-[10px] font-bold text-stormy-700 shrink-0">
                                                {{ strtoupper(substr($doc->user->name, 0, 1)) }}
                                            </div>
                                            <span class="text-sm text-gray-700 font-medium">{{ $doc->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-stormy-100 text-stormy-800">
                                            {{ ucwords(str_replace('_', ' ', $doc->type)) }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $doc->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-sm text-gray-500">
                                        @if ($doc->size)
                                            @php
                                                $size = $doc->size;
                                                if ($size >= 1048576) {
                                                    $formatted = round($size / 1048576, 1) . ' MB';
                                                } elseif ($size >= 1024) {
                                                    $formatted = round($size / 1024, 0) . ' KB';
                                                } else {
                                                    $formatted = $size . ' B';
                                                }
                                            @endphp
                                            {{ $formatted }}
                                        @else
                                            &mdash;
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('document.stream', $doc->id) }}" target="_blank" class="inline-flex items-center gap-1.5 rounded-lg bg-stormy-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition-all hover:bg-stormy-700">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 text-xs text-gray-400">
                    Showing {{ $documents->count() }} document{{ $documents->count() !== 1 ? 's' : '' }}
                </div>
            </div>
        @endif
    @endif
</div>
