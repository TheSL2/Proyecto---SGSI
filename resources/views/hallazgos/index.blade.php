<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Hallazgos') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12" x-data="hallazgosIndex()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500" x-show="checklistIdFiltro" x-cloak>
                    {{ __('Mostrando solo los hallazgos de ese ítem de checklist.') }}
                    <a href="/hallazgos" class="text-indigo-600 hover:text-indigo-800">{{ __('Ver todos') }}</a>
                </p>
                <span x-show="!checklistIdFiltro" x-cloak></span>
                <a :href="urlCrear()" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm text-white hover:bg-indigo-700">
                    {{ __('+ Nuevo hallazgo') }}
                </a>
            </div>

            <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

            <div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
                {{ __('Cargando hallazgos...') }}
            </div>

            <div x-show="!loading && !error && items.length === 0" x-cloak class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-sm text-gray-500">
                {{ __('Todavía no hay hallazgos registrados.') }}
            </div>

            <div x-show="!loading && !error && items.length > 0" x-cloak class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Tipo') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Cláusula/Control') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Auditoría') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Estado') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Acción correctiva') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="item in items" :key="item.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                                        :class="badgeClase(item.tipo_hallazgo)"
                                        x-text="item.tipo_hallazgo"
                                    ></span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700" x-text="item.clausula_o_control"></td>
                                <td class="px-6 py-4 text-sm text-gray-500" x-text="item.checklist?.auditoria?.titulo ?? '—'"></td>
                                <td class="px-6 py-4 text-sm text-gray-500" x-text="item.estado ?? '—'"></td>
                                <td class="px-6 py-4 text-sm">
                                    <span x-show="sinAcciones(item)" x-cloak class="text-red-600 font-medium">
                                        {{ __('Sin acción asignada') }}
                                    </span>
                                    <span x-show="!sinAcciones(item)" x-cloak class="text-green-600">
                                        {{ __('Asignada') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right text-sm space-x-3">
                                    <a :href="`/hallazgos/${item.id}`" class="text-indigo-600 hover:text-indigo-800">
                                        {{ __('Ver') }}
                                    </a>
                                    <a :href="`/hallazgos/${item.id}/edit`" class="text-gray-500 hover:text-gray-700">
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
