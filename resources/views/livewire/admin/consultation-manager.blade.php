<div>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        {{-- Pestañas --}}
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px px-6" aria-label="Tabs">
                <button wire:click="setTab('consulta')"
                        class="{{ $activeTab === 'consulta' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8">
                    <i class="fa-solid fa-notes-medical mr-2"></i>Consulta
                </button>
                <button wire:click="setTab('receta')"
                        class="{{ $activeTab === 'receta' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    <i class="fa-solid fa-prescription-bottle-medical mr-2"></i>Receta
                </button>
            </nav>
        </div>

        <div class="p-6">
            {{-- Tab: Consulta --}}
            @if($activeTab === 'consulta')
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Estado de la Cita <span class="text-red-500">*</span></label>
                        <select wire:model="appointmentStatus" class="w-full md:w-1/3 border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                            <option value="programado">📅 Programado</option>
                            <option value="completado">✅ Completado</option>
                            <option value="cancelado">❌ Cancelado</option>
                        </select>
                        @error('appointmentStatus') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Diagnóstico <span class="text-red-500">*</span></label>
                        <textarea wire:model="diagnosis" rows="3"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Describa el diagnóstico del paciente aquí..."></textarea>
                        @error('diagnosis') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tratamiento <span class="text-red-500">*</span></label>
                        <textarea wire:model="treatment" rows="3"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Describa el tratamiento recomendado aquí..."></textarea>
                        @error('treatment') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notas (Opcional)</label>
                        <textarea wire:model="notes" rows="3"
                                  class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"
                                  placeholder="Agregue notas adicionales sobre la consulta..."></textarea>
                        @error('notes') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            @endif

            {{-- Tab: Receta --}}
            @if($activeTab === 'receta')
                <div class="space-y-4 bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="hidden md:grid grid-cols-12 gap-4 text-sm font-medium text-gray-600 mb-2 px-2">
                        <div class="col-span-5">Medicamento</div>
                        <div class="col-span-3">Dosis</div>
                        <div class="col-span-3">Frecuencia / Duración</div>
                        <div class="col-span-1 text-center">Acción</div>
                    </div>

                    @foreach($prescriptions as $index => $prescription)
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-center bg-white p-3 rounded-lg border shadow-sm">
                            <div class="md:col-span-5">
                                <label class="md:hidden text-xs text-gray-500">Medicamento</label>
                                <input type="text" wire:model="prescriptions.{{ $index }}.medication"
                                       class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500"
                                       placeholder="Ej: Amoxicilina 500mg">
                                @error('prescriptions.'.$index.'.medication') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="md:col-span-3">
                                <label class="md:hidden text-xs text-gray-500">Dosis</label>
                                <input type="text" wire:model="prescriptions.{{ $index }}.dosage"
                                       class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500"
                                       placeholder="Ej: 1 cada 8 horas">
                                @error('prescriptions.'.$index.'.dosage') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div class="md:col-span-3">
                                <label class="md:hidden text-xs text-gray-500">Frecuencia / Duración</label>
                                <input type="text" wire:model="prescriptions.{{ $index }}.frequency"
                                       class="w-full text-sm border-gray-300 rounded-md focus:ring-blue-500"
                                       placeholder="Ej: por 7 días">
                            </div>
                            <div class="md:col-span-1 text-center mt-2 md:mt-0">
                                <button type="button" wire:click="removeMedication({{ $index }})"
                                        class="text-red-500 hover:text-red-700 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors"
                                        title="Eliminar medicamento">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <div class="mt-4">
                        <button type="button" wire:click="addMedication" 
                                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50">
                            <i class="fa-solid fa-plus mr-2"></i> Añadir Medicamento
                        </button>
                    </div>
                </div>
            @endif

            {{-- Botón de Guardar en la esquina inferior derecha --}}
            <div class="mt-8 flex justify-end">
                <button type="button" wire:click="save"
                        class="inline-flex items-center px-5 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 shadow-sm transition-colors">
                    <i class="fa-solid fa-save mr-2"></i>
                    Guardar Consulta
                </button>
            </div>
        </div>
    </div>

    {{-- Modal Historia Médica del Paciente --}}
    @if($showHistoryModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeHistoryModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-start border-b pb-3 mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                <i class="fa-solid fa-notes-medical text-blue-500 mr-2"></i>Historia médica del paciente
                            </h3>
                            <button wire:click="closeHistoryModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Cerrar</span>
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 text-sm">
                            <div>
                                <span class="block text-gray-500 mb-1">Tipo de sangre:</span>
                                <span class="font-semibold text-gray-900">{{ $appointment->patient->bloodType->name ?? 'No registrado' }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 mb-1">Alergias:</span>
                                <span class="font-semibold text-gray-900">{{ $appointment->patient->allergies ?? 'No registradas' }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 mb-1">Enfermedades crónicas:</span>
                                <span class="font-semibold text-gray-900">{{ $appointment->patient->chronic_diseases ?? 'No registradas' }}</span>
                            </div>
                            <div>
                                <span class="block text-gray-500 mb-1">Antecedentes quirúrgicos:</span>
                                <span class="font-semibold text-gray-900">{{ $appointment->patient->surgical_history ?? 'No registrados' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <a href="{{ route('admin.patients.edit', $appointment->patient) }}" target="_blank"
                           class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                            Ver / Editar Historia Médica
                        </a>
                        <button type="button" wire:click="closeHistoryModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Consultas Anteriores --}}
    @if($showPastConsultationsModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closePastConsultationsModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-start border-b pb-3 mb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">
                                <i class="fa-solid fa-clock-rotate-left text-blue-500 mr-2"></i>Consultas Anteriores
                            </h3>
                            <button wire:click="closePastConsultationsModal" class="bg-white rounded-md text-gray-400 hover:text-gray-500">
                                <span class="sr-only">Cerrar</span>
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        
                        <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                            @if(count($pastConsultations) > 0)
                                @foreach($pastConsultations as $consultation)
                                    <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center gap-2 text-blue-600 font-medium">
                                                <i class="fa-solid fa-calendar-day"></i>
                                                {{ $consultation->appointment->date->format('d/m/Y') }} a las {{ \Carbon\Carbon::parse($consultation->appointment->start_time)->format('H:i') }}
                                            </div>
                                            <a href="{{ route('admin.consultations.show', $consultation->appointment_id) }}" target="_blank"
                                               class="text-xs border border-blue-500 text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-md font-medium">
                                                Consultar Detalle
                                            </a>
                                        </div>
                                        <div class="text-sm text-gray-500 mb-3">
                                            Atendido por: {{ $consultation->appointment->doctor->user->name }}
                                        </div>
                                        
                                        <div class="bg-white border rounded-md p-3 text-sm flex flex-col gap-1.5 shadow-sm">
                                            <div><span class="font-semibold text-gray-700">Diagnóstico:</span> {{ \Illuminate\Support\Str::limit($consultation->diagnosis, 150) }}</div>
                                            <div><span class="font-semibold text-gray-700">Tratamiento:</span> {{ \Illuminate\Support\Str::limit($consultation->treatment, 150) }}</div>
                                            @if($consultation->notes)
                                                <div><span class="font-semibold text-gray-700">Notas:</span> {{ \Illuminate\Support\Str::limit($consultation->notes, 100) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-12 text-gray-500">
                                    <i class="fa-solid fa-folder-open text-gray-300 text-4xl mb-3"></i>
                                    <p>No se encontraron consultas anteriores para el paciente.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
