@php
    $usuario ??= null;
    $editableRol ??= true;
    $esSelf = $usuario?->id === auth()->id();
    $nombreCompletoActual = isset($usuario)
        ? trim(implode(' ', array_filter([$usuario->nombre, $usuario->paterno, $usuario->materno])))
        : '';
@endphp

<div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
    <h3 class="font-semibold text-slate-800 mb-4">
        {{ isset($usuario) ? 'Editar usuario' : 'Nuevo usuario' }}
    </h3>

    <div class="grid gap-5 md:grid-cols-2">
        <div class="md:col-span-2">
            <x-input-label for="nombre_completo" :value="__('Nombre completo')" />
            <x-text-input id="nombre_completo" class="mt-1 block w-full" type="text" name="nombre_completo"
                placeholder="Ej. Pedro Marcelo Arispe Quelca"
                :value="old('nombre_completo', $nombreCompletoActual)" required autofocus />
            <div id="preview_nombre_completo" class="mt-1.5 flex flex-wrap items-center gap-2 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded  text-slate-700">
                    <strong class="font-medium text-slate-500">Nombre(s):</strong>
                    <span id="prev_nombre">—</span>
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-slate-700">
                    <strong class="font-medium text-slate-500">Paterno:</strong>
                    <span id="prev_paterno">—</span>
                </span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-slate-700">
                    <strong class="font-medium text-slate-500">Materno:</strong>
                    <span id="prev_materno">—</span>
                </span>
            </div>
            <x-input-error :messages="$errors->get('nombre_completo')" class="mt-2" />
            <x-input-error :messages="$errors->get('nombre')" class="mt-2" />
        </div>

        <div class="md:col-span-2">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email"
                placeholder="correo@ejemplo.com"
                :value="old('email', $usuario->email ?? null)" required />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="rol" :value="__('Rol')" />
            @if ($editableRol)
                <select id="rol" name="rol" required
                    class="mt-1 block w-full border-gray-300 focus:border-[#5c0618] focus:ring-[#5c0618] rounded-md shadow-sm">
                    <option value="{{ App\Support\Enums\RolEnum::JURADO->value }}" @selected(old('rol', $usuario?->rol?->value ?? App\Support\Enums\RolEnum::JURADO->value) === App\Support\Enums\RolEnum::JURADO->value)>
                        {{ App\Support\Enums\RolEnum::JURADO->label() }}
                    </option>
                    <option value="{{ App\Support\Enums\RolEnum::ADMIN->value }}" @selected(old('rol', $usuario?->rol?->value ?? null) === App\Support\Enums\RolEnum::ADMIN->value)>
                        {{ App\Support\Enums\RolEnum::ADMIN->label() }}
                    </option>
                </select>
            @else
                <input type="hidden" name="rol" value="{{ $usuario->rol->value }}">
                <p class="mt-1 text-sm text-slate-600 font-medium">{{ $usuario->rol->label() }}</p>
                <p class="text-xs text-slate-400">No puedes cambiar tu propio rol.</p>
            @endif
            <x-input-error :messages="$errors->get('rol')" class="mt-2" />
        </div>

        <div class="md:pt-7 flex items-center">
            <div>
                <label class="inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="puede_controlar_tiempo" value="1"
                        @checked(old('puede_controlar_tiempo', $usuario?->puedeControlarTiempo() ?? false))
                        class="rounded border-gray-300 text-[#5c0618] focus:ring-[#5c0618]">
                    <span class="text-sm text-slate-700 font-medium">Puede controlar el cronómetro</span>
                </label>
                <x-input-error :messages="$errors->get('puede_controlar_tiempo')" class="mt-2" />
            </div>
        </div>

        <div class="md:col-span-2">
            <x-input-label for="password" :value="isset($usuario) ? __('Nueva contraseña (opcional)') : __('Contraseña')" />
            <div class="mt-1 flex flex-col sm:flex-row gap-2">
                <div class="relative flex-1">
                    <input id="password" type="text" name="password"
                        {{ isset($usuario) ? '' : 'required' }} autocomplete="off"
                        placeholder="{{ isset($usuario) ? 'Dejar en blanco para mantener la actual o clic en Generar' : 'Haz clic en Generar contraseña o escribe una' }}"
                        class="block w-full font-mono text-sm border-gray-300 focus:border-[#5c0618] focus:ring-[#5c0618] rounded-md shadow-sm pr-20">
                </div>
                <button type="button" id="btn_generar_password"
                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-[#5c0618] border border-rose-200 text-sm font-semibold rounded-md shadow-sm transition-colors whitespace-nowrap">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    Generar contraseña
                </button>
            </div>
            @if (isset($usuario))
                <p class="mt-1 text-xs text-slate-400">Dejar en blanco para no cambiar la contraseña actual.</p>
            @endif
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
    </div>

    <div class="mt-6 flex items-center justify-end gap-3">
        <a href="{{ route('admin.usuarios.index') }}" class="inline-flex items-center px-4 py-2 text-sm text-slate-600 hover:text-slate-800 rounded-md">
            Cancelar
        </a>
        <x-primary-button>
            {{ isset($usuario) ? __('Guardar cambios') : __('Crear usuario') }}
        </x-primary-button>
    </div>
</div>

<script>
    (function () {
        const inputNombre = document.getElementById('nombre_completo');
        const prevNombre = document.getElementById('prev_nombre');
        const prevPaterno = document.getElementById('prev_paterno');
        const prevMaterno = document.getElementById('prev_materno');

        function actualizarPreviewNombre() {
            if (!inputNombre) return;
            const partes = inputNombre.value.trim().split(/\s+/).filter(Boolean);
            let nombre = '—';
            let paterno = '—';
            let materno = '—';

            if (partes.length === 1) {
                nombre = partes[0];
            } else if (partes.length === 2) {
                nombre = partes[0];
                paterno = partes[1];
            } else if (partes.length >= 3) {
                materno = partes[partes.length - 1];
                paterno = partes[partes.length - 2];
                nombre = partes.slice(0, partes.length - 2).join(' ');
            }

            prevNombre.textContent = nombre;
            prevPaterno.textContent = paterno;
            prevMaterno.textContent = materno;
        }

        if (inputNombre) {
            inputNombre.addEventListener('input', actualizarPreviewNombre);
            actualizarPreviewNombre();
        }

        const inputPassword = document.getElementById('password');
        const btnGenerar = document.getElementById('btn_generar_password');
        const btnCopiar = document.getElementById('btn_copiar_password');

        function generarPassword() {
            const mayus = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
            const minus = 'abcdefghijkmnopqrstuvwxyz';
            const nums = '23456789';
            const todos = mayus + minus + nums;
            const longitud = 10;

            const chars = [
                mayus[Math.floor(Math.random() * mayus.length)],
                minus[Math.floor(Math.random() * minus.length)],
                nums[Math.floor(Math.random() * nums.length)],
            ];

            for (let i = chars.length; i < longitud; i++) {
                chars.push(todos[Math.floor(Math.random() * todos.length)]);
            }

            for (let i = chars.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [chars[i], chars[j]] = [chars[j], chars[i]];
            }

            if (inputPassword) {
                inputPassword.value = chars.join('');
                inputPassword.focus();
                inputPassword.select();
            }
        }

        if (btnGenerar) {
            btnGenerar.addEventListener('click', generarPassword);
        }

        if (btnCopiar && inputPassword) {
            btnCopiar.addEventListener('click', function () {
                if (!inputPassword.value) {
                    generarPassword();
                }
                navigator.clipboard?.writeText(inputPassword.value);
                const original = btnCopiar.textContent;
                btnCopiar.textContent = '¡Copiado!';
                setTimeout(() => {
                    btnCopiar.textContent = original;
                }, 1800);
            });
        }
    })();
</script>
