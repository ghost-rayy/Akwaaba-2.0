<div>
    {{-- Type switcher --}}
    <div class="flex items-center gap-2 mb-6 border-b border-gray-200 pb-4">
        <button type="button" wire:click="switchLetterType('posting_letter')"
                class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ $letterType === 'posting_letter' ? 'bg-stormy-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Posting Letter
        </button>
        <button type="button" wire:click="switchLetterType('appointment_letter')"
                class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ $letterType === 'appointment_letter' ? 'bg-stormy-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Appointment Letter
        </button>
    </div>

    {{-- LIST MODE --}}
    @if ($mode === 'list')
        <div class="flex items-center justify-between mb-6">
            <div>
                @if ($letterType === 'appointment_letter')
                    <h2 class="text-xl font-semibold">Appointment Letter</h2>
                    <p class="text-sm text-gray-500">Draft the letter on the platform, insert variables, then generate for validated personnel.</p>
                @else
                    <h2 class="text-xl font-semibold">Posting Letter Field Mapping</h2>
                    <p class="text-sm text-gray-500">Configure field positions on the company posting letter template.</p>
                @endif
            </div>
            @if ($letterType === 'appointment_letter')
                <div class="flex items-center gap-2">
                    <button type="button" wire:click="startDraft"
                            class="inline-flex items-center gap-2 border border-stormy-600 text-stormy-700 px-4 py-2 rounded-md hover:bg-stormy-50 text-sm font-medium">
                        {{ $template && filled($template->body) ? 'Edit Draft' : 'Draft Letter' }}
                    </button>
                    @if ($template && filled($template->body))
                        <button type="button" wire:click="startGenerate"
                                class="inline-flex items-center gap-2 bg-stormy-600 text-white px-4 py-2 rounded-md hover:bg-stormy-700 text-sm font-medium">
                            Generate Letters
                        </button>
                    @endif
                </div>
            @endif
        </div>

        @if ($letterType === 'appointment_letter')
            @if ($template && filled($template->body))
                <div class="bg-white rounded-lg shadow p-6 border border-gray-200 mb-6">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-semibold">{{ $template->name }}</h3>
                            <p class="text-sm text-gray-500">Draft saved · variables are filled when you generate for each person</p>
                        </div>
                        <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded">Active</span>
                    </div>
                    <div class="bg-gray-50 border border-gray-100 rounded-lg p-4 text-sm text-gray-700 max-h-48 overflow-y-auto mb-4">{{ \Illuminate\Support\Str::limit(trim(strip_tags($template->body)), 600) }}</div>
                    <div class="flex gap-2">
                        <button type="button" wire:click="startDraft" class="text-stormy-600 text-sm hover:text-stormy-800 font-medium">Edit Draft</button>
                        <button type="button" wire:click="deleteTemplate({{ $template->id }})"
                                wire:confirm="Delete this appointment draft?"
                                class="text-red-600 text-sm hover:text-red-800 ml-auto">
                            Delete
                        </button>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg shadow p-12 text-center mb-6">
                    <p class="text-gray-500 mb-2">No appointment letter draft yet.</p>
                    <p class="text-sm text-gray-400 mb-4">Write the letter once, insert variables like full name and start date, then issue it to validated personnel.</p>
                    <button type="button" wire:click="startDraft"
                            class="inline-flex items-center gap-2 bg-stormy-600 text-white px-4 py-2 rounded-md hover:bg-stormy-700 text-sm font-medium">
                        Draft Letter
                    </button>
                </div>
            @endif

            @if ($issuedLetters->isNotEmpty())
                <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-900">Issued Appointment Letters</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead class="bg-gray-50 text-left text-xs uppercase tracking-wider text-gray-500">
                                <tr>
                                    <th class="px-6 py-3">Personnel</th>
                                    <th class="px-6 py-3">Department</th>
                                    <th class="px-6 py-3">Issued</th>
                                    <th class="px-6 py-3">By</th>
                                    <th class="px-6 py-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($issuedLetters as $letter)
                                    <tr>
                                        <td class="px-6 py-3 font-medium text-gray-900">{{ $letter->enrollment?->user?->name }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $letter->enrollment?->department?->name ?? '—' }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $letter->updated_at->format('d M Y, h:i A') }}</td>
                                        <td class="px-6 py-3 text-gray-600">{{ $letter->issuedBy?->name ?? '—' }}</td>
                                        <td class="px-6 py-3 text-right">
                                            <a href="{{ Storage::url($letter->generated_file_path) }}?v={{ $letter->updated_at->timestamp }}" target="_blank"
                                               class="text-stormy-600 hover:text-stormy-800 font-medium">View</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        @elseif ($template)
            <div class="bg-white rounded-lg shadow p-6 border border-gray-200">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <h3 class="font-semibold">{{ $template->name }}</h3>
                        <p class="text-sm text-gray-500">
                            {{ $template->field_mappings_count }} fields mapped
                        </p>
                    </div>
                    <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded">Active</span>
                </div>
                <div class="flex gap-2">
                    <button wire:click="startMapping({{ $template->id }})"
                            class="text-stormy-600 text-sm hover:text-stormy-800 font-medium">
                        {{ $template->field_mappings_count > 0 ? 'Edit Fields' : 'Configure Fields' }}
                    </button>
                    <button wire:click="deleteTemplate({{ $template->id }})"
                            wire:confirm="Delete this template? Field mappings will be lost."
                            class="text-red-600 text-sm hover:text-red-800 ml-auto">
                        Delete
                    </button>
                </div>
            </div>
        @else
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <p class="text-gray-500 mb-2">No posting letter template uploaded yet.</p>
                <p class="text-sm text-gray-400">Go to <a href="{{ route('company.settings') }}" class="text-stormy-600 underline">Settings</a> to upload the company posting letter PDF.</p>
            </div>
        @endif

    {{-- DRAFT MODE --}}
    @elseif ($mode === 'draft' && $letterType === 'appointment_letter')
        @assets
        <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.js"></script>
        <style>
            .word-editor .ql-toolbar {
                position: sticky;
                top: 0;
                z-index: 10;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 0.75rem 0.75rem 0 0;
            }
            .word-editor .ql-container {
                border: none;
                background: transparent;
                font-size: 12pt;
                font-family: Georgia, 'Times New Roman', serif;
            }
            .word-editor .ql-editor {
                position: relative;
                width: 816px;
                max-width: calc(100% - 32px);
                min-height: 1056px;
                margin: 24px auto;
                padding: 96px 90px;
                background: #fff;
                border-radius: 2px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.25), 0 8px 24px rgba(0,0,0,0.12);
                line-height: 1.7;
                overflow-wrap: break-word;
            }
            .word-editor .ql-editor.ql-blank::before {
                left: 90px;
                right: 90px;
                top: 96px;
                font-style: normal;
                color: #9ca3af;
            }
        </style>
        @endassets

        <div class="flex items-center mb-6 gap-4">
            <button wire:click="$set('mode', 'list')" class="text-stormy-600 hover:text-stormy-800 text-sm">&larr; Back</button>
            <div>
                <h2 class="text-xl font-semibold">Draft Appointment Letter</h2>
                <p class="text-sm text-gray-500">Format the letter like a document. Click a variable to insert it at the cursor — values fill in when you generate.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-4 gap-6"
             x-data="{
                quill: null,
                init() {
                    this.quill = new Quill(this.$refs.editor, {
                        theme: 'snow',
                        placeholder: 'Dear {'+'{full_name}'+'}, ...',
                        modules: {
                            toolbar: [
                                [{ font: [] }, { size: ['small', false, 'large', 'huge'] }],
                                [{ header: [1, 2, 3, false] }],
                                ['bold', 'italic', 'underline', 'strike'],
                                [{ color: [] }, { background: [] }],
                                [{ align: [] }],
                                [{ list: 'ordered' }, { list: 'bullet' }],
                                [{ indent: '-1' }, { indent: '+1' }],
                                ['clean'],
                            ],
                        },
                    });
                    this.quill.root.innerHTML = $wire.draftBody || '';
                    this.quill.on('text-change', () => {
                        $wire.set('draftBody', this.quill.root.innerHTML, false);
                    });
                },
                insertVariable(key) {
                    const token = '{' + '{' + key + '}' + '}';
                    const range = this.quill.getSelection(true);
                    this.quill.insertText(range.index, token, 'user');
                    this.quill.setSelection(range.index + token.length);
                    $wire.set('draftBody', this.quill.root.innerHTML, false);
                }
             }">
            <div class="xl:col-span-3">
                <div class="word-editor bg-gray-200/80 rounded-xl border border-gray-200 overflow-hidden" wire:ignore>
                    <div x-ref="editor"></div>
                </div>
                @error('draftBody') <p class="text-red-500 text-xs mt-2">{{ $message }}</p> @enderror

                <div class="mt-4 flex justify-end">
                    <x-loading-button type="button" target="saveDraft" loading="Saving..."
                            wire:click="saveDraft"
                            class="inline-flex items-center gap-2 bg-stormy-600 text-white px-4 py-2 rounded-md hover:bg-stormy-700 text-sm font-medium disabled:opacity-70">
                        Save Draft
                    </x-loading-button>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow border border-gray-200 p-4 h-fit xl:sticky xl:top-6">
                <h3 class="font-semibold mb-1">Insert variables</h3>
                <p class="text-xs text-gray-500 mb-3">Click to add at the cursor position.</p>
                <div class="flex flex-wrap gap-2">
                    @foreach ($appointmentFields as $key => $label)
                        <button type="button"
                                @click="insertVariable(@js($key))"
                                class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-stormy-50 text-stormy-700 border border-stormy-100 hover:bg-stormy-100">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 text-xs text-gray-500 space-y-2">
                    <p>Example: <code class="bg-gray-50 px-1 rounded">@{{full_name}}</code> becomes the personnel’s name.</p>
                    <p><code class="bg-gray-50 px-1 rounded">@{{signature}}</code> and <code class="bg-gray-50 px-1 rounded">@{{stamp}}</code> use company assets from Settings.</p>
                </div>
            </div>
        </div>

    {{-- GENERATE MODE --}}
    @elseif ($mode === 'generate' && $letterType === 'appointment_letter')
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <button wire:click="$set('mode', 'list')" class="text-stormy-600 hover:text-stormy-800 text-sm">&larr; Back</button>
                <div>
                    <h2 class="text-xl font-semibold">Generate Appointment Letters</h2>
                    <p class="text-sm text-gray-500">Select validated personnel to issue personalized appointment letters.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                @if ($eligiblePersonnel->isNotEmpty())
                    <button type="button" wire:click="selectAllEligible"
                            class="text-sm text-gray-600 hover:text-gray-800 font-medium">Select all</button>
                    <button type="button" wire:click="clearSelection"
                            class="text-sm text-gray-600 hover:text-gray-800 font-medium">Clear</button>
                    <x-loading-button type="button" target="issueSelected" loading="Issuing..."
                            wire:click="issueSelected"
                            class="inline-flex items-center gap-2 bg-stormy-600 text-white px-4 py-2 rounded-md hover:bg-stormy-700 text-sm font-medium disabled:opacity-70">
                        Issue Selected ({{ count($selectedEnrollmentIds) }})
                    </x-loading-button>
                @endif
            </div>
        </div>

        @if ($eligiblePersonnel->isEmpty())
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <p class="text-gray-500">No validated personnel available. Appointment letters can only be issued after posting letter validation.</p>
            </div>
        @else
            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 text-left text-xs uppercase tracking-wider text-gray-500">
                        <tr>
                            <th class="px-6 py-3 w-10"></th>
                            <th class="px-6 py-3">Name</th>
                            <th class="px-6 py-3">NSS Number</th>
                            <th class="px-6 py-3">Department</th>
                            <th class="px-6 py-3">Status</th>
                            <th class="px-6 py-3">Letter</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($eligiblePersonnel as $enrollment)
                            <tr class="{{ in_array($enrollment->id, $selectedEnrollmentIds) ? 'bg-stormy-50/40' : '' }}">
                                <td class="px-6 py-3">
                                    <input type="checkbox"
                                           wire:click="toggleEnrollment({{ $enrollment->id }})"
                                           @checked(in_array($enrollment->id, $selectedEnrollmentIds))
                                           class="rounded border-gray-300 text-stormy-600 focus:ring-stormy-500">
                                </td>
                                <td class="px-6 py-3 font-medium text-gray-900">{{ $enrollment->user?->name }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $enrollment->nss_number }}</td>
                                <td class="px-6 py-3 text-gray-600">{{ $enrollment->department?->name ?? '—' }}</td>
                                <td class="px-6 py-3">
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-semibold bg-emerald-100 text-emerald-800">{{ ucfirst($enrollment->status) }}</span>
                                </td>
                                <td class="px-6 py-3 text-gray-600">
                                    {{ $enrollment->has_appointment_letter ? 'Issued' : 'Not issued' }}
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <x-loading-button type="button" target="issueOne({{ $enrollment->id }})" loading="..."
                                            wire:click="issueOne({{ $enrollment->id }})"
                                            class="text-stormy-600 hover:text-stormy-800 text-sm font-medium">
                                        {{ $enrollment->has_appointment_letter ? 'Re-issue' : 'Issue' }}
                                    </x-loading-button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    {{-- MAPPING MODE (posting only) --}}
    @elseif ($mode === 'mapping' && $letterType === 'posting_letter' && $currentTemplate)
        <div x-data="templateBuilder" wire:ignore data-pdf-url="{{ $templateBase64 }}"
             data-fields='{{ json_encode($currentTemplate->fieldMappings->map(fn($fm) => ["id" => $fm->id, "x" => (float)$fm->x, "y" => (float)$fm->y, "w" => (float)($fm->width??150), "h" => (float)($fm->height??30), "field_key" => $fm->field_key, "label" => $fm->label, "font_size" => $fm->font_size??12, "text_alignment" => $fm->text_alignment??"left", "page_number" => (int)($fm->page_number??1)])) }}'>
            <div class="flex items-center mb-4">
                <button wire:click="$set('mode', 'list')" class="text-stormy-600 hover:text-stormy-800 text-sm">&larr; Back</button>
                <h2 class="text-xl font-semibold ml-4">Field Mapping: {{ $currentTemplate->name }}</h2>
            </div>

            <div class="flex gap-6">
                <div class="flex-1 bg-white rounded-lg shadow p-4">
                    <div class="relative">
                        <canvas id="pdf-canvas" class="w-full border border-gray-300 rounded"></canvas>
                        <canvas id="field-overlay" class="absolute top-0 left-0 w-full h-full cursor-crosshair"
                                style="display:none;"></canvas>
                    </div>

                    <div x-show="numPages > 1" class="flex items-center justify-between mt-4 px-2 pt-3 border-t border-gray-100">
                        <button type="button" @click="prevPage()" :disabled="currentPage === 1"
                                class="px-3.5 py-1.5 bg-gray-50 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed text-xs font-semibold shadow-sm transition-colors">
                            &larr; Previous Page
                        </button>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Page <span x-text="currentPage" class="text-stormy-600"></span> of <span x-text="numPages"></span></span>
                        <button type="button" @click="nextPage()" :disabled="currentPage === numPages"
                                class="px-3.5 py-1.5 bg-gray-50 border border-gray-200 text-gray-700 rounded-xl hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed text-xs font-semibold shadow-sm transition-colors">
                            Next Page &rarr;
                        </button>
                    </div>
                </div>

                <div class="w-80 space-y-4">
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="font-semibold mb-3">Placed Fields</h3>
                        <template x-for="(field, idx) in fields" :key="field.id ?? idx">
                            <div :class="selectedFieldId === field.id ? 'border-stormy-500 bg-stormy-50/50 shadow-md ring-2 ring-stormy-400' : 'border-gray-200 bg-gray-50/50'"
                                 class="border rounded p-2 mb-2 text-sm transition-all cursor-pointer"
                                 @click="selectField(field.id)">
                                <div class="flex items-center justify-between gap-1 mb-2">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold bg-stormy-50 text-stormy-700 border border-stormy-100">P. <span x-text="field.page_number || 1"></span></span>
                                    <select x-model="field.field_key" @change="updateField(field.id, {field_key: field.field_key})"
                                            class="w-full text-xs border-gray-300 rounded font-semibold text-gray-700">
                                        <option value="">Select field...</option>
                                        @foreach ($availableFields as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button @click.stop="deleteField(field.id)" class="text-red-500 hover:text-red-700 ml-1 font-bold text-lg">&times;</button>
                                </div>
                                <div class="grid grid-cols-2 gap-1 mt-1">
                                    <div>
                                        <label class="text-xs text-gray-500">Font Size</label>
                                        <input type="number" x-model="field.font_size" @change="updateField(field.id, {font_size: parseInt(field.font_size)})"
                                               class="w-full text-xs border-gray-300 rounded" min="8" max="48">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-500">Align</label>
                                        <select x-model="field.text_alignment" @change="updateField(field.id, {text_alignment: field.text_alignment})"
                                                class="w-full text-xs border-gray-300 rounded">
                                            <option value="left">Left</option>
                                            <option value="center">Center</option>
                                            <option value="right">Right</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </template>
                        <p x-show="fields.length === 0" class="text-xs text-gray-400 text-center py-4">
                            Click on the PDF preview to place fields.
                        </p>
                    </div>

                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="font-semibold mb-2">Instructions</h3>
                        <ul class="text-xs text-gray-500 space-y-1 list-disc pl-4">
                            <li>Click on the PDF to place a new field rectangle</li>
                            <li>Drag rectangles to reposition</li>
                            <li>Drag the bottom-right corner to resize</li>
                            <li>Select the field type from the dropdown</li>
                        </ul>
                    </div>

                    <button @click="saveMappings()" :disabled="saving"
                            class="w-full inline-flex items-center justify-center gap-2 bg-stormy-600 text-white px-4 py-2 rounded-md hover:bg-stormy-700 text-sm font-medium disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!saving">Save Field Mappings</span>
                        <span x-show="saving" style="display: none;" class="inline-flex items-center gap-2">
                            <x-loading-spinner />
                            Saving...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
