<div class="flex items-center gap-2">
    {{-- Ver detalle --}}
    <x-wire-button href="{{ route('admin.tickets.show', $ticket) }}" blue xs>
        <i class="fa-solid fa-eye"></i>
    </x-wire-button>

    {{-- Editar ticket --}}
    <x-wire-button href="{{ route('admin.tickets.edit', $ticket) }}" amber xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    {{-- Eliminar ticket --}}
    <form action="{{ route('admin.tickets.destroy', $ticket) }}" method="POST" class="delete-ticket-form">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" red xs>
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>
