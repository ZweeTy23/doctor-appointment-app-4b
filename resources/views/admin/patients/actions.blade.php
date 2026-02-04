<div class="flex gap-2">
    <a href="{{ route('admin.patients.show', $patient) }}"
       class="text-blue-600 hover:text-blue-800"
       title="Ver expediente">
        <i class="fa-solid fa-eye"></i>
    </a>
    <a href="{{ route('admin.patients.edit', $patient) }}"
       class="text-yellow-600 hover:text-yellow-800"
       title="Editar expediente">
        <i class="fa-solid fa-pen-to-square"></i>
    </a>
</div>
