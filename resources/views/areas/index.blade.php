<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Áreas') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="areasIndex()">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

            {{-- Alta rápida --}}
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <h3 class="text-sm font-medium text-gray-700 mb-4">{{ __('Nueva área') }}</h3>
                <div class="grid grid-cols-1 sm:grid-cols-[1fr_2fr_auto] gap-3">
                    <input type="text" x-model="nuevaForm.nombre" placeholder="{{ __('Nombre') }}" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <input type="text" x-model="nuevaForm.descripcion" placeholder="{{ __('Descripción (opcional)') }}" class="rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button @click="crear()" :disabled="creando || !nuevaForm.nombre" class="inline-flex items-center justify-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md text-sm text-white hover:bg-indigo-700 disabled:opacity-50">
                        {{ __('Agregar') }}
                    </button>
                </div>
            </div>

            <div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
                {{ __('Cargando áreas...') }}
            </div>

            <div x-show="!loading && items.length > 0" x-cloak class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nombre') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Descripción') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="item in items" :key="item.id">
                            <tr class="hover:bg-gray-50">
                                <template x-if="editandoId !== item.id">
                                    <td class="px-6 py-4 text-sm text-gray-800" x-text="item.nombre"></td>
                                </template>
                                <template x-if="editandoId === item.id">
                                    <td class="px-6 py-3">
                                        <input type="text" x-model="editForm.nombre" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                    </td>
                                </template>

                                <template x-if="editandoId !== item.id">
                                    <td class="px-6 py-4 text-sm text-gray-500" x-text="item.descripcion || '—'"></td>
                                </template>
                                <template x-if="editandoId === item.id">
                                    <td class="px-6 py-3">
                                        <input type="text" x-model="editForm.descripcion" class="w-full rounded-md border-gray-300 shadow-sm text-sm">
                                    </td>
                                </template>

                                <td class="px-6 py-4 text-right text-sm space-x-3 whitespace-nowrap">
                                    <template x-if="editandoId !== item.id">
                                        <span>
                                            <button @click="editar(item)" class="text-gray-500 hover:text-gray-700">{{ __('Editar') }}</button>
                                            <button @click="eliminar(item.id)" class="text-red-600 hover:text-red-800 ml-3">{{ __('Eliminar') }}</button>
                                        </span>
                                    </template>
                                    <template x-if="editandoId === item.id">
                                        <span>
                                            <button @click="guardarEdicion(item.id)" :disabled="guardandoEdicion" class="text-indigo-600 hover:text-indigo-800">{{ __('Guardar') }}</button>
                                            <button @click="cancelarEdicion()" class="text-gray-500 hover:text-gray-700 ml-3">{{ __('Cancelar') }}</button>
                                        </span>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
