<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Usuarios') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="usuariosIndex()">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

            <div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
                {{ __('Cargando usuarios...') }}
            </div>

            <div x-show="!loading" x-cloak class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Nombre') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Email') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Rol') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Área') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Activo') }}</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="item in items" :key="item.id">
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-800" x-text="item.nombre"></td>
                                <td class="px-6 py-3 text-sm text-gray-500" x-text="item.email"></td>
                                
                                <!-- SELECT DE ROL -->
                                <td class="px-6 py-3">
                                    <select x-model="item.rol" class="rounded-md border-gray-300 shadow-sm text-sm">
                                        <template x-for="r in roles" :key="r">
                                            <option :value="r" x-text="r" :selected="r === item.rol"></option>
                                        </template>
                                    </select>
                                </td>

                                <!-- SELECT DE ÁREA -->
                                <td class="px-6 py-3">
                                    <select x-model="item._area_id" class="rounded-md border-gray-300 shadow-sm text-sm">
                                        <option value="">{{ __('Sin área') }}</option>
                                        <template x-for="area in areas" :key="area.id">
                                            <option :value="String(area.id)" x-text="area.nombre" :selected="String(area.id) === String(item._area_id)"></option>
                                        </template>
                                    </select>
                                </td>

                                <td class="px-6 py-3">
                                    <input type="checkbox" x-model="item.activo" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                </td>
                                
                                <td class="px-6 py-3 text-right text-sm">
                                    <button
                                        @click="guardar(item)"
                                        :disabled="guardandoId === item.id"
                                        class="text-indigo-600 hover:text-indigo-800 disabled:opacity-50 font-medium"
                                    >
                                        <span x-show="guardandoId !== item.id">{{ __('Guardar') }}</span>
                                        <span x-show="guardandoId === item.id" x-cloak>{{ __('Guardando...') }}</span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <p class="text-xs text-gray-400">
                {{ __('RN-UR-02: desactivar un usuario no borra su histórico de auditorías/hallazgos pasados, solo le impide iniciar sesión de nuevo.') }}
            </p>

        </div>
    </div>
</x-app-layout>
