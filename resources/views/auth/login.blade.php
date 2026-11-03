<x-guest-layout>
    <!-- Contenedor de Gala al 100% de la vista con Esquinas Doradas -->
    <div class="relative min-h-screen w-full flex-1 flex bg-[#0b0206]">
        <!-- Esquinas ornamentales doradas del sistema -->
        <span class="corner-bracket corner-tl"></span>
        <span class="corner-bracket corner-tr"></span>
        <span class="corner-bracket corner-bl"></span>
        <span class="corner-bracket corner-br"></span>

        <div class="min-h-screen w-full grid grid-cols-1 lg:grid-cols-12">
            <!-- Columna Izquierda: Identidad de Gala del Festival -->
            <div style="background: radial-gradient(circle at 50% 38%, rgba(128, 0, 32, 0.58) 0%, rgba(28, 4, 12, 0.94) 65%, rgba(9, 2, 5, 0.99) 100%);"
                class="lg:col-span-5 p-6 sm:p-8 flex flex-col items-center justify-center gap-6 text-center border-b lg:border-b-0 lg:border-r border-[#6b0015]/60 relative overflow-hidden select-none">

                <!-- Logo Oficial del Festival a todo color con iluminación dorada -->
                <div class="relative z-10 flex flex-col items-center">
                    <div class="relative p-2">
                        <img src="/images/logo-oficial.png"
                            alt="Aquí… Canta Bolivia"
                            class="w-60 sm:w-72 lg:w-72 h-auto object-contain mx-auto drop-shadow-[0_16px_36px_rgba(0,0,0,0.85)]" />
                    </div>

                    <h1 class="mt-1 font-cinzel text-2xl sm:text-3xl lg:text-4xl font-black tracking-[0.14em] gold-gradient-text uppercase">
                        Calificaciones Digitales
                    </h1>
                </div>

                <!-- Sello Institucional GADOR -->
                <div class="relative z-10 w-full max-w-xs flex flex-col items-center">
                    <div class="px-4">
                        <img src="/images/logo_gador_blanco.png"
                            alt="Gobierno Autónomo Departamental de Oruro"
                            style="height: 101px;"
                            class="w-auto object-contain mx-auto" />
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Formulario Oficial de Acceso -->
            <div class="lg:col-span-7 p-8 sm:p-12 lg:p-20 flex flex-col justify-center items-center bg-gradient-to-b from-[#110308] to-[#070104] relative">
                <div class="w-full max-w-md">
                    <div class="mb-8">
                        <span class="text-[11px] font-bold tracking-[0.24em] uppercase text-[#d4af37]/90 block mb-1.5">
                            Mesa de Evaluación & Administración
                        </span>
                        <h2 class="font-cinzel text-3xl sm:text-4xl font-extrabold gold-gradient-text tracking-wider uppercase">
                            Iniciar Sesión
                        </h2>
                        <p class="mt-2 text-sm text-slate-400">
                            Ingrese sus credenciales oficiales para acceder al sistema de calificación.
                        </p>
                    </div>

                    <!-- Session Status -->
                    <x-auth-session-status class="mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="space-y-6">
                        @csrf

                        <!-- Correo Electrónico -->
                        <div>
                            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-amber-200/90 mb-2">
                                Correo Electrónico
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#d4af37]/80 pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </span>
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    required autofocus autocomplete="username"
                                    placeholder="jurado@canta.bo"
                                    class="block w-full pl-11 pr-4 py-3.5 text-sm text-slate-100 bg-[#060103]/90 border border-[#6b0015] rounded-xl placeholder-slate-500 focus:bg-[#0c0206] focus:border-[#d4af37] focus:ring-2 focus:ring-[#d4af37]/25 transition-all" />
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2 text-rose-400" />
                        </div>

                        <!-- Contraseña de Acceso -->
                        <div>
                            <label for="password" class="block text-xs font-bold uppercase tracking-wider text-amber-200/90 mb-2">
                                Contraseña de Acceso
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-[#d4af37]/80 pointer-events-none">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <input id="password" type="password" name="password"
                                    required autocomplete="current-password"
                                    placeholder="••••••••••••"
                                    class="block w-full pl-11 pr-11 py-3.5 text-sm text-slate-100 bg-[#060103]/90 border border-[#6b0015] rounded-xl placeholder-slate-500 focus:bg-[#0c0206] focus:border-[#d4af37] focus:ring-2 focus:ring-[#d4af37]/25 transition-all" />
                                <button type="button" id="toggle_login_password"
                                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400 hover:text-[#d4af37] transition-colors"
                                    aria-label="Mostrar u ocultar contraseña">
                                    <svg id="icon_eye_open" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    <svg id="icon_eye_closed" class="w-4 h-4 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                                    </svg>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-rose-400" />
                        </div>

                        <!-- Recordar sesión -->
                        <div class="flex items-center justify-between pt-1">
                            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer select-none">
                                <input id="remember_me" type="checkbox" name="remember"
                                    class="rounded bg-[#060103] border-[#6b0015] text-[#800020] focus:ring-[#d4af37]/40 focus:ring-offset-0">
                                <span class="text-xs text-slate-400 hover:text-slate-300 transition-colors">Mantener sesión activa</span>
                            </label>

                            <a href="{{ route('inicio') }}" class="text-xs text-[#d4af37]/80 hover:text-amber-200 transition-colors">
                                Ir al Ranking Público →
                            </a>
                        </div>

                        <!-- Botón Ingresar al Sistema -->
                        <div class="pt-2">
                            <button type="submit"
                                style="background: linear-gradient(90deg, #4a0412 0%, #7a091e 50%, #4a0412 100%); box-shadow: 0 10px 28px -6px rgba(128, 0, 32, 0.7), inset 0 0 0 1px rgba(212, 175, 55, 0.45);"
                                class="w-full py-4 px-6 rounded-xl font-cinzel text-sm font-extrabold uppercase tracking-[0.18em] text-amber-100 hover:text-white hover:brightness-110 focus:outline-none focus:ring-2 focus:ring-[#d4af37] transition-all flex items-center justify-center gap-2">
                                <span>Ingresar al Sistema</span>
                                <svg class="w-4 h-4 text-[#d4af37]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const btn = document.getElementById('toggle_login_password');
            const input = document.getElementById('password');
            const eyeOpen = document.getElementById('icon_eye_open');
            const eyeClosed = document.getElementById('icon_eye_closed');

            if (btn && input) {
                btn.addEventListener('click', function () {
                    const esPassword = input.type === 'password';
                    input.type = esPassword ? 'text' : 'password';
                    eyeOpen?.classList.toggle('hidden', esPassword);
                    eyeClosed?.classList.toggle('hidden', !esPassword);
                });
            }
        })();
    </script>
</x-guest-layout>
