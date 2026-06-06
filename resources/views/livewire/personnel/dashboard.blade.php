<div>
    {{-- Success Message --}}
    @if ($successMessage)
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ $successMessage }}
        </div>
    @endif

    {{-- STEP 0: Change Password --}}
    @if ($step === 0)
        <div class="max-w-lg mx-auto">
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-xl font-semibold mb-1">Change Your Password</h2>
                <p class="text-gray-500 text-sm mb-6">You must change your password before continuing.</p>

                <form wire:submit="changePassword" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Current Password</label>
                        <input type="password" wire:model="current_password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">New Password</label>
                        <input type="password" wire:model="new_password"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('new_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                        <input type="password" wire:model="new_password_confirmation"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <button type="submit"
                            class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Change Password
                    </button>
                </form>
            </div>
        </div>

    {{-- STEP 1: Personal Info --}}
    @elseif ($step === 1)
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold">Step 1: Personal Information</h2>
                    <p class="text-gray-500 text-sm">Please provide your personal details.</p>
                    {{-- Progress dots --}}
                    <div class="flex items-center gap-2 mt-4">
                        <span class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold">1</span>
                        <span class="h-0.5 w-8 bg-gray-300"></span>
                        <span class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center text-sm">2</span>
                        <span class="h-0.5 w-8 bg-gray-300"></span>
                        <span class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center text-sm">3</span>
                    </div>
                </div>

                <form wire:submit="saveStep1" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" wire:model="full_name"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NSS Number</label>
                            <input type="text" wire:model="nss_number" readonly
                                   class="mt-1 block w-full rounded-md bg-gray-50 border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Phone</label>
                            <input type="text" wire:model="phone"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" wire:model="email" readonly
                                   class="mt-1 block w-full rounded-md bg-gray-50 border-gray-300 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Place of Residence</label>
                            <input type="text" wire:model="place_of_residence"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('place_of_residence') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Region of Residence</label>
                            <select wire:model="region_of_residence"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Region</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region }}">{{ $region }}</option>
                                @endforeach
                            </select>
                            @error('region_of_residence') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                            Save & Continue
                        </button>
                    </div>
                </form>
            </div>
        </div>

    {{-- STEP 2: Education Info --}}
    @elseif ($step === 2)
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold">Step 2: Education Information</h2>
                    <p class="text-gray-500 text-sm">Provide your educational background.</p>
                    <div class="flex items-center gap-2 mt-4">
                        <span class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">&#10003;</span>
                        <span class="h-0.5 w-8 bg-indigo-600"></span>
                        <span class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold">2</span>
                        <span class="h-0.5 w-8 bg-gray-300"></span>
                        <span class="w-8 h-8 bg-gray-200 text-gray-500 rounded-full flex items-center justify-center text-sm">3</span>
                    </div>
                </div>

                <form wire:submit="saveStep2" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">University / Institution</label>
                            <input type="text" wire:model="university"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('university') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">City of School</label>
                            <input type="text" wire:model="city_of_school"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('city_of_school') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Region of School</label>
                            <select wire:model="region_of_school"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select Region</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region }}">{{ $region }}</option>
                                @endforeach
                            </select>
                            @error('region_of_school') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Form of Education</label>
                            <select wire:model="form_of_education"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="">Select</option>
                                @foreach ($educationForms as $form)
                                    <option value="{{ $form }}">{{ $form }}</option>
                                @endforeach
                            </select>
                            @error('form_of_education') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Programme of Study</label>
                            <input type="text" wire:model="programme_of_study"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('programme_of_study') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                            Save & Continue
                        </button>
                    </div>
                </form>
            </div>
        </div>

    {{-- STEP 3: Document Upload --}}
    @elseif ($step === 3)
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="mb-6">
                    <h2 class="text-xl font-semibold">Step 3: Upload Documents</h2>
                    <p class="text-gray-500 text-sm">Upload your posting letter and optional passport photo.</p>
                    <div class="flex items-center gap-2 mt-4">
                        <span class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">&#10003;</span>
                        <span class="h-0.5 w-8 bg-green-500"></span>
                        <span class="w-8 h-8 bg-green-500 text-white rounded-full flex items-center justify-center text-sm font-bold">&#10003;</span>
                        <span class="h-0.5 w-8 bg-indigo-600"></span>
                        <span class="w-8 h-8 bg-indigo-600 text-white rounded-full flex items-center justify-center text-sm font-bold">3</span>
                    </div>
                </div>

                <form wire:submit="saveStep3" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Posting Letter (PDF)</label>
                        <input type="file" wire:model="posting_letter" accept=".pdf"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('posting_letter') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="posting_letter" class="text-indigo-600 text-sm mt-1">Uploading...</div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Passport Photo (Optional)</label>
                        <input type="file" wire:model="passport_photo" accept=".jpg,.jpeg,.png"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('passport_photo') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="passport_photo" class="text-indigo-600 text-sm mt-1">Uploading...</div>
                    </div>
                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                            Submit Documents
                        </button>
                    </div>
                </form>
            </div>
            <p class="text-xs text-gray-400 mt-2">Max file sizes: 5MB (PDF), 2MB (photo).</p>
        </div>

    {{-- STEP 4+: Dashboard --}}
    @else
        <div>
            <h2 class="text-2xl font-semibold mb-6">Personnel Dashboard</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500">Status</p>
                    <p class="text-2xl font-bold capitalize">{{ $enrollmentStatus ?? 'N/A' }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500">Company</p>
                    <p class="text-lg font-semibold">{{ $companyName ?? 'N/A' }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500">Department</p>
                    <p class="text-lg font-semibold">{{ $departmentName ?? 'Not Assigned' }}</p>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <p class="text-sm text-gray-500">Documents</p>
                    <p class="text-2xl font-bold">{{ $documentCount ?? 0 }}</p>
                </div>
            </div>

            @if ($enrollmentStatus === 'pending_review')
                <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded mb-6">
                    Your profile is under review by the HR department. You will be notified once it has been reviewed.
                </div>
            @elseif ($enrollmentStatus === 'shortlisted')
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">
                    Congratulations! You have been shortlisted. Your company admin will endorse your letter shortly.
                </div>
            @elseif ($enrollmentStatus === 'endorsed')
                <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded mb-6">
                    Your posting letter has been endorsed. You can download it below.
                </div>
            @elseif ($enrollmentStatus === 'active')
                <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded mb-6">
                    Your posting is active. You are fully enrolled at your company.
                </div>
            @endif
        </div>
    @endif
</div>
