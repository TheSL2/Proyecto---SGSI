<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle del ítem de checklist') }}
            </h2>
            <a href="/checklists" class="text-sm text-indigo-600 hover:text-indigo-800">
                &larr; {{ __('Volver al listado') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="checklistShow({{ (int) $id }})">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div x-show="!loading && !error && item" x-cloak class="flex items-center justify-end gap-4">
                <a :href="`/evidencias/create?checklist_id=${item?.id}`" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    {{ __('+ Nueva evidencia') }}
                </a>
                <a
                    x-show="esNoConforme"
                    x-cloak
                    :href="`/hallazgos/create?checklist_id=${item?.id}`"
                    class="text-sm text-red-600 hover:text-red-800 font-medium"
                >
                    {{ __('+ Crear Hallazgo vinculado') }}
                </a>
                <a :href="`/checklists/${item?.id}/edit`" class="text-sm text-gray-500 hover:text-gray-700">
                    {{ __('Editar') }}
                </a>
                <button @click="eliminar()" :disabled="eliminando" class="text-sm text-red-600 hover:text-red-800 disabled:opacity-50">
                    {{ __('Eliminar') }}
                </button>
            </div>

            <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

            <div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
                {{ __('Cargando...') }}
            </div>

            <div x-show="!loading && !error && item" x-cloak class="space-y-6">

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">{{ __('Auditoría') }}</h4>
                            <p class="text-sm text-gray-800" x-text="item?.auditoria?.titulo"></p>
                        </div>
                        <span
                            class="inline-flex px-3 py-1 text-xs font-semibold rounded-full shrink-0"
                            :class="badgeClase(item?.estado_cumplimiento)"
                            x-text="item?.estado_cumplimiento"
                        ></span>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Requisito ISO') }}</h4>
                    <p class="text-sm text-gray-800">
                        <span class="font-medium" x-text="item?.requisito_iso?.codigo"></span>
                        — <span x-text="item?.requisito_iso?.descripcion"></span>
                    </p>
                    <p class="text-xs text-gray-400 mt-1" x-text="item?.requisito_iso?.categoria"></p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Observaciones') }}</h4>
                    <p class="text-sm text-gray-800" x-text="item?.observaciones || '—'"></p>
                </div>

                <!-- SECCIÓN DE HALLAZGOS ASOCIADOS -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">{{ __('Hallazgos Vinculados') }}</h4>
                    <template x-if="item?.hallazgos && item.hallazgos.length > 0">
                        <div class="divide-y divide-gray-100">
                            <template x-for="h in item.hallazgos" :key="h.id">
                                <div class="py-2 flex items-center justify-between">
                                    <div>
                                        <span class="font-semibold text-sm text-gray-700" x-text="h.tipo_hallazgo"></span>
                                        <p class="text-xs text-gray-500" x-text="h.descripcion"></p>
                                    </div>
                                    <a :href="`/hallazgos/${h.id}`" class="text-xs text-indigo-600 hover:text-indigo-800">
                                        {{ __('Ver hallazgo') }} &rarr;
                                    </a>
                                </div>
                            </template>
                        </div>
                    </template>
                    <template x-if="!item?.hallazgos || item.hallazgos.length === 0">
                        <p class="text-xs text-gray-400">{{ __('No hay hallazgos registrados para este ítem.') }}</p>
                    </template>
                </div>

                <!-- SECCIÓN DE EVIDENCIAS ASOCIADAS -->
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-3">{{ __('Evidencias Cargadas') }}</h4>
                    <template x-if="item?.evidencias && item.evidencias.length > 0">
                        <ul class="divide-y divide-gray-100 text-sm">
                            <template x-for="ev in item.evidencias" :key="ev.id">
                                <li class="py-2 flex items-center justify-between">
                                    <span class="text-gray-700" x-text="ev.nombre_archivo || ev.ruta_almacenamiento || ('Evidencia #' + ev.id)"></span>
                                    <a :href="`/evidencias/${ev.id}`" class="text-xs text-indigo-600 hover:text-indigo-800">
                                        {{ __('Ver evidencia') }}
                                    </a>
                                </li>
                            </template>
                        </ul>
                    </template>
                    <template x-if="!item?.evidencias || item.evidencias.length === 0">
                        <p class="text-xs text-gray-400">{{ __('No se han adjuntado evidencias digitales aún.') }}</p>
                    </template>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>