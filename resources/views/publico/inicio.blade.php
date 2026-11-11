<x-layouts.publico>
    
    @php
        $catId = $categoriaSeleccionada?->id;
    @endphp

    <div id="rankingLiveWrapper" class="space-y-2 sm:space-y-3">

        <!-- ============================================================== -->
        <!-- TEXTO DE GANADORES / CATEGORÍA Y HR DIVIDER DORADO             -->
        <!-- ============================================================== -->
        <div class="w-full pt-0.5 pb-1.5 select-none">
            <div class="flex flex-col sm:flex-row sm:items-center {{ ($esPodio && $ranking->isNotEmpty()) ? 'justify-between' : 'justify-end' }} gap-2 px-1 mb-1.5">
                @if ($esPodio && $ranking->isNotEmpty())
                    <h2 class="text-xs sm:text-sm md:text-base font-black uppercase tracking-[0.15em] font-cinzel gold-gradient-text whitespace-nowrap" style="color: #d4af37; text-shadow: 0 0 15px rgba(212,175,55,0.6);">
                        @if ($categoriaSeleccionada?->tieneUnSoloGanador())
                            {{ $categoriaSeleccionada->esNusta() ? 'Elección y Proclamación de la Ñusta' : 'Ganador Oficial de la Categoría' }}
                        @else
                            Ganadores de la Categoría
                        @endif
                    </h2>
                @endif

                <h2 class="text-xs sm:text-sm md:text-base font-black uppercase tracking-[0.15em] font-cinzel gold-gradient-text sm:text-right" style="color: #d4af37; text-shadow: 0 0 15px rgba(212,175,55,0.6);">
                    {{ $categoriaSeleccionada ? $categoriaSeleccionada->categoria : 'Etapa Final Oruro 2026' }}
                </h2>
            </div>

            <!-- HR Dorado Estilizado de Gala -->
            <hr class="w-full" style="height: 2.5px; border: none; background: linear-gradient(90deg, rgba(107,0,21,0.1) 0%, rgba(212,175,55,0.7) 20%, #d4af37 100%); box-shadow: 0 0 12px rgba(212, 175, 55, 0.85); margin: 0; opacity: 1;">
        </div>

        <style>
            .podio-card-flip-container {
                perspective: 1400px;
            }
            .podio-card-inner {
                position: relative;
                width: 100%;
                transform-style: preserve-3d;
                -webkit-transform-style: preserve-3d;
                transition: transform 0.7s cubic-bezier(0.22, 1, 0.36, 1);
            }
            .podio-card-inner.is-flipped {
                transform: rotateY(180deg);
            }
            .podio-card-back,
            .podio-card-front {
                backface-visibility: hidden;
                -webkit-backface-visibility: hidden;
            }
            .podio-card-front {
                transform: rotateY(180deg);
            }
            /* Sombra profunda estática para dar efecto de elevación sin animación */
            .podio-card-shadow {
                box-shadow: 0 28px 60px -10px rgba(0, 0, 0, 0.95), 0 14px 30px -8px rgba(0, 0, 0, 0.9), 0 0 28px rgba(212, 175, 55, 0.18);
            }
            .podio-card-shadow-1 {
                box-shadow: 0 32px 68px -10px rgba(0, 0, 0, 0.98), 0 16px 34px -8px rgba(0, 0, 0, 0.92), 0 0 36px rgba(212, 175, 55, 0.28);
            }
        </style>

        @if ($esPodio && $ranking->isNotEmpty())
            
            <!-- Escenario de Podio (Sin recuadro contenedor ni clasificación general) -->
            <div id="contenedorPodioEscalones" class="w-full max-w-6xl mx-auto px-2 sm:px-6 py-2 relative">

                @if ($categoriaSeleccionada?->tieneUnSoloGanador())
                    <!-- ================================================= -->
                    <!-- MODO GANADOR ÚNICO (ÑUSTA / ESPECIAL: 1 GANADOR) -->
                    <!-- ================================================= -->
                    <div class="flex flex-col items-center justify-center pt-1 pb-2 w-full">
                        @if ($primerLugar)
                            @php
                                $foto1 = $primerLugar['foto_url'] ?: asset('images/fondo.png');
                                $bandera1 = \App\Modules\Departamentos\Models\Departamento::urlBanderaPorNombre($primerLugar['departamento'] ?? null);
                            @endphp
                            <div class="w-full flex justify-center">
                                <div class="podio-card-flip-container w-full" style="max-width: 285px;">
                                    <div id="podioCard_1" class="podio-card-inner {{ $lugar1Revelado ? 'is-flipped' : '' }}" style="height: 415px;">
                                        
                                        <!-- CARA TRASERA: PREPARACIÓN DEL PODIO (MISMO ESTILO QUE CARD DE RANKING PÚBLICO) -->
                                        <div class="podio-card-back podio-card-shadow-1 relative w-full h-full rounded-2xl overflow-hidden bg-gradient-to-b from-stone-900/90 via-[#120f0c] to-black flex flex-col justify-between p-3 select-none"
                                             style="height: 415px;">
                                            <img class="absolute inset-0 w-full h-full object-cover z-0 opacity-35"
                                                 src="{{ asset('images/fondo.png') }}"
                                                 alt="Fondo de Gala">
                                            <div aria-hidden="true" class="absolute inset-0 z-[1] pointer-events-none" style="background: linear-gradient(to bottom, rgba(28, 25, 23, 0.65), rgba(21, 18, 14, 0.72), rgba(0, 0, 0, 0.96));"></div>
                                            <div aria-hidden="true" class="absolute inset-0 opacity-15 card-dot-pattern z-[2] pointer-events-none"></div>

                                            <!-- Header estilo Ranking Público: Puesto a la izquierda -->
                                            <header class="relative z-10 px-4 pt-4 flex flex-col gap-2">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[12px] tracking-[0.2em] uppercase font-bold drop-shadow-md" style="color: #fbbf24;">
                                                        N° 1 · {{ $categoriaSeleccionada->etiquetaGanador() }}
                                                    </span>
                                                </div>
                                                <div class="w-full h-px" style="background: linear-gradient(to right, transparent, rgba(212, 175, 55, 0.4), transparent);"></div>
                                            </header>

                                            <!-- Centro: Signo '?' estático sin animación -->
                                            <div class="relative z-10 my-auto flex flex-col items-center justify-center px-3">
                                                <span class="font-serif text-6xl font-extrabold gold-gradient-text drop-shadow-[0_2px_14px_rgba(212,175,55,0.45)] leading-none select-none">
                                                    ?
                                                </span>
                                            </div>

                                            <!-- Parte baja estilo Ranking Público -->
                                            <div class="relative z-10 px-3 pb-4 mt-auto flex flex-col items-center text-center">
                                                <div aria-hidden="true" class="w-8 h-0.5 rounded-full mb-2" style="background-color: rgba(212, 175, 55, 0.5);"></div>
                                                <span class="font-serif text-base font-bold tracking-widest text-amber-50 uppercase drop-shadow-md">
                                                    En Espera
                                                </span>
                                                <span class="text-xs text-stone-400 font-sans mt-0.5">
                                                    {{ $categoriaSeleccionada->etiquetaGanador() }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- CARA DELANTERA: GANADOR REVELADO (MISMO ESTILO QUE CARD DE RANKING PÚBLICO SIN ESQUINAS DORADAS) -->
                                        <article class="podio-card-front podio-card-shadow-1 absolute inset-0 w-full h-full rounded-2xl overflow-hidden bg-gradient-to-b from-stone-900/90 via-[#120f0c] to-black flex flex-col justify-between p-3 cursor-pointer"
                                                 style="height: 415px;"
                                                 data-card-id="{{ $primerLugar['id'] }}"
                                                 onclick="abrirModalDesglose({{ $primerLugar['id'] }})">
                                            
                                            <!-- Fotografía del Participante / Fondo de Gala -->
                                            <img class="card-foto absolute inset-0 w-full h-full object-cover object-top z-0"
                                                 src="{{ $foto1 }}"
                                                 alt="{{ $primerLugar['nombre'] }}"
                                                 onerror="this.onerror=null; this.src='{{ asset('images/fondo.png') }}';">

                                            <div aria-hidden="true" class="absolute inset-0 z-[1] pointer-events-none" style="background: linear-gradient(to bottom, rgba(28, 25, 23, 0.45) 0%, rgba(21, 18, 14, 0.45) 48%, rgba(0, 0, 0, 0.95) 100%);"></div>
                                            <div aria-hidden="true" class="absolute inset-0 opacity-15 card-dot-pattern z-[2] pointer-events-none"></div>

                                            <!-- Header con Puesto 1° y Bandera -->
                                            <header class="relative z-10 px-4 pt-4 flex flex-col gap-2">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[12px] tracking-[0.2em] uppercase font-bold drop-shadow-md" style="color: #fbbf24;">
                                                        N° 1 · {{ $categoriaSeleccionada->etiquetaGanador() }}
                                                    </span>
                                                    @if ($bandera1)
                                                        <img class="w-4 h-3.5 object-cover rounded-[1px]" src="{{ $bandera1 }}" alt="Bandera de {{ $primerLugar['departamento'] ?? 'Bolivia' }}">
                                                    @endif
                                                </div>
                                                <div class="w-full h-px" style="background: linear-gradient(to right, transparent, rgba(212, 175, 55, 0.4), transparent);"></div>
                                            </header>

                                            <!-- Parte baja: Calificación sobre el Nombre y Departamento -->
                                            <div class="relative z-10 px-3 pb-4 mt-auto flex flex-col items-center text-center">
                                                <div class="flex items-baseline gap-1 mb-1">
                                                    <span class="card-score font-serif text-4xl sm:text-5xl font-extrabold tracking-tight gold-gradient-text drop-shadow-[0_2px_12px_rgba(212,175,55,0.45)]">
                                                        {{ number_format((float) $primerLugar['nota_final'], 2) }}
                                                    </span>
                                                    <span class="text-[11px] font-semibold text-yellow-200/50 uppercase tracking-wider font-sans">/100</span>
                                                </div>
                                                <div aria-hidden="true" class="w-8 h-0.5 rounded-full mb-2" style="background-color: rgba(212, 175, 55, 0.5);"></div>
                                                <h3 class="card-nombre font-serif text-lg sm:text-xl font-bold tracking-widest text-amber-50 uppercase drop-shadow-md line-clamp-2" title="{{ $primerLugar['nombre'] }}">
                                                    {{ $primerLugar['nombre'] }}
                                                </h3>
                                                <span class="text-xs text-stone-300 font-sans mt-0.5">
                                                    {{ $primerLugar['departamento'] ?? 'Bolivia' }}
                                                </span>
                                            </div>

                                        </article>

                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <!-- Estructura de Podio: 2° Lugar (Izquierda), 1° Lugar (Centro), 3° Lugar (Derecha) ocupando el espacio disponible -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 lg:gap-10 items-end justify-items-center pt-2 max-w-5xl mx-auto w-full">
                    
                    <!-- ========================= -->
                    <!-- 2° LUGAR                  -->
                    <!-- ========================= -->
                    @if ($segundoLugar)
                        @php
                            $foto2 = $segundoLugar['foto_url'] ?: asset('images/fondo.png');
                            $bandera2 = \App\Modules\Departamentos\Models\Departamento::urlBanderaPorNombre($segundoLugar['departamento'] ?? null);
                        @endphp
                        <div class="order-2 sm:order-1 flex flex-col items-center w-full">
                            <div class="podio-card-flip-container w-full" style="max-width: 265px;">
                                <div id="podioCard_2" class="podio-card-inner {{ $lugar2Revelado ? 'is-flipped' : '' }}" style="height: 390px;">
                                    
                                    <!-- CARA TRASERA: PREPARACIÓN DEL PODIO (MISMO ESTILO QUE CARD DE RANKING PÚBLICO) -->
                                    <div class="podio-card-back podio-card-shadow relative w-full h-full rounded-2xl overflow-hidden bg-gradient-to-b from-stone-900/90 via-[#120f0c] to-black flex flex-col justify-between p-3 select-none"
                                         style="height: 390px;">
                                        <img class="absolute inset-0 w-full h-full object-cover z-0 opacity-35"
                                             src="{{ asset('images/fondo.png') }}"
                                             alt="Fondo de Gala">
                                        <div aria-hidden="true" class="absolute inset-0 z-[1] pointer-events-none" style="background: linear-gradient(to bottom, rgba(28, 25, 23, 0.65), rgba(21, 18, 14, 0.72), rgba(0, 0, 0, 0.96));"></div>
                                        <div aria-hidden="true" class="absolute inset-0 opacity-15 card-dot-pattern z-[2] pointer-events-none"></div>

                                        <!-- Header con Puesto 2 -->
                                        <header class="relative z-10 px-4 pt-4 flex flex-col gap-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[12px] tracking-[0.2em] uppercase font-bold drop-shadow-md" style="color: #fbbf24;">
                                                    N° 2 · Segundo Lugar
                                                </span>
                                            </div>
                                            <div class="w-full h-px" style="background: linear-gradient(to right, transparent, rgba(212, 175, 55, 0.4), transparent);"></div>
                                        </header>

                                        <!-- Centro: Signo '?' estático -->
                                        <div class="relative z-10 my-auto flex flex-col items-center justify-center px-3">
                                            <span class="font-serif text-6xl font-extrabold gold-gradient-text drop-shadow-[0_2px_14px_rgba(212,175,55,0.45)] leading-none select-none">
                                                ?
                                            </span>
                                        </div>

                                        <!-- Parte baja -->
                                        <div class="relative z-10 px-3 pb-4 mt-auto flex flex-col items-center text-center">
                                            <div aria-hidden="true" class="w-8 h-0.5 rounded-full mb-2" style="background-color: rgba(212, 175, 55, 0.5);"></div>
                                            <span class="font-serif text-base font-bold tracking-widest text-amber-50 uppercase drop-shadow-md">
                                                En Espera
                                            </span>
                                            <span class="text-xs text-stone-400 font-sans mt-0.5">
                                                Segundo Lugar
                                            </span>
                                        </div>
                                    </div>

                                    <!-- CARA DELANTERA: GANADOR REVELADO -->
                                    <article class="podio-card-front podio-card-shadow absolute inset-0 w-full h-full rounded-2xl overflow-hidden bg-gradient-to-b from-stone-900/90 via-[#120f0c] to-black flex flex-col justify-between p-3 cursor-pointer"
                                             style="height: 390px;"
                                             data-card-id="{{ $segundoLugar['id'] }}"
                                             onclick="abrirModalDesglose({{ $segundoLugar['id'] }})">
                                        
                                        <!-- Fotografía del Participante / Fondo de Gala -->
                                        <img class="card-foto absolute inset-0 w-full h-full object-cover object-top z-0"
                                             src="{{ $foto2 }}"
                                             alt="{{ $segundoLugar['nombre'] }}"
                                             onerror="this.onerror=null; this.src='{{ asset('images/fondo.png') }}';">

                                        <div aria-hidden="true" class="absolute inset-0 z-[1] pointer-events-none" style="background: linear-gradient(to bottom, rgba(28, 25, 23, 0.45) 0%, rgba(21, 18, 14, 0.45) 48%, rgba(0, 0, 0, 0.95) 100%);"></div>
                                        <div aria-hidden="true" class="absolute inset-0 opacity-15 card-dot-pattern z-[2] pointer-events-none"></div>

                                        <!-- Header con Puesto 2 y Bandera -->
                                        <header class="relative z-10 px-4 pt-4 flex flex-col gap-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[12px] tracking-[0.2em] uppercase font-bold drop-shadow-md" style="color: #fbbf24;">
                                                    N° 2 · Segundo Lugar
                                                </span>
                                                @if ($bandera2)
                                                    <img class="w-4 h-3.5 object-cover rounded-[1px]" src="{{ $bandera2 }}" alt="Bandera de {{ $segundoLugar['departamento'] ?? 'Bolivia' }}">
                                                @endif
                                            </div>
                                            <div class="w-full h-px" style="background: linear-gradient(to right, transparent, rgba(212, 175, 55, 0.4), transparent);"></div>
                                        </header>

                                        <!-- Parte baja: Calificación sobre el Nombre y Departamento -->
                                        <div class="relative z-10 px-3 pb-4 mt-auto flex flex-col items-center text-center">
                                            <div class="flex items-baseline gap-1 mb-1">
                                                <span class="card-score font-serif text-4xl sm:text-5xl font-extrabold tracking-tight gold-gradient-text drop-shadow-[0_2px_12px_rgba(212,175,55,0.45)]">
                                                    {{ number_format((float) $segundoLugar['nota_final'], 2) }}
                                                </span>
                                                <span class="text-[11px] font-semibold text-yellow-200/50 uppercase tracking-wider font-sans">/100</span>
                                            </div>
                                            <div aria-hidden="true" class="w-8 h-0.5 rounded-full mb-2" style="background-color: rgba(212, 175, 55, 0.5);"></div>
                                            <h3 class="card-nombre font-serif text-lg font-bold tracking-widest text-amber-50 uppercase drop-shadow-md line-clamp-2" title="{{ $segundoLugar['nombre'] }}">
                                                {{ $segundoLugar['nombre'] }}
                                            </h3>
                                            <span class="text-xs text-stone-300 font-sans mt-0.5">
                                                {{ $segundoLugar['departamento'] ?? 'Bolivia' }}
                                            </span>
                                        </div>

                                    </article>

                                </div>
                            </div>
                        </div>
                    @else
                        <div class="order-2 sm:order-1 hidden sm:block"></div>
                    @endif

                    <!-- ========================= -->
                    <!-- 1° LUGAR                  -->
                    <!-- ========================= -->
                    @if ($primerLugar)
                        @php
                            $foto1 = $primerLugar['foto_url'] ?: asset('images/fondo.png');
                            $bandera1 = \App\Modules\Departamentos\Models\Departamento::urlBanderaPorNombre($primerLugar['departamento'] ?? null);
                        @endphp
                        <div class="order-1 sm:order-2 flex flex-col items-center sm:-translate-y-2 w-full">
                            <div class="podio-card-flip-container w-full" style="max-width: 280px;">
                                <div id="podioCard_1" class="podio-card-inner {{ $lugar1Revelado ? 'is-flipped' : '' }}" style="height: 410px;">
                                    
                                    <!-- CARA TRASERA: PREPARACIÓN DEL PODIO (MISMO ESTILO QUE CARD DE RANKING PÚBLICO) -->
                                    <div class="podio-card-back podio-card-shadow-1 relative w-full h-full rounded-2xl overflow-hidden bg-gradient-to-b from-stone-900/90 via-[#120f0c] to-black flex flex-col justify-between p-3 select-none"
                                         style="height: 410px;">
                                        <img class="absolute inset-0 w-full h-full object-cover z-0 opacity-35"
                                             src="{{ asset('images/fondo.png') }}"
                                             alt="Fondo de Gala">
                                        <div aria-hidden="true" class="absolute inset-0 z-[1] pointer-events-none" style="background: linear-gradient(to bottom, rgba(28, 25, 23, 0.65), rgba(21, 18, 14, 0.72), rgba(0, 0, 0, 0.96));"></div>
                                        <div aria-hidden="true" class="absolute inset-0 opacity-15 card-dot-pattern z-[2] pointer-events-none"></div>

                                        <!-- Header con Puesto 1 -->
                                        <header class="relative z-10 px-4 pt-4 flex flex-col gap-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[12px] tracking-[0.2em] uppercase font-bold drop-shadow-md" style="color: #fbbf24;">
                                                    N° 1 · Primer Lugar
                                                </span>
                                            </div>
                                            <div class="w-full h-px" style="background: linear-gradient(to right, transparent, rgba(212, 175, 55, 0.4), transparent);"></div>
                                        </header>

                                        <!-- Centro: Signo '?' estático -->
                                        <div class="relative z-10 my-auto flex flex-col items-center justify-center px-3">
                                            <span class="font-serif text-6xl font-extrabold gold-gradient-text drop-shadow-[0_2px_14px_rgba(212,175,55,0.45)] leading-none select-none">
                                                ?
                                            </span>
                                        </div>

                                        <!-- Parte baja -->
                                        <div class="relative z-10 px-3 pb-4 mt-auto flex flex-col items-center text-center">
                                            <div aria-hidden="true" class="w-8 h-0.5 rounded-full mb-2" style="background-color: rgba(212, 175, 55, 0.5);"></div>
                                            <span class="font-serif text-base font-bold tracking-widest text-amber-50 uppercase drop-shadow-md">
                                                En Espera
                                            </span>
                                            <span class="text-xs text-stone-400 font-sans mt-0.5">
                                                Primer Lugar
                                            </span>
                                        </div>
                                    </div>

                                    <!-- CARA DELANTERA: GANADOR REVELADO -->
                                    <article class="podio-card-front podio-card-shadow-1 absolute inset-0 w-full h-full rounded-2xl overflow-hidden bg-gradient-to-b from-stone-900/90 via-[#120f0c] to-black flex flex-col justify-between p-3 cursor-pointer"
                                             style="height: 410px;"
                                             data-card-id="{{ $primerLugar['id'] }}"
                                             onclick="abrirModalDesglose({{ $primerLugar['id'] }})">
                                        
                                        <!-- Fotografía del Participante / Fondo de Gala -->
                                        <img class="card-foto absolute inset-0 w-full h-full object-cover object-top z-0"
                                             src="{{ $foto1 }}"
                                             alt="{{ $primerLugar['nombre'] }}"
                                             onerror="this.onerror=null; this.src='{{ asset('images/fondo.png') }}';">

                                        <div aria-hidden="true" class="absolute inset-0 z-[1] pointer-events-none" style="background: linear-gradient(to bottom, rgba(28, 25, 23, 0.45) 0%, rgba(21, 18, 14, 0.45) 48%, rgba(0, 0, 0, 0.95) 100%);"></div>
                                        <div aria-hidden="true" class="absolute inset-0 opacity-15 card-dot-pattern z-[2] pointer-events-none"></div>

                                        <!-- Header con Puesto 1 y Bandera -->
                                        <header class="relative z-10 px-4 pt-4 flex flex-col gap-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[12px] tracking-[0.2em] uppercase font-bold drop-shadow-md" style="color: #fbbf24;">
                                                    N° 1 · Primer Lugar
                                                </span>
                                                @if ($bandera1)
                                                    <img class="w-4 h-3.5 object-cover rounded-[1px]" src="{{ $bandera1 }}" alt="Bandera de {{ $primerLugar['departamento'] ?? 'Bolivia' }}">
                                                @endif
                                            </div>
                                            <div class="w-full h-px" style="background: linear-gradient(to right, transparent, rgba(212, 175, 55, 0.4), transparent);"></div>
                                        </header>

                                        <!-- Parte baja: Calificación sobre el Nombre y Departamento -->
                                        <div class="relative z-10 px-3 pb-4 mt-auto flex flex-col items-center text-center">
                                            <div class="flex items-baseline gap-1 mb-1">
                                                <span class="card-score font-serif text-4xl sm:text-5xl font-extrabold tracking-tight gold-gradient-text drop-shadow-[0_2px_12px_rgba(212,175,55,0.45)]">
                                                    {{ number_format((float) $primerLugar['nota_final'], 2) }}
                                                </span>
                                                <span class="text-[11px] font-semibold text-yellow-200/50 uppercase tracking-wider font-sans">/100</span>
                                            </div>
                                            <div aria-hidden="true" class="w-8 h-0.5 rounded-full mb-2" style="background-color: rgba(212, 175, 55, 0.5);"></div>
                                            <h3 class="card-nombre font-serif text-lg sm:text-xl font-bold tracking-widest text-amber-50 uppercase drop-shadow-md line-clamp-2" title="{{ $primerLugar['nombre'] }}">
                                                {{ $primerLugar['nombre'] }}
                                            </h3>
                                            <span class="text-xs text-stone-300 font-sans mt-0.5">
                                                {{ $primerLugar['departamento'] ?? 'Bolivia' }}
                                            </span>
                                        </div>

                                    </article>

                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- ========================= -->
                    <!-- 3° LUGAR                  -->
                    <!-- ========================= -->
                    @if ($tercerLugar)
                        @php
                            $foto3 = $tercerLugar['foto_url'] ?: asset('images/fondo.png');
                            $bandera3 = \App\Modules\Departamentos\Models\Departamento::urlBanderaPorNombre($tercerLugar['departamento'] ?? null);
                        @endphp
                        <div class="order-3 flex flex-col items-center w-full">
                            <div class="podio-card-flip-container w-full" style="max-width: 265px;">
                                <div id="podioCard_3" class="podio-card-inner {{ $lugar3Revelado ? 'is-flipped' : '' }}" style="height: 390px;">
                                    
                                    <!-- CARA TRASERA: PREPARACIÓN DEL PODIO (MISMO ESTILO QUE CARD DE RANKING PÚBLICO) -->
                                    <div class="podio-card-back podio-card-shadow relative w-full h-full rounded-2xl overflow-hidden bg-gradient-to-b from-stone-900/90 via-[#120f0c] to-black flex flex-col justify-between p-3 select-none"
                                         style="height: 390px;">
                                        <img class="absolute inset-0 w-full h-full object-cover z-0 opacity-35"
                                             src="{{ asset('images/fondo.png') }}"
                                             alt="Fondo de Gala">
                                        <div aria-hidden="true" class="absolute inset-0 z-[1] pointer-events-none" style="background: linear-gradient(to bottom, rgba(28, 25, 23, 0.65), rgba(21, 18, 14, 0.72), rgba(0, 0, 0, 0.96));"></div>
                                        <div aria-hidden="true" class="absolute inset-0 opacity-15 card-dot-pattern z-[2] pointer-events-none"></div>

                                        <!-- Header con Puesto 3 -->
                                        <header class="relative z-10 px-4 pt-4 flex flex-col gap-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[12px] tracking-[0.2em] uppercase font-bold drop-shadow-md" style="color: #fbbf24;">
                                                    N° 3 · Tercer Lugar
                                                </span>
                                            </div>
                                            <div class="w-full h-px" style="background: linear-gradient(to right, transparent, rgba(212, 175, 55, 0.4), transparent);"></div>
                                        </header>

                                        <!-- Centro: Signo '?' estático -->
                                        <div class="relative z-10 my-auto flex flex-col items-center justify-center px-3">
                                            <span class="font-serif text-6xl font-extrabold gold-gradient-text drop-shadow-[0_2px_14px_rgba(212,175,55,0.45)] leading-none select-none">
                                                ?
                                            </span>
                                        </div>

                                        <!-- Parte baja -->
                                        <div class="relative z-10 px-3 pb-4 mt-auto flex flex-col items-center text-center">
                                            <div aria-hidden="true" class="w-8 h-0.5 rounded-full mb-2" style="background-color: rgba(212, 175, 55, 0.5);"></div>
                                            <span class="font-serif text-base font-bold tracking-widest text-amber-50 uppercase drop-shadow-md">
                                                En Espera
                                            </span>
                                            <span class="text-xs text-stone-400 font-sans mt-0.5">
                                                Tercer Lugar
                                            </span>
                                        </div>
                                    </div>

                                    <!-- CARA DELANTERA: GANADOR REVELADO -->
                                    <article class="podio-card-front podio-card-shadow absolute inset-0 w-full h-full rounded-2xl overflow-hidden bg-gradient-to-b from-stone-900/90 via-[#120f0c] to-black flex flex-col justify-between p-3 cursor-pointer"
                                             style="height: 390px;"
                                             data-card-id="{{ $tercerLugar['id'] }}"
                                             onclick="abrirModalDesglose({{ $tercerLugar['id'] }})">
                                        
                                        <!-- Fotografía del Participante / Fondo de Gala -->
                                        <img class="card-foto absolute inset-0 w-full h-full object-cover object-top z-0"
                                             src="{{ $foto3 }}"
                                             alt="{{ $tercerLugar['nombre'] }}"
                                             onerror="this.onerror=null; this.src='{{ asset('images/fondo.png') }}';">

                                        <div aria-hidden="true" class="absolute inset-0 z-[1] pointer-events-none" style="background: linear-gradient(to bottom, rgba(28, 25, 23, 0.45) 0%, rgba(21, 18, 14, 0.45) 48%, rgba(0, 0, 0, 0.95) 100%);"></div>
                                        <div aria-hidden="true" class="absolute inset-0 opacity-15 card-dot-pattern z-[2] pointer-events-none"></div>

                                        <!-- Header con Puesto 3 y Bandera -->
                                        <header class="relative z-10 px-4 pt-4 flex flex-col gap-2">
                                            <div class="flex items-center justify-between">
                                                <span class="text-[12px] tracking-[0.2em] uppercase font-bold drop-shadow-md" style="color: #fbbf24;">
                                                    N° 3 · Tercer Lugar
                                                </span>
                                                @if ($bandera3)
                                                    <img class="w-4 h-3.5 object-cover rounded-[1px]" src="{{ $bandera3 }}" alt="Bandera de {{ $tercerLugar['departamento'] ?? 'Bolivia' }}">
                                                @endif
                                            </div>
                                            <div class="w-full h-px" style="background: linear-gradient(to right, transparent, rgba(212, 175, 55, 0.4), transparent);"></div>
                                        </header>

                                        <!-- Parte baja: Calificación sobre el Nombre y Departamento -->
                                        <div class="relative z-10 px-3 pb-4 mt-auto flex flex-col items-center text-center">
                                            <div class="flex items-baseline gap-1 mb-1">
                                                <span class="card-score font-serif text-4xl sm:text-5xl font-extrabold tracking-tight gold-gradient-text drop-shadow-[0_2px_12px_rgba(212,175,55,0.45)]">
                                                    {{ number_format((float) $tercerLugar['nota_final'], 2) }}
                                                </span>
                                                <span class="text-[11px] font-semibold text-yellow-200/50 uppercase tracking-wider font-sans">/100</span>
                                            </div>
                                            <div aria-hidden="true" class="w-8 h-0.5 rounded-full mb-2" style="background-color: rgba(212, 175, 55, 0.5);"></div>
                                            <h3 class="card-nombre font-serif text-lg font-bold tracking-widest text-amber-50 uppercase drop-shadow-md line-clamp-2" title="{{ $tercerLugar['nombre'] }}">
                                                {{ $tercerLugar['nombre'] }}
                                            </h3>
                                            <span class="text-xs text-stone-300 font-sans mt-0.5">
                                                {{ $tercerLugar['departamento'] ?? 'Bolivia' }}
                                            </span>
                                        </div>

                                    </article>

                                </div>
                            </div>
                        </div>
                    @else
                        <div class="order-3 hidden sm:block"></div>
                    @endif

                </div>
                @endif

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
                            :id="$item['id']"
                            :numero="$numero"
                            :nombre="$item['nombre']"
                            :departamento="$item['departamento']"
                            :banderaUrl="$bandera"
                            :fotoUrl="$foto"
                            :notaFinal="$item['nota_final']"
                            :juradosCalificados="$item['jurados_calificados_count'] ?? 0"
                            :juradosTotal="$item['jurados_total_count'] ?? 5"
                            :juradosLista="$item['jurados'] ?? []"
                            onclick="abrirModalDesglose({{ $item['id'] }})"
                        />
                    @endforeach
                </main>
                <!-- END: ContestantsGrid -->
            @endif

        @endif

        <!-- ============================================================== -->
        <!-- MODALES DE DESGLOSE DE EVALUACIÓN (FILA DE 5 REDONDOS)         -->
        <!-- ============================================================== -->
        @foreach ($ranking as $item)
            @php
                $juradosModal = collect($item['jurados'] ?? [])->values()->pad(5, null)->take(5);
            @endphp
            <div id="modalDesglose_{{ $item['id'] }}" class="fixed inset-0 z-50 flex items-center justify-center bg-[#0d0307]/85 backdrop-blur-md hidden p-3 sm:p-4" onclick="if(event.target === this) cerrarModalDesglose({{ $item['id'] }})">
                <div class="bg-[#16050c] border border-[#800020] rounded-2xl max-w-md w-full p-5 sm:p-6 shadow-2xl space-y-4 text-left relative overflow-hidden" onclick="event.stopPropagation()">
                    
                    <div class="corner-bracket-tl"></div>
                    <div class="corner-bracket-tr"></div>
                    <div class="corner-bracket-bl"></div>
                    <div class="corner-bracket-br"></div>

                    <div class="flex items-center justify-between border-b border-[#4a0817] pb-3">
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-[#d4af37] font-cinzel">Mesa Evaluadora Oficial</span>
                            <h4 class="text-base sm:text-lg font-bold text-white uppercase font-cinzel">{{ $item['nombre'] }}</h4>
                        </div>
                        <button onclick="cerrarModalDesglose({{ $item['id'] }})" class="text-slate-400 hover:text-white p-1 rounded-lg hover:bg-white/10 transition cursor-pointer">
                            ✕
                        </button>
                    </div>

                    <!-- Fila de 5 Redondos con las Calificaciones de los Jurados -->
                    <div class="grid grid-cols-5 gap-2.5 sm:gap-3.5 py-3 justify-items-center" data-modal-jurados-fila>
                        @foreach ($juradosModal as $idx => $j)
                            @php
                                $esSecretoOculto = !empty($j['es_secreto']) && empty($j['es_revelado']);
                                $secretoHaVotado = $esSecretoOculto && !empty($j['calificado']);
                                $tieneNota = !$esSecretoOculto && isset($j['nota_total']) && $j['nota_total'] !== null;
                            @endphp
                            <div data-modal-jurado-index="{{ $idx }}"
                                 class="w-14 h-14 sm:w-16 sm:h-16 rounded-full flex flex-col items-center justify-center transition-all duration-300 {{ $esSecretoOculto ? ($secretoHaVotado ? 'bg-gradient-to-b from-emerald-900/80 to-[#062016] border-2 border-emerald-400 shadow-[0_0_16px_rgba(16,185,129,0.45)]' : 'bg-[#1a050b]/85 border-2 border-rose-500/80 shadow-[0_0_10px_rgba(244,63,94,0.25)]') : ($tieneNota ? 'bg-gradient-to-b from-[#240612] to-[#0c0206] border-2 border-[#d4af37] shadow-[0_0_14px_rgba(212,175,55,0.28)]' : 'bg-[#0c0206]/80 border-2 border-[#4a0817]/70') }}">
                                @if ($esSecretoOculto)
                                    <span data-modal-jurado-valor class="font-cinzel text-2xl sm:text-3xl font-black {{ $secretoHaVotado ? 'text-emerald-300 drop-shadow-[0_0_8px_rgba(16,185,129,0.7)]' : 'text-rose-300' }} leading-none">?</span>
                                    <span class="sr-only">🔒 Reservado</span>
                                @elseif ($tieneNota)
                                    <span data-modal-jurado-valor class="text-xs sm:text-sm font-black font-mono text-amber-300 tabular-nums leading-none">
                                        {{ is_numeric($j['nota_total']) ? number_format((float) $j['nota_total'], 1) : $j['nota_total'] }}
                                    </span>
                                @else
                                    <span data-modal-jurado-valor class="text-sm font-bold font-mono text-slate-500 leading-none">—</span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Promedio / Nota Final -->
                    <div class="pt-3 border-t border-[#4a0817] flex items-center justify-between text-xs">
                        <span class="font-bold text-slate-300 uppercase font-cinzel tracking-wider">Promedio Final Oficial:</span>
                        <span data-modal-promedio-final class="text-sm sm:text-base font-black gold-gradient-text font-mono tabular-nums">
                            {{ $item['nota_final'] !== null ? number_format($item['nota_final'], 2) . ' pts' : 'En Evaluación' }}
                        </span>
                    </div>

                </div>
            </div>
        @endforeach

    </div>

    <!-- ============================================================== -->
    <!-- SCRIPTS DE ACTUALIZACIÓN EN VIVO (WEBSOCKETS Y ANIMACIÓN FLIP) -->
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

        // ==============================================================
        // MOTOR DE ANIMACIÓN FLIP ULTRA-FLUIDO EN TIEMPO REAL (60/120 FPS)
        // ==============================================================
        
        // Interpolador numérico suave para actualización de notas
        function interpolarPuntaje(element, valorInicial, valorFinal, duracion = 500) {
            if (isNaN(valorInicial) || isNaN(valorFinal)) {
                element.textContent = isNaN(valorFinal) ? 'N/C' : valorFinal.toFixed(1);
                return;
            }
            if (Math.abs(valorFinal - valorInicial) < 0.05) {
                element.textContent = valorFinal.toFixed(1);
                return;
            }

            const inicio = performance.now();
            function paso(ahora) {
                const progreso = Math.min((ahora - inicio) / duracion, 1);
                // Easing cúbico desacelerado suave
                const factor = 1 - Math.pow(1 - progreso, 3);
                const actual = valorInicial + (valorFinal - valorInicial) * factor;
                element.textContent = actual.toFixed(1);
                if (progreso < 1) {
                    requestAnimationFrame(paso);
                } else {
                    element.textContent = valorFinal.toFixed(1);
                }
            }
            requestAnimationFrame(paso);
        }

        function animarReordenamientoCards(gridContainer, accionReordenar) {
            if (!gridContainer) return;

            // 1. FIRST: Lectura por lote (Batch Read) sin mutaciones intermedias
            const cards = Array.from(gridContainer.querySelectorAll('.musician-card[data-card-id]'));
            const firstRects = new Map();
            for (let i = 0; i < cards.length; i++) {
                const id = cards[i].getAttribute('data-card-id');
                if (id) {
                    firstRects.set(id, cards[i].getBoundingClientRect());
                }
            }

            // 2. MUTACIÓN POR LOTE: Reordenamiento y actualización de datos en el DOM
            accionReordenar();

            // 3. LAST: Segunda lectura por lote (Batch Read 2)
            const currentCards = Array.from(gridContainer.querySelectorAll('.musician-card[data-card-id]'));
            const movingCards = [];

            for (let i = 0; i < currentCards.length; i++) {
                const card = currentCards[i];
                const id = card.getAttribute('data-card-id');
                if (id && firstRects.has(id)) {
                    const first = firstRects.get(id);
                    const last = card.getBoundingClientRect();
                    const dx = first.left - last.left;
                    const dy = first.top - last.top;

                    if (Math.abs(dx) > 0.5 || Math.abs(dy) > 0.5) {
                        movingCards.push({ card, dx, dy });
                    }
                }
            }

            // 4. INVERT: Escritura por lote de transformaciones iniciales
            for (let i = 0; i < movingCards.length; i++) {
                const item = movingCards[i];
                item.card.classList.add('is-static');
                item.card.style.transform = `translate3d(${item.dx}px, ${item.dy}px, 0)`;
            }

            // 5. PLAY: Disparar reflow único y transición GPU en frame sincronizado
            if (movingCards.length > 0) {
                void gridContainer.offsetHeight;

                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        for (let i = 0; i < movingCards.length; i++) {
                            const card = movingCards[i].card;
                            card.classList.remove('is-static');
                            card.classList.add('is-reordering');
                            card.style.transform = 'translate3d(0, 0, 0)';
                        }

                        setTimeout(() => {
                            for (let i = 0; i < movingCards.length; i++) {
                                const card = movingCards[i].card;
                                card.classList.remove('is-reordering');
                                card.style.transform = '';
                            }
                        }, 800);
                    });
                });
            }
        }

        // Actualizar los 5 puntos indicadores de votos de jurados en la card
        function actualizarPuntosJuradosCard(card, item) {
            if (!card || !item) return;
            const dotsContainer = card.querySelector('.card-jurados-dots');
            if (!dotsContainer) return;

            const dots = Array.from(dotsContainer.querySelectorAll('.card-jurado-dot'));
            const jurados = Array.isArray(item.jurados) ? item.jurados : [];
            const calificadosCount = typeof item.jurados_calificados_count === 'number'
                ? item.jurados_calificados_count
                : jurados.filter(j => j && j.calificado).length;

            dotsContainer.setAttribute('data-calificados-count', String(calificadosCount));
            dotsContainer.setAttribute('title', `Votos emitidos: ${calificadosCount} / 5`);

            dots.forEach((dot, idx) => {
                const j = jurados[idx];
                const haVotado = j !== undefined
                    ? Boolean(j && j.calificado)
                    : (idx < calificadosCount);
                const esSecreto = j !== undefined
                    ? Boolean(j && j.es_secreto && !j.es_revelado)
                    : (dot.getAttribute('data-es-secreto') === '1');

                dot.setAttribute('data-votado', haVotado ? '1' : '0');
                dot.setAttribute('data-es-secreto', esSecreto ? '1' : '0');
                dot.textContent = esSecreto ? '?' : '';

                if (haVotado) {
                    dot.className = 'card-jurado-dot w-4 h-4 rounded-full inline-flex items-center justify-center text-[10px] font-black font-mono leading-none select-none transition-all duration-300 bg-emerald-400 border border-emerald-300 text-slate-950 shadow-[0_0_8px_#10b981]';
                } else {
                    dot.className = 'card-jurado-dot w-4 h-4 rounded-full inline-flex items-center justify-center text-[10px] font-black font-mono leading-none select-none transition-all duration-300 bg-rose-500/35 border border-rose-500/80 text-rose-200 shadow-[0_0_5px_rgba(244,63,94,0.3)]';
                }
            });
        }

        // Actualizar los 5 círculos del modal de desglose en tiempo real
        function actualizarModalDesglose(item) {
            if (!item || !item.id) return;
            const modal = document.getElementById('modalDesglose_' + item.id);
            if (!modal) return;

            const jurados = Array.isArray(item.jurados) ? item.jurados : [];
            const circulos = Array.from(modal.querySelectorAll('[data-modal-jurado-index]'));

            circulos.forEach((circulo, idx) => {
                const j = jurados[idx] || null;
                const esSecretoOculto = Boolean(j && j.es_secreto && !j.es_revelado);
                const secretoHaVotado = Boolean(esSecretoOculto && j && j.calificado);
                const tieneNota = !esSecretoOculto && j && j.nota_total !== null && j.nota_total !== undefined && j.nota_total !== '';

                if (esSecretoOculto) {
                    if (secretoHaVotado) {
                        circulo.className = 'w-14 h-14 sm:w-16 sm:h-16 rounded-full flex flex-col items-center justify-center transition-all duration-300 bg-gradient-to-b from-emerald-900/80 to-[#062016] border-2 border-emerald-400 shadow-[0_0_16px_rgba(16,185,129,0.45)]';
                        circulo.innerHTML = '<span data-modal-jurado-valor class="font-cinzel text-2xl sm:text-3xl font-black text-emerald-300 drop-shadow-[0_0_8px_rgba(16,185,129,0.7)] leading-none">?</span><span class="sr-only">🔒 Reservado</span>';
                    } else {
                        circulo.className = 'w-14 h-14 sm:w-16 sm:h-16 rounded-full flex flex-col items-center justify-center transition-all duration-300 bg-[#1a050b]/85 border-2 border-rose-500/80 shadow-[0_0_10px_rgba(244,63,94,0.25)]';
                        circulo.innerHTML = '<span data-modal-jurado-valor class="font-cinzel text-2xl sm:text-3xl font-black text-rose-300 leading-none">?</span><span class="sr-only">🔒 Reservado</span>';
                    }
                } else if (tieneNota) {
                    const num = parseFloat(j.nota_total);
                    const textoNota = isNaN(num) ? String(j.nota_total) : num.toFixed(1);
                    circulo.className = 'w-14 h-14 sm:w-16 sm:h-16 rounded-full flex flex-col items-center justify-center transition-all duration-300 bg-gradient-to-b from-[#240612] to-[#0c0206] border-2 border-[#d4af37] shadow-[0_0_14px_rgba(212,175,55,0.28)]';
                    circulo.innerHTML = `<span data-modal-jurado-valor class="text-xs sm:text-sm font-black font-mono text-amber-300 tabular-nums leading-none">${textoNota}</span>`;
                } else {
                    circulo.className = 'w-14 h-14 sm:w-16 sm:h-16 rounded-full flex flex-col items-center justify-center transition-all duration-300 bg-[#0c0206]/80 border-2 border-[#4a0817]/70';
                    circulo.innerHTML = '<span data-modal-jurado-valor class="text-sm font-bold font-mono text-slate-500 leading-none">—</span>';
                }
            });

            const promEl = modal.querySelector('[data-modal-promedio-final]');
            if (promEl) {
                const nf = item.nota_final !== null && item.nota_final !== undefined && item.nota_final !== '' ? parseFloat(item.nota_final) : NaN;
                promEl.textContent = isNaN(nf) ? 'En Evaluación' : (nf.toFixed(2) + ' pts');
            }
        }

        // Respuesta instantánea cuando llega evento individual de calificación registrada
        function marcarVotoIndividualWebSocket(data) {
            if (!data || !data.presentacion_id) return;
            const card = document.querySelector(`.musician-card[data-card-id="${data.presentacion_id}"]`);
            if (!card) return;

            const selector = data.es_secreto
                ? '.card-jurado-dot[data-es-secreto="1"]'
                : '.card-jurado-dot[data-votado="0"]:not([data-es-secreto="1"])';

            const targetDot = card.querySelector(selector) || card.querySelector('.card-jurado-dot[data-votado="0"]');
            if (targetDot) {
                targetDot.setAttribute('data-votado', '1');
                if (targetDot.getAttribute('data-es-secreto') === '1') {
                    targetDot.textContent = '?';
                }
                targetDot.className = 'card-jurado-dot w-4 h-4 rounded-full inline-flex items-center justify-center text-[10px] font-black font-mono leading-none select-none transition-all duration-300 bg-emerald-400 border border-emerald-300 text-slate-950 shadow-[0_0_8px_#10b981]';
            }
        }

        // Procesar evento WebSocket directo sin peticiones de red
        function procesarRankingWebSocket(data) {
            if (!data || !Array.isArray(data.ranking)) {
                refrescarRankingEnVivo();
                return;
            }

            const gridContainer = document.querySelector('[data-purpose="desktop-showcase-grid"]');
            if (!gridContainer) {
                // Si estamos en vista de podio o estado finalizado, refrescar por fetch
                refrescarRankingEnVivo();
                return;
            }

            animarReordenamientoCards(gridContainer, () => {
                data.ranking.forEach((item, index) => {
                    const card = gridContainer.querySelector(`.musician-card[data-card-id="${item.id}"]`);
                    if (card) {
                        // Actualizar puntaje de forma interpolada con resplandor
                        const scoreEl = card.querySelector('.card-score-value');
                        let numFinal = NaN;
                        if (item.nota_final !== null && item.nota_final !== undefined && item.nota_final !== '') {
                            numFinal = parseFloat(item.nota_final);
                        }

                        if (scoreEl) {
                            const textoActual = scoreEl.textContent.trim();
                            const numInicial = parseFloat(textoActual);
                            const notaStr = isNaN(numFinal) ? 'N/C' : numFinal.toFixed(1);

                            if (textoActual !== notaStr) {
                                if (!isNaN(numInicial) && !isNaN(numFinal)) {
                                    interpolarPuntaje(scoreEl, numInicial, numFinal, 500);
                                } else {
                                    scoreEl.textContent = notaStr;
                                }
                                card.classList.add('score-updated-pulse');
                                setTimeout(() => card.classList.remove('score-updated-pulse'), 1300);
                            }
                        }

                        // Actualizar número de posición
                        const numEl = card.querySelector('.card-numero-value');
                        const pos = item.posicion !== undefined && item.posicion !== null ? item.posicion : (index + 1);
                        if (numEl) {
                            numEl.textContent = 'N° ' + pos;
                        }

                        // Actualizar fotografía si cambió
                        if (item.foto_url) {
                            const imgEl = card.querySelector('.card-foto');
                            if (imgEl && imgEl.getAttribute('src') !== item.foto_url) {
                                imgEl.src = item.foto_url;
                            }
                        }

                        // Actualizar los 5 puntos de jurados en la card y los 5 círculos del modal
                        actualizarPuntosJuradosCard(card, item);
                        actualizarModalDesglose(item);

                        // Reordenar nodo DOM en el contenedor
                        gridContainer.appendChild(card);
                    }
                });
            });
        }

        // Actualización inteligente del DOM con preservación de nodos
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
                        const gridActual = contenedorActual.querySelector('[data-purpose="desktop-showcase-grid"]');
                        const gridNuevo = nuevoContenido.querySelector('[data-purpose="desktop-showcase-grid"]');

                        // Si ambos son modo Gala Grid, reordenar en sitio preservando nodos
                        if (gridActual && gridNuevo) {
                            animarReordenamientoCards(gridActual, () => {
                                const newCards = Array.from(gridNuevo.querySelectorAll('.musician-card[data-card-id]'));
                                newCards.forEach(newCard => {
                                    const id = newCard.getAttribute('data-card-id');
                                    const existingCard = gridActual.querySelector(`.musician-card[data-card-id="${id}"]`);
                                    if (existingCard) {
                                        // Actualizar textos/puntajes
                                        const oldScore = existingCard.querySelector('.card-score-value');
                                        const newScore = newCard.querySelector('.card-score-value');
                                        if (oldScore && newScore && oldScore.textContent.trim() !== newScore.textContent.trim()) {
                                            const nInit = parseFloat(oldScore.textContent.trim());
                                            const nEnd = parseFloat(newScore.textContent.trim());
                                            if (!isNaN(nInit) && !isNaN(nEnd)) {
                                                interpolarPuntaje(oldScore, nInit, nEnd, 500);
                                            } else {
                                                oldScore.textContent = newScore.textContent.trim();
                                            }
                                            existingCard.classList.add('score-updated-pulse');
                                            setTimeout(() => existingCard.classList.remove('score-updated-pulse'), 1300);
                                        }

                                        const oldNum = existingCard.querySelector('.card-numero-value');
                                        const newNum = newCard.querySelector('.card-numero-value');
                                        if (oldNum && newNum) {
                                            oldNum.textContent = newNum.textContent.trim();
                                        }

                                        const oldImg = existingCard.querySelector('.card-foto');
                                        const newImg = newCard.querySelector('.card-foto');
                                        if (oldImg && newImg && oldImg.getAttribute('src') !== newImg.getAttribute('src')) {
                                            oldImg.src = newImg.getAttribute('src');
                                        }

                                        const oldDots = existingCard.querySelector('.card-jurados-dots');
                                        const newDots = newCard.querySelector('.card-jurados-dots');
                                        if (oldDots && newDots) {
                                            oldDots.innerHTML = newDots.innerHTML;
                                            oldDots.setAttribute('data-calificados-count', newDots.getAttribute('data-calificados-count') || '0');
                                            oldDots.setAttribute('title', newDots.getAttribute('title') || '');
                                        }

                                        gridActual.appendChild(existingCard);
                                    } else {
                                        gridActual.appendChild(newCard.cloneNode(true));
                                    }
                                });
                            });

                            // Actualizar modales de desglose
                            const modalesNuevos = nuevoContenido.querySelectorAll('[id^="modalDesglose_"]');
                            modalesNuevos.forEach(m => {
                                const mActual = document.getElementById(m.id);
                                if (mActual) {
                                    const estaAbierto = !mActual.classList.contains('hidden');
                                    mActual.innerHTML = m.innerHTML;
                                    if (estaAbierto) mActual.classList.remove('hidden');
                                } else {
                                    contenedorActual.appendChild(m.cloneNode(true));
                                }
                            });
                        } else {
                            // Cambio de modo (e.g. paso a podio de gala)
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
                }
            } catch (err) {
                console.warn('Error al actualizar contenido en vivo:', err);
            } finally {
                actualizandoContenido = false;
            }
        }

        // Función de revelación de paso del podio (animación 3D Flip Card)
        function aplicarPasoPodio(data) {
            if (!data) return;
            const paso = parseInt(data.paso, 10);
            const pos = data.posicion_revelada ? parseInt(data.posicion_revelada, 10) : null;

            const card1 = document.getElementById('podioCard_1');
            const card2 = document.getElementById('podioCard_2');
            const card3 = document.getElementById('podioCard_3');

            if (paso === 0) {
                // Voltear todas las cartas a misterio
                if (card1) card1.classList.remove('is-flipped');
                if (card2) card2.classList.remove('is-flipped');
                if (card3) card3.classList.remove('is-flipped');
                return;
            }

            // Inyectar datos del ganador si viene en el payload
            if (data.ganador && pos) {
                const cardTarget = document.getElementById(`podioCard_${pos}`);
                if (cardTarget) {
                    const front = cardTarget.querySelector('.podio-card-front');
                    if (front) {
                        front.setAttribute('data-card-id', data.ganador.id);
                        front.onclick = () => abrirModalDesglose(data.ganador.id);
                        const nombreEl = front.querySelector('.card-nombre');
                        if (nombreEl) {
                            nombreEl.textContent = data.ganador.nombre;
                            nombreEl.title = data.ganador.nombre;
                        }
                        const scoreEl = front.querySelector('.card-score');
                        if (scoreEl) {
                            const nf = parseFloat(data.ganador.nota_final);
                            scoreEl.textContent = isNaN(nf) ? '0.00' : nf.toFixed(2);
                        }
                        if (data.ganador.foto_url) {
                            const fotoEl = front.querySelector('.card-foto');
                            if (fotoEl) {
                                fotoEl.src = data.ganador.foto_url;
                            }
                        }
                    }
                }
            }

            if (paso === 3) {
                if (card3) card3.classList.add('is-flipped');
                if (card2) card2.classList.remove('is-flipped');
                if (card1) card1.classList.remove('is-flipped');
            } else if (paso === 2) {
                if (card3) card3.classList.add('is-flipped');
                if (card2) card2.classList.add('is-flipped');
                if (card1) card1.classList.remove('is-flipped');
            } else if (paso === 1) {
                if (card3) card3.classList.add('is-flipped');
                if (card2) card2.classList.add('is-flipped');
                if (card1) card1.classList.add('is-flipped');
            }
        }

        // Suscripción a WebSockets
        function iniciarEscuchaEcho() {
            if (!window.Echo) return false;

            try {
                const canalRanking = window.Echo.channel('ranking');
                if (canalRanking) {
                    canalRanking.listen('.ranking.actualizado', (data) => {
                        procesarRankingWebSocket(data);
                    });
                    canalRanking.listen('ranking.actualizado', (data) => {
                        procesarRankingWebSocket(data);
                    });
                    canalRanking.listen('.podio.paso_revelado', (data) => {
                        aplicarPasoPodio(data);
                    });
                    canalRanking.listen('podio.paso_revelado', (data) => {
                        aplicarPasoPodio(data);
                    });
                    
                    ['.calificacion.registrada', 'calificacion.registrada', '.calificacion.corregida', 'calificacion.corregida', '.jurado_secreto.revelado', 'jurado_secreto.revelado', '.categoria.finalizada', 'categoria.finalizada', '.categoria.activa.cambiada', 'categoria.activa.cambiada', '.presentacion.estado.cambiado', 'presentacion.estado.cambiado'].forEach(ev => {
                        canalRanking.listen(ev, (data) => {
                            if (ev.includes('calificacion.registrada')) {
                                marcarVotoIndividualWebSocket(data);
                            }
                            if (data && data.ranking) {
                                procesarRankingWebSocket(data);
                            } else {
                                refrescarRankingEnVivo();
                            }
                        });
                    });
                }

                const canalPresentaciones = window.Echo.channel('presentaciones');
                if (canalPresentaciones) {
                    canalPresentaciones.listen('.podio.paso_revelado', (data) => {
                        aplicarPasoPodio(data);
                    });
                    canalPresentaciones.listen('podio.paso_revelado', (data) => {
                        aplicarPasoPodio(data);
                    });

                    ['.calificacion.registrada', 'calificacion.registrada', '.calificacion.corregida', 'calificacion.corregida', '.presentacion.estado.cambiado', 'presentacion.estado.cambiado', '.cronometro.actualizado', 'cronometro.actualizado', '.categoria.activa.cambiada', 'categoria.activa.cambiada'].forEach(ev => {
                        canalPresentaciones.listen(ev, (data) => {
                            if (ev.includes('calificacion.registrada')) {
                                marcarVotoIndividualWebSocket(data);
                            }
                            if (data && data.ranking) {
                                procesarRankingWebSocket(data);
                            } else {
                                refrescarRankingEnVivo();
                            }
                        });
                    });
                }

                return true;
            } catch (e) {
                console.warn('Error suscribiendo a canales Echo:', e);
                return false;
            }
        }

        function setupLiveSync() {
            if (!iniciarEscuchaEcho()) {
                let attempts = 0;
                const echoInterval = setInterval(function () {
                    attempts++;
                    if (iniciarEscuchaEcho() || attempts > 30) {
                        clearInterval(echoInterval);
                    }
                }, 250);
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupLiveSync);
        } else {
            setupLiveSync();
        }
        window.addEventListener('load', setupLiveSync);

        // Polling de respaldo de baja frecuencia para evitar colisiones
        setInterval(refrescarRankingEnVivo, 8000);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('[id^="modalDesglose_"]').forEach(m => m.classList.add('hidden'));
            }
        });
    </script>

</x-layouts.publico>