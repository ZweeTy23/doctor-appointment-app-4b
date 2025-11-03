<!-- resources/views/admin/roles/edit.blade.php -->
<x-admin-layout
    title="Roles | MediCitas"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route ('admin.dashboard'),
        ],
        [
            'name' => 'Roles',
            'href' => route ('admin.roles.index')
        ],
        [
            'name' => 'Editar'
        ]
    ]">

    <div class="p-6">
        <h1 class="text-2xl font-semibold">Editar</h1>
        <!-- Optional: show role name if passed to the view -->
        @isset($role)
            <p class="mt-2 text-gray-600">Editing: {{ $role->name }}</p>
        @endisset

        <!-- Place your edit form here -->
    </div>

</x-admin-layout>
