{{-- Espera: $hallazgos, $usuarios (de la ruta web) y el x-data accionCorrectivaForm() del padre --}}

<div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
    {{ __('Cargando...') }}
</div>

<div x-show="!loading" x-cloak class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

    <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Hallazgo origen') }}</label>
        <select x-model="form.hallazgo_id" @change="cargarEvidenciasDelHallazgo(form.hallazgo_id)" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">{{ __('Selecciona un hallazgo') }}</option>
            @foreach ($hallazgos as $hallazgo)
                <option value="{{ $hallazgo->id }}">
                    {{ $hallazgo->tipo_hallazgo }} — {{ $hallazgo->clausula_o_control }} ({{ $hallazgo->checklist->auditoria->titulo ?? '—' }})
                </option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Análisis de causa raíz') }}</label>
        <textarea x-model="form.causa_raiz" rows="3" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        <p class="text-xs text-gray-400 mt-1">
            {{ __('RN-AC-01: obligatorio si el hallazgo origen es una No Conformidad Mayor o Menor.') }}
        </p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Descripción de la acción') }}</label>
        <textarea x-model="form.descripcion_accion" rows="3" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Responsable') }}</label>
            <select x-model="form.responsable_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">{{ __('Selecciona un responsable') }}</option>
                @foreach ($usuarios as $usuario)
                    <option value="{{ $usuario->id }}">{{ $usuario->name }} ({{ $usuario->rol }})</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Fecha límite') }}</label>
            <input type="date" x-model="form.fecha_limite" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4 border-t">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Estado') }}</label>
            <select x-model="form.estado" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <template x-for="opcion in opcionesEstado" :key="opcion">
                    <option :value="opcion" x-text="opcion"></option>
                </template>
            </select>
        </div>
        <div x-show="form.estado === 'Verificada'" x-cloak>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Evidencia de cierre') }}</label>
            <select x-model="form.evidencia_cierre_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">{{ __('Selecciona una evidencia') }}</option>
                <template x-for="ev in evidenciasDelHallazgo" :key="ev.id">
                    <option :value="ev.id" x-text="ev.nombre_archivo"></option>
                </template>
            </select>
            <p class="text-xs text-gray-400 mt-1" x-show="!evidenciasDelHallazgo.length">
                {{ __('Este hallazgo todavía no tiene evidencias digitales subidas.') }}
            </p>
        </div>
    </div>
    <p class="text-xs text-gray-400" x-show="form.estado === 'Verificada'" x-cloak>
        {{ __('RN-AC-02: el responsable de la acción no puede verificar su propio cierre; debe hacerlo un Auditor, Consultor o Administrador distinto.') }}
    </p>

    <div class="flex items-center justify-end gap-3 pt-2 border-t">
        <a href="/acciones-correctivas" class="text-sm text-gray-500 hover:text-gray-700">
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
