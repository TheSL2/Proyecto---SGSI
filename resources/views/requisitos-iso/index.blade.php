<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Requisitos ISO / Anexo A') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="requisitosIsoIndex()">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    {{ __('Marca un requisito como No Aplicable si no corresponde a la Declaración de Aplicabilidad (SoA) de tu organización.') }}
                </p>
                <select x-model="filtroCategoria" class="rounded-md border-gray-300 shadow-sm text-sm">
                    <option value="">{{ __('Todas las categorías') }}</option>
                    <template x-for="cat in categorias" :key="cat">
                        <option :value="cat" x-text="cat"></option>
                    </template>
                </select>
            </div>

            <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

            <div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
                {{ __('Cargando catálogo...') }}
            </div>

            <div x-show="!loading" x-cloak class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Código') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Categoría') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Descripción') }}</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Aplicable') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="item in itemsFiltrados" :key="item.id">
                            <tr class="hover:bg-gray-50" :class="{ 'opacity-50': !item.aplicable }">
                                <td class="px-6 py-3 text-sm font-medium text-gray-800" x-text="item.codigo"></td>
                                <td class="px-6 py-3 text-sm text-gray-500" x-text="item.categoria"></td>
                                <td class="px-6 py-3 text-sm text-gray-600" x-text="item.descripcion"></td>
                                <td class="px-6 py-3 text-center">
                                    <button
                                        @click="toggleAplicable(item)"
                                        :disabled="cambiandoId === item.id"
                                        class="relative inline-flex h-6 w-11 items-center rounded-full transition disabled:opacity-50"
                                        :class="item.aplicable ? 'bg-indigo-600' : 'bg-gray-300'"
                                    >
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition" :class="item.aplicable ? 'translate-x-6' : 'translate-x-1'"></span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
