<div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-3xl font-bold text-indigo-600">{{ $totalPersonnel }}</div>
            <div class="text-sm text-gray-500 mt-1">Total Personnel</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-3xl font-bold text-green-600">{{ $activePersonnel }}</div>
            <div class="text-sm text-gray-500 mt-1">Active Personnel</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-3xl font-bold text-yellow-600">{{ $pendingReview }}</div>
            <div class="text-sm text-gray-500 mt-1">Pending Review</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-3xl font-bold text-blue-600">{{ $totalDepartments }}</div>
            <div class="text-sm text-gray-500 mt-1">Departments</div>
        </div>
    </div>
</div>
