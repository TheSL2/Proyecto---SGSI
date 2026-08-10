<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalle de la evidencia') }}
            </h2>
            <a href="/evidencias" class="text-sm text-indigo-600 hover:text-indigo-800">
                &larr; {{ __('Volver al listado') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12" x-data="evidenciaShow({{ (int) $id }})">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div x-show="!loading && !error && item" x-cloak class="flex items-center justify-end gap-4">
                <a :href="item?.url" target="_blank" class="text-sm text-gray-500 hover:text-gray-700">
                    {{ __('Descargar / ver archivo') }}
                </a>
                <button @click="eliminar()" :disabled="eliminando" class="text-sm text-red-600 hover:text-red-800 disabled:opacity-50">
                    {{ __('Eliminar') }}
                </button>
            </div>

            <div x-show="error" x-cloak class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-md p-4" x-text="error"></div>

            <div x-show="loading" x-cloak class="text-center text-gray-500 text-sm py-8">
                {{ __('Cargando...') }}
            </div>

            <div x-show="!loading && !error && item" x-cloak class="bg-white shadow-sm sm:rounded-lg p-6 space-y-4">
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">{{ __('Archivo') }}</h4>
                    <p class="text-sm text-gray-800" x-text="item?.nombre_archivo"></p>
                </div>
                <div>
                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">{{ __('Hash SHA-256') }}</h4>
                    <p class="text-xs text-gray-500 font-mono break-all" x-text="item?.hash_sha256"></p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">{{ __('Subido por') }}</h4>
                        <p class="text-sm text-gray-800" x-text="item?.subido_por?.nombre ?? '—'"></p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wide mb-1">{{ __('Fecha') }}</h4>
                        <p class="text-sm text-gray-800" x-text="item?.created_at?.slice(0, 10)"></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
