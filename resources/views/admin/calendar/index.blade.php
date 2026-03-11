<x-admin-layout
    title="Calendario | MediMatch"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Calendario'],
    ]">

    <x-wire-card>
        @livewire('admin.calendar-view')
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
