<div>
    {{-- Header: Doctor selector + Navigation --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <div class="flex items-center gap-4">
            <select wire:model.live="selectedDoctorId"
                    class="border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500 text-sm">
                @foreach($doctors as $doctor)
                    <option value="{{ $doctor->id }}">
                        {{ $doctor->user->name }}
                        @if($doctor->speciality) — {{ $doctor->speciality->name }} @endif
                    </option>
                @endforeach
            </select>

            @if($selectedDoctor)
                <a href="{{ route('admin.schedules.index', $selectedDoctor) }}"
                   class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                    <i class="fa-solid fa-pen-to-square mr-1"></i>
                    Editar horario
                </a>
            @endif
        </div>

        {{-- Month navigation --}}
        <div class="flex items-center gap-3">
            <button wire:click="previousMonth" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            <span class="text-lg font-semibold text-gray-800 capitalize min-w-[180px] text-center">
                {{ $monthName }}
            </span>
            <button wire:click="nextMonth" class="p-2 rounded-lg hover:bg-gray-100 text-gray-600">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
    </div>

    {{-- Leyenda --}}
    <div class="flex gap-4 mb-4 text-xs">
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-green-400 inline-block"></span> Disponible</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-orange-400 inline-block"></span> Parcial</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-gray-400 inline-block"></span> Ocupado</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-full bg-red-400 inline-block"></span> No disponible</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Calendario mensual --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                {{-- Cabecera de días --}}
                <div class="grid grid-cols-7 bg-gray-50 border-b">
                    @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $dayName)
                        <div class="p-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ $dayName }}</div>
                    @endforeach
                </div>

                {{-- Días del mes --}}
                <div class="grid grid-cols-7">
                    @foreach($calendarDays as $calDay)
                        @if($calDay['day'] === null)
                            <div class="p-3 min-h-[80px] border-b border-r border-gray-100"></div>
                        @else
                            @php
                                $bgColor = match($calDay['status']) {
                                    'disponible'    => 'bg-green-50 hover:bg-green-100',
                                    'parcial'       => 'bg-orange-50 hover:bg-orange-100',
                                    'ocupado'       => 'bg-gray-100 hover:bg-gray-200',
                                    'no_disponible' => 'bg-red-50 hover:bg-red-100',
                                    default         => 'bg-white',
                                };
                                $dotColor = match($calDay['status']) {
                                    'disponible'    => 'bg-green-400',
                                    'parcial'       => 'bg-orange-400',
                                    'ocupado'       => 'bg-gray-400',
                                    'no_disponible' => 'bg-red-400',
                                    default         => 'bg-gray-300',
                                };
                                $isSelected = $selectedDate === $calDay['date'];
                            @endphp
                            <div wire:click="selectDay('{{ $calDay['date'] }}')"
                                 class="p-3 min-h-[80px] border-b border-r border-gray-100 cursor-pointer transition-colors {{ $bgColor }} {{ $isSelected ? 'ring-2 ring-blue-500 ring-inset' : '' }}">
                                <div class="flex justify-between items-start">
                                    <span class="text-sm font-medium {{ $calDay['isToday'] ? 'bg-blue-600 text-white rounded-full w-7 h-7 flex items-center justify-center' : 'text-gray-700' }}">
                                        {{ $calDay['day'] }}
                                    </span>
                                    <span class="w-2.5 h-2.5 rounded-full {{ $dotColor }}"></span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Panel derecho: Detalle del día --}}
        <div class="lg:col-span-1">
            @if($selectedDate)
                <div class="bg-white rounded-lg border border-gray-200 p-4">
                    <h4 class="font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-clock text-blue-500"></i>
                        {{ \Carbon\Carbon::parse($selectedDate)->locale('es')->isoFormat('dddd D [de] MMMM') }}
                    </h4>

                    <div class="space-y-2">
                        @foreach($dayDetail as $slot)
                            @php
                                $slotBg = match($slot['status']) {
                                    'disponible'    => 'bg-green-50 border-green-200 text-green-800',
                                    'ocupado'       => 'bg-gray-100 border-gray-300 text-gray-700',
                                    'no_disponible' => 'bg-red-50 border-red-200 text-red-700',
                                    default         => 'bg-gray-50 border-gray-200',
                                };
                                $slotIcon = match($slot['status']) {
                                    'disponible'    => 'fa-circle-check text-green-500',
                                    'ocupado'       => 'fa-calendar-check text-gray-500',
                                    'no_disponible' => 'fa-circle-xmark text-red-400',
                                    default         => 'fa-circle text-gray-400',
                                };
                            @endphp
                            <div class="p-3 rounded-lg border {{ $slotBg }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid {{ $slotIcon }} text-sm"></i>
                                        <span class="text-sm font-medium">{{ $slot['hour'] }}</span>
                                    </div>
                                    <span class="text-xs font-semibold">{{ $slot['label'] }}</span>
                                </div>
                                @if($slot['patientName'])
                                    <p class="text-xs mt-1 ml-6 opacity-75">
                                        <i class="fa-solid fa-user mr-1"></i>{{ $slot['patientName'] }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white rounded-lg border border-gray-200 p-8 text-center">
                    <i class="fa-solid fa-hand-pointer text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-400">Selecciona un día del calendario para ver el detalle de horas.</p>
                </div>
            @endif
        </div>
    </div>
</div>
