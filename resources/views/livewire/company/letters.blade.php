<div>
    @if (session('message'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- LIST MODE --}}
    @if ($mode === 'list')
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold">Letter Templates</h2>
                <p class="text-sm text-gray-500">Upload and configure NSS posting letter templates.</p>
            </div>
            <button wire:click="showCreate"
                    class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium">
                Upload Template
            </button>
        </div>

        @if ($templates->isEmpty())
            <div class="bg-white rounded-lg shadow p-12 text-center">
                <p class="text-gray-500">No templates yet. Upload a PDF template to get started.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($templates as $template)
                    <div class="bg-white rounded-lg shadow p-6 border {{ $template->is_active ? 'border-green-300' : 'border-gray-200' }}">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3 class="font-semibold">{{ $template->name }}</h3>
                                <p class="text-xs text-gray-500">
                                    {{ $template->field_mappings_count }} fields mapped
                                    &middot; {{ $template->pages_count }} page(s)
                                </p>
                            </div>
                            @if ($template->is_active)
                                <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded">Active</span>
                            @endif
                        </div>
                        <div class="flex gap-2">
                            <button wire:click="startMapping({{ $template->id }})"
                                    class="text-indigo-600 text-sm hover:text-indigo-800 font-medium">
                                {{ $template->field_mappings_count > 0 ? 'Edit Fields' : 'Configure Fields' }}
                            </button>
                            <button wire:click="deleteTemplate({{ $template->id }})"
                                    wire:confirm="Delete this template?"
                                    class="text-red-600 text-sm hover:text-red-800 ml-auto">
                                Delete
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    {{-- CREATE MODE --}}
    @elseif ($mode === 'create')
        <div class="max-w-lg mx-auto">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center mb-6">
                    <button wire:click="$set('mode', 'list')" class="text-indigo-600 hover:text-indigo-800 text-sm">&larr; Back</button>
                    <h2 class="text-xl font-semibold ml-4">Upload Letter Template</h2>
                </div>
                <form wire:submit="saveTemplate" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Template Name</label>
                        <input type="text" wire:model="name"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">PDF File</label>
                        <input type="file" wire:model="template_file" accept=".pdf"
                               class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('template_file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        <div wire:loading wire:target="template_file" class="text-indigo-600 text-sm mt-1">Uploading...</div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" wire:click="$set('mode', 'list')"
                                class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-md hover:bg-gray-200">
                            Cancel
                        </button>
                        <button type="submit"
                                class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm">
                            Upload Template
                        </button>
                    </div>
                </form>
            </div>
        </div>

    {{-- MAPPING MODE --}}
    @elseif ($mode === 'mapping' && $currentTemplate)
        <div x-data="templateBuilder()" x-init="init()" @load-fields.window="setFields($event.detail.fields)">
            <div class="flex items-center mb-4">
                <button wire:click="$set('mode', 'list')" class="text-indigo-600 hover:text-indigo-800 text-sm">&larr; Back to Templates</button>
                <h2 class="text-xl font-semibold ml-4">Field Mapping: {{ $currentTemplate->name }}</h2>
            </div>

            <div class="flex gap-6">
                <div class="flex-1 bg-white rounded-lg shadow p-4 relative">
                    <canvas id="pdf-canvas" class="w-full border border-gray-300 rounded"></canvas>
                    <canvas id="field-overlay" class="absolute top-4 left-4 cursor-crosshair"
                            style="display:none;"></canvas>
                </div>

                <div class="w-80 space-y-4">
                    <div class="bg-white rounded-lg shadow p-4">
                        <h3 class="font-semibold mb-3">Placed Fields</h3>
                        <template x-for="(field, idx) in fields" :key="field.id">
                            <div class="border rounded p-2 mb-2 text-sm">
                                <div class="flex items-center justify-between mb-1">
                                    <select x-model="field.field_key" @change="updateField(field.id, {field_key: field.field_key})"
                                            class="w-full text-xs border-gray-300 rounded">
                                        <option value="">Select field...</option>
                                        @foreach ($availableFields as $key => $label)
                                            <option value="{{ $key }}">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    <button @click="deleteField(field.id)" class="text-red-500 hover:text-red-700 ml-1">&times;</button>
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

                    <button @click="saveMappings()"
                            class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 text-sm font-medium">
                        Save Field Mappings
                    </button>
                </div>
            </div>
        </div>

        <script>
            function templateBuilder() {
                return {
                    fields: [],
                    builder: null,
                    init() {
                        const pdfUrl = '{{ Storage::url($currentTemplate->template_file_path) }}';
                        this.$nextTick(() => {
                            this.builder = window.initPdfBuilder('pdf-canvas', 'field-overlay', pdfUrl, this.fields);
                        });
                    },
                    setFields(fields) {
                        this.fields = fields;
                        if (this.builder) this.builder.setFields(fields);
                    },
                    updateField(id, data) {
                        if (this.builder) this.builder.updateField(id, data);
                    },
                    deleteField(id) {
                        this.fields = this.fields.filter(f => f.id !== id);
                        if (this.builder) this.builder.deleteField(id);
                    },
                    saveMappings() {
                        const valid = this.fields.filter(f => f.field_key);
                        if (valid.length === 0) {
                            alert('Place at least one field on the template.');
                            return;
                        }
                        @this.call('saveFieldMappings', valid);
                    },
                };
            }
        </script>
    @endif
</div>
