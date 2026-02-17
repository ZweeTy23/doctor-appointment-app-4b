@php
    // Definimos qué campos pertenecen a cada pestaña para detectar errores
    $errorGroups = [
        'antecedentes' => ['allergies', 'chronic_diseases', 'surgery_history'],
        'informacion_general' => ['blood_type_id', 'observations'],
        'contacto_emergencia' => ['emergency_contact_name', 'emergency_contact_phone', 'emergency_relationship'],
    ];

    // Pestaña por defecto
    $initialTab = 'datos_personales';

    // Si hay errores, buscamos en qué grupo están para abrir esa pestaña automáticamente
    foreach ($errorGroups as $tabName => $fields) {
        if ($errors->hasAny($fields)) {
            $initialTab = $tabName;
            break;
        }
    }

    // Detectar errores por pestaña
    $hasErrorAntecedentes = $errors->hasAny($errorGroups['antecedentes']);
    $hasErrorInfoGeneral = $errors->hasAny($errorGroups['informacion_general']);
    $hasErrorContacto = $errors->hasAny($errorGroups['contacto_emergencia']);
@endphp

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

    <form action="{{ route('admin.patients.update', $patient) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Encabezado con foto y acciones --}}
        <x-wire-card class="mb-8">
            <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center">
                <div class="flex items-center">
                    <img src="{{ $patient->user->profile_photo_url }}"
                         alt="{{ $patient->user->name }}"
                         class="h-20 w-20 rounded-full object-cover object-center">
                    <div class="ml-4">
                        <p class="text-2xl font-bold text-gray-900">{{ $patient->user->name }}</p>
                    </div>
                </div>
                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.patients.index') }}">
                        Volver
                    </x-wire-button>
                    <x-wire-button type="submit">
                        <i class="fa-solid fa-check mr-1"></i>
                        Guardar cambios
                    </x-wire-button>
                </div>
            </div>
        </x-wire-card>

        {{-- Tabs de navegación y contenido --}}
        <x-wire-card>
            <div x-data="{ tab: '{{ $initialTab }}' }">

                {{-- Menú de pestañas --}}
                <div class="border-b border-gray-200">
                    <ul class="flex flex-wrap -mb-px text-sm font-medium text-gray-500">

                        {{-- Tab 1: Datos Personales --}}
                        <li>
                            <a href="#"
                               @click.prevent="tab = 'datos_personales'"
                               :class="{
                                   'text-blue-600 border-blue-600 active': tab === 'datos_personales',
                                   'text-gray-500 border-transparent hover:text-blue-600 hover:border-gray-300': tab !== 'datos_personales'
                               }"
                               class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg transition-colors duration-200 grow">
                                <i class="fa-solid fa-user mr-2"></i>
                                Datos Personales
                            </a>
                        </li>

                        {{-- Tab 2: Antecedentes --}}
                        @php $hasError = $hasErrorAntecedentes; @endphp
                        <li>
                            <a href="#"
                               @click.prevent="tab = 'antecedentes'"
                               :class="{
                                   'text-red-600 border-red-600': {{ $hasError ? 'true' : 'false' }} && tab !== 'antecedentes',
                                   'text-blue-600 border-blue-600 active': tab === 'antecedentes' && !{{ $hasError ? 'true' : 'false' }},
                                   'text-red-600 border-red-600 active': tab === 'antecedentes' && {{ $hasError ? 'true' : 'false' }},
                                   'text-gray-500 border-transparent hover:text-blue-600 hover:border-gray-300': tab !== 'antecedentes' && !{{ $hasError ? 'true' : 'false' }}
                               }"
                               class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg transition-colors duration-200 grow">
                                <i class="fa-solid fa-file-lines mr-2"></i>
                                Antecedentes
                                @if($hasError)
                                    <i class="fa-solid fa-circle-exclamation ms-2 animate-pulse"></i>
                                @endif
                            </a>
                        </li>

                        {{-- Tab 3: Información General --}}
                        @php $hasError = $hasErrorInfoGeneral; @endphp
                        <li>
                            <a href="#"
                               @click.prevent="tab = 'informacion_general'"
                               :class="{
                                   'text-red-600 border-red-600': {{ $hasError ? 'true' : 'false' }} && tab !== 'informacion_general',
                                   'text-blue-600 border-blue-600 active': tab === 'informacion_general' && !{{ $hasError ? 'true' : 'false' }},
                                   'text-red-600 border-red-600 active': tab === 'informacion_general' && {{ $hasError ? 'true' : 'false' }},
                                   'text-gray-500 border-transparent hover:text-blue-600 hover:border-gray-300': tab !== 'informacion_general' && !{{ $hasError ? 'true' : 'false' }}
                               }"
                               class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg transition-colors duration-200 grow">
                                <i class="fa-solid fa-circle-info mr-2"></i>
                                Información General
                                @if($hasError)
                                    <i class="fa-solid fa-circle-exclamation ms-2 animate-pulse"></i>
                                @endif
                            </a>
                        </li>

                        {{-- Tab 4: Contacto de Emergencia --}}
                        @php $hasError = $hasErrorContacto; @endphp
                        <li>
                            <a href="#"
                               @click.prevent="tab = 'contacto_emergencia'"
                               :class="{
                                   'text-red-600 border-red-600': {{ $hasError ? 'true' : 'false' }} && tab !== 'contacto_emergencia',
                                   'text-blue-600 border-blue-600 active': tab === 'contacto_emergencia' && !{{ $hasError ? 'true' : 'false' }},
                                   'text-red-600 border-red-600 active': tab === 'contacto_emergencia' && {{ $hasError ? 'true' : 'false' }},
                                   'text-gray-500 border-transparent hover:text-blue-600 hover:border-gray-300': tab !== 'contacto_emergencia' && !{{ $hasError ? 'true' : 'false' }}
                               }"
                               class="inline-flex items-center justify-center p-4 border-b-2 rounded-t-lg transition-colors duration-200 grow">
                                <i class="fa-solid fa-heart mr-2"></i>
                                Contacto de Emergencia
                                @if($hasError)
                                    <i class="fa-solid fa-circle-exclamation ms-2 animate-pulse"></i>
                                @endif
                            </a>
                        </li>

                    </ul>
                </div>

                {{-- Contenido de los tabs --}}
                <div class="px-4 py-4 mt-4">

                    {{-- Contenido Tab 1: Datos Personales --}}
                    <div x-show="tab === 'datos_personales'" style="display: none;">
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-6 rounded-r-lg shadow-sm">
                            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4">
                                {{-- Lado izquierdo: información --}}
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fa-solid fa-user-gear text-blue-500 text-xl mt-1"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-bold text-blue-800">Edición de cuenta de usuario</h3>
                                        <div class="mt-1 text-sm text-gray-600">
                                            <p>La <strong>información de acceso</strong> (nombre, email y contraseña) debe gestionarse desde la cuenta de usuario asociada.</p>
                                        </div>
                                    </div>
                                </div>
                                {{-- Lado derecho: botón de acción --}}
                                <div class="flex-shrink-0">
                                    <x-wire-button primary href="{{ route('admin.users.edit', $patient->user) }}" target="_blank">
                                        Editar usuario
                                        <i class="fa-solid fa-arrow-up-right-from-square ml-1"></i>
                                    </x-wire-button>
                                </div>
                            </div>
                        </div>

                        {{-- Datos del paciente (solo lectura) --}}
                        <div class="grid lg:grid-cols-2 gap-4">
                            <div>
                                <span class="text-gray-900 text-sm font-semibold ml-1">Teléfono:</span>
                                <span class="text-gray-500 text-sm ml-1">{{ $patient->user->phone ?? 'No registrado' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-900 text-sm font-semibold ml-1">Email:</span>
                                <span class="text-gray-500 text-sm ml-1">{{ $patient->user->email }}</span>
                            </div>
                            <div>
                                <span class="text-gray-900 text-sm font-semibold ml-1">Dirección:</span>
                                <span class="text-gray-500 text-sm ml-1">{{ $patient->user->address ?? 'No registrada' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Contenido Tab 2: Antecedentes --}}
                    <div x-show="tab === 'antecedentes'" style="display: none;">
                        <div class="grid lg:grid-cols-2 gap-4">
                            <div>
                                <x-wire-textarea
                                    label="Alergias conocidas"
                                    name="allergies"
                                    placeholder="Ej: mariscos, penicilina..."
                                >{{ old('allergies', $patient->allergies) }}</x-wire-textarea>
                            </div>
                            <div>
                                <x-wire-textarea
                                    label="Enfermedades crónicas"
                                    name="chronic_diseases"
                                    placeholder="Ej: diabetes, hipertensión..."
                                >{{ old('chronic_diseases', $patient->chronic_diseases) }}</x-wire-textarea>
                            </div>
                            <div>
                                <x-wire-textarea
                                    label="Antecedentes quirúrgicos"
                                    name="surgery_history"
                                    placeholder="Ej: apendicectomía 2020..."
                                >{{ old('surgery_history', $patient->surgery_history) }}</x-wire-textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Contenido Tab 3: Información General --}}
                    <div x-show="tab === 'informacion_general'" style="display: none;">
                        <div class="grid lg:grid-cols-2 gap-4">
                            <div>
                                <x-wire-native-select label="Tipo de sangre" class="mb-4" name="blood_type_id">
                                    <option value="">Selecciona un tipo de sangre</option>
                                    @foreach($bloodTypes as $bloodType)
                                        <option value="{{ $bloodType->id }}"
                                            @if(old('blood_type_id', $patient->blood_type_id) == $bloodType->id) selected @endif
                                        >{{ $bloodType->name }}</option>
                                    @endforeach
                                </x-wire-native-select>
                            </div>
                            <div>
                                <x-wire-textarea
                                    label="Observaciones"
                                    name="observations"
                                    placeholder="Observaciones del médico..."
                                >{{ old('observations', $patient->observations) }}</x-wire-textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Contenido Tab 4: Contacto de Emergencia --}}
                    <div x-show="tab === 'contacto_emergencia'" style="display: none;">
                        <div class="space-y-4">
                            <x-wire-input
                                label="Nombre de contacto"
                                name="emergency_contact_name"
                                placeholder="Ej: familiar, amigo..."
                                value="{{ old('emergency_contact_name', $patient->emergency_contact_name) }}"
                            />

                            <x-wire-maskable
                                label="Teléfono de contacto"
                                name="emergency_contact_phone"
                                mask="(###) ###-####"
                                placeholder="(999) 999-9999"
                                value="{{ old('emergency_contact_phone', $patient->emergency_contact_phone) }}"
                            />

                            <x-wire-input
                                label="Relación con el contacto"
                                name="emergency_relationship"
                                placeholder="Ej: familiar, amigo, etc."
                                value="{{ old('emergency_relationship', $patient->emergency_relationship) }}"
                            />
                        </div>
                    </div>

                </div>
            </div>
        </x-wire-card>
    </form>

</x-admin-layout>
