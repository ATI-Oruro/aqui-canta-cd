<x-layouts.publico>
    <div class="text-center max-w-4xl mx-auto">
        <img src="/images/acb_logo.png" alt="ACB" class="h-20 w-20 mx-auto object-contain mb-3 drop-shadow-md">
        <h1 class="text-3xl sm:text-5xl font-black text-white drop-shadow-lg tracking-tight">Aquí… Canta Bolivia</h1>
        <p class="mt-2 text-base sm:text-lg text-white/90 font-medium">XXXIII Festival Nacional de la Canción Boliviana</p>
        <p class="mt-0.5 text-xs sm:text-sm text-white/70">Oruro · 20 y 21 de noviembre de 2026</p>
    </div>

    <!-- Navegación de Categorías -->
    <div class="mt-8 max-w-6xl mx-auto">
        <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none">
            @foreach ($categorias as $cat)
                @php
                    $activo = $categoriaSeleccionada && $categoriaSeleccionada->id === $cat->id;
                @endphp
                <a href="{{ route('inicio', ['categoria' => $cat->id]) }}"
                   @class([
                       'px-4 py-2 rounded-xl text-xs sm:text-sm font-bold whitespace-nowrap transition shadow-sm shrink-0 flex items-center gap-1.5',
                       'bg-amber-400 text-slate-950 ring-2 ring-amber-300' => $activo,
                       'bg-white/10 hover:bg-white/20 text-white backdrop-blur-sm' => ! $activo,
                   ])>
                    @if ($cat->estaFinalizada())
                        <span class="text-xs">🏆</span>
                    @elseif ($cat->estaRevelada())
                        <span class="text-xs">🔓</span>
                    @else
                        <span class="text-xs">🔒</span>
                    @endif
                    <span>{{ $cat->categoria }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <!-- Panel Principal de Ranking -->
    <div class="mt-6 max-w-6xl mx-auto space-y-6">
        @if ($categoriaSeleccionada)
            <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-xl border border-white/20 p-6 sm:p-8">
                <!-- Encabezado de la Categoría Seleccionada -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
                    <div>
                        <span class="text-xs uppercase font-bold tracking-wider text-indigo-600">Ranking en Vivo · Etapa Final</span>
                        <h2 class="text-2xl sm:text-3xl font-black text-slate-900 mt-0.5">
                            {{ $categoriaSeleccionada->categoria }}
                        </h2>
                    </div>

                    <div class="flex items-center gap-2">
                        @if ($categoriaSeleccionada->estaFinalizada())
                            <div class="px-3.5 py-1.5 bg-emerald-100 border border-emerald-300 text-emerald-900 text-xs sm:text-sm font-black rounded-xl flex items-center gap-1.5 shadow-sm">
                                <span>🏆</span>
                                <span>Categoría Finalizada Oficial</span>
                            </div>
                        @elseif ($categoriaSeleccionada->estaRevelada())
                            <div class="px-3.5 py-1.5 bg-purple-100 border border-purple-300 text-purple-900 text-xs sm:text-sm font-black rounded-xl flex items-center gap-1.5 shadow-sm">
                                <span>🔓</span>
                                <span>Jurado Secreto Revelado</span>
                            </div>
                        @else
                            <div class="px-3.5 py-1.5 bg-amber-50 border border-amber-300 text-amber-900 text-xs sm:text-sm font-bold rounded-xl flex items-center gap-1.5 shadow-sm">
                                <span>🔒</span>
                                <span>Jurado Secreto Oculto</span>
                            </div>
                        @endif
                    </div>
                </div>

                @if (! $categoriaSeleccionada->estaRevelada())
                    <!-- Aviso de Secreto Oculto -->
                    <div class="my-4 p-4 rounded-xl bg-amber-500/10 border border-amber-400/30 text-amber-900 text-xs sm:text-sm flex items-center gap-3">
                        <span class="text-2xl">🔒</span>
                        <div>
                            <p class="font-bold">El voto del Jurado Secreto se encuentra oculto temporalmente.</p>
                            <p class="text-xs text-amber-800">
                                La calificación del jurado secreto está calculada internamente en el promedio pero se revelará públicamente al finalizar la categoría.
                            </p>
                        </div>
                    </div>
                @endif

                <!-- Tabla de Ranking -->
                <div class="mt-6 overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-800">
                        <thead class="bg-slate-100 text-slate-600 text-xs font-bold uppercase tracking-wider rounded-lg">
                            <tr>
                                <th class="px-4 py-3 text-center w-16">Pos.</th>
                                <th class="px-4 py-3">Participante / Presentación</th>
                                <th class="px-4 py-3">Origen</th>
                                <th class="px-4 py-3">Evaluación Mesa de Jurados</th>
                                <th class="px-4 py-3 text-right">Nota Final</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($ranking as $item)
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="px-4 py-4 text-center">
                                        @if ($item['posicion'] === 1 && $item['nota_final'] !== null)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-400 text-slate-950 font-black text-sm shadow">
                                                🥇
                                            </span>
                                        @elseif ($item['posicion'] === 2 && $item['nota_final'] !== null)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-slate-200 text-slate-800 font-black text-sm shadow">
                                                🥈
                                            </span>
                                        @elseif ($item['posicion'] === 3 && $item['nota_final'] !== null)
                                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-amber-700 text-white font-black text-sm shadow">
                                                🥉
                                            </span>
                                        @else
                                            <span class="font-bold text-slate-400 text-sm">
                                                {{ $item['posicion'] }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            @if ($item['foto_url'])
                                                <img src="{{ $item['foto_url'] }}" alt="{{ $item['nombre'] }}" class="h-10 w-10 rounded-full object-cover border border-slate-200 shadow-sm">
                                            @endif
                                            <div>
                                                <h4 class="font-bold text-slate-900 text-base leading-tight">{{ $item['nombre'] }}</h4>
                                                <p class="text-xs text-slate-500 mt-0.5">
                                                    {{ implode(', ', $item['participantes']) }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 font-semibold text-slate-700">
                                        {{ $item['departamento'] ?? 'Nacional' }}
                                    </td>

                                    <!-- Desglose de Jurados -->
                                    <td class="px-4 py-4">
                                        <div class="flex flex-wrap gap-1.5">
                                            @forelse ($item['jurados'] as $j)
                                                <div @class([
                                                    'px-2.5 py-1 rounded-lg text-xs font-semibold flex items-center gap-1 border',
                                                    'bg-purple-100 text-purple-900 border-purple-200 shadow-sm' => $j['es_secreto'] && $j['es_revelado'],
                                                    'bg-amber-100 text-amber-900 border-amber-300' => $j['es_secreto'] && ! $j['es_revelado'],
                                                    'bg-slate-100 text-slate-700 border-slate-200' => ! $j['es_secreto'] && $j['calificado'],
                                                    'bg-slate-50 text-slate-400 border-dashed border-slate-200' => ! $j['es_secreto'] && ! $j['calificado'],
                                                ]) title="{{ $j['jurado_nombre'] }}">
                                                    @if ($j['es_secreto'])
                                                        <span>🔒</span>
                                                    @endif
                                                    <span class="truncate max-w-[100px]">{{ $j['jurado_nombre'] }}:</span>
                                                    @if ($j['es_revelado'] && $j['nota_total'] !== null)
                                                        <span class="font-black tabular-nums">{{ number_format($j['nota_total'], 1) }}</span>
                                                    @elseif ($j['es_secreto'] && ! $j['es_revelado'])
                                                        <span class="font-bold text-amber-700">???</span>
                                                    @elseif ($j['calificado'] && $j['nota_total'] !== null)
                                                        <span class="font-black tabular-nums">{{ number_format($j['nota_total'], 1) }}</span>
                                                    @else
                                                        <span class="text-slate-400">—</span>
                                                    @endif
                                                </div>
                                            @empty
                                                <span class="text-xs text-slate-400 italic">Sin jurados asignados</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <!-- Nota Final -->
                                    <td class="px-4 py-4 text-right">
                                        @if ($item['nota_final'] !== null)
                                            <div class="text-2xl font-black text-amber-600 tabular-nums">
                                                {{ number_format($item['nota_final'], 2) }}
                                            </div>
                                            <span class="text-[10px] text-slate-400 font-semibold uppercase">Puntaje Oficial</span>
                                        @else
                                            <span class="inline-block px-2.5 py-1 bg-slate-100 text-slate-500 text-xs font-semibold rounded-lg">
                                                En curso
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-12 text-center text-slate-400 text-sm">
                                        No hay presentaciones registradas para esta categoría.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.Echo) {
                // Escuchar actualizaciones de ranking en vivo
                window.Echo.channel('ranking')
                    .listen('.ranking.actualizado', function (e) {
                        if (e.categoria_id == {{ $categoriaSeleccionada?->id ?? 0 }}) {
                            window.location.reload();
                        }
                    })
                    .listen('.jurado_secreto.revelado', function (e) {
                        if (e.categoria_id == {{ $categoriaSeleccionada?->id ?? 0 }}) {
                            window.location.reload();
                        }
                    })
                    .listen('.categoria.finalizada', function (e) {
                        if (e.categoria_id == {{ $categoriaSeleccionada?->id ?? 0 }}) {
                            window.location.reload();
                        }
                    });
            }
        });
    </script>
    @endpush
</x-layouts.publico>