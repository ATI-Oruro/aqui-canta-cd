<x-layouts.publico>
    
    @php
        $catId = $categoriaSeleccionada?->id;
    @endphp

    <div id="rankingLiveWrapper" class="space-y-6">

        <!-- ============================================================== -->
        <!-- TEXTO DE CATEGORÍA A LA DERECHA Y HR DIVIDER DORADO            -->
        <!-- ============================================================== -->
        <div class="w-full pt-1 pb-4 select-none">
            <!-- Texto de la Categoría alineado al lado derecho -->
            <div class="flex justify-end items-center px-1 mb-2">
                <h2 class="text-base sm:text-lg md:text-xl lg:text-2xl font-black uppercase tracking-[0.25em] font-cinzel gold-gradient-text" style="color: #d4af37; text-shadow: 0 0 15px rgba(212,175,55,0.6);">
                    {{ $categoriaSeleccionada ? $categoriaSeleccionada->categoria : 'Etapa Final Oruro 2026' }}
                </h2>
            </div>

            <!-- HR Dorado Estilizado de Gala -->
            <hr class="w-full" style="height: 2.5px; border: none; background: linear-gradient(90deg, rgba(107,0,21,0.1) 0%, rgba(212,175,55,0.7) 20%, #d4af37 100%); box-shadow: 0 0 12px rgba(212, 175, 55, 0.85); margin: 0; opacity: 1;">
        </div>

        <!-- ============================================================== -->
        <!-- MODO 1: PODIO ESTILO F1 (CUANDO LA CATEGORÍA ESTÁ REVELADA)    -->
        <!-- ============================================================== -->
        @if ($esPodio && $ranking->isNotEmpty())
            
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Columna Izquierda/Centro: Podio de 3 Escalones (Estilo F1) -->
                <div class="lg:col-span-8 xl:col-span-9 bg-gradient-to-b from-[#14040a] via-[#090205] to-[#050103] rounded-3xl border border-[#6b0015]/60 p-5 sm:p-8 shadow-2xl relative overflow-hidden">
                    
                    <div class="corner-bracket-tl"></div>
                    <div class="corner-bracket-tr"></div>
                    <div class="corner-bracket-bl"></div>
                    <div class="corner-bracket-br"></div>

                    <!-- Título de Podio Oficial -->
                    <div class="text-center mb-6 border-b border-[#3b000b] pb-4">
                        <span class="text-[10px] font-black uppercase tracking-[0.3em] text-[#d4af37] font-cinzel block">
                            Cuadro de Honor Oficial
                        </span>
                        <h2 class="text-xl sm:text-2xl font-black gold-gradient-text font-cinzel tracking-wider uppercase">
                            Podio de Ganadores · {{ $categoriaSeleccionada?->categoria }}
                        </h2>
                    </div>

                    <!-- Estructura de Podio: 2° Lugar (Izquierda), 1° Lugar (Centro), 3° Lugar (Derecha) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 items-end justify-center pt-4">
                        
                        <!-- ========================= -->
                        <!-- 2° LUGAR (ESCALÓN PLATA)  -->
                        <!-- ========================= -->
                        @if ($segundoLugar)
                            <div class="order-2 sm:order-1 flex flex-col items-center">
                                <div class="w-full max-w-[260px] bg-gradient-to-b from-[#1e0813] via-[#10030a] to-[#070104] rounded-2xl border-2 border-slate-400/50 hover:border-slate-300 shadow-2xl overflow-hidden group transition-all duration-300 transform hover:-translate-y-1">
                                    
                                    <!-- Insignia Número 2 -->
                                    <div class="bg-gradient-to-r from-slate-700 to-slate-900 border-b border-slate-500/40 py-2 px-3 flex items-center justify-between">
                                        <span class="text-2xl font-black font-cinzel text-slate-200 tracking-wider">2</span>
                                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-slate-300 font-cinzel">Segundo Lugar</span>
                                    </div>

                                    <!-- Imagen del Participante -->
                                    <div class="relative w-full aspect-[4/4] bg-[#050103] overflow-hidden">
                                        @if ($segundoLugar['foto_url'])
                                            <img src="{{ $segundoLugar['foto_url'] }}" alt="{{ $segundoLugar['nombre'] }}" class="w-full h-full object-cover object-top transition duration-500 group-hover:scale-105">
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center bg-[radial-gradient(ellipse_at_center,rgba(100,116,139,0.3),rgba(5,1,3,0.95)_70%)] p-4">
                                                <img src="/images/logo-oficial.png" alt="ACB" class="h-20 w-20 object-contain opacity-80 group-hover:opacity-100 transition">
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-[#070104] via-transparent to-transparent pointer-events-none"></div>
                                    </div>

                                    <!-- Nombre y Puntaje Final -->
                                    <div class="p-4 text-center space-y-2 bg-[#070104]">
                                        <h3 class="text-sm sm:text-base font-bold text-white font-cinzel uppercase tracking-wide line-clamp-2 min-h-[2.5rem] flex items-center justify-center" title="{{ $segundoLugar['nombre'] }}">
                                            {{ $segundoLugar['nombre'] }}
                                        </h3>

                                        @if (!empty($segundoLugar['departamento']))
                                            @php
                                                $bandera2 = \App\Modules\Departamentos\Models\Departamento::urlBanderaPorNombre($segundoLugar['departamento']);
                                            @endphp
                                            <p class="text-[11px] font-semibold text-slate-300 uppercase tracking-wider flex items-center justify-center gap-1.5">
                                                @if ($bandera2)
                                                    <img src="{{ $bandera2 }}" alt="{{ $segundoLugar['departamento'] }}" class="h-3.5 w-auto object-cover rounded-[2px] shadow-sm">
                                                @endif
                                                <span>{{ $segundoLugar['departamento'] }}</span>
                                            </p>
                                        @endif

                                        <div class="pt-2 border-t border-slate-800/80 flex items-center justify-center">
                                            <span class="text-base sm:text-lg font-black text-slate-200 font-mono tabular-nums bg-slate-900/90 border border-slate-700 px-3 py-1 rounded-xl shadow-inner">
                                                {{ number_format($segundoLugar['nota_final'], 2) }} <span class="text-[10px] text-slate-400">pts</span>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @else
                            <div class="order-2 sm:order-1 hidden sm:block"></div>
                        @endif

                        <!-- ========================= -->
                        <!-- 1° LUGAR (ESCALÓN ORO)    -->
                        <!-- ========================= -->
                        @if ($primerLugar)
                            <div class="order-1 sm:order-2 flex flex-col items-center sm:-translate-y-4">
                                <div class="w-full max-w-[280px] bg-gradient-to-b from-[#2e091a] via-[#17040e] to-[#090105] rounded-2xl border-2 border-[#d4af37] shadow-[0_0_30px_rgba(212,175,55,0.25)] hover:shadow-[0_0_40px_rgba(212,175,55,0.4)] overflow-hidden group transition-all duration-300 transform hover:-translate-y-1 relative">
                                    
                                    <!-- Corona / Insignia Número 1 -->
                                    <div class="bg-gradient-to-r from-[#b45309] via-[#d4af37] to-[#b45309] py-2.5 px-3 flex items-center justify-between text-slate-950">
                                        <div class="flex items-center gap-1.5">
                                            <span class="text-xl">👑</span>
                                            <span class="text-2xl font-black font-cinzel tracking-wider">1</span>
                                        </div>
                                        <span class="text-[11px] font-black uppercase tracking-[0.2em] font-cinzel">Primer Lugar</span>
                                    </div>

                                    <!-- Imagen del Participante -->
                                    <div class="relative w-full aspect-[4/4] bg-[#050103] overflow-hidden">
                                        @if ($primerLugar['foto_url'])
                                            <img src="{{ $primerLugar['foto_url'] }}" alt="{{ $primerLugar['nombre'] }}" class="w-full h-full object-cover object-top transition duration-500 group-hover:scale-105">
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center bg-[radial-gradient(ellipse_at_center,rgba(212,175,55,0.4),rgba(5,1,3,0.95)_70%)] p-4">
                                                <img src="/images/logo-oficial.png" alt="ACB" class="h-24 w-24 object-contain opacity-90 group-hover:opacity-100 transition drop-shadow-[0_0_15px_rgba(212,175,55,0.5)]">
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-[#090105] via-transparent to-transparent pointer-events-none"></div>
                                    </div>

                                    <!-- Nombre y Puntaje Final -->
                                    <div class="p-4 text-center space-y-2 bg-[#090105]">
                                        <h3 class="text-base sm:text-lg font-black text-[#fef9c3] font-cinzel uppercase tracking-wide line-clamp-2 min-h-[2.75rem] flex items-center justify-center group-hover:text-[#d4af37] transition" title="{{ $primerLugar['nombre'] }}">
                                            {{ $primerLugar['nombre'] }}
                                        </h3>

                                        @if (!empty($primerLugar['departamento']))
                                            @php
                                                $bandera1 = \App\Modules\Departamentos\Models\Departamento::urlBanderaPorNombre($primerLugar['departamento']);
                                            @endphp
                                            <p class="text-xs font-bold text-amber-300 uppercase tracking-wider flex items-center justify-center gap-1.5">
                                                @if ($bandera1)
                                                    <img src="{{ $bandera1 }}" alt="{{ $primerLugar['departamento'] }}" class="h-3.5 w-auto object-cover rounded-[2px] shadow-sm">
                                                @endif
                                                <span>{{ $primerLugar['departamento'] }}</span>
                                            </p>
                                        @endif

                                        <div class="pt-2 border-t border-[#800020]/80 flex items-center justify-center">
                                            <span class="text-lg sm:text-xl font-black gold-gradient-text font-mono tabular-nums bg-[#1f0511] border border-[#d4af37] px-4 py-1 rounded-xl shadow-lg">
                                                {{ number_format($primerLugar['nota_final'], 2) }} <span class="text-xs text-amber-300">pts</span>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endif

                        <!-- ========================= -->
                        <!-- 3° LUGAR (ESCALÓN BRONCE) -->
                        <!-- ========================= -->
                        @if ($tercerLugar)
                            <div class="order-3 flex flex-col items-center">
                                <div class="w-full max-w-[260px] bg-gradient-to-b from-[#1a080f] via-[#0e0308] to-[#050103] rounded-2xl border-2 border-amber-700/60 hover:border-amber-600 shadow-2xl overflow-hidden group transition-all duration-300 transform hover:-translate-y-1">
                                    
                                    <!-- Insignia Número 3 -->
                                    <div class="bg-gradient-to-r from-[#78350f] to-[#451a03] border-b border-amber-800/40 py-2 px-3 flex items-center justify-between">
                                        <span class="text-2xl font-black font-cinzel text-amber-400 tracking-wider">3</span>
                                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-amber-300 font-cinzel">Tercer Lugar</span>
                                    </div>

                                    <!-- Imagen del Participante -->
                                    <div class="relative w-full aspect-[4/4] bg-[#050103] overflow-hidden">
                                        @if ($tercerLugar['foto_url'])
                                            <img src="{{ $tercerLugar['foto_url'] }}" alt="{{ $tercerLugar['nombre'] }}" class="w-full h-full object-cover object-top transition duration-500 group-hover:scale-105">
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center bg-[radial-gradient(ellipse_at_center,rgba(180,83,9,0.3),rgba(5,1,3,0.95)_70%)] p-4">
                                                <img src="/images/logo-oficial.png" alt="ACB" class="h-20 w-20 object-contain opacity-80 group-hover:opacity-100 transition">
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-[#050103] via-transparent to-transparent pointer-events-none"></div>
                                    </div>

                                    <!-- Nombre y Puntaje Final -->
                                    <div class="p-4 text-center space-y-2 bg-[#050103]">
                                        <h3 class="text-sm sm:text-base font-bold text-white font-cinzel uppercase tracking-wide line-clamp-2 min-h-[2.5rem] flex items-center justify-center" title="{{ $tercerLugar['nombre'] }}">
                                            {{ $tercerLugar['nombre'] }}
                                        </h3>

                                        @if (!empty($tercerLugar['departamento']))
                                            @php
                                                $bandera3 = \App\Modules\Departamentos\Models\Departamento::urlBanderaPorNombre($tercerLugar['departamento']);
                                            @endphp
                                            <p class="text-[11px] font-semibold text-amber-400/90 uppercase tracking-wider flex items-center justify-center gap-1.5">
                                                @if ($bandera3)
                                                    <img src="{{ $bandera3 }}" alt="{{ $tercerLugar['departamento'] }}" class="h-3.5 w-auto object-cover rounded-[2px] shadow-sm">
                                                @endif
                                                <span>{{ $tercerLugar['departamento'] }}</span>
                                            </p>
                                        @endif

                                        <div class="pt-2 border-t border-[#3b000b] flex items-center justify-center">
                                            <span class="text-base sm:text-lg font-black text-amber-400 font-mono tabular-nums bg-[#15040a] border border-amber-800/60 px-3 py-1 rounded-xl shadow-inner">
                                                {{ number_format($tercerLugar['nota_final'], 2) }} <span class="text-[10px] text-amber-500">pts</span>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @else
                            <div class="order-3 hidden sm:block"></div>
                        @endif

                    </div>

                </div>

                <!-- Columna Derecha: Listado General de Participantes (4°, 5°, 6°, etc.) -->
                <div class="lg:col-span-4 xl:col-span-3 bg-gradient-to-b from-[#14040a] via-[#090205] to-[#070104] rounded-3xl border border-[#6b0015]/60 p-5 shadow-2xl space-y-4">
                    
                    <div class="border-b border-[#3b000b] pb-3 flex items-center justify-between">
                        <div>
                            <span class="text-[9px] font-black uppercase tracking-[0.25em] text-[#d4af37] font-cinzel block">
                                Posiciones Oficiales
                            </span>
                            <h3 class="text-sm font-bold text-white font-cinzel uppercase tracking-wider">
                                Clasificación General
                            </h3>
                        </div>
                        <span class="text-xs font-mono text-slate-400 bg-[#070104] px-2 py-0.5 rounded border border-[#3b000b]">
                            {{ $ranking->count() }} participantes
                        </span>
                    </div>

                    @if ($restoParticipantes->isEmpty())
                        <p class="text-xs text-slate-400 text-center py-8">
                            No hay más participantes en esta categoría.
                        </p>
                    @else
                        <div class="space-y-2">
                            @foreach ($restoParticipantes as $p)
                                <div class="p-2.5 rounded-xl bg-[#070104]/80 border border-[#3b000b]/80 hover:border-[#6b0015] flex items-center justify-between gap-3 transition cursor-pointer group"
                                     onclick="abrirModalDesglose({{ $p['id'] }})">
                                    
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <!-- Posición -->
                                        <span class="w-6 h-6 rounded-lg bg-[#1a050d] border border-[#6b0015] text-[11px] font-bold font-mono text-slate-300 flex items-center justify-center shrink-0">
                                            {{ $p['posicion'] }}°
                                        </span>

                                        <!-- Foto / Avatar miniatura -->
                                        @if ($p['foto_url'])
                                            <img src="{{ $p['foto_url'] }}" alt="{{ $p['nombre'] }}" class="w-8 h-8 rounded-lg object-cover border border-[#6b0015] shrink-0">
                                        @endif

                                        <div class="truncate">
                                            <h4 class="text-xs font-bold text-slate-200 font-cinzel truncate group-hover:text-amber-200 transition" title="{{ $p['nombre'] }}">
                                                {{ $p['nombre'] }}
                                            </h4>
                                            @php
                                                $banderaP = \App\Modules\Departamentos\Models\Departamento::urlBanderaPorNombre($p['departamento']);
                                            @endphp
                                            <span class="text-[9px] text-slate-400 uppercase tracking-wider flex items-center gap-1 mt-0.5">
                                                @if ($banderaP)
                                                    <img src="{{ $banderaP }}" alt="{{ $p['departamento'] }}" class="h-2.5 w-auto object-cover rounded-[1px] inline-block">
                                                @endif
                                                <span>{{ $p['departamento'] ?? 'Bolivia' }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Puntaje Final -->
                                    <div class="text-right shrink-0">
                                        <span class="text-xs font-bold text-amber-300 font-mono tabular-nums">
                                            {{ $p['nota_final'] !== null ? number_format($p['nota_final'], 2) : '—' }}
                                        </span>
                                        <span class="text-[8px] text-slate-500 block">pts</span>
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>

            </div>

        <!-- ============================================================== -->
        <!-- MODO 2: RETRATOS DE GALA EN EVALUACIÓN (CUANDO AÚN NO REVELADO) -->
        <!-- ============================================================== -->
        @else
            
            @if ($ranking->isEmpty())
                <div class="bg-[#0e0307]/80 rounded-2xl border border-[#6b0015]/40 p-16 text-center space-y-3 shadow-2xl max-w-xl mx-auto">
                    <span class="text-4xl text-[#d4af37]">🎵</span>
                    <h3 class="text-base font-bold text-white font-cinzel">No hay participaciones para la categoría activa</h3>
                    <p class="text-xs text-slate-400">El jurado delegado de mesa iniciará las evaluaciones para esta categoría.</p>
                </div>
            @else
                <!-- BEGIN: ContestantsGrid -->
                <main class="w-full max-w-7xl mx-auto grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-5 md:gap-6 justify-items-center relative z-10" data-purpose="desktop-showcase-grid">
                    @foreach ($ranking as $item)
                        @php
                            $pos = $item['posicion'];
                            $numero = is_numeric($pos) ? $pos : $loop->iteration;
                            $foto = $item['foto_url'] ?: null;
                            $bandera = \App\Modules\Departamentos\Models\Departamento::urlBanderaPorNombre($item['departamento']);
                        @endphp

                        <x-participante-card
                            :numero="$numero"
                            :nombre="$item['nombre']"
                            :departamento="$item['departamento']"
                            :banderaUrl="$bandera"
                            :fotoUrl="$foto"
                            :notaFinal="$item['nota_final']"
                            onclick="abrirModalDesglose({{ $item['id'] }})"
                        />
                    @endforeach
                </main>
                <!-- END: ContestantsGrid -->
            @endif

        @endif

        <!-- ============================================================== -->
        <!-- MODALES DE DESGLOSE DE EVALUACIÓN (5 JURADOS)                  -->
        <!-- ============================================================== -->
        @foreach ($ranking as $item)
            <div id="modalDesglose_{{ $item['id'] }}" class="fixed inset-0 z-50 flex items-center justify-center bg-[#050204]/85 backdrop-blur-md hidden p-3 sm:p-4" onclick="if(event.target === this) cerrarModalDesglose({{ $item['id'] }})">
                <div class="bg-[#0f0308] border border-[#6b0015] rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl space-y-3 text-left relative overflow-hidden" onclick="event.stopPropagation()">
                    
                    <div class="corner-bracket-tl"></div>
                    <div class="corner-bracket-tr"></div>
                    <div class="corner-bracket-bl"></div>
                    <div class="corner-bracket-br"></div>

                    <div class="flex items-center justify-between border-b border-[#3b000b] pb-2.5">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#d4af37] font-cinzel">Mesa Evaluadora Oficial</span>
                            <h4 class="text-base font-bold text-white uppercase font-cinzel">{{ $item['nombre'] }}</h4>
                        </div>
                        <button type="button" onclick="cerrarModalDesglose({{ $item['id'] }})" class="text-slate-400 hover:text-white text-lg font-bold">
                            ✕
                        </button>
                    </div>

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
                                                🔒 Reservado
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

                    <div class="pt-2 border-t border-[#3b000b] flex items-center justify-between bg-[#070104] p-3 rounded-xl">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-300 font-cinzel">Puntaje Final:</span>
                            <p class="text-[9px] text-slate-500">
                                {{ ($item['categoria_revelada'] || $item['categoria_finalizada']) ? 'Promedio Oficial Consolidado (5 jurados)' : 'Puntaje Preliminar (Jurados públicos)' }}
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

    <!-- ============================================================== -->
    <!-- SCRIPTS DE ACTUALIZACIÓN EN VIVO (WEBSOCKETS Y MODALES)        -->
    <!-- ============================================================== -->
    <script>
        function abrirModalDesglose(id) {
            const modal = document.getElementById('modalDesglose_' + id);
            if (modal) modal.classList.remove('hidden');
        }

        function cerrarModalDesglose(id) {
            const modal = document.getElementById('modalDesglose_' + id);
            if (modal) modal.classList.add('hidden');
        }

        // Actualización fluida del DOM en tiempo real
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
                        const modalesAbiertos = [];
                        document.querySelectorAll('[id^="modalDesglose_"]:not(.hidden)').forEach(m => {
                            modalesAbiertos.push(m.id);
                        });

                        contenedorActual.innerHTML = nuevoContenido.innerHTML;

                        modalesAbiertos.forEach(id => {
                            const modal = document.getElementById(id);
                            if (modal) modal.classList.remove('hidden');
                        });
                    }
                }
            } catch (err) {
                console.warn('Error al actualizar contenido en vivo:', err);
            } finally {
                actualizandoContenido = false;
            }
        }

        // Suscripción a WebSockets
        function iniciarEscuchaEcho() {
            if (!window.Echo) return false;

            try {
                const canalRanking = window.Echo.channel('ranking');
                if (canalRanking) {
                    canalRanking.listenToAll((event, data) => {
                        refrescarRankingEnVivo();
                    });
                    ['.ranking.actualizado', 'ranking.actualizado', '.calificacion.registrada', 'calificacion.registrada', '.calificacion.corregida', 'calificacion.corregida', '.jurado_secreto.revelado', 'jurado_secreto.revelado', '.categoria.finalizada', 'categoria.finalizada', '.categoria.activa.cambiada', 'categoria.activa.cambiada', '.presentacion.estado.cambiado', 'presentacion.estado.cambiado'].forEach(ev => {
                        canalRanking.listen(ev, refrescarRankingEnVivo);
                    });
                }

                const canalPresentaciones = window.Echo.channel('presentaciones');
                if (canalPresentaciones) {
                    canalPresentaciones.listenToAll((event, data) => {
                        refrescarRankingEnVivo();
                    });
                    ['.calificacion.registrada', 'calificacion.registrada', '.calificacion.corregida', 'calificacion.corregida', '.presentacion.estado.cambiado', 'presentacion.estado.cambiado', '.cronometro.actualizado', 'cronometro.actualizado', '.categoria.activa.cambiada', 'categoria.activa.cambiada'].forEach(ev => {
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

            setInterval(refrescarRankingEnVivo, 2500);
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id^="modalDesglose_"]').forEach(m => m.classList.add('hidden'));
            }
        });
    </script>

</x-layouts.publico>