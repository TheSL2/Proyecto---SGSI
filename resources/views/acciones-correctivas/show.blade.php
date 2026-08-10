<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle de la acción correctiva') }}
            </h2>
            <a href="/acciones-correctivas" class="text-sm text-indigo-600 hover:text-indigo-800">
                &larr; {{ __('Volver al listado') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="accionCorrectivaShow({{ (int) $id }})">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div x-show="!loading && !error && item" x-cloak class="flex items-center justify-end gap-4">
                <a :href="`/acciones-correctivas/${item?.id}/edit`" class="text-sm text-gray-500 hover:text-gray-700">
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
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">{{ __('Descripción de la acción') }}</h4>
                        <span
                            class="inline-flex px-3 py-1 text-xs font-semibold rounded-full shrink-0"
                            :class="badgeClase(item?.estado)"
                            x-text="item?.estado"
                        ></span>
                    </div>
                    <p class="text-sm text-gray-800" x-text="item?.descripcion_accion"></p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Causa raíz') }}</h4>
                    <p class="text-sm text-gray-800" x-text="item?.causa_raiz || '—'"></p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Responsable') }}</h4>
                        <p class="text-sm text-gray-800" x-text="item?.responsable?.nombre ?? '—'"></p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Fecha límite') }}</h4>
                        <p class="text-sm text-gray-800" x-text="item?.fecha_limite"></p>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6" x-show="item?.verificado_por?.id">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Verificada por') }}</h4>
                    <p class="text-sm text-gray-800" x-text="item?.verificado_por?.nombre"></p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6" x-show="item?.evidencia_cierre?.id">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Evidencia de cierre') }}</h4>
                    <a :href="item?.evidencia_cierre?.url" target="_blank" class="text-sm text-indigo-600 hover:text-indigo-800" x-text="item?.evidencia_cierre?.nombre_archivo"></a>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
