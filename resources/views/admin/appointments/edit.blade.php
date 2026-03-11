<x-admin-layout
    title="Editar Cita | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas', 'href' => route('admin.appointments.index')],
        ['name' => 'Editar'],
    ]">

    <x-wire-card>
        <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-1">
                    <i class="fa-solid fa-pen-to-square text-amber-500 mr-2"></i>
                    Editar Cita #{{ $appointment->id }}
                </h3>
                <p class="text-sm text-gray-500 mb-4">
                    <strong>Paciente:</strong> {{ $appointment->patient->user->name }} —
                    <strong>Doctor:</strong> {{ $appointment->doctor->user->name }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                {{-- Estado --}}
                <x-wire-native-select label="Estado de la cita" name="status" required>
                    <option value="programado" @selected(old('status', $appointment->status) == 'programado')>📅 Programado</option>
                    <option value="completado" @selected(old('status', $appointment->status) == 'completado')>✅ Completado</option>
                    <option value="cancelado" @selected(old('status', $appointment->status) == 'cancelado')>❌ Cancelado</option>
                </x-wire-native-select>

                {{-- Fecha --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                    <input type="date"
                           name="date"
                           value="{{ old('date', $appointment->date->format('Y-m-d')) }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                           required>
                </div>

                {{-- Hora inicio (la hora fin = inicio + 1h automático) --}}
                <div>
                    <x-wire-native-select label="Hora (1 hora)" name="start_time" required>
                        @for ($h = 8; $h < 17; $h++)
                            @php $timeVal = sprintf('%02d:00', $h); @endphp
                            <option value="{{ $timeVal }}" @selected(old('start_time', date('H:i', strtotime($appointment->start_time))) == $timeVal)>
                                {{ $timeVal }} - {{ sprintf('%02d:00', $h + 1) }}
                            </option>
                        @endfor
                    </x-wire-native-select>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <x-wire-button outline href="{{ route('admin.appointments.index') }}">
                    Cancelar
                </x-wire-button>
                <x-wire-button type="submit" blue>
                    <i class="fa-solid fa-check mr-1"></i>
                    Guardar Cambios
                </x-wire-button>
            </div>
        </form>
    </x-wire-card>

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
