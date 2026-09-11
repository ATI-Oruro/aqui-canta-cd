<x-layouts.jurado>
    <x-slot:header>
        <h2 class="text-xl font-semibold text-slate-800">Panel de Jurado</h2>
    </x-slot:header>

    <x-slot:sidebar>
        <a href="{{ route('jurado.inicio') }}" class="block px-4 py-2 rounded-lg bg-slate-700 text-white font-semibold">Inicio</a>
        <a href="{{ route('jurado.presentaciones.actual') }}" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white">Presentación actual</a>
        <a href="{{ route('jurado.presentaciones.actual') }}" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white">Cronómetro</a>
    </x-slot:sidebar>

    @if (session('success'))
        <div class="mb-4 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="mb-4 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if (session('info'))
        <div class="mb-4 p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm">
            {{ session('info') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-4xl">
        <h3 class="font-semibold text-slate-800">Bienvenido/a, {{ auth()->user()->nombre }}</h3>

        @php
            $asignaciones = auth()->user()->presentacionesJurado()
                ->withPivot(['id', 'es_controlador', 'estado'])
                ->with(['inscripcion.categoria', 'inscripcion.departamento'])
                ->orderByDesc('updated_at')
                ->get();
        @endphp

        @if ($asignaciones->isEmpty())
            <p class="mt-2 text-sm text-slate-500">
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
</x-layouts.jurado>