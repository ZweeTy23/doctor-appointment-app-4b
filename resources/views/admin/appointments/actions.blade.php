<div class="flex items-center gap-2">
    {{-- Consulta Médica --}}
    <x-wire-button href="{{ route('admin.consultations.show', $appointment) }}" green xs title="Consulta Médica">
        <i class="fa-solid fa-stethoscope"></i>
    </x-wire-button>

    {{-- Editar cita --}}
    <x-wire-button href="{{ route('admin.appointments.edit', $appointment) }}" blue xs title="Editar Cita">
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    {{-- Eliminar cita --}}
    <form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST" class="delete-appointment-form">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs title="Eliminar Cita">
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>
