<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard Ejecutivo') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="dashboardKpis()">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Selector de año --}}
            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-gray-600">
                    {{ __('Año') }}:
                    <input
                        type="number"
                        x-model.number="anio"
                        @change="cargar()"
                        class="w-24 rounded-md border-gray-300 shadow-sm text-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                </label>

                <button
                    @click="cargar()"
                    class="text-sm text-indigo-600 hover:text-indigo-800"
                    :disabled="loading"
                >
                    {{ __('Actualizar') }}
                </button>
            </div>

            {{-- Error --}}
            <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

            {{-- Loading --}}
            <div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
                {{ __('Cargando indicadores...') }}
            </div>

            <div x-show="!loading && !error" x-cloak class="space-y-6">

                {{-- KPI 1: Auditorías --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">
                            {{ __('Auditorías') }}
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div class="border rounded-lg p-4 text-center">
                                <p class="text-3xl font-semibold text-gray-900" x-text="data.auditorias.programadas"></p>
                                <p class="text-sm text-gray-500 mt-1">{{ __('Programadas') }}</p>
                            </div>
                            <div class="border rounded-lg p-4 text-center">
                                <p class="text-3xl font-semibold text-blue-600" x-text="data.auditorias.en_ejecucion"></p>
                                <p class="text-sm text-gray-500 mt-1">{{ __('En ejecución') }}</p>
                            </div>
                            <div class="border rounded-lg p-4 text-center">
                                <p class="text-3xl font-semibold text-green-600" x-text="data.auditorias.ejecutadas"></p>
                                <p class="text-sm text-gray-500 mt-1">{{ __('Ejecutadas') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- KPI 2: Hallazgos por tipo --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">
                                {{ __('Hallazgos por tipo') }}
                            </h3>

                            <template x-if="totalHallazgos === 0">
                                <p class="text-sm text-gray-400">{{ __('Sin hallazgos registrados en el año.') }}</p>
                            </template>

                            <div class="space-y-3">
                                <template x-for="[tipo, total] in Object.entries(data.hallazgos_por_tipo)" :key="tipo">
                                    <div>
                                        <div class="flex justify-between text-sm text-gray-700 mb-1">
                                            <span x-text="tipo"></span>
                                            <span x-text="total"></span>
                                        </div>
                                        <div class="w-full bg-gray-100 rounded-full h-2">
                                            <div
                                                class="h-2 rounded-full"
                                                :class="{
                                                    'bg-red-600': tipo === 'No Conforme Mayor',
                                                    'bg-orange-400': tipo === 'No Conforme Menor',
                                                    'bg-yellow-400': tipo === 'Oportunidad de Mejora',
                                                    'bg-gray-400': tipo === 'Observacion',
                                                }"
                                                :style="`width: ${porcentajeHallazgo(total)}%`"
                                            ></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- KPI 3: Cumplimiento Anexo A --}}
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">
                                {{ __('Cumplimiento Anexo A') }}
                            </h3>
                            <div class="flex items-center gap-6">
                                <p class="text-4xl font-semibold text-indigo-600" x-text="`${data.tasa_cumplimiento_anexo_a.tasa}%`"></p>
                                <div class="text-sm text-gray-500">
                                    <p><span class="font-medium text-gray-800" x-text="data.tasa_cumplimiento_anexo_a.conformes"></span> {{ __('conformes') }}</p>
                                    <p>{{ __('de') }} <span class="font-medium text-gray-800" x-text="data.tasa_cumplimiento_anexo_a.total_evaluados"></span> {{ __('evaluados') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- KPI 4: Acciones correctivas --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-4">
                            {{ __('Acciones correctivas') }}
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="border rounded-lg p-4 text-center">
                                <p class="text-2xl font-semibold text-blue-600" x-text="data.acciones_correctivas.a_tiempo"></p>
                                <p class="text-sm text-gray-500 mt-1">{{ __('A tiempo') }}</p>
                            </div>
                            <div class="border rounded-lg p-4 text-center">
                                <p class="text-2xl font-semibold text-red-600" x-text="data.acciones_correctivas.vencidas"></p>
                                <p class="text-sm text-gray-500 mt-1">{{ __('Vencidas') }}</p>
                            </div>
                            <div class="border rounded-lg p-4 text-center">
                                <p class="text-2xl font-semibold text-green-600" x-text="data.acciones_correctivas.cerradas"></p>
                                <p class="text-sm text-gray-500 mt-1">{{ __('Cerradas') }}</p>
                            </div>
                            <div class="border rounded-lg p-4 text-center">
                                <p class="text-2xl font-semibold text-gray-500" x-text="data.acciones_correctivas.rechazadas"></p>
                                <p class="text-sm text-gray-500 mt-1">{{ __('Rechazadas') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>