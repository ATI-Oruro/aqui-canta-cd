<x-layouts.publico>
    
    @php
        $catId = $categoriaSeleccionada?->id;
        $deptoId = $departamentoSeleccionadoId;
        $vista = $modoVista ?? 'tarjetas';

        $banderas = [
            'Oruro' => '⚪🔴',
            'La Paz' => '🔴🟢',
            'Cochabamba' => '🔵',
            'Potosí' => '🔴⚪',
            'Santa Cruz' => '🟢⚪🟢',
            'Chuquisaca' => '⚪🔴',
            'Tarija' => '🔴⚪',
            'Beni' => '🟢',
            'Pando' => '⚪🟢',
        ];
    @endphp

    <div class="space-y-8">

        <!-- ============================================================== -->
        <!-- BARRA SUPERIOR DE FILTROS (ORURO CARMESÍ, GUINDO Y DORADO)      -->
        <!-- ============================================================== -->
        <div class="bg-gradient-to-r from-[#120308] via-[#0b0206] to-[#120308] rounded-2xl border border-[#6b0015]/50 p-4 sm:p-5 shadow-2xl backdrop-blur-md flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Selectores / Dropdowns de Filtros -->
            <div class="flex flex-wrap items-center gap-3 sm:gap-4">
                
                <!-- Dropdown Categoría -->
                <div class="relative min-w-[240px]">
                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-[#d4af37] mb-1 font-cinzel">
                        Categoría
                    </label>
                    <select onchange="window.location.href=this.value"
                            class="w-full bg-[#070104] border border-[#6b0015] focus:border-amber-400 focus:ring-1 focus:ring-amber-400 rounded-xl px-3.5 py-2 text-xs font-semibold text-amber-100 cursor-pointer shadow-inner">
                        <option value="{{ route('inicio', array_filter(['departamento' => $deptoId, 'vista' => $vista])) }}">
                            ⭐ Todas las 13 Categorías ({{ $conteoCategorias->sum('total') }})
                        </option>
                        @foreach ($conteoCategorias as $cat)
                            <option value="{{ route('inicio', array_filter(['categoria' => $cat['id'], 'departamento' => $deptoId, 'vista' => $vista])) }}"
                                    {{ $catId === $cat['id'] ? 'selected' : '' }}>
                                {{ $cat['finalizada'] ? '🏆' : '✨' }} {{ $cat['nombre'] }} ({{ $cat['total'] }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Dropdown Departamento -->
                <div class="relative min-w-[210px]">
                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-[#d4af37] mb-1 font-cinzel">
                        Departamento
                    </label>
                    <select onchange="window.location.href=this.value"
                            class="w-full bg-[#070104] border border-[#6b0015] focus:border-amber-400 focus:ring-1 focus:ring-amber-400 rounded-xl px-3.5 py-2 text-xs font-semibold text-slate-200 cursor-pointer shadow-inner">
                        <option value="{{ route('inicio', array_filter(['categoria' => $catId, 'vista' => $vista])) }}">
                            🇧🇴 Todos los Departamentos ({{ $totalGeneral }})
                        </option>
                        @foreach ($conteoDepartamentos as $depto)
                            <option value="{{ route('inicio', array_filter(['categoria' => $catId, 'departamento' => $depto['id'], 'vista' => $vista])) }}"
                                    {{ $deptoId === $depto['id'] ? 'selected' : '' }}>
                                {{ $banderas[$depto['nombre']] ?? '🇧🇴' }} {{ $depto['nombre'] }} ({{ $depto['total'] }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Selector de Modo de Vista (Tarjetas / Tabla) -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-[#d4af37] mb-1 font-cinzel">
                        Vista
                    </label>
                    <div class="flex items-center gap-1 bg-[#070104] border border-[#6b0015] p-0.5 rounded-xl">
                        <a href="{{ route('inicio', array_filter(['categoria' => $catId, 'departamento' => $deptoId, 'vista' => 'tarjetas'])) }}"
                           class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $vista === 'tarjetas' ? 'bg-[#3b000b] text-amber-200 shadow-md border border-[#800020]' : 'text-slate-400 hover:text-white' }}"
                           title="Modo Retratos de Gala">
                            Tarjetas
                        </a>
                        <a href="{{ route('inicio', array_filter(['categoria' => $catId, 'departamento' => $deptoId, 'vista' => 'tabla'])) }}"
                           class="px-3.5 py-1.5 rounded-lg text-xs font-bold transition {{ $vista === 'tabla' ? 'bg-[#3b000b] text-amber-200 shadow-md border border-[#800020]' : 'text-slate-400 hover:text-white' }}"
                           title="Modo Tabla Oficial">
                            Tabla
                        </a>
                    </div>
                </div>

            </div>

            <!-- Estado de la Categoría y Filtros Activos -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                
                @if ($catId || $deptoId)
                    <a href="{{ route('inicio', ['vista' => $vista]) }}"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#1f050f] hover:bg-[#340719] text-amber-200/80 hover:text-white text-xs font-semibold border border-[#6b0015] transition">
                        <span>✕</span> Limpiar filtros
                    </a>
                @endif

                @if ($categoriaSeleccionada && $categoriaSeleccionada->estaFinalizada())
                    <div class="px-3.5 py-1.5 rounded-xl bg-[#062013] border border-emerald-500/40 text-emerald-300 text-xs font-bold flex items-center gap-2 shadow-lg">
                        <span>🏆</span>
                        <span class="font-cinzel tracking-wider uppercase text-[11px]">Resultados Oficiales Proclamados</span>
                    </div>
                @elseif ($categoriaSeleccionada && $categoriaSeleccionada->estaRevelada())
                    <div class="px-3.5 py-1.5 rounded-xl bg-[#1d0628] border border-purple-500/40 text-purple-300 text-xs font-bold flex items-center gap-2 shadow-lg">
                        <span class="font-cinzel tracking-wider uppercase text-[11px]">Puntaje Oficial Consolidado</span>
                    </div>
                @endif

            </div>

        </div>

        <!-- ========================================================= -->
        <!-- MODO 1: RETRATOS DE GALA (ESTILO REFERENCIA LA VELADA)    -->
        <!-- ========================================================= -->
        @if ($vista === 'tarjetas')
            
            @if ($ranking->isEmpty())
                <div class="bg-[#0e0307]/80 rounded-2xl border border-[#6b0015]/40 p-16 text-center space-y-3 shadow-2xl">
                    <span class="text-4xl text-[#d4af37]">🎵</span>
                    <h3 class="text-base font-bold text-white font-cinzel">No hay participaciones que coincidan con los filtros</h3>
                    <p class="text-xs text-slate-400">Seleccione otra categoría o departamento en los selectores superiores.</p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 justify-items-center">
                    @foreach ($ranking as $item)
                        @php
                            $pos = $item['posicion'];
                            $foto = $item['foto_url'] ?: null;
                            $estaRevelada = $item['categoria_revelada'] || $item['categoria_finalizada'];
                        @endphp

                        <!-- TARJETA RETRATO COMPACTA (ASPECT RATIO 3/4 ESTILO LA VELADA) -->
                        <div class="relative w-full max-w-[300px] aspect-[3/4] bg-gradient-to-b from-[#14040a] via-[#090205] to-[#050103] rounded-2xl border border-[#6b0015]/40 hover:border-[#d4af37]/80 p-4 shadow-2xl flex flex-col justify-between overflow-hidden group transition-all duration-500 hover:shadow-2xl hover:shadow-[#800020]/25 cursor-pointer"
                             onclick="abrirModalDesglose({{ $item['id'] }})">
                            
                            <!-- Esquineros Dorados Finos (Estilo Referencia) -->
                            <div class="corner-bracket-tl"></div>
                            <div class="corner-bracket-tr"></div>
                            <div class="corner-bracket-bl"></div>
                            <div class="corner-bracket-br"></div>

                            <!-- Fondo / Fotografía del Participante o Logo del Festival -->
                            <div class="absolute inset-0 z-0 overflow-hidden flex items-center justify-center bg-[#050103]">
                                @if ($foto)
                                    <img src="{{ $foto }}" alt="{{ $item['nombre'] }}" class="w-full h-full object-cover object-top transition duration-700 group-hover:scale-105">
                                @else
                                    <!-- Fallback: Logo Oficial del Festival con Halo Guindo/Dorado -->
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-[radial-gradient(ellipse_at_center,rgba(128,0,32,0.4),rgba(5,1,3,0.95)_70%)] p-6">
                                        <img src="/images/acb_logo.png" alt="Aquí Canta Bolivia" class="h-24 w-24 object-contain drop-shadow-[0_10px_20px_rgba(212,175,55,0.2)] opacity-80 group-hover:opacity-100 group-hover:scale-110 transition duration-500" onerror="this.src='/images/favicon.ico'">
                                        <span class="text-[9px] uppercase tracking-[0.25em] text-[#d4af37]/80 font-cinzel font-bold mt-3">Festival 2026</span>
                                    </div>
                                @endif

                                <!-- Gradiente Inferior para fundir la imagen y garantizar legibilidad -->
                                <div class="absolute inset-0 bg-gradient-to-t from-[#050103] via-[#050103]/75 to-transparent pointer-events-none"></div>
                            </div>

                            <!-- Header Superior del Card (Estilo "COMBATE · 03") -->
                            <div class="relative z-10 flex items-center justify-between gap-2 border-b border-[#3b000b]/80 pb-2">
                                
                                <!-- Podio / Posición minimalista -->
                                <div>
                                    @if ($pos === 1 && $item['nota_final'] !== null)
                                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#d4af37] font-cinzel">
                                            1° LUGAR
                                        </span>
                                    @elseif ($pos === 2 && $item['nota_final'] !== null)
                                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-300 font-cinzel">
                                            2° LUGAR
                                        </span>
                                    @elseif ($pos === 3 && $item['nota_final'] !== null)
                                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-amber-600 font-cinzel">
                                            3° LUGAR
                                        </span>
                                    @elseif (is_numeric($pos))
                                        <span class="text-[10px] font-semibold text-slate-400 font-mono">
                                            #{{ str_pad($pos, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                    @else
                                        <span class="text-[9px] uppercase tracking-wider text-slate-400 font-semibold">
                                            EN CURSO
                                        </span>
                                    @endif
                                </div>

                                <!-- Categoría e Identificador -->
                                <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-[0.2em] text-[#d4af37]/90 font-cinzel truncate max-w-[140px]" title="{{ $item['categoria'] }}">
                                    {{ $item['categoria'] }}
                                </span>
                            </div>

                            <!-- Información Inferior (Estilo EDU AGUIRRE / ESPAÑA) -->
                            <div class="relative z-10 space-y-2 text-center pt-8">
                                
                                <!-- Línea divisoria sutil -->
                                <div class="w-6 h-[1.5px] bg-[#d4af37]/50 mx-auto"></div>

                                <!-- Nombre de la Presentación -->
                                <div class="space-y-0.5">
                                    <h3 class="text-base sm:text-lg font-bold text-[#fef9c3] tracking-[0.08em] uppercase font-cinzel group-hover:text-[#d4af37] transition line-clamp-1" title="{{ $item['nombre'] }}">
                                        {{ $item['nombre'] }}
                                    </h3>
                                    
                                    <!-- Origen / Bandera con tipografía sobria -->
                                    <div class="flex items-center justify-center gap-1.5 text-[11px] font-semibold uppercase tracking-[0.2em] text-slate-300">
                                        <span>{{ $banderas[$item['departamento']] ?? '🇧🇴' }}</span>
                                        <span>{{ $item['departamento'] ?? 'Bolivia' }}</span>
                                    </div>

                                    @if (!empty($item['participantes']) && $item['participantes'][0] !== $item['nombre'])
                                        <p class="text-[9px] text-slate-400 truncate px-2" title="{{ implode(', ', $item['participantes']) }}">
                                            {{ implode(' · ', $item['participantes']) }}
                                        </p>
                                    @endif
                                </div>

                                <!-- Barra Inferior: Puntaje y Botón Desglose -->
                                <div class="pt-2 border-t border-[#3b000b]/80 flex items-center justify-between gap-2">
                                    
                                    <!-- Puntaje Final -->
                                    <div class="text-left font-mono">
                                        @if ($item['nota_final'] !== null)
                                            <span class="text-sm font-black gold-gradient-text tabular-nums">
                                                {{ number_format($item['nota_final'], 2) }}
                                            </span>
                                            <span class="text-[8px] text-slate-500">pts</span>
                                        @else
                                            <span class="text-[10px] text-slate-500 font-sans uppercase">
                                                Evaluando
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Indicador / Desglose -->
                                    <span class="px-2 py-0.5 rounded bg-[#1a050d] border border-[#6b0015] group-hover:border-amber-400/60 text-amber-200/90 text-[10px] font-semibold uppercase tracking-wider transition">
                                        {{ $estaRevelada ? 'Desglose' : '🔒 Reservado' }}
                                    </span>

                                </div>

                            </div>

                        </div>

                        <!-- MODAL DE DESGLOSE DE MESA DE EVALUACIÓN (CUPOS COMPLETOS PARA 5 JURADOS) -->
                        <div id="modalDesglose_{{ $item['id'] }}" class="fixed inset-0 z-50 flex items-center justify-center bg-[#050204]/85 backdrop-blur-md hidden p-3 sm:p-4" onclick="if(event.target === this) cerrarModalDesglose({{ $item['id'] }})">
                            <div class="bg-[#0f0308] border border-[#6b0015] rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl space-y-3.5 text-left relative overflow-hidden" onclick="event.stopPropagation()">
                                
                                <div class="corner-bracket-tl"></div>
                                <div class="corner-bracket-tr"></div>
                                <div class="corner-bracket-bl"></div>
                                <div class="corner-bracket-br"></div>

                                <!-- Header Modal -->
                                <div class="flex items-center justify-between border-b border-[#3b000b] pb-2.5">
                                    <div>
                                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#d4af37] font-cinzel">Mesa Evaluadora Oficial</span>
                                        <h4 class="text-base font-bold text-white uppercase font-cinzel">{{ $item['nombre'] }}</h4>
                                    </div>
                                    <button type="button" onclick="cerrarModalDesglose({{ $item['id'] }})" class="text-slate-400 hover:text-white text-lg font-bold">
                                        ✕
                                    </button>
                                </div>

                                <!-- Desglose por Jurados (Espacio óptimo para 5 jurados sin scroll forzado) -->
                                <div class="space-y-1.5">
                                    @foreach ($item['jurados'] as $idx => $j)
                                        <div class="py-2 px-3 rounded-xl bg-[#070104] border border-[#3b000b] flex items-center justify-between">
                                            <div>
                                                <div class="flex items-center gap-1.5">
                                                    @if ($j['es_secreto'] && ! $j['es_revelado'])
                                                        <span class="text-xs font-bold text-amber-300">
                                                            Jurado Secreto
                                                        </span>
                                                        <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-[#3b000b] text-amber-300 border border-[#800020]">
                                                            En Resguardo
                                                        </span>
                                                    @else
                                                        <span class="text-xs font-semibold text-slate-200">
                                                            {{ $j['jurado_nombre'] }}
                                                        </span>
                                                        @if ($j['es_secreto'])
                                                            <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-purple-950/60 text-purple-300 border border-purple-500/40">
                                                                Oficial
                                                            </span>
                                                        @endif
                                                    @endif
                                                </div>
                                                <span class="text-[9px] text-slate-500">Mesa Calificadora</span>
                                            </div>

                                            <div class="text-right font-mono">
                                                @if ($j['es_revelado'] && $j['nota_total'] !== null)
                                                    <span class="text-sm font-bold text-amber-300 tabular-nums">
                                                        {{ number_format($j['nota_total'], 1) }}
                                                    </span>
                                                    <span class="text-[9px] text-slate-500">pts</span>
                                                @elseif ($j['es_secreto'] && ! $j['es_revelado'])
                                                    <span class="text-xs font-semibold text-amber-400/80">
                                                        Calificación Reservada
                                                    </span>
                                                @elseif ($j['calificado'] && $j['nota_total'] !== null)
                                                    <span class="text-sm font-bold text-emerald-400 tabular-nums">
                                                        {{ number_format($j['nota_total'], 1) }}
                                                    </span>
                                                    <span class="text-[9px] text-slate-500">pts</span>
                                                @else
                                                    <span class="text-xs text-slate-500 italic">Pendiente</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <!-- Resumen Final en Modal -->
                                <div class="pt-2.5 border-t border-[#3b000b] flex items-center justify-between bg-[#070104] p-3 rounded-xl">
                                    <div>
                                        <span class="text-xs font-bold uppercase tracking-wider text-slate-300 font-cinzel">Puntaje Final:</span>
                                        <p class="text-[9px] text-slate-500">
                                            {{ $estaRevelada ? 'Promedio Oficial Consolidado (5 jurados)' : 'Puntaje Preliminar (Jurados públicos)' }}
                                        </p>
                                    </div>
                                    <span class="text-lg font-bold gold-gradient-text font-mono tabular-nums">
                                        {{ $item['nota_final'] !== null ? number_format($item['nota_final'], 2).' pts' : 'En evaluación' }}
                                    </span>
                                </div>

                                <div class="text-right pt-1">
                                    <button type="button" onclick="cerrarModalDesglose({{ $item['id'] }})" class="px-4 py-1.5 bg-[#1f050f] hover:bg-[#3b000b] text-white text-xs font-semibold rounded-lg border border-[#6b0015] transition">
                                        Cerrar
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        <!-- ========================================================= -->
        <!-- MODO 2: TABLA OFICIAL (AUDITORÍA Y NOTARÍA)               -->
        <!-- ========================================================= -->
        @else
            <div class="bg-[#0f0308]/90 rounded-2xl border border-[#6b0015]/50 shadow-2xl overflow-hidden backdrop-blur-md">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs sm:text-sm text-slate-200">
                        <thead class="bg-[#070104] text-amber-300 uppercase tracking-wider text-[10px] font-bold border-b border-[#3b000b] font-cinzel">
                            <tr>
                                <th class="px-4 py-3.5 text-center w-16">Pos.</th>
                                <th class="px-4 py-3.5">Participación Oficial</th>
                                <th class="px-4 py-3.5">Categoría</th>
                                <th class="px-4 py-3.5">Origen</th>
                                <th class="px-4 py-3.5">Mesa de Evaluación</th>
                                <th class="px-4 py-3.5 text-right">Puntaje Final</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#3b000b]/60">
                            @forelse ($ranking as $item)
                                <tr class="hover:bg-[#1a050d]/50 transition">
                                    
                                    <!-- Posición -->
                                    <td class="px-4 py-3.5 text-center font-bold">
                                        @if ($item['posicion'] === 1 && $item['nota_final'] !== null)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-xs font-cinzel">1°</span>
                                        @elseif ($item['posicion'] === 2 && $item['nota_final'] !== null)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-500/20 text-slate-200 border border-slate-400/40 text-xs font-cinzel">2°</span>
                                        @elseif ($item['posicion'] === 3 && $item['nota_final'] !== null)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-[#800020]/40 text-amber-200 border border-[#800020] text-xs font-cinzel">3°</span>
                                        @else
                                            <span class="text-slate-400 font-mono text-xs">{{ $item['posicion'] }}</span>
                                        @endif
                                    </td>

                                    <!-- Participante -->
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            @if ($item['foto_url'])
                                                <img src="{{ $item['foto_url'] }}" alt="{{ $item['nombre'] }}" class="w-8 h-8 rounded-lg object-cover border border-[#6b0015] shrink-0">
                                            @endif
                                            <div>
                                                <h4 class="font-bold text-white font-cinzel text-xs sm:text-sm leading-tight">{{ $item['nombre'] }}</h4>
                                                <p class="text-[10px] text-slate-400">{{ implode(', ', $item['participantes']) }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Categoría -->
                                    <td class="px-4 py-3.5 text-slate-300 text-xs">
                                        {{ $item['categoria'] }}
                                    </td>

                                    <!-- Origen -->
                                    <td class="px-4 py-3.5 font-medium text-slate-300 text-xs">
                                        <span class="mr-1">{{ $banderas[$item['departamento']] ?? '🇧🇴' }}</span>
                                        <span>{{ $item['departamento'] ?? 'Nacional' }}</span>
                                    </td>

                                    <!-- Mesa de Evaluación -->
                                    <td class="px-4 py-3.5">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach ($item['jurados'] as $j)
                                                <div class="px-2 py-0.5 rounded text-[10px] font-mono {{ $j['es_revelado'] && $j['nota_total'] !== null ? 'bg-[#070104] text-amber-300 border border-[#6b0015]' : ($j['es_secreto'] && ! $j['es_revelado'] ? 'bg-[#1a050d] text-amber-400 border border-[#800020]' : 'bg-[#050204] text-slate-500') }}"
                                                     title="{{ $j['es_revelado'] ? $j['jurado_nombre'] : 'Calificación Reservada' }}">
                                                    @if ($j['es_revelado'] && $j['nota_total'] !== null)
                                                        {{ number_format($j['nota_total'], 1) }}
                                                    @elseif ($j['es_secreto'] && ! $j['es_revelado'])
                                                        🔒
                                                    @else
                                                        —
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>

                                    <!-- Puntaje Final -->
                                    <td class="px-4 py-3.5 text-right">
                                        @if ($item['nota_final'] !== null)
                                            <span class="text-sm sm:text-base font-bold text-amber-300 font-mono tabular-nums">
                                                {{ number_format($item['nota_final'], 2) }}
                                            </span>
                                            <span class="text-[9px] text-slate-500">pts</span>
                                        @else
                                            <span class="text-xs text-slate-500 italic">En evaluación</span>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-12 text-center text-slate-500 text-xs font-cinzel">
                                        No hay presentaciones registradas para esta selección.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>

    <!-- Scripts de Interacción y WebSockets en Tiempo Real -->
    <script>
        function abrirModalDesglose(id) {
            const modal = document.getElementById('modalDesglose_' + id);
            if (modal) modal.classList.remove('hidden');
        }

        function cerrarModalDesglose(id) {
            const modal = document.getElementById('modalDesglose_' + id);
            if (modal) modal.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (window.Echo) {
                // Canal público de ranking y jurado secreto
                window.Echo.channel('ranking')
                    .listen('.ranking.actualizado', function (e) {
                        window.location.reload();
                    })
                    .listen('.jurado_secreto.revelado', function (e) {
                        window.location.reload();
                    })
                    .listen('.categoria.finalizada', function (e) {
                        window.location.reload();
                    });
            }
        });
    </script>

</x-layouts.publico>