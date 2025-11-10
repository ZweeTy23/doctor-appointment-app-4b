<!-- resources/views/admin/roles/create.blade.php -->
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
            'name' => 'Crear'
        ]
    ]">

    <div class="p-6">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
            <h1 class="text-2xl font-semibold mb-6">Crear Nuevo Rol</h1>

            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Rol
                    </label>
                    <input 
                        type="text" 
                        name="name" 
                        id="name" 
                        value="{{ old('name') }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        required
                    >
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.roles.index') }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Cancelar
                    </a>
                    <button 
                        type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Crear Rol
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
