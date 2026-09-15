<x-layouts.jurado>
    <x-slot:header>
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-semibold text-slate-800">Panel de Jurado</h2>
            @if ($esDelegadoMesa ?? false)
                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-900 border border-amber-300 shadow-sm flex items-center gap-1.5">
                    <span>⏱️</span>
                    <span>Jurado Delegado de Mesa / Control de Tiempo</span>
                </span>
            @endif
        </div>
    </x-slot:header>

    <x-slot:sidebar>
        <a href="{{ route('jurado.inicio') }}" class="block px-4 py-2 rounded-lg bg-slate-700 text-white font-semibold">Inicio</a>
        <a href="{{ route('jurado.presentaciones.actual') }}" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white">Presentación actual</a>
        <a href="{{ route('jurado.presentaciones.actual') }}" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white">Cronómetro</a>
    </x-slot:sidebar>

    @if (session('status'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold shadow-sm">
            ✓ {{ session('status') }}
        </div>
    @endif

    @if (session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="space-y-6 max-w-6xl">

        <!-- ============================================================== -->
        <!-- SECCIÓN EXCLUSIVA: MESA DE CONTROL DEL JURADO DELEGADO          -->
        <!-- ============================================================== -->
        @if ($esDelegadoMesa ?? false)
            <div class="bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 text-white rounded-2xl border border-amber-500/30 p-6 shadow-xl space-y-6">
                
                <!-- Encabezado de Mesa -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xl">🎛️</span>
                            <h3 class="text-lg font-bold text-white tracking-wide">Mesa de Control y Evaluación</h3>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">
                            Seleccione la categoría activa para coordinar las presentaciones en vivo, cerrar votaciones y proclamar el podio.
                        </p>
                    </div>

                    @if ($categoriaActiva)
                        <div class="flex flex-wrap items-center gap-2">
                            <!-- Botón Cerrar Calificaciones -->
                            @if (! $categoriaActiva->estaFinalizada())
                                <form method="POST" action="{{ route('mesa.categorias.cerrar', $categoriaActiva) }}" onsubmit="return confirm('¿Está seguro de cerrar las calificaciones de la categoría {{ $categoriaActiva->categoria }}? Una vez cerrada se actualizarán las posiciones de todos los participantes.')">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs shadow transition flex items-center gap-1.5">
                                        <span>🔒</span>
                                        <span>Cerrar Votaciones de Categoría</span>
                                    </button>
                                </form>
                            @else
                                <span class="px-3 py-1.5 rounded-xl bg-emerald-950/80 border border-emerald-500/40 text-emerald-300 text-xs font-bold">
                                    ✓ Votaciones Cerradas
                                </span>
                            @endif

                            <!-- Botón Revelar Ganador / Jurado Secreto -->
                            @if (! $categoriaActiva->estaRevelada())
                                <form method="POST" action="{{ route('mesa.categorias.revelar', $categoriaActiva) }}" onsubmit="return confirm('¿Revelar la calificación del jurado secreto y proclamar el Podio F1 de ganadores en la vista pública?')">
                                    @csrf
                                    <button type="submit" class="px-3.5 py-2 rounded-xl bg-gradient-to-r from-purple-700 to-indigo-700 hover:from-purple-600 hover:to-indigo-600 text-white font-black text-xs shadow-lg transition flex items-center gap-1.5">
                                        <span>🏆</span>
                                        <span>Revelar Ganador (Podio F1)</span>
                                    </button>
                                </form>
                            @else
                                <span class="px-3 py-1.5 rounded-xl bg-purple-950/80 border border-purple-500/40 text-purple-300 text-xs font-bold">
                                    🏆 Podio Revelado
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Selector de Categoría a Calificar -->
                <div class="space-y-2">
                    <label class="text-xs font-bold uppercase tracking-wider text-amber-400 block">
                        Categoría en Evaluación:
                    </label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-2">
                        @foreach ($categorias as $cat)
                            @php
                                $esActiva = ($categoriaActiva?->id === $cat->id);
                            @endphp
                            <form method="POST" action="{{ route('mesa.categorias.seleccionar', $cat) }}" class="w-full">
                                @csrf
                                <button type="submit"
                                        class="w-full text-left p-2.5 rounded-xl border text-xs transition {{ $esActiva ? 'bg-amber-500/20 text-amber-200 border-amber-500 shadow-md font-bold ring-1 ring-amber-400' : 'bg-slate-950/60 text-slate-300 border-slate-800 hover:bg-slate-800 hover:border-slate-700' }}">
                                    <div class="flex items-center justify-between gap-1">
                                        <span class="truncate font-semibold">{{ $cat->categoria }}</span>
                                        @if ($cat->estaFinalizada())
                                            <span class="text-[10px] text-emerald-400 shrink-0">🏆</span>
                                        @elseif ($cat->estaRevelada())
                                            <span class="text-[10px] text-purple-400 shrink-0">✨</span>
                                        @endif
                                    </div>
                                    <span class="text-[10px] text-slate-400 block mt-0.5">
                                        {{ $cat->tiempo_presentacion }} min
                                    </span>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>

                <!-- Participantes de la Categoría Seleccionada en Orden de Presentación -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-300">
                                Participantes en Orden de Presentación ({{ $presentacionesCategoria->count() }})
                            </span>
                        </div>
                        <a href="{{ route('jurado.presentaciones.actual') }}" class="text-xs font-bold text-amber-400 hover:text-amber-300 transition flex items-center gap-1">
                            <span>Ver Escenario en Vivo</span>
                            <span>→</span>
                        </a>
                    </div>

                    @if ($presentacionesCategoria->isEmpty())
                        <div class="p-8 text-center text-slate-400 text-xs bg-slate-950/50 rounded-xl border border-slate-800">
                            No hay participantes registrados en la categoría seleccionada.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs text-slate-300">
                                <thead class="bg-slate-950 text-slate-400 uppercase tracking-wider text-[10px] font-bold border-b border-slate-800">
                                    <tr>
                                        <th class="px-3 py-2.5 text-center w-12">Orden</th>
                                        <th class="px-3 py-2.5">Participación / Integrantes</th>
                                        <th class="px-3 py-2.5">Departamento</th>
                                        <th class="px-3 py-2.5 text-center">Estado</th>
                                        <th class="px-3 py-2.5 text-center">Jurados</th>
                                        <th class="px-3 py-2.5 text-right">Nota Final</th>
                                        <th class="px-3 py-2.5 text-right">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                                    @foreach ($presentacionesCategoria as $idx => $pres)
                                        @php
                                            $juradosCalificados = $pres->jurados->filter(fn ($jp) => $jp->calificacion && in_array($jp->calificacion->estado, [\App\Support\Enums\CalificacionEstadoEnum::EMITIDO, \App\Support\Enums\CalificacionEstadoEnum::CORREGIDO], true))->count();
                                            $totalJurados = $pres->jurados->count();
                                            $enEscenario = $pres->estado === \App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO;
                                            $participando = $pres->estado === \App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO;
                                            $finalizado = $pres->estado === \App\Support\Enums\PresentacionEstadoEnum::FINALIZADO;
                                        @endphp
                                        <tr class="hover:bg-slate-900/60 transition {{ $participando ? 'bg-emerald-950/20' : ($enEscenario ? 'bg-amber-950/20' : '') }}">
                                            
                                            <!-- Orden -->
                                            <td class="px-3 py-2.5 text-center font-mono font-bold text-slate-400">
                                                #{{ $idx + 1 }}
                                            </td>

                                            <!-- Participante -->
                                            <td class="px-3 py-2.5">
                                                <div class="flex items-center gap-2">
                                                    @if ($pres->fotoUrl())
                                                        <img src="{{ $pres->fotoUrl() }}" alt="{{ $pres->nombreMostrado() }}" class="w-7 h-7 rounded-lg object-cover border border-slate-700 shrink-0">
                                                    @endif
                                                    <div>
                                                        <p class="font-bold text-white">{{ $pres->nombreMostrado() }}</p>
                                                        @if ($pres->inscripcion?->participantes->isNotEmpty() && $pres->inscripcion->participantes->first()->nombreCompleto() !== $pres->nombreMostrado())
                                                            <p class="text-[10px] text-slate-400">{{ $pres->inscripcion->participantes->pluck('nombre_completo')->implode(' · ') }}</p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Departamento -->
                                            <td class="px-3 py-2.5 text-slate-300">
                                                {{ $pres->inscripcion?->departamento?->nombre ?? 'Nacional' }}
                                            </td>

                                            <!-- Estado -->
                                            <td class="px-3 py-2.5 text-center">
                                                @if ($participando)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 animate-pulse">
                                                        Participando
                                                    </span>
                                                @elseif ($enEscenario)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">
                                                        En Escenario
                                                    </span>
                                                @elseif ($finalizado)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300">
                                                        Finalizado
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-900 text-slate-400">
                                                        Por Participar
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Jurados -->
                                            <td class="px-3 py-2.5 text-center font-mono text-[11px]">
                                                <span class="{{ $juradosCalificados === $totalJurados && $totalJurados > 0 ? 'text-emerald-400 font-bold' : 'text-amber-400' }}">
                                                    {{ $juradosCalificados }}/{{ $totalJurados }}
                                                </span>
                                            </td>

                                            <!-- Nota Final -->
                                            <td class="px-3 py-2.5 text-right font-mono font-bold">
                                                @if ($pres->nota_final !== null)
                                                    <span class="text-amber-300">{{ number_format($pres->nota_final, 2) }}</span>
                                                @else
                                                    <span class="text-slate-500 text-[10px] font-normal italic">Pendiente</span>
                                                @endif
                                            </td>

                                            <!-- Acciones -->
                                            <td class="px-3 py-2.5 text-right">
                                                <div class="flex items-center justify-end gap-1.5">
                                                    @if ($pres->estado === \App\Support\Enums\PresentacionEstadoEnum::POR_PARTICIPAR)
                                                        <form method="POST" action="{{ route('mesa.presentaciones.transicion', $pres) }}">
                                                            @csrf
                                                            <input type="hidden" name="estado" value="{{ \App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO->value }}">
                                                            <button type="submit" class="px-2.5 py-1 rounded bg-amber-600 hover:bg-amber-500 text-white text-[10px] font-bold transition shadow">
                                                                Llamar al Escenario
                                                            </button>
                                                        </form>
                                                    @elseif ($enEscenario)
                                                        <form method="POST" action="{{ route('presentaciones.cronometro.iniciar', $pres) }}">
                                                            @csrf
                                                            <button type="submit" class="px-2.5 py-1 rounded bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-bold transition shadow">
                                                                ▶ Iniciar
                                                            </button>
                                                        </form>
                                                    @elseif ($participando)
                                                        <form method="POST" action="{{ route('presentaciones.cronometro.detener', $pres) }}">
                                                            @csrf
                                                            <button type="submit" class="px-2.5 py-1 rounded bg-rose-600 hover:bg-rose-500 text-white text-[10px] font-bold transition shadow">
                                                                ⏹ Detener
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <a href="{{ route('jurado.calificaciones.evaluar', $pres) }}" class="px-2 py-1 rounded bg-slate-800 hover:bg-slate-700 text-slate-200 text-[10px] font-semibold transition border border-slate-700">
                                                        Evaluar
                                                    </a>
                                                </div>
                                            </td>

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>

            </div>
        @endif

        <!-- ============================================================== -->
        <!-- SECCIÓN DE CALIFICACIONES ASIGNADAS AL JURADO                   -->
        <!-- ============================================================== -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <h3 class="font-semibold text-slate-800 text-base">Mis Asignaciones de Evaluación</h3>
            <p class="text-xs text-slate-500 mt-0.5">Participaciones asignadas a su mesa de calificación individual.</p>

            @php
                $asignaciones = auth()->user()->presentacionesJurado()
                    ->withPivot(['id', 'es_controlador', 'estado'])
                    ->with(['inscripcion.categoria', 'inscripcion.departamento'])
                    ->orderByDesc('updated_at')
                    ->get();
            @endphp

            @if ($asignaciones->isEmpty())
                <p class="mt-4 text-sm text-slate-500 bg-slate-50 p-4 rounded-xl border border-slate-200">
                    Aún no tiene presentaciones asignadas. Cuando el administrador le asigne participaciones
                    aparecerán aquí junto con el cronómetro en vivo y las opciones de evaluación.
                </p>
            @else
                <div class="mt-4 space-y-3">
                    @foreach ($asignaciones as $presentacion)
                        @php
                            $juradoPresId = $presentacion->pivot->id ?? null;
                            $calificacion = $juradoPresId ? App\Modules\Calificaciones\Models\Calificacion::where('id_jurado_presentacion', $juradoPresId)->first() : null;
                            $esControlador = (bool) ($presentacion->pivot->es_controlador ?? false);
                            $puedeOperarCronometro = $esControlador
                                && in_array($presentacion->estado?->value, [
                                    App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO->value,
                                    App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO->value,
                                ], true);

                            $puedeCalificar = in_array($presentacion->estado?->value, [
                                App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO->value,
                                App\Support\Enums\PresentacionEstadoEnum::FINALIZADO->value,
                            ], true);

                            $esFinalizado = $presentacion->estado?->value === App\Support\Enums\PresentacionEstadoEnum::FINALIZADO->value;
                        @endphp
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-slate-200 rounded-lg p-4 bg-slate-50/50">
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="font-medium text-slate-800">{{ $presentacion->nombreMostrado() }}</p>
                                    @if ($calificacion && $calificacion->esEmitida())
                                        <span class="px-2 py-0.5 bg-emerald-100 text-emerald-800 text-xs font-semibold rounded-full border border-emerald-200">
                                            Nota: {{ number_format($calificacion->nota_total, 2) }} pts
                                        </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $presentacion->inscripcion?->categoria?->categoria ?? '—' }}
                                    @if ($presentacion->inscripcion?->departamento)
                                        · {{ $presentacion->inscripcion->departamento->nombre }}
                                    @endif
                                    · estado {{ $presentacion->estado?->label() ?? '—' }}
                                </p>
                            </div>

                            <div class="flex flex-wrap items-center gap-3">
                                @if ($calificacion && $calificacion->esEmitida())
                                    @if ($esFinalizado)
                                        <a href="{{ route('jurado.calificaciones.corregir.form', $presentacion) }}"
                                           class="px-3 py-1.5 bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                            Corregir nota
                                        </a>
                                    @endif
                                @elseif ($puedeCalificar)
                                    <a href="{{ route('jurado.calificaciones.evaluar', $presentacion) }}"
                                       class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                        ⭐ Calificar
                                    </a>
                                @endif

                                @if ($puedeOperarCronometro)
                                    @if ($presentacion->cronometroCorriendo())
                                        <form method="POST" action="{{ route('presentaciones.cronometro.detener', $presentacion) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-500 text-white text-xs font-semibold rounded-lg shadow-sm">
                                                Detener cronómetro
                                            </button>
                                        </form>
                                    @elseif ($presentacion->cronometro_iniciado_at === null)
                                        <form method="POST" action="{{ route('presentaciones.cronometro.iniciar', $presentacion) }}">
                                            @csrf
                                            <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg shadow-sm">
                                                Iniciar cronómetro
                                            </button>
                                        </form>
                                    @endif
                                @endif

                                <span class="text-sm text-slate-500 font-mono tabular-nums">
                                    {{ gmdate('i:s', $presentacion->segundosRestantes()) }} / {{ $presentacion->tiempo_maximo }}:00
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    <a href="{{ route('jurado.presentaciones.actual') }}" class="inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg shadow transition">
                        Ver escenario en vivo
                    </a>
                </div>
            @endif
        </div>

    </div>
</x-layouts.jurado>