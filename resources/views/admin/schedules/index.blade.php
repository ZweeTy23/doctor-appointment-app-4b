<x-admin-layout
    title="Horarios | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Calendario', 'href' => route('admin.calendar.index')],
        ['name' => 'Horarios'],
    ]">

    <x-wire-card>
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900">
                    <i class="fa-solid fa-calendar-days text-blue-500 mr-2"></i>
                    Gestor de horarios
                </h3>
                <p class="text-sm text-gray-500 mt-1">
                    Dr. {{ $doctor->user->name }} — Configura los bloques de disponibilidad (1 hora)
                </p>
            </div>
        </div>

        @livewire('admin.schedule-manager', ['doctorId' => $doctor->id])
    </x-wire-card>

    <div class="mt-6">
        <a href="{{ route('admin.calendar.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Volver al calendario
        </a>
    </div>

    @push('js')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('schedule-saved', () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Horario guardado',
                        text: 'La disponibilidad ha sido actualizada correctamente.',
                        confirmButtonColor: '#3085d6',
                    });
                });
            });
        </script>
    @endpush
</x-admin-layout>
