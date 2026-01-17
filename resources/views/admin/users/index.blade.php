<x-admin-layout
    title="Usuarios | simify"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Usuarios',
        ],
    ]">

    <x-slot name="action">
        <x-wire-button blue href="{{ route('admin.users.create') }}">
            <i class="fa-solid fa-plus"></i>
            Nuevo
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.user-table')

    @push('js')
        {{-- Script para capturar las alertas 'swal' enviadas desde el UserController --}}
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

        {{-- Script de confirmación para eliminar (se dispara desde la tabla) --}}
        <script>
            window.addEventListener('confirm-user-deletion', event => {
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "El usuario será movido a la papelera.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Aquí Livewire ejecuta la acción de borrar
                        window.livewire.emit('deleteUser', event.detail.userId);
                    }
                })
            });
        </script>
    @endpush
</x-admin-layout>
