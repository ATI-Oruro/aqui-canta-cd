@php
    $participante ??= null;
@endphp

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-800 mb-4">
        {{ isset($participante) ? 'Editar participante' : 'Registrar participante' }}
    </h3>

    <div class="grid gap-4 md:grid-cols-2">
        <div>
            <x-input-label for="nombre" :value="__('Nombre')" />
            <x-text-input id="nombre" class="mt-1 block w-full" type="text" name="nombre"
                :value="old('nombre', $participante->nombre ?? null)" required autofocus />
            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="paterno" :value="__('Apellido paterno')" />
            <x-text-input id="paterno" class="mt-1 block w-full" type="text" name="paterno"
                :value="old('paterno', $participante->paterno ?? null)" />
            <x-input-error :messages="$errors->get('paterno')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="materno" :value="__('Apellido materno')" />
            <x-text-input id="materno" class="mt-1 block w-full" type="text" name="materno"
                :value="old('materno', $participante->materno ?? null)" />
            <x-input-error :messages="$errors->get('materno')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="pseudonimo" :value="__('Pseudónimo (opcional)')" />
            <x-text-input id="pseudonimo" class="mt-1 block w-full" type="text" name="pseudonimo"
                :value="old('pseudonimo', $participante->pseudonimo ?? null)" />
            <x-input-error :messages="$errors->get('pseudonimo')" class="mt-2" />
            <p class="mt-1 text-xs text-slate-400">Es el nombre artístico individual, distinto del nombre de presentación.</p>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-end gap-3">
        <a href="{{ route('admin.participantes.index') }}" class="inline-flex items-center px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-md">
            Cancelar
        </a>
        <x-primary-button>
            {{ isset($participante) ? __('Guardar cambios') : __('Registrar participante') }}
        </x-primary-button>
    </div>
</div>