<x-admin-layout
    title="Detalle del Paciente | simify"
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
            'name' => 'Detalle'
        ]
    ]">

    <x-slot name="action">
        <x-wire-button amber href="{{ route('admin.patients.edit', $patient) }}">
            <i class="fa-solid fa-pen-to-square"></i>
            Editar
        </x-wire-button>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Columna izquierda: Info del usuario --}}
        <div class="lg:col-span-1">
            <x-wire-card>
                <div class="text-center mb-4">
                    <div class="w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fa-solid fa-user-injured text-blue-600 text-4xl"></i>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $patient->user->name }}</h2>
                    <p class="text-gray-500">{{ $patient->user->email }}</p>
                </div>

                <div class="space-y-3 border-t pt-4">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-id-card text-gray-400 w-5"></i>
                        <div>
                            <span class="text-sm text-gray-500">ID:</span>
                            <p class="font-medium">{{ $patient->user->id_number ?? 'No registrado' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-phone text-gray-400 w-5"></i>
                        <div>
                            <span class="text-sm text-gray-500">Teléfono:</span>
                            <p class="font-medium">{{ $patient->user->phone ?? 'No registrado' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-location-dot text-gray-400 w-5"></i>
                        <div>
                            <span class="text-sm text-gray-500">Dirección:</span>
                            <p class="font-medium">{{ $patient->user->address ?? 'No registrada' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-droplet text-red-400 w-5"></i>
                        <div>
                            <span class="text-sm text-gray-500">Tipo de Sangre:</span>
                            <p class="font-medium">
                                @if($patient->bloodType)
                                    <span class="bg-red-100 text-red-800 px-2 py-1 rounded-full text-sm">
                                        {{ $patient->bloodType->name }}
                                    </span>
                                @else
                                    <span class="text-gray-400">No registrado</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </x-wire-card>

            {{-- Contacto de emergencia --}}
            <x-wire-card class="mt-6">
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    <i class="fa-solid fa-phone-volume text-red-500 mr-2"></i>
                    Contacto de Emergencia
                </h3>

                @if($patient->emergency_contact_name || $patient->emergency_contact_phone)
                    <div class="space-y-2">
                        <div>
                            <span class="text-sm text-gray-500">Nombre:</span>
                            <p class="font-medium">{{ $patient->emergency_contact_name ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Teléfono:</span>
                            <p class="font-medium">{{ $patient->emergency_contact_phone ?? 'No registrado' }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">Parentesco:</span>
                            <p class="font-medium">{{ $patient->emergency_relationship ?? 'No especificado' }}</p>
                        </div>
                    </div>
                @else
                    <p class="text-gray-400 text-center py-4">
                        <i class="fa-solid fa-exclamation-triangle mr-2"></i>
                        No hay contacto de emergencia registrado
                    </p>
                @endif
            </x-wire-card>
        </div>

        {{-- Columna derecha: Info médica --}}
        <div class="lg:col-span-2">
            <x-wire-card>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">
                    <i class="fa-solid fa-notes-medical text-blue-500 mr-2"></i>
                    Historial Médico
                </h3>

                <div class="space-y-6">
                    {{-- Alergias --}}
                    <div>
                        <h4 class="flex items-center gap-2 font-medium text-gray-700 mb-2">
                            <i class="fa-solid fa-allergies text-orange-500"></i>
                            Alergias
                        </h4>
                        <div class="bg-orange-50 p-4 rounded-lg">
                            @if($patient->allergies)
                                <p class="text-gray-700">{{ $patient->allergies }}</p>
                            @else
                                <p class="text-gray-400 italic">Sin alergias registradas</p>
                            @endif
                        </div>
                    </div>

                    {{-- Enfermedades Crónicas --}}
                    <div>
                        <h4 class="flex items-center gap-2 font-medium text-gray-700 mb-2">
                            <i class="fa-solid fa-heart-pulse text-red-500"></i>
                            Enfermedades Crónicas
                        </h4>
                        <div class="bg-red-50 p-4 rounded-lg">
                            @if($patient->chronic_diseases)
                                <p class="text-gray-700">{{ $patient->chronic_diseases }}</p>
                            @else
                                <p class="text-gray-400 italic">Sin enfermedades crónicas registradas</p>
                            @endif
                        </div>
                    </div>

                    {{-- Historial de Cirugías --}}
                    <div>
                        <h4 class="flex items-center gap-2 font-medium text-gray-700 mb-2">
                            <i class="fa-solid fa-syringe text-purple-500"></i>
                            Historial de Cirugías
                        </h4>
                        <div class="bg-purple-50 p-4 rounded-lg">
                            @if($patient->surgery_history)
                                <p class="text-gray-700">{{ $patient->surgery_history }}</p>
                            @else
                                <p class="text-gray-400 italic">Sin cirugías registradas</p>
                            @endif
                        </div>
                    </div>

                    {{-- Observaciones --}}
                    <div>
                        <h4 class="flex items-center gap-2 font-medium text-gray-700 mb-2">
                            <i class="fa-solid fa-clipboard text-blue-500"></i>
                            Observaciones
                        </h4>
                        <div class="bg-blue-50 p-4 rounded-lg">
                            @if($patient->observations)
                                <p class="text-gray-700">{{ $patient->observations }}</p>
                            @else
                                <p class="text-gray-400 italic">Sin observaciones</p>
                            @endif
                        </div>
                    </div>
                </div>
            </x-wire-card>

            {{-- Fechas --}}
            <x-wire-card class="mt-6">
                <div class="flex justify-between text-sm text-gray-500">
                    <div>
                        <i class="fa-solid fa-calendar-plus mr-1"></i>
                        Registrado: {{ $patient->created_at->format('d/m/Y H:i') }}
                    </div>
                    <div>
                        <i class="fa-solid fa-calendar-check mr-1"></i>
                        Última actualización: {{ $patient->updated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            </x-wire-card>
        </div>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.patients.index') }}" class="text-blue-600 hover:text-blue-800">
            <i class="fa-solid fa-arrow-left mr-2"></i>
            Volver a la lista de pacientes
        </a>
    </div>

</x-admin-layout>
