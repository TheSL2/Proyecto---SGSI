<div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
    {{ __('Cargando...') }}
</div>

<div x-show="!loading" x-cloak class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

    <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Ítem de checklist origen') }}</label>
        <select x-model="form.checklist_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">{{ __('Selecciona un ítem de checklist') }}</option>
            @foreach ($checklists as $checklist)
                <option value="{{ $checklist->id }}">
                    {{ $checklist->auditoria->titulo ?? '—' }} — {{ $checklist->requisitoIso->codigo ?? '' }} ({{ $checklist->estado_cumplimiento }})
                </option>
            @endforeach
        </select>
        <p class="text-xs text-gray-400 mt-1">
            {{ __('RN-HL-02: el hallazgo debe originarse de un ítem de checklist evaluado, del cual hereda la trazabilidad a la auditoría y al requisito ISO.') }}
        </p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Tipo de hallazgo') }}</label>
        <select x-model="form.tipo_hallazgo" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <template x-for="opcion in opcionesTipo" :key="opcion">
                <option :value="opcion" x-text="opcion"></option>
            </template>
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Cláusula o control incumplido') }}</label>
        <input type="text" x-model="form.clausula_o_control" placeholder="{{ __('Ej. A.8.9 o Cláusula 9.2') }}" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Descripción') }}</label>
        <textarea x-model="form.descripcion" rows="4" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Estado') }}</label>
            <select x-model="form.estado" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <option value="">{{ __('Sin definir') }}</option>
                <template x-for="opcion in opcionesEstado" :key="opcion">
                    <option :value="opcion" x-text="opcion"></option>
                </template>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Estado de notificación') }}</label>
            <select x-model="form.estado_notificacion" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                <template x-for="opcion in opcionesEstadoNotificacion" :key="opcion">
                    <option :value="opcion" x-text="opcion"></option>
                </template>
            </select>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Fecha de notificación') }}</label>
        <input type="date" x-model="form.fecha_notificacion" class="w-full sm:w-auto rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
        <p class="text-xs text-gray-400 mt-1">
            {{ __('RN-HL-03: el auditado tiene 5 días hábiles desde el cierre de campo para aceptar la notificación.') }}
        </p>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2 border-t">
        <a href="/hallazgos" class="text-sm text-gray-500 hover:text-gray-700">
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
