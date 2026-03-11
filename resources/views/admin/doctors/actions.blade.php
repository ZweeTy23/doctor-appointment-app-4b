<div class="flex gap-2">
    {{-- Ver horario --}}
    <a href="{{ route('admin.schedules.index', $doctor) }}"
       class="text-green-600 hover:text-green-800"
       title="Ver Horario">
        <i class="fa-solid fa-calendar-days"></i>
    </a>

    <a href="{{ route('admin.doctors.edit', $doctor) }}"
       class="text-yellow-600 hover:text-yellow-800"
       title="Editar perfil">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
</div>
