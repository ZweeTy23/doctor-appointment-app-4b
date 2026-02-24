<x-admin-layout
    title="Editar Doctor | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Doctores',
            'href' => route('admin.doctors.index'),
        ],
        [
            'name' => 'Editar',
        ],
    ]">

    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Encabezado con foto y acciones --}}
        <x-wire-card class="mb-4">
            <div class="lg:flex lg:justify-between lg:items-center">
                <div class="flex items-center">
                    <img src="{{ $doctor->user->profile_photo_url }}" alt="{{ $doctor->user->name }}"
                         class="w-20 h-20 rounded-full object-cover object-center">
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $doctor->user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $doctor->user->email }}</p>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline href="{{ route('admin.doctors.index') }}">Volver</x-wire-button>
                    <x-wire-button type="submit">
                        <i class="fa-solid fa-check mr-1"></i>
                        Guardar Cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        {{-- Aviso datos de usuario --}}
        <div class="bg-blue-100 border-l-4 border-blue-500 p-4 mb-4 rounded-r-lg shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start">
                    <i class="fas fa-user-cog text-blue-500 text-xl mt-1"></i>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-blue-800">Edición de cuenta de usuario</h3>
                        <p class="mt-1 text-sm text-blue-600">
                            La <strong>información de acceso</strong> del doctor (nombre, email y contraseña)
                            debe gestionarse desde la cuenta de usuario asociado.
                        </p>
                    </div>
                </div>
                <div class="flex-shrink-0 mt-3 sm:mt-0">
                    <x-wire-button primary sm href="{{ route('admin.users.edit', $doctor->user_id) }}" target="_blank">
                        <i class="fa-solid fa-pen-to-square mr-1"></i>
                        Editar cuenta de usuario
                    </x-wire-button>
                </div>
            </div>
        </div>

        {{-- Datos profesionales --}}
        <x-wire-card>
            <div class="grid lg:grid-cols-2 gap-6">

                {{-- Especialidad --}}
                <div>
                    <x-wire-native-select label="Especialidad" name="speciality_id">
                        <option value="">Sin especialidad</option>
                        @foreach($specialities as $speciality)
                            <option value="{{ $speciality->id }}"
                                @selected(old('speciality_id', $doctor->speciality_id) == $speciality->id)>
                                {{ $speciality->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                    @error('speciality_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Licencia médica --}}
                <div>
                    <x-wire-input
                        label="Número de licencia médica"
                        name="medical_license_number"
                        placeholder="Ej. MED-12345"
                        value="{{ old('medical_license_number', $doctor->medical_license_number) }}"
                    />
                    @error('medical_license_number')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Biografía --}}
                <div class="lg:col-span-2">
                    <x-wire-textarea
                        label="Biografía / Descripción profesional"
                        name="biography"
                        placeholder="Describe la experiencia y especialización del doctor..."
                    >{{ old('biography', $doctor->biography) }}</x-wire-textarea>
                    @error('biography')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>
        </x-wire-card>

    </form>

</x-admin-layout>
