<x-admin-layout
    title="Consulta | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Citas', 'href' => route('admin.appointments.index')],
        ['name' => 'Consulta'],
    ]">

    <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 leading-tight">
                {{ $appointment->patient->user->name }}
            </h2>
            <p class="text-gray-500 text-sm mt-1">
                DNI: {{ $appointment->patient->user->id_number }}
                <span class="mx-2">•</span>
                Motivo: {{ $appointment->reason ?? 'Consulta General' }}
            </p>
        </div>

        {{-- Botones de Modales que controlan la instancia Livewire internamente --}}
        <div class="flex gap-3">
            <button type="button" onclick="Livewire.first().openHistoryModal()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fa-solid fa-id-card-clip mr-2 text-gray-400"></i> Ver Historia
            </button>
            <button type="button" onclick="Livewire.first().openPastConsultationsModal()"
                    class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                <i class="fa-solid fa-clock-rotate-left mr-2 text-gray-400"></i> Consultas Anteriores
            </button>
        </div>
    </div>

    {{-- Componente Principal de Consulta --}}
    @livewire('admin.consultation-manager', ['appointment' => $appointment])

</x-admin-layout>
