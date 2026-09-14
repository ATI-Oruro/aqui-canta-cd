<x-layouts.publico>
    
    @php
        $catId = $categoriaSeleccionada?->id;
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

    <!-- ============================================================== -->
    <!-- GATILLO LATERAL POR HOVER (ZONA SENSIBLE EN EL BORDE IZQUIERDO) -->
    <!-- ============================================================== -->
    <div id="hoverTriggerZone"
         class="fixed top-24 left-0 w-4 h-[70vh] z-40 cursor-pointer hidden lg:block"
         title="Pase el cursor para abrir Categorías"
         onmouseenter="abrirSidebarCategorias()">
        <div class="h-full w-1.5 bg-gradient-to-b from-transparent via-[#800020]/50 to-transparent hover:w-2 hover:bg-[#d4af37] transition-all rounded-r-full"></div>
    </div>

    <!-- ============================================================== -->
    <!-- SIDEBAR DRAWER DE CATEGORÍAS (FASE FINAL NACIONAL)              -->
    <!-- ============================================================== -->
    <div id="sidebarBackdrop"
         class="fixed inset-0 bg-[#050204]/80 backdrop-blur-md z-50 opacity-0 pointer-events-none transition-opacity duration-300"
         onclick="cerrarSidebarCategorias()">
    </div>

    <aside id="sidebarCategorias"
           class="fixed top-0 left-0 h-full w-80 sm:w-96 bg-gradient-to-b from-[#0f0308] via-[#070104] to-[#0d0207] border-r border-[#6b0015]/70 shadow-2xl z-50 transform -translate-x-full transition-transform duration-300 ease-out flex flex-col justify-between overflow-hidden"
           onmouseleave="cerrarSidebarPorHover()">
        
        <!-- Header del Sidebar -->
        <div class="p-5 border-b border-[#3b000b] bg-[#070104]/90 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <img src="/images/acb_logo.png" alt="ACB" class="h-8 w-8 object-contain" onerror="this.src='/images/favicon.ico'">
                <div>
                    <span class="text-[9px] uppercase tracking-[0.25em] text-[#d4af37] font-cinzel font-bold block">
                        Fase Final Nacional
                    </span>
                    <h3 class="text-sm font-bold text-white font-cinzel uppercase tracking-wider">
                        13 Categorías Oficiales
                    </h3>
                </div>
            </div>
            <button type="button"
                    onclick="cerrarSidebarCategorias()"
                    class="w-8 h-8 rounded-lg bg-[#1a050d] hover:bg-[#3b000b] text-slate-400 hover:text-white flex items-center justify-center text-sm border border-[#6b0015] transition">
                ✕
            </button>
        </div>

        <!-- Lista de 13 Categorías -->
        <div class="flex-1 overflow-y-auto p-4 space-y-1.5 custom-scrollbar">
            
            <!-- Opción: Todas las Categorías -->
            <a href="{{ route('inicio', ['vista' => $vista]) }}"
               class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl border transition {{ !$catId ? 'bg-gradient-to-r from-[#3b000b] to-[#1a050d] text-amber-200 border-[#800020] shadow-lg' : 'bg-[#070104]/60 text-slate-300 border-[#3b000b]/50 hover:bg-[#14040a] hover:border-[#6b0015] hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <span class="text-base {{ !$catId ? 'text-[#d4af37]' : 'text-slate-500' }}">⭐</span>
                    <div>
                        <span class="text-xs font-bold font-cinzel tracking-wider uppercase block">Todas las Categorías</span>
                        <span class="text-[10px] text-slate-400">Total Nacional ({{ $conteoCategorias->sum('total') }} finalistas)</span>
                    </div>
                </div>
                <span class="text-[10px] font-mono px-2 py-0.5 rounded-full {{ !$catId ? 'bg-[#800020] text-amber-200' : 'bg-[#120308] text-slate-400' }}">
                    {{ $conteoCategorias->sum('total') }}
                </span>
            </a>

            <div class="pt-2 pb-1">
                <span class="text-[9px] uppercase tracking-[0.2em] text-[#d4af37]/70 font-cinzel font-bold px-2">
                    Convocatoria Oficial
                </span>
            </div>

            <!-- 13 Categorías Oficiales -->
            @foreach ($conteoCategorias as $index => $cat)
                @php
                    $esSeleccionada = ($catId === $cat['id']);
                @endphp
                <a href="{{ route('inicio', ['categoria' => $cat['id'], 'vista' => $vista]) }}"
                   class="group flex items-center justify-between px-3.5 py-2.5 rounded-xl border transition {{ $esSeleccionada ? 'bg-gradient-to-r from-[#3b000b] to-[#1a050d] text-amber-100 border-[#800020] shadow-lg shadow-[#800020]/20' : 'bg-[#070104]/60 text-slate-300 border-[#3b000b]/40 hover:bg-[#14040a] hover:border-[#6b0015] hover:text-white' }}">
                    
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="text-[10px] font-mono font-bold {{ $esSeleccionada ? 'text-[#d4af37]' : 'text-slate-500' }} w-5">
                            {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                        </span>
                        
                        <div class="truncate">
                            <span class="text-xs font-semibold font-cinzel tracking-wide block truncate {{ $esSeleccionada ? 'text-amber-200 font-bold' : '' }}">
                                {{ $cat['nombre'] }}
                            </span>
                            
                            <div class="flex items-center gap-1.5 text-[9px] text-slate-400">
                                @if ($cat['finalizada'])
                                    <span class="text-emerald-400 font-semibold">🏆 Proclamada</span>
                                @elseif ($cat['revelada'])
                                    <span class="text-purple-400 font-semibold">Consolidado</span>
                                @else
                                    <span class="text-amber-500/80">✨ En evaluación</span>
                                @endif
                                <span>·</span>
                                <span>{{ $cat['total'] }} finalistas</span>
                            </div>
                        </div>
                    </div>

                    <span class="text-[10px] font-mono px-1.5 py-0.5 rounded {{ $esSeleccionada ? 'bg-[#800020] text-amber-200 font-bold' : 'bg-[#0f0308] text-slate-400' }}">
                        {{ $cat['total'] }}
                    </span>
                </a>
            @endforeach

        </div>

        <!-- Footer del Sidebar -->
        <div class="p-4 border-t border-[#3b000b] bg-[#070104] text-[10px] text-slate-400 flex items-center justify-between">
            <span class="font-cinzel text-[#d4af37]/80">Oruro · Bolivia</span>
            <button type="button"
                    onclick="toggleFullscreen(); cerrarSidebarCategorias();"
                    class="px-2.5 py-1 rounded bg-[#1f050f] hover:bg-[#3b000b] text-amber-200 border border-[#6b0015] font-bold text-[10px] transition flex items-center gap-1">
                <span class="fullscreen-icon">⛶</span>
                <span>Pantalla Completa</span>
            </button>
        </div>

    </aside>

    <div id="rankingLiveWrapper" class="space-y-6">

        <!-- ============================================================== -->
        <!-- BARRA SUPERIOR DE ACCIONES Y MODO DE VISTA                      -->
        <!-- ============================================================== -->
        <div class="bg-gradient-to-r from-[#120308] via-[#090205] to-[#120308] rounded-2xl border border-[#6b0015]/50 p-3 sm:p-4 shadow-2xl backdrop-blur-md flex flex-wrap items-center justify-between gap-3 sm:gap-4">
            
            <!-- Botón del Filtrador Lateral de Categorías -->
            <div class="flex items-center gap-3">
                <button type="button"
                        id="btnToggleSidebar"
                        onclick="toggleSidebarCategorias()"
                        onmouseenter="abrirSidebarCategorias()"
                        class="px-4 py-2 rounded-xl bg-gradient-to-r from-[#2a0510] to-[#1a040b] hover:from-[#3b000b] hover:to-[#2a0510] text-amber-200 font-cinzel font-bold text-xs border border-[#800020] hover:border-amber-400/80 transition flex items-center gap-2.5 shadow-lg group">
                    <span class="text-sm group-hover:scale-110 transition duration-300">☰</span>
                    <span>Categorías</span>
                    <span class="px-1.5 py-0.2 rounded text-[10px] bg-[#070104] text-[#d4af37] border border-[#6b0015]">
                        {{ $categoriaSeleccionada ? $categoriaSeleccionada->categoria : '13 Categorías' }}
                    </span>
                </button>

                @if ($catId)
                    <a href="{{ route('inicio', ['vista' => $vista]) }}"
                       class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg bg-[#14040a] hover:bg-[#280512] text-slate-300 hover:text-white text-xs border border-[#3b000b] transition"
                       title="Ver todas las categorías">
                        <span>✕</span>
                        <span class="hidden sm:inline">Ver Todas</span>
                    </a>
                @endif
            </div>

            <!-- Estado de la Categoría Seleccionada -->
            <div class="flex items-center gap-2">
                @if ($categoriaSeleccionada && $categoriaSeleccionada->estaFinalizada())
                    <div class="px-3.5 py-1.5 rounded-xl bg-[#062013] border border-emerald-500/40 text-emerald-300 text-xs font-bold flex items-center gap-2 shadow-lg">
                        <span>🏆</span>
                        <span class="font-cinzel tracking-wider uppercase text-[10px] sm:text-[11px]">Resultados Oficiales Proclamados</span>
                    </div>
                @elseif ($categoriaSeleccionada && $categoriaSeleccionada->estaRevelada())
                    <div class="px-3.5 py-1.5 rounded-xl bg-[#1d0628] border border-purple-500/40 text-purple-300 text-xs font-bold flex items-center gap-2 shadow-lg">
                        <span class="font-cinzel tracking-wider uppercase text-[10px] sm:text-[11px]">Puntaje Oficial Consolidado</span>
                    </div>
                @else
                    <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-xl bg-[#090205] border border-[#3b000b] text-slate-400 text-xs">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse"></span>
                        <span class="font-cinzel text-[10px] uppercase tracking-wider text-amber-200/90">
                            {{ $categoriaSeleccionada ? $categoriaSeleccionada->categoria : 'Etapa Final Nacional Oruro 2026' }}
                        </span>
                    </div>
                @endif
            </div>

            <!-- Modo de Vista (Tarjetas / Tabla) y Pantalla Completa LED -->
            <div class="flex items-center gap-2.5">
                
                <!-- Selector Tarjetas / Tabla -->
                <div class="flex items-center gap-1 bg-[#070104] border border-[#6b0015] p-0.5 rounded-xl">
                    <a href="{{ route('inicio', array_filter(['categoria' => $catId, 'vista' => 'tarjetas'])) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $vista === 'tarjetas' ? 'bg-[#3b000b] text-amber-200 shadow-md border border-[#800020]' : 'text-slate-400 hover:text-white' }}"
                       title="Modo Retratos de Gala">
                        <span>🎴</span>
                        <span class="hidden sm:inline">Tarjetas</span>
                    </a>
                    <a href="{{ route('inicio', array_filter(['categoria' => $catId, 'vista' => 'tabla'])) }}"
                       class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1.5 {{ $vista === 'tabla' ? 'bg-[#3b000b] text-amber-200 shadow-md border border-[#800020]' : 'text-slate-400 hover:text-white' }}"
                       title="Modo Tabla Oficial">
                        <span>📊</span>
                        <span class="hidden sm:inline">Tabla</span>
                    </a>
                </div>

                <!-- Botón de Pantalla Completa (Toda la ventana para pantallas LED) -->
                <button type="button"
                        onclick="toggleFullscreen()"
                        id="btnFullscreenToggle"
                        class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-[#1f050f] to-[#120308] hover:from-[#3b000b] hover:to-[#22040d] text-amber-200 font-cinzel font-bold text-xs border border-[#800020] hover:border-amber-400 transition flex items-center gap-1.5 shadow-lg"
                        title="Proyectar esta vista en toda la pantalla (F11 / Pantalla Completa)">
                    <span id="fullscreenBtnIcon">⛶</span>
                    <span id="fullscreenBtnText" class="hidden sm:inline">Pantalla Completa</span>
                </button>

            </div>

        </div>


        <!-- ========================================================= -->
        <!-- MODO 1: RETRATOS DE GALA (ESTILO REFERENCIA LA VELADA)    -->
        <!-- ========================================================= -->
        @if ($vista === 'tarjetas')
            
            @if ($ranking->isEmpty())
                <div class="bg-[#0e0307]/80 rounded-2xl border border-[#6b0015]/40 p-16 text-center space-y-3 shadow-2xl">
                    <span class="text-4xl text-[#d4af37]">🎵</span>
                    <h3 class="text-base font-bold text-white font-cinzel">No hay participaciones para la categoría seleccionada</h3>
                    <p class="text-xs text-slate-400">Seleccione otra categoría en el menú lateral para consultar las calificaciones oficiales.</p>
                    <button type="button" onclick="abrirSidebarCategorias()" class="px-4 py-2 bg-[#1f050f] text-amber-200 rounded-xl border border-[#6b0015] text-xs font-bold font-cinzel hover:bg-[#3b000b] transition mt-2">
                        Abrir Categorías
                    </button>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-6 justify-items-center">
                    @foreach ($ranking as $item)
                        @php
                            $pos = $item['posicion'];
                            $foto = $item['foto_url'] ?: null;
                            $estaRevelada = $item['categoria_revelada'] || $item['categoria_finalizada'];
                        @endphp

                        <!-- TARJETA RETRATO COMPACTA (ASPECT RATIO 3/4 ESTILO LA VELADA) -->
                        <div class="relative w-full max-w-[290px] aspect-[3/4] bg-gradient-to-b from-[#14040a] via-[#090205] to-[#050103] rounded-2xl border border-[#6b0015]/40 hover:border-[#d4af37]/80 p-4 shadow-2xl flex flex-col justify-between overflow-hidden group transition-all duration-500 hover:shadow-2xl hover:shadow-[#800020]/25 cursor-pointer"
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

                            <!-- Header Superior del Card -->
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

                                <!-- Categoría -->
                                <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-[0.2em] text-[#d4af37]/90 font-cinzel truncate max-w-[130px]" title="{{ $item['categoria'] }}">
                                    {{ $item['categoria'] }}
                                </span>
                            </div>

                            <!-- Información Inferior -->
                            <div class="relative z-10 space-y-2 text-center pt-8">
                                
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

                        <!-- MODAL DE DESGLOSE DE MESA DE EVALUACIÓN (5 JURADOS SIN SCROLLBARS) -->
                        <div id="modalDesglose_{{ $item['id'] }}" class="fixed inset-0 z-50 flex items-center justify-center bg-[#050204]/85 backdrop-blur-md hidden p-3 sm:p-4" onclick="if(event.target === this) cerrarModalDesglose({{ $item['id'] }})">
                            <div class="bg-[#0f0308] border border-[#6b0015] rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl space-y-3 text-left relative overflow-hidden" onclick="event.stopPropagation()">
                                
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

                                <!-- Desglose por Jurados (5 jurados con espaciado óptimo sin scroll) -->
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
                                <div class="pt-2 border-t border-[#3b000b] flex items-center justify-between bg-[#070104] p-3 rounded-xl">
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
                                <tr class="hover:bg-[#1a050d]/50 transition cursor-pointer" onclick="abrirModalDesglose({{ $item['id'] }})">
                                    
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

    <!-- ============================================================== -->
    <!-- SCRIPTS DE SIDEBAR, MODALES Y WEBSOCKETS EN TIEMPO REAL        -->
    <!-- ============================================================== -->
    <script>
        function abrirSidebarCategorias() {
            const sidebar = document.getElementById('sidebarCategorias');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('translate-x-0');
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100', 'pointer-events-auto');
            }
        }

        function cerrarSidebarCategorias() {
            const sidebar = document.getElementById('sidebarCategorias');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                sidebar.classList.remove('translate-x-0');
                sidebar.classList.add('-translate-x-full');
                backdrop.classList.remove('opacity-100', 'pointer-events-auto');
                backdrop.classList.add('opacity-0', 'pointer-events-none');
            }
        }

        function cerrarSidebarPorHover() {
            // Cierra suavemente al retirar el cursor del panel
            cerrarSidebarCategorias();
        }

        function toggleSidebarCategorias() {
            const sidebar = document.getElementById('sidebarCategorias');
            if (sidebar && sidebar.classList.contains('-translate-x-full')) {
                abrirSidebarCategorias();
            } else {
                cerrarSidebarCategorias();
            }
        }

        function abrirModalDesglose(id) {
            const modal = document.getElementById('modalDesglose_' + id);
            if (modal) modal.classList.remove('hidden');
        }

        function cerrarModalDesglose(id) {
            const modal = document.getElementById('modalDesglose_' + id);
            if (modal) modal.classList.add('hidden');
        }

        // Control de Pantalla Completa
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.error('Error al entrar a pantalla completa:', err);
                });
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        document.addEventListener('fullscreenchange', function () {
            const btnIcon = document.getElementById('fullscreenBtnIcon');
            const btnText = document.getElementById('fullscreenBtnText');
            const isFull = !!document.fullscreenElement;

            if (btnIcon) btnIcon.textContent = isFull ? '🗗' : '⛶';
            if (btnText) btnText.textContent = isFull ? 'Salir Pantalla' : 'Pantalla Completa';

            document.querySelectorAll('.fullscreen-icon').forEach(el => {
                el.textContent = isFull ? '🗗' : '⛶';
            });
        });

        // Actualización fluida del DOM sin recargar la página completa
        let actualizandoContenido = false;
        async function refrescarRankingEnVivo() {
            if (actualizandoContenido) return;
            actualizandoContenido = true;

            try {
                const fetchUrl = new URL(window.location.href);
                fetchUrl.searchParams.set('_t', Date.now());

                const response = await fetch(fetchUrl.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    cache: 'no-store'
                });

                if (response.ok) {
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    const nuevoContenido = doc.getElementById('rankingLiveWrapper');
                    const contenedorActual = document.getElementById('rankingLiveWrapper');

                    if (nuevoContenido && contenedorActual) {
                        // Preservar modales que estuvieran abiertos
                        const modalesAbiertos = [];
                        document.querySelectorAll('[id^="modalDesglose_"]:not(.hidden)').forEach(m => {
                            modalesAbiertos.push(m.id);
                        });

                        contenedorActual.innerHTML = nuevoContenido.innerHTML;

                        // Restaurar modales si seguían abiertos
                        modalesAbiertos.forEach(id => {
                            const modal = document.getElementById(id);
                            if (modal) modal.classList.remove('hidden');
                        });
                    }

                    // Actualizar sidebar de categorías si está presente
                    const nuevoSidebar = doc.getElementById('sidebarCategorias');
                    const sidebarActual = document.getElementById('sidebarCategorias');
                    if (nuevoSidebar && sidebarActual) {
                        const listaNueva = nuevoSidebar.querySelector('.flex-1');
                        const listaActual = sidebarActual.querySelector('.flex-1');
                        if (listaNueva && listaActual) {
                            listaActual.innerHTML = listaNueva.innerHTML;
                        }
                    }
                }
            } catch (err) {
                console.warn('Error al actualizar contenido en vivo:', err);
            } finally {
                actualizandoContenido = false;
            }
        }

        // Sincronización WebSockets en Tiempo Real
        function iniciarEscuchaEcho() {
            if (!window.Echo) return false;

            try {
                const canalRanking = window.Echo.channel('ranking');
                if (canalRanking) {
                    canalRanking.listenToAll((event, data) => {
                        refrescarRankingEnVivo();
                    });
                    ['.ranking.actualizado', 'ranking.actualizado', '.calificacion.registrada', 'calificacion.registrada', '.calificacion.corregida', 'calificacion.corregida', '.jurado_secreto.revelado', 'jurado_secreto.revelado', '.categoria.finalizada', 'categoria.finalizada', '.presentacion.estado.cambiado', 'presentacion.estado.cambiado'].forEach(ev => {
                        canalRanking.listen(ev, refrescarRankingEnVivo);
                    });
                }

                const canalPresentaciones = window.Echo.channel('presentaciones');
                if (canalPresentaciones) {
                    canalPresentaciones.listenToAll((event, data) => {
                        refrescarRankingEnVivo();
                    });
                    ['.calificacion.registrada', 'calificacion.registrada', '.calificacion.corregida', 'calificacion.corregida', '.presentacion.estado.cambiado', 'presentacion.estado.cambiado', '.cronometro.actualizado', 'cronometro.actualizado'].forEach(ev => {
                        canalPresentaciones.listen(ev, refrescarRankingEnVivo);
                    });
                }

                return true;
            } catch (e) {
                console.warn('Error suscribiendo a canales Echo:', e);
                return false;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            if (!iniciarEscuchaEcho()) {
                let attempts = 0;
                const echoInterval = setInterval(function () {
                    attempts++;
                    if (iniciarEscuchaEcho() || attempts > 25) {
                        clearInterval(echoInterval);
                    }
                }, 200);
            }

            // Sincronización periódica activa (cada 2.5s)
            setInterval(refrescarRankingEnVivo, 2500);
        });

        // Atajo de teclado Escape para cerrar sidebar y modales
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                cerrarSidebarCategorias();
                document.querySelectorAll('[id^="modalDesglose_"]').forEach(m => m.classList.add('hidden'));
            }
        });
    </script>

</x-layouts.publico>