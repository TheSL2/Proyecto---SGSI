<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nueva evidencia digital') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="evidenciaForm()">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

                <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>
                <div x-show="exito" x-cloak class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-md p-4" x-text="exito"></div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Ítem de checklist (opcional)') }}</label>
                    <select x-model="form.checklist_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('Ninguno') }}</option>
                        @foreach ($checklists as $checklist)
                            <option value="{{ $checklist->id }}" :selected="String('{{ $checklist->id }}') === String(form.checklist_id)">
                                {{ $checklist->auditoria->titulo ?? '—' }} — {{ $checklist->requisitoIso->codigo ?? '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Selector de Hallazgo en evidencias/create.blade.php -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Hallazgo (opcional)') }}</label>
                    <select x-model="form.hallazgo_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">{{ __('Ninguno') }}</option>
                        @foreach ($hallazgos as $hallazgo)
                            <option value="{{ $hallazgo->id }}" :selected="String('{{ $hallazgo->id }}') === String(form.hallazgo_id)">
                                {{ $hallazgo->tipo_hallazgo }} — {{ $hallazgo->clausula_o_control }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Archivo') }}</label>
                    <input
                        type="file"
                        x-ref="fileInput"
                        @change="seleccionarArchivo($event)"
                        accept=".pdf,.png,.jpg,.jpeg,.xlsx,.docx"
                        class="w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                    >
                    <p class="text-xs text-gray-400 mt-1">
                        {{ __('RN-EV-01: PDF, PNG, JPG, XLSX o DOCX, máximo 10MB. Se calcula un hash SHA-256 al subir (RN-EV-03).') }}
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2 border-t">
                    <a href="/evidencias" class="text-sm text-gray-500 hover:text-gray-700">
                        {{ __('Cancelar') }}
                    </a>
                    <button
                        type="button"
                        @click="subir()"
                        :disabled="guardando || !archivo"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm text-white hover:bg-indigo-700 disabled:opacity-50"
                    >
                        <span x-show="!guardando">{{ __('Subir evidencia') }}</span>
                        <span x-show="guardando" x-cloak>{{ __('Subiendo...') }}</span>
                    </button>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>