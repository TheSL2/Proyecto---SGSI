<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar ítem de checklist') }}
        </h2>
    </x-slot>

    <div class="py-12" x-data="checklistForm('editar', {{ (int) $id }})">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @include('checklists._form')
        </div>
    </div>
</x-app-layout>
