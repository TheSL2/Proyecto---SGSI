<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Acciones Correctivas') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="accionesCorrectivasIndex()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500" x-show="hallazgoIdFiltro" x-cloak>
                    {{ __('Mostrando solo las acciones de ese hallazgo.') }}
                    <a href="/acciones-correctivas" class="text-indigo-600 hover:text-indigo-800">{{ __('Ver todas') }}</a>
                </p>
                <span x-show="!hallazgoIdFiltro" x-cloak></span>
                <a :href="urlCrear()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm text-white hover:bg-indigo-700">
                    {{ __('+ Nueva acción correctiva') }}
                </a>
            </div>

            <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

            <div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
                {{ __('Cargando acciones correctivas...') }}
            </div>

            <div x-show="!loading && !error && items.length === 0" x-cloak class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-sm text-gray-500">
                {{ __('Todavía no hay acciones correctivas registradas.') }}
            </div>

            <div x-show="!loading && !error && items.length > 0" x-cloak class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Descripción') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Responsable') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Fecha límite') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Estado') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="item in items" :key="item.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 text-sm text-gray-700" x-text="item.descripcion_accion"></td>
                                <td class="px-6 py-4 text-sm text-gray-500" x-text="item.responsable?.nombre ?? '—'"></td>
                                <td class="px-6 py-4 text-sm text-gray-500" x-text="item.fecha_limite"></td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="badgeClase(item.estado)"
                                        x-text="item.estado"
                                    ></span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-3">
                                    <a :href="`/acciones-correctivas/${item.id}`" class="text-indigo-600 hover:text-indigo-800">
                                        {{ __('Ver') }}
                                    </a>
                                    <a :href="`/acciones-correctivas/${item.id}/edit`" class="text-gray-500 hover:text-gray-700">
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
