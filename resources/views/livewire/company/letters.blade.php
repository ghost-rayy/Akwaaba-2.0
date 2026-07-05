<div>
    {{-- LIST MODE --}}
    @if ($mode === 'list')
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-semibold">Posting Letter Field Mapping</h2>
                <p class="text-sm text-gray-500">Configure field positions on the company posting letter template.</p>
            </div>
        </div>

        @if ($template)
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

    {{-- MAPPING MODE --}}
    @elseif ($mode === 'mapping' && $currentTemplate)
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
                    
                    {{-- Page Controls --}}
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
