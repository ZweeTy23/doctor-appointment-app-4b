<x-admin-layout
    title="Pacientes | simify"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route ('admin.dashboard'),
        ],

        [
            'name' => 'Pacientes',
            'href' => route('admin.patients.create')
        ],
        [
            'name' => 'Nuevo'
        ]
    ]">

</x-admin-layout>
