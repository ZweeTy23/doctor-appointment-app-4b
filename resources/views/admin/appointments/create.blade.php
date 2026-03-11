<x-admin-layout
    title="Nueva Cita | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas', 'href' => route('admin.appointments.index')],
        ['name' => 'Nuevo'],
    ]">

    {{-- Paso 1: Seleccionar Especialidad y Filtros --}}
    <x-wire-card class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-1">
            <i class="fa-solid fa-search text-blue-500 mr-2"></i>
            Buscar disponibilidad
        </h3>
        <p class="text-sm text-gray-500 mb-4">Encuentra el horario perfecto para tu cita.</p>

        <form action="{{ route('admin.appointments.create') }}" method="GET">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                {{-- Especialidad --}}
                <div>
                    <x-wire-native-select label="Especialidad" name="speciality_id" required>
                        <option value="">Selecciona una especialidad</option>
                        @foreach ($specialities as $speciality)
                            <option value="{{ $speciality->id }}" @selected(request('speciality_id') == $speciality->id)>
                                {{ $speciality->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>

                {{-- Doctor (aparece si hay especialidad) --}}
                @if($availableDoctors->isNotEmpty())
                    <div>
                        <x-wire-native-select label="Doctor" name="doctor_id">
                            <option value="">Selecciona un doctor</option>
                            @foreach ($availableDoctors as $doctor)
                                <option value="{{ $doctor->id }}" @selected(request('doctor_id') == $doctor->id)>
                                    {{ $doctor->user->name }}
                                </option>
                            @endforeach
                        </x-wire-native-select>
                    </div>
                @endif

                {{-- Fecha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                    <input type="date"
                           name="date"
                           value="{{ request('date', date('Y-m-d', strtotime('+1 day'))) }}"
                           min="{{ date('Y-m-d') }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    >
                </div>

                {{-- Botón buscar --}}
                <div>
                    <x-wire-button type="submit" blue class="w-full">
                        <i class="fa-solid fa-search mr-1"></i>
                        Buscar disponibilidad
                    </x-wire-button>
                </div>
            </div>
        </form>
    </x-wire-card>

    {{-- Paso 2: Horarios disponibles y formulario de creación --}}
    @if(request()->filled(['doctor_id', 'date']))
        <x-wire-card>
            @if(empty($availableSlots))
                <div class="text-center py-8">
                    <i class="fa-solid fa-calendar-xmark text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">No hay horarios disponibles para esta fecha y doctor.</p>
                    <p class="text-sm text-gray-400 mt-1">El doctor no tiene disponibilidad o ya tiene todas las horas ocupadas.</p>
                </div>
            @else
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    <i class="fa-solid fa-clock text-green-500 mr-2"></i>
                    Horarios disponibles ({{ count($availableSlots) }})
                </h3>

                <form action="{{ route('admin.appointments.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="doctor_id" value="{{ request('doctor_id') }}">
                    <input type="hidden" name="date" value="{{ request('date') }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        {{-- Seleccionar paciente --}}
                        <div>
                            <x-wire-native-select label="Paciente" name="patient_id" required>
                                <option value="">Selecciona un paciente</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}" @selected(old('patient_id') == $patient->id)>
                                        {{ $patient->user->name }}
                                    </option>
                                @endforeach
                            </x-wire-native-select>
                        </div>

                        {{-- Seleccionar horario --}}
                        <div>
                            <x-wire-native-select label="Horario (1 hora)" name="start_time" required>
                                <option value="">Selecciona un horario</option>
                                @foreach ($availableSlots as $slot)
                                    <option value="{{ $slot['start'] }}" @selected(old('start_time') == $slot['start'])>
                                        {{ $slot['label'] }}
                                    </option>
                                @endforeach
                            </x-wire-native-select>
                        </div>
                    </div>

                    {{-- Resumen --}}
                    @php
                        $selectedDoc = $availableDoctors->firstWhere('id', request('doctor_id'));
                    @endphp
                    <div class="bg-blue-50 p-4 rounded-lg mb-6">
                        <h4 class="font-medium text-blue-800 mb-2">
                            <i class="fa-solid fa-calendar-check mr-1"></i>
                            Resumen de la cita
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-700">
                            <div>
                                <span class="font-semibold">Doctor:</span>
                                {{ $selectedDoc?->user?->name ?? 'N/A' }}
                            </div>
                            <div>
                                <span class="font-semibold">Fecha:</span>
                                {{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}
                            </div>
                            <div>
                                <span class="font-semibold">Duración:</span>
                                1 hora
                            </div>
                        </div>

                        {{-- Motivo de la Cita --}}
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de la cita</label>
                            <textarea name="reason" rows="3" 
                                      class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Ej. Chequeo de medicamentos, Dolor de garganta, etc.">{{ old('reason') }}</textarea>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                        <x-wire-button outline href="{{ route('admin.appointments.index') }}">
                            Cancelar
                        </x-wire-button>
                        <x-wire-button type="submit" blue>
                            <i class="fa-solid fa-calendar-plus mr-1"></i>
                            Programar Cita
                        </x-wire-button>
                    </div>
                </form>
            @endif
        </x-wire-card>
    @endif

    @push('js')
        @if (session('swal'))
            <script>
                Swal.fire({
                    icon: "{{ session('swal.icon') }}",
                    title: "{{ session('swal.title') }}",
                    text: "{{ session('swal.text') }}",
                    confirmButtonColor: '#3085d6',
                });
            </script>
        @endif
    @endpush
</x-admin-layout>
