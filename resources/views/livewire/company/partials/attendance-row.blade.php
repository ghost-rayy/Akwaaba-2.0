@if ($mobile)
    <div class="p-4 space-y-3">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="font-semibold text-gray-900">{{ $record->user->name }}</p>
                <p class="text-xs font-mono text-gray-500">{{ $record->user->enrollment?->nss_number ?? '—' }}</p>
            </div>
            <span class="text-xs capitalize px-2 py-0.5 rounded-full bg-gray-100 text-gray-700">{{ $record->status }}</span>
        </div>
        @if ($record->isAbsent())
            <p class="text-sm text-gray-600"><span class="font-medium">Reason:</span> {{ $record->remarks }}</p>
        @else
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div>
                    <span class="text-gray-500">In:</span>
                    {{ $record->check_in ? substr($record->check_in, 0, 5) : '—' }}
                </div>
                <div>
                    <span class="text-gray-500">Out:</span>
                    {{ $record->check_out ? substr($record->check_out, 0, 5) : '—' }}
                </div>
            </div>
        @endif
        <div class="flex flex-wrap gap-2">
            @if ($record->needsCheckInValidation())
                <x-loading-button type="button" target="validateCheckIn({{ $record->id }})" loading="Validating..."
                        wire:click="validateCheckIn({{ $record->id }})"
                        class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-medium">
                    Validate Check In
                </x-loading-button>
            @endif
            @if ($record->needsCheckOutValidation())
                <x-loading-button type="button" target="validateCheckOut({{ $record->id }})" loading="Validating..."
                        wire:click="validateCheckOut({{ $record->id }})"
                        class="px-3 py-1.5 bg-stormy-600 text-white rounded-lg text-xs font-medium">
                    Validate Check Out
                </x-loading-button>
            @endif
            @if ($record->needsAbsenceValidation())
                <x-loading-button type="button" target="validateAbsence({{ $record->id }})" loading="Validating..."
                        wire:click="validateAbsence({{ $record->id }})"
                        class="px-3 py-1.5 bg-rose-600 text-white rounded-lg text-xs font-medium">
                    Validate Absence
                </x-loading-button>
            @endif
        </div>
    </div>
@else
    <tr class="hover:bg-gray-50">
        <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $record->user->name }}</td>
        <td class="px-6 py-3 text-sm font-mono text-gray-600">{{ $record->user->enrollment?->nss_number ?? '—' }}</td>
        <td class="px-6 py-3 text-sm capitalize">{{ $record->status }}</td>
        <td class="px-6 py-3 text-sm">
            @if ($record->check_in)
                {{ substr($record->check_in, 0, 5) }}
                @if ($record->check_in_validated_at)
                    <span class="ml-1 text-xs text-emerald-600 font-medium">Validated</span>
                @else
                    <span class="ml-1 text-xs text-amber-600 font-medium">Pending</span>
                @endif
            @else
                —
            @endif
        </td>
        <td class="px-6 py-3 text-sm">
            @if ($record->check_out)
                {{ substr($record->check_out, 0, 5) }}
                @if ($record->check_out_validated_at)
                    <span class="ml-1 text-xs text-emerald-600 font-medium">Validated</span>
                @else
                    <span class="ml-1 text-xs text-amber-600 font-medium">Pending</span>
                @endif
            @else
                —
            @endif
        </td>
        <td class="px-6 py-3 text-sm text-gray-600 max-w-xs truncate">{{ $record->isAbsent() ? $record->remarks : '—' }}</td>
        <td class="px-6 py-3 text-right">
            <div class="flex flex-wrap justify-end gap-2">
                @if ($record->needsCheckInValidation())
                    <x-loading-button type="button" target="validateCheckIn({{ $record->id }})" loading="Validating..."
                            wire:click="validateCheckIn({{ $record->id }})"
                            class="px-3 py-1.5 bg-emerald-600 text-white rounded-lg text-xs font-medium">
                        Validate In
                    </x-loading-button>
                @endif
                @if ($record->needsCheckOutValidation())
                    <x-loading-button type="button" target="validateCheckOut({{ $record->id }})" loading="Validating..."
                            wire:click="validateCheckOut({{ $record->id }})"
                            class="px-3 py-1.5 bg-stormy-600 text-white rounded-lg text-xs font-medium">
                        Validate Out
                    </x-loading-button>
                @endif
                @if ($record->needsAbsenceValidation())
                    <x-loading-button type="button" target="validateAbsence({{ $record->id }})" loading="Validating..."
                            wire:click="validateAbsence({{ $record->id }})"
                            class="px-3 py-1.5 bg-rose-600 text-white rounded-lg text-xs font-medium">
                        Validate Absence
                    </x-loading-button>
                @endif
            </div>
        </td>
    </tr>
@endif
