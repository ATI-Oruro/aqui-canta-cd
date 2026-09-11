<x-layouts.jurado>
    <x-slot:header>
        <h2 class="text-xl font-semibold text-slate-800">Panel de Jurado</h2>
    </x-slot:header>

    <x-slot:sidebar>
        <a href="{{ route('jurado.inicio') }}" class="block px-4 py-2 rounded-lg bg-slate-700 text-white font-semibold">Inicio</a>
        <a href="{{ route('jurado.presentaciones.actual') }}" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white">Presentación actual</a>
        <a href="{{ route('jurado.presentaciones.actual') }}" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white">Cronómetro</a>
    </x-slot:sidebar>

    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 max-w-2xl">
        <h3 class="font-semibold text-slate-800">Bienvenido/a, {{ auth()->user()->nombre }}</h3>

        @php
            $asignaciones = auth()->user()->presentacionesJurado()
                ->withPivot(['es_controlador'])
                ->orderByDesc('updated_at')
                ->get();
        @endphp

        @if ($asignaciones->isEmpty())
            <p class="mt-2 text-sm text-slate-500">
                Aún no tiene presentaciones asignadas. Cuando el administrador le asigne participaciones
                aparecerán aquí junto con el cronómetro en vivo.
            </p>
        @else
            <div class="mt-4 space-y-3">
                @foreach ($asignaciones as $presentacion)
                    @php
                        $esControlador = (bool) ($presentacion->pivot->es_controlador ?? false);
                        $puedeOperarCronometro = $esControlador
                            && in_array($presentacion->estado?->value, [
                                App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO->value,
                                App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO->value,
                            ], true);
                    @endphp
                    <div class="flex items-center justify-between gap-4 border border-slate-200 rounded-lg p-3">
                        <div>
                            <p class="font-medium text-slate-800">{{ $presentacion->nombreMostrado() }}</p>
                            <p class="text-xs text-slate-400">
                                {{ $presentacion->inscripcion?->categoria?->categoria ?? '—' }} · estado {{ $presentacion->estado?->label() ?? '—' }}
                            </p>
                        </div>
                        <div class="flex items-center gap-3">
                            @if ($puedeOperarCronometro)
                                @if ($presentacion->cronometroCorriendo())
                                    <form method="POST" action="{{ route('presentaciones.cronometro.detener', $presentacion) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg">
                                            Detener cronómetro
                                        </button>
                                    </form>
                                @elseif ($presentacion->cronometro_iniciado_at === null)
                                    <form method="POST" action="{{ route('presentaciones.cronometro.iniciar', $presentacion) }}">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-lg">
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
            <a href="{{ route('jurado.presentaciones.actual') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold rounded-lg">
                Ver escenario en vivo
            </a>
        @endif
    </div>
</x-layouts.jurado>