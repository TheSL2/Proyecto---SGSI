<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle de Auditoría') }}
            </h2>
            <a href="{{ route('web.auditorias.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">
                &larr; {{ __('Volver al listado') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="auditoriaShow({{ (int) $id }})">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Acciones --}}
            <div x-show="!loading && !error && auditoria" x-cloak class="flex items-center justify-end gap-4">
                <span x-show="errorInforme" x-cloak class="text-sm text-red-600" x-text="errorInforme"></span>
                <a :href="`/checklists?auditoria_id=${auditoria?.id}`" class="text-sm text-indigo-600 hover:text-indigo-800">
                    {{ __('Ver checklist') }}
                </a>
                <button
                    @click="descargarInforme()"
                    :disabled="descargandoInforme"
                    class="text-sm text-indigo-600 hover:text-indigo-800 disabled:opacity-50"
                >
                    <span x-show="!descargandoInforme">{{ __('Descargar informe (PDF)') }}</span>
                    <span x-show="descargandoInforme" x-cloak>{{ __('Generando...') }}</span>
                </button>
                <a :href="`/auditorias/${auditoria?.id}/edit`" class="text-sm text-gray-500 hover:text-gray-700">
                    {{ __('Editar') }}
                </a>
                <button @click="eliminar()" :disabled="eliminando" class="text-sm text-red-600 hover:text-red-800 disabled:opacity-50">
                    {{ __('Eliminar') }}
                </button>
            </div>

            {{-- Error --}}
            <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

            {{-- Loading --}}
            <div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
                {{ __('Cargando auditoría...') }}
            </div>

            <div x-show="!loading && !error && auditoria" x-cloak class="space-y-6">

                {{-- Encabezado --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-start justify-between gap-4">
                        <h3 class="text-lg font-semibold text-gray-900" x-text="auditoria?.titulo"></h3>
                        <span
                            class="inline-flex px-3 py-1 text-xs font-semibold rounded-full shrink-0"
                            :class="badgeClase(auditoria?.estado)"
                            x-text="auditoria?.estado"
                        ></span>
                    </div>
                    <p class="text-sm text-gray-500 mt-1">
                        <span x-text="auditoria?.fecha_inicio"></span> &rarr; <span x-text="auditoria?.fecha_fin"></span>
                    </p>
                </div>

                {{-- Objetivo y alcance --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Objetivo') }}</h4>
                        <p class="text-sm text-gray-800" x-text="auditoria?.objetivo || '—'"></p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Alcance') }}</h4>
                        <p class="text-sm text-gray-800" x-text="auditoria?.alcance || '—'"></p>
                    </div>
                </div>

                {{-- Equipo --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Auditor líder') }}</h4>
                        <p class="text-sm text-gray-800" x-text="auditoria?.auditor_lider?.nombre ?? 'Sin asignar'"></p>
                    </div>
                    <div class="bg-white shadow-sm sm:rounded-lg p-6">
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Equipo auditor') }}</h4>
                        <template x-if="!auditoria?.equipo_auditor?.length">
                            <p class="text-sm text-gray-400">{{ __('Sin equipo asignado') }}</p>
                        </template>
                        <ul class="text-sm text-gray-800 space-y-1">
                            <template x-for="miembro in auditoria?.equipo_auditor ?? []" :key="miembro.id">
                                <li x-text="miembro.nombre"></li>
                            </template>
                        </ul>
                    </div>
                </div>

                {{-- Áreas evaluadas --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Áreas evaluadas') }}</h4>
                    <template x-if="!auditoria?.areas?.length">
                        <p class="text-sm text-gray-400">{{ __('Sin áreas asignadas') }}</p>
                    </template>
                    <div class="flex flex-wrap gap-2">
                        <template x-for="area in auditoria?.areas ?? []" :key="area.id">
                            <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-gray-100 text-gray-700" x-text="area.nombre"></span>
                        </template>
                    </div>
                </div>

                {{-- Conclusiones --}}
                <div class="bg-white shadow-sm sm:rounded-lg p-6" x-show="auditoria?.conclusiones">
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-2">{{ __('Conclusiones') }}</h4>
                    <p class="text-sm text-gray-800" x-text="auditoria?.conclusiones"></p>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>