<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Auditorías') }}
            </h2>
            <a href="{{ route('web.auditorias.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm text-white hover:bg-indigo-700">
                {{ __('+ Nueva Auditoría') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="auditoriasIndex()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Error --}}
            <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

            {{-- Loading --}}
            <div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
                {{ __('Cargando auditorías...') }}
            </div>

            {{-- Sin datos --}}
            <div x-show="!loading && !error && auditorias.length === 0" x-cloak class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-sm text-gray-500">
                {{ __('Todavía no hay auditorías registradas.') }}
            </div>

            {{-- Tabla --}}
            <div x-show="!loading && !error && auditorias.length > 0" x-cloak class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Título') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Estado') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Fechas') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Auditor líder') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Áreas') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="auditoria in auditorias" :key="auditoria.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900" x-text="auditoria.titulo"></td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="badgeClase(auditoria.estado)"
                                        x-text="auditoria.estado"
                                    ></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500" x-text="`${auditoria.fecha_inicio} → ${auditoria.fecha_fin}`"></td>
                                <td class="px-6 py-4 text-sm text-gray-500" x-text="auditoria.auditor_lider?.nombre ?? '—'"></td>
                                <td class="px-6 py-4 text-sm text-gray-500" x-text="nombresAreas(auditoria)"></td>
                                <td class="px-6 py-4 text-right text-sm space-x-3">
                                    <a :href="`/auditorias/${auditoria.id}`" class="text-indigo-600 hover:text-indigo-800">
                                        {{ __('Ver') }}
                                    </a>
                                    <a :href="`/auditorias/${auditoria.id}/edit`" class="text-gray-500 hover:text-gray-700">
                                        {{ __('Editar') }}
                                    </a>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
