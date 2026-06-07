<div>
    {{-- Success Message --}}
    @if ($successMessage)
        <div class="alert-dismiss mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl shadow-sm animate-fade-in">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-emerald-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-emerald-800">{{ $successMessage }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- Error message from middleware --}}
    @if (session('error'))
        <div class="alert-dismiss mb-6 bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl shadow-sm">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-rose-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-semibold text-rose-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    {{-- STEP 0: Change Password --}}
    @if ($step === 0)
        <div class="max-w-md mx-auto">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-stormy-700 to-stormy-800 px-6 py-5 text-white">
                    <h2 class="text-xl font-bold">Update Your Password</h2>
                    <p class="text-stormy-100 text-xs mt-1">To secure your new account, please change your default password.</p>
                </div>
                <div class="p-6">
                    <form wire:submit="changePassword" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Current Password</label>
                            <input type="password" wire:model="current_password"
                                   class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all"
                                   placeholder="••••••••">
                            @error('current_password') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">New Password</label>
                            <input type="password" wire:model="new_password"
                                   class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all"
                                   placeholder="••••••••">
                            @error('new_password') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Confirm New Password</label>
                            <input type="password" wire:model="new_password_confirmation"
                                   class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all"
                                   placeholder="••••••••">
                        </div>
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-stormy-600 to-stormy-700 hover:from-stormy-700 hover:to-stormy-800 text-white font-semibold py-3 px-4 rounded-xl shadow-md transition-all duration-200 mt-2">
                            Change & Setup Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>

    {{-- STEP 1: Personal Info --}}
    @elseif ($step === 1)
        <div class="max-w-2xl mx-auto">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-xl p-6 sm:p-8">
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Step 1: Personal Profile</h2>
                            <p class="text-gray-500 text-sm">Please verify and complete your personal details.</p>
                        </div>
                        <span class="text-xs font-bold text-stormy-600 bg-stormy-50 border border-stormy-100 px-3 py-1 rounded-full">Step 1 of 3</span>
                    </div>

                    {{-- Stepper UI --}}
                    <div class="relative flex items-center justify-between mt-6 max-w-sm">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="h-0.5 w-full bg-gray-100"></div>
                        </div>
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-stormy-600 text-white font-bold ring-4 ring-white shadow-md text-sm">1</div>
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-400 font-semibold ring-4 ring-white text-sm">2</div>
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-400 font-semibold ring-4 ring-white text-sm">3</div>
                    </div>
                </div>

                <form wire:submit="saveStep1" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Full Name</label>
                            <input type="text" wire:model="full_name"
                                   class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all">
                            @error('full_name') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">NSS Number (Locked)</label>
                            <input type="text" wire:model="nss_number" readonly
                                   class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-500 text-sm cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Phone Number</label>
                            <input type="text" wire:model="phone"
                                   class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all">
                            @error('phone') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-400 mb-1">Email (Locked)</label>
                            <input type="email" wire:model="email" readonly
                                   class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl bg-gray-50 text-gray-500 text-sm cursor-not-allowed">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Place of Residence</label>
                            <input type="text" wire:model="place_of_residence"
                                   class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all"
                                   placeholder="Street, City">
                            @error('place_of_residence') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Region of Residence</label>
                            <select wire:model="region_of_residence"
                                    class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all">
                                <option value="">Select Region</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region }}">{{ $region }}</option>
                                @endforeach
                            </select>
                            @error('region_of_residence') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">NSS Year</label>
                            <select wire:model="nss_year"
                                    class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all bg-white">
                                <option value="">Select Year</option>
                                @foreach ($years as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                            @error('nss_year') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                                class="bg-gradient-to-r from-stormy-600 to-stormy-700 hover:from-stormy-700 hover:to-stormy-800 text-white font-semibold py-2.5 px-6 rounded-xl shadow-md transition-all">
                            Save & Continue &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

    {{-- STEP 2: Education Info --}}
    @elseif ($step === 2)
        <div class="max-w-2xl mx-auto">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-xl p-6 sm:p-8">
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Step 2: Educational Background</h2>
                            <p class="text-gray-500 text-sm">Provide details about your academic institution.</p>
                        </div>
                        <span class="text-xs font-bold text-stormy-600 bg-stormy-50 border border-stormy-100 px-3 py-1 rounded-full">Step 2 of 3</span>
                    </div>

                    {{-- Stepper UI --}}
                    <div class="relative flex items-center justify-between mt-6 max-w-sm">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="h-0.5 w-full bg-gradient-to-r from-emerald-500 to-gray-100"></div>
                        </div>
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 text-white font-bold ring-4 ring-white shadow-md text-sm">&#10003;</div>
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-stormy-600 text-white font-bold ring-4 ring-white shadow-md text-sm">2</div>
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 text-gray-400 font-semibold ring-4 ring-white text-sm">3</div>
                    </div>
                </div>

                <form wire:submit="saveStep2" class="space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">University / Institution</label>
                            <input type="text" wire:model="university"
                                   class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all"
                                   placeholder="e.g. University of Ghana">
                            @error('university') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">City of School</label>
                            <input type="text" wire:model="city_of_school"
                                   class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all"
                                   placeholder="e.g. Legon">
                            @error('city_of_school') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Region of School</label>
                            <select wire:model="region_of_school"
                                    class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all">
                                <option value="">Select Region</option>
                                @foreach ($regions as $region)
                                    <option value="{{ $region }}">{{ $region }}</option>
                                @endforeach
                            </select>
                            @error('region_of_school') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Form of Education</label>
                            <select wire:model="form_of_education"
                                    class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all">
                                <option value="">Select Qualification</option>
                                @foreach ($educationForms as $form)
                                    <option value="{{ $form }}">{{ $form }}</option>
                                @endforeach
                            </select>
                            @error('form_of_education') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-gray-500 mb-1">Programme of Study</label>
                            <input type="text" wire:model="programme_of_study"
                                   class="block w-full px-4 py-2.5 border border-gray-200 rounded-xl focus:ring-2 focus:ring-stormy-500 focus:border-stormy-500 text-sm transition-all"
                                   placeholder="e.g. BSc. Computer Science">
                            @error('programme_of_study') <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end pt-4">
                        <button type="submit"
                                class="bg-gradient-to-r from-stormy-600 to-stormy-700 hover:from-stormy-700 hover:to-stormy-800 text-white font-semibold py-2.5 px-6 rounded-xl shadow-md transition-all">
                            Save & Continue &rarr;
                        </button>
                    </div>
                </form>
            </div>
        </div>

    {{-- STEP 3: Document Upload --}}
    @elseif ($step === 3)
        <div class="max-w-2xl mx-auto">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-xl p-6 sm:p-8">
                <div class="mb-8">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">Step 3: Document Upload</h2>
                            <p class="text-gray-500 text-sm">Please upload your official postings to activate your portal.</p>
                        </div>
                        <span class="text-xs font-bold text-stormy-600 bg-stormy-50 border border-stormy-100 px-3 py-1 rounded-full">Step 3 of 3</span>
                    </div>

                    {{-- Stepper UI --}}
                    <div class="relative flex items-center justify-between mt-6 max-w-sm">
                        <div class="absolute inset-0 flex items-center" aria-hidden="true">
                            <div class="h-0.5 w-full bg-emerald-500"></div>
                        </div>
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 text-white font-bold ring-4 ring-white shadow-md text-sm">&#10003;</div>
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-emerald-500 text-white font-bold ring-4 ring-white shadow-md text-sm">&#10003;</div>
                        <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-stormy-600 text-white font-bold ring-4 ring-white shadow-md text-sm">3</div>
                    </div>
                </div>

                <form wire:submit="saveStep3" class="space-y-6">
                    <div class="bg-gray-50 rounded-2xl p-5 border border-dashed border-gray-300">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Posting Letter (PDF only, max 5MB)</label>
                        <input type="file" wire:model="posting_letter" accept=".pdf"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-stormy-50 file:text-stormy-700 hover:file:bg-stormy-100 transition-colors">
                        @error('posting_letter') <p class="text-rose-500 text-xs mt-1.5 font-semibold">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="posting_letter" class="flex items-center gap-2 text-stormy-600 text-sm mt-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Uploading posting letter...
                        </div>
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-5 border border-dashed border-gray-300">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Passport Photo (Optional, JPG/PNG, max 2MB)</label>
                        <input type="file" wire:model="passport_photo" accept=".jpg,.jpeg,.png"
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-stormy-50 file:text-stormy-700 hover:file:bg-stormy-100 transition-colors">
                        @error('passport_photo') <p class="text-rose-500 text-xs mt-1.5 font-semibold">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="passport_photo" class="flex items-center gap-2 text-stormy-600 text-sm mt-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Uploading photo...
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button type="submit"
                                class="bg-gradient-to-r from-stormy-600 to-stormy-700 hover:from-stormy-700 hover:to-stormy-800 text-white font-semibold py-2.5 px-6 rounded-xl shadow-md transition-all">
                            Submit & Finalize
                        </button>
                    </div>
                </form>
            </div>
        </div>

    {{-- STEP 4+: Dashboard --}}
    @else
        <div class="space-y-6">
            {{-- Status Banner --}}
            @if ($enrollmentStatus === 'pending_review')
                <div class="alert-dismiss bg-amber-50 border-l-4 border-amber-500 text-amber-900 p-5 rounded-2xl shadow-sm flex items-start gap-4">
                    <div class="p-2 bg-amber-100 rounded-xl text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-amber-950">Registration Under Review</h4>
                        <p class="text-sm text-amber-800 mt-1">Your submitted profile and posting letter are being reviewed by the company's HR department. You'll receive full access once approved.</p>
                    </div>
                </div>
            @elseif ($enrollmentStatus === 'shortlisted')
                <div class="alert-dismiss bg-emerald-50 border-l-4 border-emerald-500 text-emerald-950 p-5 rounded-2xl shadow-sm flex items-start gap-4">
                    <div class="p-2 bg-emerald-100 rounded-xl text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-emerald-900">Congratulations! You've been Shortlisted</h4>
                        <p class="text-sm text-emerald-800 mt-1">Your application is approved. An administrator will endorse your official posting letter shortly. Keep an eye on your Documents tab!</p>
                    </div>
                </div>
            @elseif ($enrollmentStatus === 'rejected')
                <div class="alert-dismiss bg-rose-50 border-l-4 border-rose-500 text-rose-950 p-5 rounded-2xl shadow-sm flex items-start gap-4">
                    <div class="p-2 bg-rose-100 rounded-xl text-rose-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-rose-900">Onboarding Status: Rejected</h4>
                        @if ($rejectionReason)
                            <p class="text-sm text-rose-800 mt-1"><strong>Reason:</strong> {{ $rejectionReason }}</p>
                        @else
                            <p class="text-sm text-rose-800 mt-1">Please reach out to your HR administrator for clarification and next steps.</p>
                        @endif
                    </div>
                </div>
            @elseif ($enrollmentStatus === 'endorsed' || $enrollmentStatus === 'active')
                <div class="alert-dismiss bg-sky-50 border-l-4 border-sky-500 text-sky-950 p-5 rounded-2xl shadow-sm flex items-start gap-4">
                    <div class="p-2 bg-sky-100 rounded-xl text-sky-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-lg text-sky-950">Active Placement</h4>
                        @if ($enrollmentStatus === 'endorsed')
                            <p class="text-sm text-sky-800 mt-1">Your deployment letter has been officially endorsed! Go to the Documents tab to download it.</p>
                        @else
                            <p class="text-sm text-sky-800 mt-1">Your National Service placement is active. Please record daily check-ins on the Attendance tab.</p>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Stat Cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Enrollment Status</span>
                        <span class="p-1.5 bg-stormy-50 rounded-xl text-stormy-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                        </span>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <span class="text-2xl font-bold tracking-tight capitalize text-gray-900">{{ str_replace('_', ' ', $enrollmentStatus ?? 'Pending') }}</span>
                    </div>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Assigned Company</span>
                        <span class="p-1.5 bg-stormy-50 rounded-xl text-stormy-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </span>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-gray-900 block truncate">{{ $companyName ?? 'N/A' }}</span>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Department</span>
                        <span class="p-1.5 bg-stormy-50 rounded-xl text-stormy-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                        </span>
                    </div>
                    <span class="text-xl font-bold tracking-tight text-gray-900 block truncate">{{ $departmentName ?? 'Unassigned' }}</span>
                </div>

                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Documents Uploaded</span>
                        <span class="p-1.5 bg-stormy-50 rounded-xl text-stormy-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        </span>
                    </div>
                    <span class="text-2xl font-bold tracking-tight text-gray-900">{{ $documentCount ?? 0 }}</span>
                </div>
            </div>

            {{-- Endorsed Letter --}}
            @if ($endorsedLetter)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h4 class="font-bold text-gray-900 text-lg">Endorsed Letter Ready</h4>
                        <p class="text-gray-500 text-sm mt-0.5">Your official endorsement letter is signed and ready for downloading.</p>
                    </div>
                    <a href="{{ Storage::url($endorsedLetter->generated_file_path) }}" target="_blank"
                       class="inline-flex items-center gap-2 bg-gradient-to-r from-stormy-600 to-stormy-700 hover:from-stormy-700 hover:to-stormy-800 text-white font-semibold py-2.5 px-5 rounded-xl shadow-md transition-all self-start sm:self-auto text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        Download Endorsed Letter
                    </a>
                </div>
            @endif

            {{-- Lists --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Recent Attendance --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                        <h3 class="font-bold text-gray-900 text-lg">Recent Attendance</h3>
                        <a href="{{ route('personnel.attendance') }}" class="text-xs font-bold text-stormy-600 hover:text-stormy-700" wire:navigate>View Details &rarr;</a>
                    </div>
                    @if ($recentAttendance->isEmpty())
                        <div class="text-center py-8">
                            <span class="text-gray-300 block mb-2"><svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>
                            <p class="text-sm text-gray-400">No attendance records registered yet.</p>
                        </div>
                    @else
                        <div class="divide-y divide-gray-100">
                            @foreach ($recentAttendance as $att)
                                <div class="flex items-center justify-between py-3 text-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="w-2.5 h-2.5 rounded-full {{ match($att->status) { 'present' => 'bg-emerald-500', 'late' => 'bg-amber-500', 'absent' => 'bg-rose-500', default => 'bg-gray-400' } }}"></div>
                                        <span class="font-medium text-gray-700">{{ $att->date->format('D, d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs text-gray-400">{{ $att->check_in ? $att->date->setTimeFromTimeString($att->check_in)->format('h:i A') : 'N/A' }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                            {{ match($att->status) { 'present' => 'bg-emerald-50 text-emerald-700 border border-emerald-100', 'late' => 'bg-amber-50 text-amber-700 border border-amber-100', 'absent' => 'bg-rose-50 text-rose-700 border border-rose-100', default => 'bg-gray-50 text-gray-700' } }}">
                                            {{ ucfirst($att->status) }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Recent Evaluations --}}
                <div class="bg-white border border-gray-100 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4 border-b border-gray-100 pb-3">
                        <h3 class="font-bold text-gray-900 text-lg">Recent Monthly Evaluations</h3>
                    </div>
                    @if ($evaluations->isEmpty())
                        <div class="text-center py-8">
                            <span class="text-gray-300 block mb-2"><svg class="w-10 h-10 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg></span>
                            <p class="text-sm text-gray-400">No evaluations submitted yet.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($evaluations as $ev)
                                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $ev->period_start->format('M Y') }}</span>
                                        <span class="inline-flex items-center gap-1 bg-stormy-50 text-stormy-700 font-bold px-2.5 py-1 rounded-lg text-sm border border-stormy-100">
                                            Score: {{ $ev->overall_score }}/5
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs font-medium text-gray-600 mt-2 border-t border-gray-200/60 pt-2.5">
                                        <div>Punctuality: <span class="text-gray-900 font-bold">{{ $ev->punctuality_score }}</span></div>
                                        <div>Performance: <span class="text-gray-900 font-bold">{{ $ev->performance_score }}</span></div>
                                        <div>Attitude: <span class="text-gray-900 font-bold">{{ $ev->attitude_score }}</span></div>
                                        <div>Teamwork: <span class="text-gray-900 font-bold">{{ $ev->teamwork_score }}</span></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>
