<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $header ?? '' }}
        </h2>
    </x-slot>

    <div class="border-b border-gray-200 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex space-x-8 overflow-x-auto" aria-label="Tabs">
                @php $user = auth()->user(); @endphp
                
                <a href="{{ route('company.dashboard') }}" 
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('company.dashboard') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Dashboard
                </a>
                <a href="{{ route('company.onboard') }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('company.onboard') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Onboard Personnel
                </a>
                <a href="{{ route('company.departments') }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('company.departments') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Departments
                </a>
                <a href="{{ route('company.shortlist') }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('company.shortlist') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Shortlist Personnel
                </a>
                @if($user->isCompanyAdmin())
                    <a href="{{ route('company.endorse') }}"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('company.endorse') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Endorse Letters
                    </a>
                @endif
                <a href="{{ route('company.personnel') }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('company.personnel') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Manage Personnel
                </a>
                <a href="{{ route('company.attendance') }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('company.attendance') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Attendance
                </a>
                <a href="{{ route('company.evaluations') }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('company.evaluations') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Evaluations
                </a>
                <a href="{{ route('company.letters') }}"
                   class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('company.letters') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Letters
                </a>
                @if($user->isCompanyAdmin())
                    <a href="{{ route('company.settings') }}"
                       class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ request()->routeIs('company.settings') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Settings
                    </a>
                @endif
            </nav>
        </div>
    </div>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </div>
</x-app-layout>
