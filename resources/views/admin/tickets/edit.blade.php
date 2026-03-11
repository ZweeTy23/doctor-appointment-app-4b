<x-admin-layout
    title="Editar Ticket | simify"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Soporte', 'href' => route('admin.tickets.index')],
        ['name' => 'Ticket #' . $ticket->id, 'href' => route('admin.tickets.show', $ticket)],
        ['name' => 'Editar'],
    ]">

    <x-wire-card>
        <form action="{{ route('admin.tickets.update', $ticket) }}" method="POST">
            @csrf
            @method('PUT')

            {{-- Encabezado con info del ticket --}}
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-1">
                    <i class="fa-solid fa-pen-to-square text-amber-500 mr-2"></i>
                    Gestionar Ticket #{{ $ticket->id }}
                </h3>
                <p class="text-sm text-gray-500 mb-4">
                    <strong>Creado por:</strong> {{ $ticket->user->name }} —
                    <strong>Asunto:</strong> {{ $ticket->title }}
                </p>
            </div>

            {{-- Descripción original (solo lectura) --}}
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción del usuario</label>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <p class="text-gray-700 whitespace-pre-line">{{ $ticket->description }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                {{-- Estado --}}
                <x-wire-native-select label="Estado del ticket" name="status" required>
                    <option value="abierto" @selected(old('status', $ticket->status) == 'abierto')>📬 Abierto</option>
                    <option value="en_progreso" @selected(old('status', $ticket->status) == 'en_progreso')>🔄 En Progreso</option>
                    <option value="cerrado" @selected(old('status', $ticket->status) == 'cerrado')>✅ Cerrado</option>
                </x-wire-native-select>

                {{-- Prioridad --}}
                <x-wire-native-select label="Prioridad" name="priority" required>
                    <option value="baja" @selected(old('priority', $ticket->priority) == 'baja')>🟢 Baja</option>
                    <option value="media" @selected(old('priority', $ticket->priority) == 'media')>🟡 Media</option>
                    <option value="alta" @selected(old('priority', $ticket->priority) == 'alta')>🔴 Alta</option>
                </x-wire-native-select>
            </div>

            {{-- Respuesta del admin --}}
            <div class="mb-6">
                <x-wire-textarea
                    label="Respuesta del administrador"
                    name="admin_response"
                    value="{{ old('admin_response', $ticket->admin_response) }}"
                    placeholder="Escribe tu respuesta para el usuario..."
                />
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <x-wire-button outline href="{{ route('admin.tickets.show', $ticket) }}">
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
