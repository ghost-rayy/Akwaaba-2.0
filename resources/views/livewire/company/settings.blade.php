<div>
    <div class="max-w-3xl mx-auto space-y-8">
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
                    <x-loading-button target="updateProfile" loading="Saving..."
                            class="px-6 py-2.5 bg-stormy-600 text-white rounded-lg hover:bg-stormy-700 text-sm font-medium transition-colors">
                        Save Changes
                    </x-loading-button>
                </div>
            </form>
        </div>

        @php $company = auth()->user()->company; @endphp

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Company Logo</h2>
            <p class="text-sm text-gray-500 mb-4">Upload your company logo. It will appear in the navigation bar for your team.</p>
            @if ($company->logo_path)
                <div class="mb-4 p-4 bg-gray-50 rounded-lg inline-flex items-center gap-4">
                    <img src="{{ asset('storage/' . $company->logo_path) }}" alt="{{ $company->name }} logo" class="h-16 w-16 object-contain rounded-lg border border-gray-200 bg-white p-1">
                    <div>
                        <p class="text-sm font-medium text-gray-700">Logo uploaded</p>
                        <p class="text-xs text-gray-500">Re-upload to replace the current logo.</p>
                    </div>
                </div>
            @endif
            <form
                x-data="{
                    uploading: false,
                    error: null,
                    async submit(event) {
                        event.preventDefault();
                        const file = this.$refs.fileInput.files[0];
                        if (!file) {
                            this.error = 'Please select an image first.';
                            return;
                        }
                        this.uploading = true;
                        this.error = null;
                        const formData = new FormData();
                        formData.append('file', file);
                        try {
                            const response = await fetch('{{ route('company.upload.logo') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                },
                                body: formData,
                                credentials: 'same-origin',
                            });
                            const data = await response.json();
                            if (!response.ok) {
                                const message = data.errors?.file?.[0] ?? data.message ?? (response.status === 419 ? 'Session expired. Please refresh the page.' : 'Upload failed');
                                throw new Error(message);
                            }
                            if (window.toast) window.toast(data.message, 'success');
                            if (data.logo_url && window.Livewire) {
                                Livewire.dispatch('company-logo-updated', { url: data.logo_url });
                            }
                            $wire.$refresh();
                            this.$refs.fileInput.value = '';
                        } catch (e) {
                            this.error = e.message || 'Upload failed. Please try again.';
                        } finally {
                            this.uploading = false;
                        }
                    },
                }"
                @submit="submit($event)"
                class="flex items-end gap-4"
            >
                <div class="flex-1">
                    <input x-ref="fileInput" type="file" accept="image/png,image/jpeg" class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-stormy-50 file:text-stormy-700 hover:file:bg-stormy-100">
                    <p x-show="error" x-text="error" class="text-red-500 text-xs mt-1" style="display: none;"></p>
                </div>
                <button type="submit" :disabled="uploading"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-stormy-600 text-white rounded-lg hover:bg-stormy-700 text-sm font-medium transition-colors whitespace-nowrap disabled:opacity-70 disabled:cursor-not-allowed">
                    <span x-show="!uploading">Upload</span>
                    <span x-show="uploading" style="display: none;" class="inline-flex items-center gap-2">
                        <x-loading-spinner />
                        Uploading...
                    </span>
                </button>
            </form>
        </div>

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
            <form
                x-data="{
                    uploading: false,
                    error: null,
                    async submit(event) {
                        event.preventDefault();
                        const file = this.$refs.fileInput.files[0];
                        if (!file) {
                            this.error = 'Please select a PDF file first.';
                            return;
                        }
                        this.uploading = true;
                        this.error = null;
                        const formData = new FormData();
                        formData.append('file', file);
                        try {
                            const response = await fetch('{{ route('company.upload.posting-letter') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                },
                                body: formData,
                                credentials: 'same-origin',
                            });
                            const data = await response.json();
                            if (!response.ok) {
                                const message = data.errors?.file?.[0] ?? data.message ?? (response.status === 419 ? 'Session expired. Please refresh the page.' : 'Upload failed');
                                throw new Error(message);
                            }
                            if (window.toast) window.toast(data.message, 'success');
                            $wire.$refresh();
                            this.$refs.fileInput.value = '';
                        } catch (e) {
                            this.error = e.message || 'Upload failed. Please try again.';
                        } finally {
                            this.uploading = false;
                        }
                    },
                }"
                @submit="submit($event)"
                class="flex items-end gap-4"
            >
                <div class="flex-1">
                    <input x-ref="fileInput" type="file" accept=".pdf,application/pdf" class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-stormy-50 file:text-stormy-700 hover:file:bg-stormy-100">
                    <p x-show="error" x-text="error" class="text-red-500 text-xs mt-1" style="display: none;"></p>
                </div>
                <button type="submit" :disabled="uploading"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-stormy-600 text-white rounded-lg hover:bg-stormy-700 text-sm font-medium transition-colors whitespace-nowrap disabled:opacity-70 disabled:cursor-not-allowed">
                    <span x-show="!uploading">Upload</span>
                    <span x-show="uploading" style="display: none;" class="inline-flex items-center gap-2">
                        <x-loading-spinner />
                        Uploading...
                    </span>
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Digital Signature</h2>
            @if ($company->digital_signature_path)
                <div class="mb-4 p-4 bg-gray-50 rounded-lg inline-block">
                    <img src="{{ asset('storage/' . $company->digital_signature_path) }}" alt="Signature" class="h-16 object-contain">
                </div>
            @endif
            <form
                x-data="{
                    uploading: false,
                    error: null,
                    async submit(event) {
                        event.preventDefault();
                        const file = this.$refs.fileInput.files[0];
                        if (!file) {
                            this.error = 'Please select an image first.';
                            return;
                        }
                        this.uploading = true;
                        this.error = null;
                        const formData = new FormData();
                        formData.append('file', file);
                        try {
                            const response = await fetch('{{ route('company.upload.signature') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                },
                                body: formData,
                                credentials: 'same-origin',
                            });
                            const data = await response.json();
                            if (!response.ok) {
                                const message = data.errors?.file?.[0] ?? data.message ?? (response.status === 419 ? 'Session expired. Please refresh the page.' : 'Upload failed');
                                throw new Error(message);
                            }
                            if (window.toast) window.toast(data.message, 'success');
                            $wire.$refresh();
                            this.$refs.fileInput.value = '';
                        } catch (e) {
                            this.error = e.message || 'Upload failed. Please try again.';
                        } finally {
                            this.uploading = false;
                        }
                    },
                }"
                @submit="submit($event)"
                class="flex items-end gap-4"
            >
                <div class="flex-1">
                    <input x-ref="fileInput" type="file" accept="image/png,image/jpeg" class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-stormy-50 file:text-stormy-700 hover:file:bg-stormy-100">
                    <p x-show="error" x-text="error" class="text-red-500 text-xs mt-1" style="display: none;"></p>
                </div>
                <button type="submit" :disabled="uploading"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-stormy-600 text-white rounded-lg hover:bg-stormy-700 text-sm font-medium transition-colors whitespace-nowrap disabled:opacity-70 disabled:cursor-not-allowed">
                    <span x-show="!uploading">Upload</span>
                    <span x-show="uploading" style="display: none;" class="inline-flex items-center gap-2">
                        <x-loading-spinner />
                        Uploading...
                    </span>
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Company Stamp</h2>
            @if ($company->stamp_path)
                <div class="mb-4 p-4 bg-gray-50 rounded-lg inline-block">
                    <img src="{{ asset('storage/' . $company->stamp_path) }}" alt="Stamp" class="h-20 object-contain">
                </div>
            @endif
            <form
                x-data="{
                    uploading: false,
                    error: null,
                    async submit(event) {
                        event.preventDefault();
                        const file = this.$refs.fileInput.files[0];
                        if (!file) {
                            this.error = 'Please select an image first.';
                            return;
                        }
                        this.uploading = true;
                        this.error = null;
                        const formData = new FormData();
                        formData.append('file', file);
                        try {
                            const response = await fetch('{{ route('company.upload.stamp') }}', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                                    'Accept': 'application/json',
                                },
                                body: formData,
                                credentials: 'same-origin',
                            });
                            const data = await response.json();
                            if (!response.ok) {
                                const message = data.errors?.file?.[0] ?? data.message ?? (response.status === 419 ? 'Session expired. Please refresh the page.' : 'Upload failed');
                                throw new Error(message);
                            }
                            if (window.toast) window.toast(data.message, 'success');
                            $wire.$refresh();
                            this.$refs.fileInput.value = '';
                        } catch (e) {
                            this.error = e.message || 'Upload failed. Please try again.';
                        } finally {
                            this.uploading = false;
                        }
                    },
                }"
                @submit="submit($event)"
                class="flex items-end gap-4"
            >
                <div class="flex-1">
                    <input x-ref="fileInput" type="file" accept="image/png,image/jpeg" class="w-full text-sm text-gray-500 file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-stormy-50 file:text-stormy-700 hover:file:bg-stormy-100">
                    <p x-show="error" x-text="error" class="text-red-500 text-xs mt-1" style="display: none;"></p>
                </div>
                <button type="submit" :disabled="uploading"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-stormy-600 text-white rounded-lg hover:bg-stormy-700 text-sm font-medium transition-colors whitespace-nowrap disabled:opacity-70 disabled:cursor-not-allowed">
                    <span x-show="!uploading">Upload</span>
                    <span x-show="uploading" style="display: none;" class="inline-flex items-center gap-2">
                        <x-loading-spinner />
                        Uploading...
                    </span>
                </button>
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
                    <x-loading-button target="changePassword" loading="Changing password..."
                            class="px-6 py-2.5 bg-stormy-600 text-white rounded-lg hover:bg-stormy-700 text-sm font-medium transition-colors">
                        Change Password
                    </x-loading-button>
                </div>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Create and Manage HR Staff</h2>
                <p class="text-sm text-gray-500 mt-1">Add HR accounts for your company. They sign in at the HR portal and can manage day-to-day personnel workflows.</p>
            </div>

            <form wire:submit="createHrStaff" class="space-y-4 mb-8 pb-8 border-b border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                        <input wire:model="hr_name" type="text" placeholder="Jane Mensah"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                        @error('hr_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input wire:model="hr_email" type="email" placeholder="hr@company.com"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                        @error('hr_email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input wire:model.live="hr_phone" type="text" inputmode="numeric" maxlength="10" placeholder="0244000000"
                               x-on:input="$el.value = $el.value.replace(/\D/g, '').slice(0, 10)"
                               class="w-full rounded-lg border-gray-300 shadow-sm focus:border-stormy-500 focus:ring-stormy-500 text-sm">
                        @error('hr_phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex justify-end">
                    <x-loading-button target="createHrStaff" loading="Creating HR staff..."
                            class="px-6 py-2.5 bg-stormy-600 text-white rounded-lg hover:bg-stormy-700 text-sm font-medium transition-colors">
                        Create HR Staff
                    </x-loading-button>
                </div>
            </form>

            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Existing HR Staff ({{ $hrStaff->count() }})</h3>
                @if ($hrStaff->isEmpty())
                    <p class="text-sm text-gray-500 bg-gray-50 border border-dashed border-gray-200 rounded-lg px-4 py-6 text-center">
                        No HR staff yet. Create one above to get started.
                    </p>
                @else
                    <div class="overflow-hidden rounded-lg border border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Name</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Contact</th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Added</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                @foreach ($hrStaff as $staff)
                                    <tr class="hover:bg-gray-50/60">
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $staff->name }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            <div class="text-gray-900">{{ $staff->email }}</div>
                                            <div class="text-gray-400">{{ $staff->phone }}</div>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                            {{ $staff->created_at->format('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm">
                                            <div class="inline-flex items-center gap-2">
                                                <button
                                                    type="button"
                                                    wire:click="resetHrPassword({{ $staff->id }})"
                                                    wire:confirm="Reset password for {{ $staff->name }} and email a new temporary password?"
                                                    wire:loading.attr="disabled"
                                                    wire:target="resetHrPassword({{ $staff->id }})"
                                                    class="text-stormy-600 hover:text-stormy-800 font-medium text-xs disabled:opacity-70"
                                                >
                                                    <span wire:loading.remove wire:target="resetHrPassword({{ $staff->id }})">Reset Password</span>
                                                    <span wire:loading wire:target="resetHrPassword({{ $staff->id }})" style="display: none;">Sending...</span>
                                                </button>
                                                <button
                                                    type="button"
                                                    wire:click="removeHrStaff({{ $staff->id }})"
                                                    wire:confirm="Remove {{ $staff->name }} as HR staff? They will no longer be able to sign in."
                                                    wire:loading.attr="disabled"
                                                    wire:target="removeHrStaff({{ $staff->id }})"
                                                    class="text-red-600 hover:text-red-800 font-medium text-xs disabled:opacity-70"
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
