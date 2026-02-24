<x-admin-layout
    title="Doctores | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Doctores',
        ],
    ]">

    <p class="text-sm text-gray-500 mb-4">
        <i class="fa-solid fa-info-circle mr-1"></i>
        Los doctores se crean automáticamente al asignar el rol "Doctor" a un usuario.
    </p>

    @livewire('admin.datatables.doctor-table')

    @push('js')
        @if (session('swal'))
            <script>
                Swal.fire({
                    icon: "{{ session('swal.icon') }}",
                    title: "{{ session('swal.title') }}",
                    confirmButtonColor: '#3085d6',
                });
            </script>
        @endif
    @endpush
</x-admin-layout>
