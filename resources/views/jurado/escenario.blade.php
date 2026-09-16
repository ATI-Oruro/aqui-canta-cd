<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'ACB Canta Bolivia') }} — Escenario</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased h-full min-h-screen bg-slate-950 text-white flex flex-col justify-between select-none overflow-x-hidden"
          data-escenario-raiz
          data-escenario-id="{{ $presentacion->id ?? '' }}"
          data-estado-actual="{{ $presentacion?->estado?->value ?? '' }}"
          data-cronometro-corriendo="{{ ($cronometro['corriendo'] ?? false) ? '1' : '0' }}"
          data-cronometro-iniciado-at="{{ $cronometro['cronometro_iniciado_at'] ?? $presentacion?->cronometro_iniciado_at?->toIso8601String() ?? '' }}"
          data-cronometro-finalizado-at="{{ $cronometro['cronometro_finalizado_at'] ?? $presentacion?->cronometro_finalizado_at?->toIso8601String() ?? '' }}"
          data-cronometro-iniciado-at-ms="{{ $cronometro['cronometro_iniciado_at_ms'] ?? $presentacion?->cronometro_iniciado_at?->valueOf() ?? '' }}"
          data-cronometro-finalizado-at-ms="{{ $cronometro['cronometro_finalizado_at_ms'] ?? $presentacion?->cronometro_finalizado_at?->valueOf() ?? '' }}"
          data-server-now-ms="{{ $cronometro['server_now_ms'] ?? (int) (microtime(true) * 1000) }}"
          data-segundos-restantes="{{ $cronometro['segundos_restantes'] ?? (($presentacion->tiempo_maximo ?? 10) * 60) }}"
          data-tiempo-maximo="{{ $presentacion->tiempo_maximo ?? 10 }}">

        <!-- Barra Superior Compacta -->
        <header class="px-4 py-2 border-b border-slate-800/80 bg-slate-900/40 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold tracking-wider uppercase text-amber-400">Aquí Canta Bolivia</span>
                <span class="text-slate-600">|</span>
                <span class="text-xs text-slate-400">XXXIII Festival Nacional</span>
            </div>

            <div class="flex items-center gap-3">
                @auth
                    <span class="text-xs text-slate-400">
                        {{ auth()->user()->nombre }} <span class="text-slate-600">({{ auth()->user()->rol->value }})</span>
                    </span>
                    <a href="{{ auth()->user()->esJurado() ? route('jurado.inicio') : route('admin.inicio') }}"
                       class="text-[11px] font-semibold text-slate-300 hover:text-white bg-slate-800 px-2.5 py-1 rounded-md border border-slate-700 transition">
                        Panel
                    </a>
                @endauth
            </div>
        </header>

        <!-- Contenedor Principal (2 Columnas sin scroll forzado) -->
        <main class="flex-1 flex flex-col justify-center px-4 sm:px-6 py-2">
            
            {{-- Presentación Activa --}}
            <div data-escenario-activo class="{{ $presentacion !== null ? 'w-full max-w-7xl mx-auto' : 'hidden w-full max-w-7xl mx-auto' }}">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                    
                    <!-- ========================================== -->
                    <!-- COLUMNA IZQUIERDA: INFORMACIÓN Y CRONÓMETRO -->
                    <!-- ========================================== -->
                    <div class="lg:col-span-6 flex flex-col items-center justify-center text-center py-2 px-2">
                        
                        <!-- Badge de Estado -->
                        <span data-estado class="inline-block px-4 py-1 rounded-full text-xs sm:text-sm font-semibold tracking-wide uppercase {{ ($presentacion?->estado?->value === App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO->value) ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : (($presentacion?->estado?->value === App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO->value) ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-slate-500/20 text-slate-300 border border-slate-600') }}">
                            {{ $presentacion?->estado?->label() ?? 'En escenario' }}
                        </span>

                        <!-- Categoría y Fase -->
                        <p data-categoria-fase class="mt-4 text-xs sm:text-sm uppercase tracking-[0.25em] text-slate-400 font-medium">
                            {{ $presentacion?->inscripcion?->categoria?->categoria ?? '' }} · {{ $presentacion?->inscripcion?->fase?->fase?->label() ?? '' }}
                            @if ($presentacion?->inscripcion?->departamento)
                                <span class="inline-flex items-center gap-1.5 align-middle">
                                    <span>·</span>
                                    @if ($presentacion->inscripcion->departamento->bandera_url)
                                        <img src="{{ $presentacion->inscripcion->departamento->bandera_url }}" alt="{{ $presentacion->inscripcion->departamento->nombre }}" class="h-3.5 w-auto object-cover rounded-[2px] shadow-sm inline-block">
                                    @endif
                                    <span>{{ $presentacion->inscripcion->departamento->nombre }}</span>
                                </span>
                            @endif
                        </p>

                        <!-- Fotografía / Imagen Opcional del Participante -->
                        @if ($presentacion?->fotoUrl())
                            <div class="mt-2 mb-1 flex justify-center">
                                <img src="{{ $presentacion->fotoUrl() }}" alt="{{ $presentacion->nombreMostrado() }}"
                                     class="h-24 w-24 sm:h-28 sm:w-28 object-cover rounded-2xl border-2 border-amber-400/50 shadow-2xl">
                            </div>
                        @endif

                        <!-- Nombre del Participante -->
                        <h1 data-nombre class="mt-2 text-3xl sm:text-4xl xl:text-5xl font-extrabold leading-tight text-white tracking-tight">
                            {{ $presentacion?->nombreMostrado() ?? '' }}
                        </h1>

                        <!-- Cronómetro Principal Gigante -->
                        <div class="mt-4 flex items-center justify-center gap-3">
                            <span class="text-5xl sm:text-6xl xl:text-7xl font-black tabular-nums tracking-tight font-mono text-white" data-cronometro-display>
                                {{ gmdate('i:s', $cronometro['segundos_restantes'] ?? (($presentacion->tiempo_maximo ?? 10) * 60)) }}
                            </span>
                            <span class="text-2xl sm:text-3xl font-semibold text-slate-500 tabular-nums font-mono" data-tiempo-max-display>
                                / {{ $presentacion->tiempo_maximo ?? 10 }}:00
                            </span>
                        </div>

                        <p class="mt-2 text-xs text-slate-400" data-tiempo-max-texto>
                            Tiempo máximo de presentación: {{ $presentacion->tiempo_maximo ?? 10 }} minuto(s)
                        </p>

                        <!-- Controles de Cronómetro para Jurado Controlador / Admin -->
                        @if (($puedeControlar ?? false) && $presentacion !== null)
                            <div data-control-tiempo-caja
                                 data-url-iniciar="{{ route('presentaciones.cronometro.iniciar', $presentacion) }}"
                                 data-url-detener="{{ route('presentaciones.cronometro.detener', $presentacion) }}"
                                 class="mt-4 p-2.5 rounded-xl bg-slate-900/90 border border-emerald-500/40 shadow-lg flex flex-col sm:flex-row items-center gap-2.5">
                                <span class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                    Control de Tiempo:
                                </span>
                                @if ($presentacion->cronometroCorriendo())
                                    <form method="POST" action="{{ route('presentaciones.cronometro.detener', $presentacion) }}">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-rose-600 hover:bg-rose-500 text-white font-black text-xs uppercase tracking-wider shadow-md hover:shadow-rose-600/30 transition flex items-center gap-1.5">
                                            <span>⏹</span>
                                            <span>Detener cronómetro</span>
                                        </button>
                                    </form>
                                @elseif ($presentacion->cronometro_iniciado_at === null)
                                    <form method="POST" action="{{ route('presentaciones.cronometro.iniciar', $presentacion) }}">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white font-black text-xs uppercase tracking-wider shadow-md hover:shadow-emerald-600/30 transition flex items-center gap-1.5">
                                            <span>▶</span>
                                            <span>Iniciar cronómetro</span>
                                        </button>
                                    </form>
                                @else
                                    <span data-control-estado-texto class="text-xs text-slate-400 font-semibold">Cronómetro finalizado</span>
                                @endif
                            </div>
                        @endif

                        <!-- Aviso Informativo cuando la presentación está Finalizada -->
                        @php
                            $esFinalizado = ($presentacion?->estado === App\Support\Enums\PresentacionEstadoEnum::FINALIZADO || $presentacion?->cronometro_finalizado_at !== null);
                        @endphp

                        <div data-aviso-finalizado
                             class="{{ $esFinalizado ? 'block' : 'hidden' }} mt-4 w-full max-w-md p-3 rounded-xl bg-slate-900/90 border border-amber-500/40 shadow-lg text-center space-y-1">
                            <div class="flex items-center justify-center gap-2 text-amber-400 text-xs font-bold uppercase tracking-wider">
                                <span class="h-2 w-2 rounded-full bg-amber-400 animate-pulse"></span>
                                <span>Presentación Finalizada</span>
                            </div>
                            <p class="text-[11px] text-slate-300">
                                Los jurados pueden registrar o verificar sus calificaciones hasta que el administrador llame al siguiente participante.
                            </p>
                        </div>

                    </div>

                    <!-- ========================================== -->
                    <!-- COLUMNA DERECHA: PARÁMETROS DE CALIFICACIÓN -->
                    <!-- ========================================== -->
                    <div class="lg:col-span-6 flex flex-col justify-center">
                        
                        @if (auth()->check() && auth()->user()->esJurado())
                            
                            @if ($juradoPresentacion)
                                {{-- Jurado Asignado --}}

                                @if ($calificacion && $calificacion->esEmitida())
                                    <!-- Caso: Calificación ya emitida -->
                                    <div class="bg-slate-900/80 rounded-2xl border border-slate-800 p-5 space-y-4 shadow-xl">
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                            <div class="flex items-center gap-2">
                                                <span class="text-emerald-400 font-bold">✓</span>
                                                <h3 class="text-sm font-bold text-white">Calificación Registrada</h3>
                                            </div>
                                            <span class="px-2 py-0.5 bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-bold rounded-full">
                                                Emitida
                                            </span>
                                        </div>

                                        <div class="bg-slate-950 rounded-xl p-3.5 text-center flex items-center justify-around">
                                            <span class="text-xs text-slate-400 uppercase font-semibold">Su Nota Total:</span>
                                            <span class="text-3xl font-black text-emerald-400 font-mono tabular-nums">
                                                {{ number_format($calificacion->nota_total, 2) }}
                                                <span class="text-xs text-slate-500 font-normal">/ 100 pts</span>
                                            </span>
                                        </div>

                                        <div class="space-y-1.5">
                                            @foreach ($calificacion->detalles as $det)
                                                @php
                                                    $cp = $categoriaParametros->firstWhere('id_parametro', $det->id_parametro);
                                                    $maxVal = $cp?->valor_maximo ?? 100;
                                                @endphp
                                                <div class="flex items-center justify-between px-3 py-1.5 rounded-lg bg-slate-950/60 border border-slate-800/80 text-xs">
                                                    <span class="text-slate-300 truncate mr-2">{{ $det->parametro?->parametro ?? "Parámetro #{$det->id_parametro}" }}</span>
                                                    <span class="font-mono font-bold text-amber-300 shrink-0">
                                                        {{ number_format($det->valor, 1) }} / {{ number_format($maxVal, 0) }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>

                                        @if ($presentacion->inscripcion?->categoria?->estaFinalizada())
                                            <div class="pt-1">
                                                <div class="py-2 px-3 rounded-lg bg-slate-950/80 border border-slate-800 text-slate-400 text-xs font-semibold text-center flex items-center justify-center gap-1.5">
                                                    <span>🔒</span>
                                                    <span>Calificaciones cerradas para esta categoría</span>
                                                </div>
                                            </div>
                                        @else
                                            <div data-contenedor-corregir class="{{ ($presentacion->estado === App\Support\Enums\PresentacionEstadoEnum::FINALIZADO) ? 'pt-1' : 'hidden pt-1' }}">
                                                <a href="{{ route('jurado.calificaciones.corregir.form', $presentacion) }}"
                                                   class="block text-center py-2 px-3 rounded-lg bg-amber-600/20 hover:bg-amber-600/30 border border-amber-500/40 text-amber-300 font-bold text-xs transition">
                                                    ✏️ Corregir calificación
                                                </a>
                                            </div>
                                        @endif
                                    </div>

                                @elseif ($presentacion->inscripcion?->categoria?->estaFinalizada())
                                    <!-- Caso: Calificaciones cerradas sin emitir voto -->
                                    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 text-center space-y-3 shadow-xl">
                                        <div class="w-12 h-12 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-400 mx-auto flex items-center justify-center text-xl">
                                            🔒
                                        </div>
                                        <h3 class="text-sm font-bold text-white">Calificaciones Cerradas</h3>
                                        <p class="text-xs text-slate-400">
                                            La categoría "{{ $presentacion->inscripcion?->categoria?->categoria }}" ha cerrado sus evaluaciones. No se permiten nuevos registros de notas.
                                        </p>
                                    </div>

                                @else
                                    <!-- Caso: Formulario de Calificación Activo (Compacto, sin scroll) -->
                                    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-4 sm:p-5 shadow-xl">
                                        
                                        <!-- Header de Calificación -->
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3 mb-3">
                                            <h3 class="text-sm font-bold text-white flex items-center gap-1.5">
                                                <span>⭐</span>
                                                <span>Calificar Participante</span>
                                            </h3>

                                            <div class="flex items-center gap-1.5 bg-slate-950 border border-amber-500/30 px-3 py-1 rounded-xl">
                                                <span class="text-[10px] uppercase font-bold text-slate-400">Total:</span>
                                                <span id="displayTotalTop" class="text-base font-black text-amber-400 font-mono tabular-nums">0.00</span>
                                                <span class="text-[10px] text-slate-500">/ 100</span>
                                            </div>
                                        </div>

                                        <form id="formCalificacion" method="POST" action="{{ route('jurado.calificaciones.guardar', $presentacion) }}" class="space-y-3">
                                            @csrf
                                            <input type="hidden" name="redirect_to" value="{{ request()->fullUrl() }}">

                                            <!-- Lista de Parámetros (Ajustada compacta) -->
                                            <div class="space-y-2.5">
                                                @foreach ($categoriaParametros as $cp)
                                                    @php
                                                        $idParam = $cp->id_parametro;
                                                        $nombreParam = $cp->parametro?->parametro ?? "Parámetro #{$idParam}";
                                                        $minVal = (float) $cp->valor_minimo;
                                                        $maxVal = (float) $cp->valor_maximo;
                                                        $valorPrevio = old("valores.{$idParam}", $minVal);
                                                    @endphp

                                                    <div class="bg-slate-950/70 border border-slate-800/90 rounded-xl p-2.5 transition hover:border-slate-700"
                                                         data-param-container="{{ $idParam }}">
                                                        
                                                        <div class="flex items-center justify-between text-xs mb-1">
                                                            <span class="font-semibold text-slate-200 truncate mr-2">{{ $nombreParam }}</span>
                                                            <div class="flex items-center gap-2 shrink-0">
                                                                <span class="text-[10px] text-slate-500 font-mono">Máx: {{ number_format($maxVal, 0) }}</span>
                                                                <span class="text-[11px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.2 rounded font-mono">
                                                                    <span id="badge_nota_{{ $idParam }}">{{ number_format($valorPrevio, 1) }}</span>
                                                                </span>
                                                            </div>
                                                        </div>

                                                        <div class="flex items-center gap-2.5">
                                                            <input type="range"
                                                                   id="slider_{{ $idParam }}"
                                                                   min="{{ $minVal }}"
                                                                   max="{{ $maxVal }}"
                                                                   step="0.5"
                                                                   value="{{ $valorPrevio }}"
                                                                   class="festival-slider flex-1"
                                                                   oninput="sincronizarDesdeSlider({{ $idParam }})">

                                                            <input type="number"
                                                                   name="valores[{{ $idParam }}]"
                                                                   id="input_{{ $idParam }}"
                                                                   min="{{ $minVal }}"
                                                                   max="{{ $maxVal }}"
                                                                   step="0.5"
                                                                   value="{{ $valorPrevio }}"
                                                                   required
                                                                   class="w-16 py-1 px-1.5 text-center text-xs font-black text-white bg-slate-900 border border-slate-700 rounded-lg focus:ring-1 focus:ring-amber-500 tabular-nums font-mono shrink-0"
                                                                   oninput="sincronizarDesdeInput({{ $idParam }})"
                                                                   onblur="validarLimiteBlur(this)">
                                                        </div>

                                                        @error("valores.{$idParam}")
                                                            <p class="text-[10px] text-rose-400 font-semibold mt-0.5">{{ $message }}</p>
                                                        @enderror
                                                    </div>
                                                @endforeach
                                            </div>

                                            <div class="pt-2">
                                                @php
                                                    $puedeCalificarAhora = ($presentacion !== null) && ($presentacion->cronometroCorriendo() || $presentacion->estado === App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO || $presentacion->estado === App\Support\Enums\PresentacionEstadoEnum::FINALIZADO || $presentacion->cronometro_iniciado_at !== null);
                                                @endphp
                                                <button type="button"
                                                        id="btnConfirmarCalificacion"
                                                        onclick="abrirModalConfirmacion()"
                                                        @disabled(! $puedeCalificarAhora)
                                                        class="w-full py-2.5 px-4 rounded-xl {{ $puedeCalificarAhora ? 'bg-gradient-to-r from-[#5c0618] to-rose-700 hover:from-[#720a20] hover:to-rose-600 text-white cursor-pointer shadow-lg' : 'bg-slate-800 border border-slate-700 text-slate-400 cursor-not-allowed opacity-60' }} font-bold text-xs uppercase tracking-wider transition flex items-center justify-center gap-1.5">
                                                    <span>{{ $puedeCalificarAhora ? '✓' : '⏳' }}</span>
                                                    <span id="btnCalificarTexto">{{ $puedeCalificarAhora ? 'Registrar calificación oficial' : 'Esperando inicio de presentación para calificar' }}</span>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif

                            @else
                                {{-- Jurado no asignado --}}
                                <div class="bg-slate-900/80 rounded-2xl border border-slate-800 p-6 text-center space-y-2">
                                    <p class="text-sm font-semibold text-slate-300">Modo Espectador</p>
                                    <p class="text-xs text-slate-500">No está asignado como jurado para esta presentación.</p>
                                </div>
                            @endif

                        @else
                            {{-- Administrador --}}
                            <div class="bg-slate-900/80 rounded-2xl border border-slate-800 p-5 space-y-3 shadow-xl">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <h3 class="text-xs font-bold text-slate-200 uppercase tracking-wider">Monitoreo de Jurados</h3>
                                    <span class="text-xs text-slate-400">{{ $juradosAsignados->count() }} asignados</span>
                                </div>

                                <div class="space-y-2">
                                    @forelse ($juradosAsignados as $jp)
                                        @php
                                            $calif = $jp->calificacion;
                                            $haCalificado = $calif && $calif->esEmitida();
                                        @endphp
                                        <div class="flex items-center justify-between p-2 rounded-lg bg-slate-950/60 border border-slate-800 text-xs"
                                             data-jurado-usuario-id="{{ $jp->id_usuario }}">
                                            <span class="text-slate-300 font-medium">{{ $jp->usuario?->nombreCompleto() ?? 'Jurado' }}</span>
                                            <span data-jurado-estado-badge class="px-2 py-0.5 rounded text-[10px] font-bold {{ $haCalificado ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300' }}">
                                                {{ $haCalificado ? 'Voto emitido' : 'Pendiente' }}
                                            </span>
                                        </div>
                                    @empty
                                        <p class="text-xs text-slate-500 text-center py-2">Sin jurados asignados.</p>
                                    @endforelse
                                </div>
                            </div>
                        @endif

                    </div>

                </div>
            </div>

            {{-- Escenario Vacío --}}
            <div data-escenario-vacio class="{{ $presentacion === null ? 'flex-1 flex flex-col items-center justify-center text-center px-6 py-12' : 'hidden flex-1 flex flex-col items-center justify-center text-center px-6 py-12' }}">
                <h2 class="text-3xl font-bold text-slate-200">No hay presentación en curso</h2>
                <p class="mt-2 text-xs sm:text-sm text-slate-400">Cuando se llame a una presentación aparecerá aquí.</p>
            </div>

        </main>

        <!-- Barra Inferior: Siguiente Presentación (Compacta y Fija) -->
        @if ($siguiente !== null)
            <footer data-siguiente-contenedor class="px-4 py-2 border-t border-slate-800/80 bg-slate-900/60 flex items-center justify-between gap-4 max-w-7xl w-full mx-auto shrink-0 text-xs">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] uppercase font-bold tracking-wider text-slate-500">Siguiente</span>
                    <span data-siguiente-nombre class="font-semibold truncate text-slate-200">{{ $siguiente->nombreMostrado() }}</span>
                </div>
                <span data-siguiente-categoria class="text-slate-400">{{ $siguiente->inscripcion?->categoria?->categoria ?? '—' }}</span>
            </footer>
        @endif

        <!-- Modal de Confirmación para Jurados -->
        <div id="modalConfirmacion" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm hidden">
            <div class="bg-slate-900 border border-slate-800 rounded-2xl p-6 max-w-sm w-full mx-4 shadow-2xl space-y-4">
                <h3 class="text-base font-black text-white">¿Confirmar calificación?</h3>
                <p class="text-xs text-slate-300 leading-relaxed">
                    Está registrando una calificación total de <strong id="modalNotaTotal" class="text-amber-400 font-bold font-mono">0.00</strong> puntos para <strong class="text-white">{{ $presentacion?->nombreMostrado() ?? '' }}</strong>.
                </p>
                <div class="flex items-center justify-end gap-2 pt-1">
                    <button type="button" onclick="cerrarModalConfirmacion()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-lg">
                        Cancelar
                    </button>
                    <button type="button" onclick="document.getElementById('formCalificacion').submit()" class="px-4 py-1.5 bg-rose-700 hover:bg-rose-600 text-white text-xs font-bold rounded-lg shadow">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>

        <!-- Notificación cuando se llama al siguiente participante al escenario -->
        <div id="avisoSiguienteParticipante" class="hidden fixed bottom-6 right-6 z-50 max-w-sm bg-slate-900/95 border-2 border-amber-500/80 rounded-2xl p-4 shadow-2xl space-y-2.5 backdrop-blur-sm animate-pulse">
            <div class="flex items-center gap-2 text-amber-300 font-bold text-xs uppercase tracking-wider">
                <span class="text-base">📢</span>
                <span>Nuevo participante en escenario</span>
            </div>
            <p id="avisoSiguienteTexto" class="text-xs text-slate-200 leading-relaxed">
                El siguiente participante ha sido llamado al escenario.
            </p>
            <div class="flex items-center justify-between pt-1 gap-2 border-t border-slate-800/80">
                <span id="avisoSiguienteContador" class="text-[11px] text-amber-400 font-mono font-bold">Cargando en 4s...</span>
                <button type="button" onclick="window.location.reload()" class="px-3 py-1 bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black rounded-lg transition shadow">
                    Cargar ahora
                </button>
            </div>
        </div>

        <script>
            function sincronizarDesdeSlider(idParam) {
                const slider = document.getElementById('slider_' + idParam);
                const input = document.getElementById('input_' + idParam);
                const badge = document.getElementById('badge_nota_' + idParam);

                if (slider && input) {
                    const min = parseFloat(slider.min) || 0;
                    const max = parseFloat(slider.max) || 100;
                    let val = parseFloat(slider.value) || 0;
                    if (val > max) val = max;
                    if (val < min) val = min;

                    slider.value = val;
                    input.value = val;
                    if (badge) badge.textContent = val.toFixed(1);
                    recalcularTotal();
                }
            }

            function sincronizarDesdeInput(idParam) {
                const slider = document.getElementById('slider_' + idParam);
                const input = document.getElementById('input_' + idParam);
                const badge = document.getElementById('badge_nota_' + idParam);

                if (slider && input) {
                    const min = parseFloat(input.min) || 0;
                    const max = parseFloat(input.max) || 100;
                    let val = parseFloat(input.value);

                    if (!isNaN(val)) {
                        if (val > max) {
                            val = max;
                            input.value = max;
                        } else if (val < 0) {
                            val = min;
                            input.value = min;
                        }
                        slider.value = Math.max(min, Math.min(max, val));
                        if (badge) badge.textContent = val.toFixed(1);
                    }
                    recalcularTotal();
                }
            }

            function validarLimiteBlur(input) {
                const min = parseFloat(input.min) || 0;
                const max = parseFloat(input.max) || 100;
                let val = parseFloat(input.value);

                if (isNaN(val) || val < min) {
                    input.value = min;
                } else if (val > max) {
                    input.value = max;
                }
                const idParam = input.id.replace('input_', '');
                sincronizarDesdeInput(idParam);
            }

            function recalcularTotal() {
                let total = 0;
                document.querySelectorAll('input[type="number"][name^="valores"]').forEach(inp => {
                    const min = parseFloat(inp.min) || 0;
                    const max = parseFloat(inp.max) || 100;
                    let val = parseFloat(inp.value);
                    if (isNaN(val) || val < min) {
                        val = min;
                    } else if (val > max) {
                        val = max;
                    }
                    total += val;
                });

                total = Math.round(total * 100) / 100;
                const formatted = total.toFixed(2);
                
                const displayTop = document.getElementById('displayTotalTop');
                const displayModal = document.getElementById('modalNotaTotal');
                
                if (displayTop) displayTop.textContent = formatted;
                if (displayModal) displayModal.textContent = formatted;
            }

            function abrirModalConfirmacion() {
                document.querySelectorAll('input[type="number"][name^="valores"]').forEach(inp => {
                    validarLimiteBlur(inp);
                });
                recalcularTotal();
                const modal = document.getElementById('modalConfirmacion');
                if (modal) modal.classList.remove('hidden');
            }

            function cerrarModalConfirmacion() {
                const modal = document.getElementById('modalConfirmacion');
                if (modal) modal.classList.add('hidden');
            }

            document.addEventListener('DOMContentLoaded', () => {
                recalcularTotal();
            });
        </script>
    </body>
</html>