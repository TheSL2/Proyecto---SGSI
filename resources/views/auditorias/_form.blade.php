{{-- Espera: $areas, $auditores (colecciones de la ruta web) y el x-data auditoriaForm() del padre --}}

<div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
    {{ __('Cargando...') }}
</div>

<div x-show="!loading" x-cloak class="bg-white shadow-sm sm:rounded-lg p-6 space-y-6">

    <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Título') }}</label>
        <input type="text" x-model="form.titulo" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Fecha inicio') }}</label>
            <input type="date" x-model="form.fecha_inicio" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Fecha fin') }}</label>
            <input type="date" x-model="form.fecha_fin" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Objetivo') }}</label>
        <textarea x-model="form.objetivo" rows="2" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Alcance') }}</label>
        <textarea x-model="form.alcance" rows="2" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Estado') }}</label>
        <select x-model="form.estado" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <template x-for="opcion in opcionesEstado" :key="opcion">
                <option :value="opcion" x-text="opcion"></option>
            </template>
        </select>
        <p class="text-xs text-gray-400 mt-1">
            {{ __('El flujo de estados es estricto (RN-PA-03): solo puedes quedarte igual o avanzar un paso a la vez.') }}
        </p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Auditor líder') }}</label>
        <select x-model="form.auditor_lider_id" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
            <option value="">{{ __('Sin asignar') }}</option>
            @foreach ($auditores as $auditor)
                <option value="{{ $auditor->id }}">{{ $auditor->name }}</option>
            @endforeach
        </select>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Equipo auditor') }}</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach ($auditores as $auditor)
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" value="{{ $auditor->id }}" x-model.number="form.equipo_auditor" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    {{ $auditor->name }}
                </label>
            @endforeach
            @if ($auditores->isEmpty())
                <p class="text-sm text-gray-400">{{ __('No hay usuarios con rol Auditor todavía.') }}</p>
            @endif
        </div>
        <p class="text-xs text-gray-400 mt-1">
            {{ __('Un auditor no puede evaluar (como líder o parte del equipo) un área a la que pertenece (RN-USUARIOS Y ROLES-01).') }}
        </p>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Áreas evaluadas') }}</label>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            @foreach ($areas as $area)
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input type="checkbox" value="{{ $area->id }}" x-model.number="form.areas" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    {{ $area->nombre }}
                </label>
            @endforeach
            @if ($areas->isEmpty())
                <p class="text-sm text-gray-400">{{ __('No hay áreas registradas todavía.') }}</p>
            @endif
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('Conclusiones') }}</label>
        <textarea x-model="form.conclusiones" rows="3" class="w-full rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
        <p class="text-xs text-gray-400 mt-1">
            {{ __('Requeridas para poder generar el informe oficial cuando la auditoría esté Cerrada o En Revisión de Informe.') }}
        </p>
    </div>

    <div class="flex items-center justify-end gap-3 pt-2 border-t">
        <a href="{{ route('web.auditorias.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
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
