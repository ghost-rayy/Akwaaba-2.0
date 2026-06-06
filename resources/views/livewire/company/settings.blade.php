<div>
    <div class="max-w-3xl mx-auto space-y-8">
        @if (session('profile_message'))
            <div class="alert-dismiss bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ session('profile_message') }}</div>
        @endif
        @if (session('signature_message'))
            <div class="alert-dismiss bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ session('signature_message') }}</div>
        @endif
        @if (session('stamp_message'))
            <div class="alert-dismiss bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ session('stamp_message') }}</div>
        @endif
        @if (session('posting_letter_message'))
            <div class="alert-dismiss bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ session('posting_letter_message') }}</div>
        @endif
        @if (session('password_message'))
            <div class="alert-dismiss bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg text-sm">{{ session('password_message') }}</div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Company Profile</h2>
            <form wire:submit="updateProfile" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                        <input wire:model="name" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input wire:model="email" type="email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                        @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input wire:model="phone" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                        @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Registration Number</label>
                        <input wire:model="registration_number" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                        @error('registration_number') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contact Person</label>
                        <input wire:model="contact_person" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                        @error('contact_person') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input wire:model="location" type="text" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                        @error('location') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Today's Date</label>
                        <input type="date" value="{{ now()->format('Y-m-d') }}" readonly
                               class="w-full rounded-lg border-gray-300 bg-gray-50 text-gray-500 shadow-sm cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">Auto-set to today. Saved with profile updates.</p>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Postal Address</label>
                    <textarea wire:model="postal_address" rows="2" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500"></textarea>
                    @error('postal_address') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-stormy-600 text-white rounded-lg hover:bg-stormy-700 text-sm font-medium transition-colors">Save Changes</button>
                </div>
            </form>
        </div>

        @php $company = auth()->user()->company; @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Posting Letter Template</h2>
            <p class="text-sm text-gray-500 mb-4">Upload a sample NSS posting letter PDF. After upload, go to the <a href="{{ route('company.letters') }}" class="text-stormy-600 underline">Letters</a> tab to configure field mappings.</p>
            @if ($company->posting_letter_path)
                <div class="mb-4 p-4 bg-gray-50 rounded-lg flex items-center gap-3">
                    <svg class="w-8 h-8 text-stormy-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    <div>
                        <p class="text-sm font-medium text-gray-700">Posting letter uploaded</p>
                        <p class="text-xs text-gray-500">Re-upload to replace the current template.</p>
                    </div>
                </div>
            @endif
            <form wire:submit="uploadPostingLetter" class="flex items-end gap-4">
                <div class="flex-1">
                    <input wire:model="posting_letter" type="file" accept=".pdf" class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-stormy-50 file:text-stormy-700 hover:file:bg-stormy-100">
                    @error('posting_letter') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    <div wire:loading wire:target="posting_letter" class="text-stormy-600 text-sm mt-1">Uploading...</div>
                </div>
                <button type="submit" class="px-4 py-2 bg-stormy-600 text-white rounded-lg hover:bg-stormy-700 text-sm font-medium transition-colors whitespace-nowrap">Upload</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Digital Signature</h2>
            @if ($company->digital_signature_path)
                <div class="mb-4 p-4 bg-gray-50 rounded-lg inline-block">
                    <img src="{{ asset('storage/' . $company->digital_signature_path) }}" alt="Signature" class="h-16 object-contain">
                </div>
            @endif
            <form wire:submit="uploadSignature" class="flex items-end gap-4">
                <div class="flex-1">
                    <input wire:model="signature" type="file" accept="image/png,image/jpeg" class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-stormy-50 file:text-stormy-700 hover:file:bg-stormy-100">
                    @error('signature') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="px-4 py-2 bg-stormy-600 text-white rounded-lg hover:bg-stormy-700 text-sm font-medium transition-colors whitespace-nowrap">Upload</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Company Stamp</h2>
            @if ($company->stamp_path)
                <div class="mb-4 p-4 bg-gray-50 rounded-lg inline-block">
                    <img src="{{ asset('storage/' . $company->stamp_path) }}" alt="Stamp" class="h-20 object-contain">
                </div>
            @endif
            <form wire:submit="uploadStamp" class="flex items-end gap-4">
                <div class="flex-1">
                    <input wire:model="stamp" type="file" accept="image/png,image/jpeg" class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-stormy-50 file:text-stormy-700 hover:file:bg-stormy-100">
                    @error('stamp') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="px-4 py-2 bg-stormy-600 text-white rounded-lg hover:bg-stormy-700 text-sm font-medium transition-colors whitespace-nowrap">Upload</button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Change Password</h2>
            <form wire:submit="changePassword" class="space-y-4 max-w-md">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
                    <input wire:model="current_password" type="password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                    @error('current_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                    <input wire:model="new_password" type="password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                    @error('new_password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                    <input wire:model="new_password_confirmation" type="password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500">
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-2.5 bg-stormy-600 text-white rounded-lg hover:bg-stormy-700 text-sm font-medium transition-colors">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</div>
