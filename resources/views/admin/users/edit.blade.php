<x-admin-layout
    title="Editar Usuario"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Usuarios', 'href' => route('admin.users.index')],
        ['name' => 'Editar']
    ]"
>
    <x-wire-card>
        <h1 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Editar usuario</h1>

        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nombre --}}
                <x-wire-input label="Nombre" name="name" value="{{ old('name', $user->name) }}" required />

                {{-- Correo --}}
                <x-wire-input type="email" label="Correo" name="email" value="{{ old('email', $user->email) }}" required />

                {{-- ID Number (Agregado para que no falle el validate) --}}
                <x-wire-input label="Número de ID" name="id_number" value="{{ old('id_number', $user->id_number) }}" required />

                {{-- Teléfono --}}
                <x-wire-input label="Teléfono" name="phone" value="{{ old('phone', $user->phone) }}" required />

                {{-- Dirección --}}
                <div class="col-span-2">
                    <x-wire-input label="Dirección" name="address" value="{{ old('address', $user->address) }}" required />
                </div>

                {{-- Rol --}}
                <div class="col-span-1">
                    <x-wire-native-select name="role_id" label="Rol de Usuario" required>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" @selected(old('role_id', $user->roles->first()?->id) == $role->id)>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>
            </div>

            <hr class="my-6">
            <p class="text-sm text-gray-500 mb-4">Si no deseas cambiar la contraseña, deja los campos vacíos.</p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <x-wire-input type="password" label="Nueva contraseña" name="password" />
                <x-wire-input type="password" label="Confirmar contraseña" name="password_confirmation" />
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">Cancelar</a>
                <x-wire-button type="submit" blue>Actualizar Usuario</x-wire-button>
            </div>
        </form>
    </x-wire-card>
</x-admin-layout>
