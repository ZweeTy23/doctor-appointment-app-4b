<x-admin-layout
    title="Citas | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Citas',
        ],
    ]">

    <x-slot name="action">
        <x-wire-button blue href="{{ route('admin.appointments.create') }}">
            <i class="fa-solid fa-plus"></i>
            Nuevo
        </x-wire-button>
    </x-slot>

    @livewire('admin.datatables.appointment-table')

    @push('js')
        {{-- Alerta SweetAlert2 cuando hay un mensaje flash --}}
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

        {{-- Confirmación de eliminación --}}
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.delete-appointment-form').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        e.preventDefault();
                        Swal.fire({
                            title: '¿Estás seguro?',
                            text: 'La cita será eliminada permanentemente.',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sí, eliminar',
                            cancelButtonText: 'Cancelar'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
</x-admin-layout>
