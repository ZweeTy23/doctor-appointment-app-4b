<x-admin-layout
    title="Editar Paciente | simify"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Pacientes',
            'href' => route('admin.patients.index')
        ],
        [
            'name' => 'Editar'
        ]
    ]">

    <x-wire-card>
        <form action="{{ route('admin.patients.update', $patient) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    <i class="fa-solid fa-user mr-2"></i>
                    Información del Usuario
                </h3>

                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <span class="text-sm text-gray-500">Nombre:</span>
                            <p class="font-medium">{{ $patient->user->name }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Email:</span>
                            <p class="font-medium">{{ $patient->user->email }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Teléfono:</span>
                            <p class="font-medium">{{ $patient->user->phone ?? 'No registrado' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    <i class="fa-solid fa-notes-medical mr-2"></i>
                    Información Médica
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="col-span-1">
                        <x-wire-native-select name="blood_type_id" label="Tipo de Sangre">
                            <option value="">Seleccione tipo de sangre</option>
                            @foreach($bloodTypes as $bloodType)
                                <option value="{{ $bloodType->id }}" @selected(old('blood_type_id', $patient->blood_type_id) == $bloodType->id)>
                                    {{ $bloodType->name }}
                                </option>
                            @endforeach
                        </x-wire-native-select>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <x-wire-textarea
                            label="Alergias"
                            name="allergies"
                            placeholder="Ej: Penicilina, Mariscos, Polen..."
                            rows="3"
                        >{{ old('allergies', $patient->allergies) }}</x-wire-textarea>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <x-wire-textarea
                            label="Enfermedades Crónicas"
                            name="chronic_diseases"
                            placeholder="Ej: Diabetes, Hipertensión, Asma..."
                            rows="3"
                        >{{ old('chronic_diseases', $patient->chronic_diseases) }}</x-wire-textarea>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <x-wire-textarea
                            label="Historial de Cirugías"
                            name="surgery_history"
                            placeholder="Ej: Apendicectomía 2020, Cirugía de rodilla 2018..."
                            rows="3"
                        >{{ old('surgery_history', $patient->surgery_history) }}</x-wire-textarea>
                    </div>

                    <div class="col-span-1 md:col-span-2">
                        <x-wire-textarea
                            label="Observaciones"
                            name="observations"
                            placeholder="Notas adicionales sobre el paciente..."
                            rows="3"
                        >{{ old('observations', $patient->observations) }}</x-wire-textarea>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    <i class="fa-solid fa-phone-volume mr-2"></i>
                    Contacto de Emergencia
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="col-span-1">
                        <x-wire-input
                            label="Nombre del Contacto"
                            name="emergency_contact_name"
                            value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}"
                            placeholder="Ej: María García"
                        />
                    </div>

                    <div class="col-span-1">
                        <x-wire-input
                            label="Teléfono de Emergencia"
                            name="emergency_contact_phone"
                            value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}"
                            placeholder="Ej: 9991234567"
                            inputmode="tel"
                        />
                    </div>

                    <div class="col-span-1">
                        <x-wire-input
                            label="Parentesco"
                            name="emergency_relationship"
                            value="{{ old('emergency_relationship', $patient->emergency_relationship) }}"
                            placeholder="Ej: Esposa, Madre, Hijo..."
                        />
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.patients.index') }}" class="btn btn-secondary">
                    Cancelar
                </a>
                <x-wire-button type="submit" blue class="px-6">
                    <i class="fa-solid fa-save mr-2"></i>
                    Actualizar Paciente
                </x-wire-button>
            </div>
        </form>
    </x-wire-card>

</x-admin-layout>
