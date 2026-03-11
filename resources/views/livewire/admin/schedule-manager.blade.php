<div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="p-3 text-gray-600 font-semibold bg-gray-50 min-w-[100px]">Día/Hora</th>
                    @foreach($days as $dayIndex => $dayName)
                        <th class="p-3 text-center text-gray-600 font-semibold bg-gray-50 uppercase">
                            {{ $dayName }}
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                {{-- Fila "Todos" para toggle de día completo --}}
                <tr class="border-b bg-gray-50">
                    <td class="p-3 font-medium text-gray-500 text-xs">TODOS</td>
                    @foreach($days as $dayIndex => $dayName)
                        <td class="p-2 text-center">
                            @php
                                $allDayChecked = true;
                                foreach ($hours as $hour) {
                                    if (!isset($selectedSlots[$dayIndex . '_' . $hour])) {
                                        $allDayChecked = false;
                                        break;
                                    }
                                }
                            @endphp
                            <label class="cursor-pointer">
                                <input type="checkbox"
                                    wire:click="toggleDay({{ $dayIndex }})"
                                    {{ $allDayChecked ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                >
                            </label>
                        </td>
                    @endforeach
                </tr>

                {{-- Filas de cada hora --}}
                @foreach($hours as $hour)
                    @php $h = (int) substr($hour, 0, 2); @endphp
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                        <td class="p-3 font-medium text-gray-700 bg-gray-50 text-sm">
                            {{ $hour }} - {{ sprintf('%02d:00', $h + 1) }}
                        </td>

                        @foreach($days as $dayIndex => $dayName)
                            @php
                                $key = $dayIndex . '_' . $hour;
                                $isChecked = isset($selectedSlots[$key]);
                            @endphp
                            <td class="p-2 text-center">
                                <label class="inline-flex items-center justify-center cursor-pointer w-full">
                                    <input type="checkbox"
                                        wire:click="toggleSlot({{ $dayIndex }}, '{{ $hour }}')"
                                        {{ $isChecked ? 'checked' : '' }}
                                        class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2"
                                    >
                                </label>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Botón guardar dentro del componente Livewire --}}
    <div class="flex justify-end mt-6 pt-4 border-t border-gray-100">
        <button wire:click="save"
                class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
            <i class="fa-solid fa-save mr-2"></i>
            Guardar horario
        </button>
    </div>
</div>
