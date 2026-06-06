<div>
    {{-- Global Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-8 gap-3 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $totalCompanies }}</p>
            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-1">Companies</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $totalPersonnel }}</p>
            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-1">Enrollments</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-emerald-700">{{ $activePersonnel }}</p>
            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-1">Active</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-amber-700">{{ $pendingReview }}</p>
            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-1">Pending Review</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-sky-700">{{ $shortlisted }}</p>
            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-1">Shortlisted</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-stormy-700">{{ $endorsed }}</p>
            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-1">Endorsed</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-indigo-700">{{ $todayPresent }}<span class="text-sm font-normal text-gray-400">/{{ $todayAttendance }}</span></p>
            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-1">Present Today</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <p class="text-2xl font-bold text-gray-900">{{ $totalDepartments }}</p>
            <p class="text-[10px] font-semibold text-gray-500 uppercase tracking-wider mt-1">Departments</p>
        </div>
    </div>

    {{-- Company Stats Table --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-8">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="text-sm font-bold text-gray-900">Companies Overview</h3>
            <a href="{{ route('admin.companies') }}" wire:navigate class="text-xs font-semibold text-stormy-600 hover:text-stormy-700">Manage</a>
        </div>
        @if ($companyStats->isEmpty())
            <div class="text-center py-10 text-sm text-gray-400">No companies registered.</div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-50">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Company</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Enrollments</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Active</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Depts</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Registered</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach ($companyStats as $c)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-3.5 whitespace-nowrap text-sm font-medium text-gray-900">{{ $c->name }}</td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-sm text-center text-gray-700">{{ $c->enrollments_count }}</td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-sm text-center font-semibold text-emerald-700">{{ $c->active_enrollments_count }}</td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-sm text-center text-gray-700">{{ $c->departments_count }}</td>
                                <td class="px-6 py-3.5 whitespace-nowrap text-sm text-right text-gray-500">{{ $c->created_at->format('d M Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Recent Companies --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-sm font-bold text-gray-900">Recently Registered Companies</h3>
        </div>
        @if ($recentCompanies->isEmpty())
            <div class="text-center py-6 text-sm text-gray-400">No companies registered yet.</div>
        @else
            <div class="divide-y divide-gray-50">
                @foreach ($recentCompanies as $company)
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-stormy-100 rounded-lg flex items-center justify-center">
                                <span class="text-xs font-bold text-stormy-600">{{ substr($company->name, 0, 1) }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $company->name }}</p>
                                <p class="text-xs text-gray-400">{{ $company->email ?? 'No email' }}</p>
                            </div>
                        </div>
                        <span class="text-xs text-gray-500">{{ $company->created_at->diffForHumans() }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
