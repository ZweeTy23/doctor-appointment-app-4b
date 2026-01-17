<x-admin-layout
    title="Crear Usuario"
    :breadcrumbs="[
        ['name' => 'Dashboard', 'href' => route('admin.dashboard')],
        ['name' => 'Usuarios', 'href' => route('admin.users.index')],
        ['name' => 'Crear']
    ]"
>

    <x-wire-card>

        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    Información Personal
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Nombre --}}
                    <div class="col-span-1">
                        <x-wire-input label="Nombre Completo" name="name" value="{{ old('name') }}" placeholder="Ej. Juan Pérez" autocomplete="name" required />
                    </div>

                    {{-- ID Number --}}
                    <div class="col-span-1">
                        <x-wire-input label="Número de ID" name="id_number" value="{{ old('id_number') }}" placeholder="Ej. 123456789" autocomplete="off" inputmode="numeric" required />
                    </div>

                    {{-- Teléfono --}}
                    <div class="col-span-1">
                        <x-wire-input label="Teléfono" name="phone" value="{{ old('phone') }}" placeholder="Ej. 9991234567" autocomplete="tel" inputmode="tel" required />
                    </div>

                    {{-- Dirección (Ocupa 2 columnas para más espacio) --}}
                    <div class="col-span-1 md:col-span-2">
                        <x-wire-input label="Dirección Física" name="address" value="{{ old('address') }}" placeholder="Ej. Calle 45 x 30 y 32, Centro" autocomplete="street-address" required />
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    Cuenta y Permisos
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Correo --}}
                    <div class="col-span-1">
                        <x-wire-input type="email" label="Correo Electrónico" name="email" value="{{ old('email') }}" placeholder="usuario@ejemplo.com" autocomplete="email" required />
                    </div>

                    {{-- Rol --}}
                    <div class="col-span-1">
                        <x-wire-native-select name="role_id" label="Rol de Usuario" required>
                            <option value="">Seleccione un nivel de acceso</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </x-wire-native-select>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    Seguridad
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Contraseña --}}
                    <div class="col-span-1">
                        <x-wire-input type="password" label="Contraseña" name="password" placeholder="Mínimo 8 caracteres" autocomplete="new-password" required />
                    </div>

                    {{-- Confirmar Contraseña --}}
                    <div class="col-span-1">
                        <x-wire-input type="password" label="Confirmar Contraseña" name="password_confirmation" placeholder="Repite la contraseña" autocomplete="new-password" required />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>
                <x-wire-button type="submit" blue class="px-6">
                    Guardar Usuario
                </x-wire-button>
            </div>

        </form>
    </x-wire-card>

</x-admin-layout>
