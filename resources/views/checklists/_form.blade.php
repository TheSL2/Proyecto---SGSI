{{-- Espera: $auditorias, $requisitos (colecciones de la ruta web) y el x-data checklistForm() del padre --}}

<div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
    {{ __('Cargando...') }}
</div>

<div x-show="!loading" x-cloak class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

    <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Auditoría') }}</label>
        <select x-model="form.auditoria_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">{{ __('Selecciona una auditoría') }}</option>
            @foreach ($auditorias as $auditoria)
                <option value="{{ $auditoria->id }}">{{ $auditoria->titulo }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Requisito ISO') }}</label>
        <select x-model="form.requisito_iso_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">{{ __('Selecciona un requisito') }}</option>
            @foreach ($requisitos as $requisito)
                <option value="{{ $requisito->id }}">{{ $requisito->codigo }} — {{ $requisito->descripcion }}</option>
            @endforeach
        </select>
        <p class="text-xs text-gray-400 mt-1">
            {{ __('Solo se listan requisitos marcados como aplicables en la Declaración de Aplicabilidad (SoA).') }}
        </p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Estado de cumplimiento') }}</label>
        <select x-model="form.estado_cumplimiento" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <template x-for="opcion in opcionesEstado" :key="opcion">
                <option :value="opcion" x-text="opcion"></option>
            </template>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Observaciones') }}</label>
        <textarea x-model="form.observaciones" rows="3" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('Justificación (SoA)') }}
            <span x-show="requiereJustificacion" x-cloak class="text-red-500">*</span>
        </label>
        <textarea x-model="form.justificacion" rows="3" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        <p class="text-xs text-gray-400 mt-1" x-show="requiereJustificacion" x-cloak>
            {{ __('Obligatoria cuando el estado es "No Aplicable".') }}
        </p>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2 border-t">
        <a href="/checklists" class="text-sm text-gray-500 hover:text-gray-700">
            {{ __('Cancelar') }}
        </a>
        <button
            @click="guardar()"
            :disabled="guardando"
            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm text-white hover:bg-indigo-700 disabled:opacity-50"
        >
            <span x-show="!guardando">{{ __('Guardar') }}</span>
            <span x-show="guardando" x-cloak>{{ __('Guardando...') }}</span>
        </button>
    </div>

</div>
