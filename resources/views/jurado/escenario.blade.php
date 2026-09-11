<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'ACB Canta Bolivia') }} — Escenario</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen bg-slate-950 text-white flex flex-col"
          data-escenario-raiz
          data-escenario-id="{{ $presentacion->id ?? '' }}"
          data-cronometro-corriendo="{{ ($cronometro['corriendo'] ?? false) ? '1' : '0' }}"
          data-segundos-restantes="{{ $cronometro['segundos_restantes'] ?? (($presentacion->tiempo_maximo ?? 10) * 60) }}"
          data-tiempo-maximo="{{ $presentacion->tiempo_maximo ?? 10 }}">

        {{-- Bloque de Presentación Activa --}}
        <div data-escenario-activo class="{{ $presentacion !== null ? 'flex-1 flex flex-col items-center justify-center text-center px-6' : 'hidden flex-1 flex flex-col items-center justify-center text-center px-6' }}">
            <div class="w-full max-w-4xl">
                <span data-estado class="inline-block px-4 py-1 rounded-full text-sm font-semibold tracking-wide uppercase {{ ($presentacion?->estado?->value === App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO->value) ? 'bg-emerald-500/20 text-emerald-300' : (($presentacion?->estado?->value === App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO->value) ? 'bg-amber-500/20 text-amber-300' : 'bg-slate-500/20 text-slate-300') }}">
                    {{ $presentacion?->estado?->label() ?? 'En escenario' }}
                </span>

                <p data-categoria-fase class="mt-6 text-xs md:text-sm uppercase tracking-[0.3em] text-slate-400">
                    {{ $presentacion?->inscripcion?->categoria?->categoria ?? '' }} · {{ $presentacion?->inscripcion?->fase?->fase?->label() ?? '' }}
                </p>

                <h1 data-nombre class="mt-4 text-4xl md:text-6xl font-bold leading-tight">
                    {{ $presentacion?->nombreMostrado() ?? '' }}
                </h1>

                <div class="mt-8 flex items-center justify-center gap-4">
                    <span class="text-6xl md:text-8xl font-black tabular-nums tracking-tight" data-cronometro-display>
                        {{ gmdate('i:s', $cronometro['segundos_restantes'] ?? (($presentacion->tiempo_maximo ?? 10) * 60)) }}
                    </span>
                    <span class="text-3xl md:text-4xl font-semibold text-slate-500 tabular-nums" data-tiempo-max-display>
                        / {{ $presentacion->tiempo_maximo ?? 10 }}:00
                    </span>
                </div>

                <p class="mt-4 text-sm text-slate-400" data-tiempo-max-texto>
                    Tiempo máximo de presentación: {{ $presentacion->tiempo_maximo ?? 10 }} minuto(s)
                </p>
            </div>
        </div>

        {{-- Bloque Vacío (cuando no hay presentación en curso) --}}
        <div data-escenario-vacio class="{{ $presentacion === null ? 'flex-1 flex flex-col items-center justify-center text-center px-6' : 'hidden flex-1 flex flex-col items-center justify-center text-center px-6' }}">
            <h1 class="text-3xl md:text-5xl font-bold text-slate-200">No hay presentación en curso</h1>
            <p class="mt-4 text-slate-400">Cuando un administrador llame a una presentación aparecerá aquí.</p>
        </div>

        @if ($siguiente !== null)
            <div data-siguiente-contenedor class="px-6 py-4 border-t border-slate-800 bg-slate-900/50 flex items-center justify-between gap-4 max-w-5xl w-full mx-auto">
                <span class="text-xs uppercase tracking-wider text-slate-500">Siguiente</span>
                <span data-siguiente-nombre class="text-lg font-semibold truncate text-slate-200">{{ $siguiente->nombreMostrado() }}</span>
                <span data-siguiente-categoria class="text-sm text-slate-400">{{ $siguiente->inscripcion?->categoria?->categoria ?? '—' }}</span>
            </div>
        @endif
    </body>
</html>