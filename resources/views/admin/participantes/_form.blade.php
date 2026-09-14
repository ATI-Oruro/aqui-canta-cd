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

        <div class="md:col-span-2 border-t border-slate-100 pt-4">
            <x-input-label for="foto" :value="__('Fotografía / Imagen del participante (opcional)')" />
            
            <div class="mt-2 flex flex-col sm:flex-row items-start sm:items-center gap-4">
                @if (isset($participante) && $participante->fotoUrl())
                    <div class="relative shrink-0">
                        <img src="{{ $participante->fotoUrl() }}" alt="{{ $participante->nombreCompleto() }}"
                             class="h-20 w-20 rounded-xl object-cover border border-slate-200 shadow-sm">
                    </div>
                @endif

                <div class="flex-1">
                    <input type="file" id="foto" name="foto" accept="image/*"
                           class="block w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 cursor-pointer" />
                    <x-input-error :messages="$errors->get('foto')" class="mt-1" />
                    <p class="mt-1 text-xs text-slate-400">Formatos permitidos: JPG, PNG, WEBP (máx. 4MB). Si se deja vacío, no se cargará imagen.</p>
                </div>

                @if (isset($participante) && $participante->foto)
                    <div class="flex items-center gap-2 mt-2 sm:mt-0">
                        <label class="inline-flex items-center text-xs text-rose-600 font-semibold cursor-pointer">
                            <input type="checkbox" name="eliminar_foto" value="1" class="rounded border-slate-300 text-rose-600 focus:ring-rose-500 mr-1.5">
                            Eliminar foto actual
                        </label>
                    </div>
                @endif
            </div>
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