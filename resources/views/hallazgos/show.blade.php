<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle del hallazgo') }}
            </h2>
            <a href="/hallazgos" class="text-sm text-indigo-600 hover:text-indigo-800">
                &larr; {{ __('Volver al listado') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="hallazgoShow({{ (int) $id }})">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div x-show="!loading && !error && item" x-cloak class="flex items-center justify-end gap-4">
                <a :href="`/evidencias/create?hallazgo_id=${item?.id}`" class="text-sm text-indigo-600 hover:text-indigo-800">
                    {{ __('+ Nueva evidencia') }}
                </a>
                <a :href="`/hallazgos/${item?.id}/edit`" class="text-sm text-gray-500 hover:text-gray-700">
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
                            <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">{{ __('Cláusula / Control') }}</h4>
                            <p class="text-sm text-gray-800" x-text="item?.clausula_o_control"></p>
                        </div>
                        <span
                            class="inline-flex px-3 py-1 text-xs font-semibold rounded-full shrink-0"
                            :class="badgeClase(item?.tipo_hallazgo)"
                            x-text="item?.tipo_hallazgo"
                        ></span>
                    </div>
                    <p class="text-sm text-gray-500 mt-2">
                        {{ __('Auditoría') }}: <span x-text="item?.checklist?.auditoria?.titulo ?? '—'"></span>
                    </p>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Descripción') }}</h4>
                    <p class="text-sm text-gray-800" x-text="item?.descripcion"></p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Estado') }}</h4>
                        <p class="text-sm text-gray-800" x-text="item?.estado ?? '—'"></p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Notificación') }}</h4>
                        <p class="text-sm text-gray-800">
                            <span x-text="item?.estado_notificacion ?? '—'"></span>
                            <span x-show="item?.fecha_notificacion" x-cloak>
                                (<span x-text="item?.fecha_notificacion"></span>)
                            </span>
                        </p>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide">{{ __('Acciones correctivas') }}</h4>
                        <a :href="`/acciones-correctivas/create?hallazgo_id=${item?.id}`" class="text-sm text-indigo-600 hover:text-indigo-800">
                            {{ __('+ Nueva acción correctiva') }}
                        </a>
                    </div>

                    <div x-show="sinAccionCorrectiva" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-3">
                        {{ __('Esta No Conformidad todavía no tiene ningún plan de acción correctiva asignado.') }}
                    </div>

                    <ul class="divide-y divide-gray-100" x-show="!sinAccionCorrectiva" x-cloak>
                        <template x-for="accion in item?.acciones_correctivas ?? []" :key="accion.id">
                            <li class="py-3 flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-800" x-text="accion.descripcion_accion"></p>
                                    <p class="text-xs text-gray-400">
                                        {{ __('Responsable') }}: <span x-text="accion.responsable?.nombre ?? '—'"></span>
                                        · {{ __('Vence') }}: <span x-text="accion.fecha_limite"></span>
                                    </p>
                                </div>
                                <span class="text-xs font-medium text-gray-600" x-text="accion.estado"></span>
                                <a 
                                    x-show="accion.estado !== 'Verificada'" 
                                    :href="`/acciones-correctivas/${accion.id}/edit`" 
                                    class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                                >
                                    Editar
                                </a>
                            </li>
                        </template>
                    </ul>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
