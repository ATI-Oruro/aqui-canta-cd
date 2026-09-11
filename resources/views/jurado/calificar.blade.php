<x-layouts.jurado>
    <x-slot:header>
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ route('jurado.inicio') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 inline-flex items-center gap-1">
                    ← Volver
                </a>
                <h2 class="text-xl font-bold text-slate-800">Evaluación de Participante</h2>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-semibold rounded-full border border-amber-300">
                    {{ $presentacion->inscripcion?->fase?->fase?->label() ?? 'Etapa' }}
                </span>
                <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-semibold rounded-full border border-blue-300">
                    {{ $presentacion->inscripcion?->categoria?->categoria ?? 'Categoría' }}
                </span>
            </div>
        </div>
    </x-slot:header>

    <x-slot:sidebar>
        <a href="{{ route('jurado.inicio') }}" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white">Inicio</a>
        <a href="{{ route('jurado.presentaciones.actual') }}" class="block px-4 py-2 rounded-lg text-slate-300 hover:bg-slate-700 hover:text-white">Escenario en vivo</a>
    </x-slot:sidebar>

    <div class="max-w-6xl mx-auto space-y-6">

        @if (session('error'))
            <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center gap-3 shadow-sm">
                <span class="text-xl">⚠️</span>
                <p class="font-medium">{{ session('error') }}</p>
            </div>
        @endif

        @php
            $inscripcion = $presentacion->inscripcion;
            $participante = $inscripcion?->participantes->first();
            $nombrePrincipal = $presentacion->nombreMostrado();
            $pseudonimo = $participante?->pseudonimo ?? $presentacion->nombre_presentacion ?? $nombrePrincipal;
            $inicial = mb_substr($nombrePrincipal, 0, 1, 'UTF-8');
            $esActivo = in_array($presentacion->estado?->value, [
                App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO->value,
                App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO->value,
            ], true);
        @endphp

        <!-- Contenedor Principal: 2 Columnas -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- Columna Izquierda: Tarjeta Resumen del Participante (4 cols) -->
            <div class="lg:col-span-4 space-y-4 sticky top-6">
                <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 overflow-hidden">
                    <!-- Franja Tricolor Superior -->
                    <div class="h-2 w-full flex">
                        <div class="w-1/3 bg-[#dc2626]"></div>
                        <div class="w-1/3 bg-[#facc15]"></div>
                        <div class="w-1/3 bg-[#16a34a]"></div>
                    </div>

                    <div class="p-6 text-center space-y-4">
                        <!-- Avatar con Inicial -->
                        <div class="relative inline-block mx-auto">
                            <div class="h-24 w-24 rounded-full bg-[#5c0618] border-4 border-[#e2b765] shadow-md flex items-center justify-center text-white font-black text-4xl">
                                {{ $inicial }}
                            </div>
                            <span class="absolute bottom-1 right-1 h-4 w-4 rounded-full bg-emerald-500 border-2 border-white"></span>
                        </div>

                        <!-- Categoría -->
                        <div>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-[#3d040f] text-amber-300 text-xs font-bold tracking-wide">
                                <span>♪</span>
                                <span>{{ $inscripcion?->categoria?->categoria ?? 'Categoría' }}</span>
                            </span>
                        </div>

                        <!-- Nombre del participante -->
                        <div>
                            <h2 class="text-xl font-black text-slate-900 leading-tight">
                                {{ $nombrePrincipal }}
                            </h2>
                            <p class="text-xs font-semibold text-[#8b1528] italic mt-0.5">
                                «{{ $pseudonimo }}»
                            </p>
                        </div>

                        <!-- Grid 4 cajas informativas -->
                        <div class="grid grid-cols-2 gap-2 text-left pt-2">
                            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-2.5">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Fecha</span>
                                <p class="text-xs font-bold text-slate-800 truncate">
                                    {{ $inscripcion?->fecha_presentacion ? \Carbon\Carbon::parse($inscripcion->fecha_presentacion)->isoFormat('D MMM YYYY') : 'Hoy' }}
                                </p>
                            </div>

                            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-2.5">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Hora</span>
                                <p class="text-xs font-bold text-slate-800">
                                    {{ $inscripcion?->hora_presentacion ?? ($presentacion->hora_inicio ? $presentacion->hora_inicio->format('H:i') : '14:35') }}
                                </p>
                            </div>

                            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-2.5">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">N.° Presentación</span>
                                <p class="text-xs font-black text-[#8b1528]">
                                    #{{ $presentacion->id }}
                                </p>
                            </div>

                            <div class="bg-slate-50 border border-slate-200/80 rounded-xl p-2.5 flex flex-col justify-center">
                                <span class="text-[9px] font-bold uppercase tracking-wider text-slate-400 block">Estado</span>
                                <div>
                                    <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold {{ $esActivo ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                                        {{ $presentacion->estado?->label() }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Cronómetro Integrado -->
                        <div class="bg-slate-900 text-white rounded-2xl p-3.5 flex items-center justify-between">
                            <div class="text-left">
                                <span class="text-[9px] uppercase font-bold tracking-widest text-amber-400 block">Cronómetro</span>
                                <span class="text-xl font-black font-mono tracking-tight tabular-nums text-white">
                                    {{ gmdate('i:s', $presentacion->segundosRestantes()) }}
                                </span>
                            </div>
                            <span class="text-xs font-mono text-slate-400">
                                / {{ $presentacion->tiempo_maximo }}:00
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Columna Derecha: Formulario de Criterios de Calificación (8 cols) -->
            <div class="lg:col-span-8 space-y-6">
                <form id="formCalificacion" method="POST" action="{{ route('jurado.calificaciones.guardar', $presentacion) }}">
                    @csrf

                    <div class="bg-white rounded-3xl shadow-xl border border-slate-200/80 p-6 sm:p-8 space-y-6">
                        
                        <!-- Header del Card Criterios de Calificación -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-5">
                            <div>
                                <h3 class="text-xl font-black text-[#5c0618] tracking-tight">Criterios de Calificación</h3>
                                <p class="text-xs text-slate-400 mt-0.5">Ingresa la nota deslizando o escribiendo el valor</p>
                            </div>

                            <div class="flex items-center gap-2 bg-[#faf5f0] border border-amber-200/80 px-4 py-2 rounded-2xl self-start sm:self-auto shadow-sm">
                                <span class="text-xs font-bold text-slate-700">Nota Total:</span>
                                <span id="displayTotalTop" class="text-xl font-black text-[#5c0618] tabular-nums">0.00</span>
                                <span class="text-xs text-slate-400 font-medium">/ 100 pts</span>
                            </div>
                        </div>

                        <!-- Lista de Parámetros -->
                        <div class="space-y-6">
                            @foreach ($categoriaParametros as $cp)
                                @php
                                    $idParam = $cp->id_parametro;
                                    $nombreParam = $cp->parametro?->parametro ?? "Parámetro #{$idParam}";
                                    $minVal = (float) $cp->valor_minimo;
                                    $maxVal = (float) $cp->valor_maximo;
                                    $valorPrevio = old("valores.{$idParam}", $minVal);
                                @endphp

                                <div class="bg-slate-50/70 hover:bg-slate-50 border border-slate-200/80 rounded-2xl p-4 sm:p-5 transition space-y-3"
                                     data-param-container="{{ $idParam }}">
                                    
                                    <!-- Fila Superior: Nombre del Parámetro + Rango + Nota -->
                                    <div class="flex items-center justify-between gap-2">
                                        <h4 class="font-bold text-slate-900 text-sm sm:text-base leading-snug">
                                            {{ $nombreParam }}
                                        </h4>

                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="text-[11px] text-slate-400 bg-white px-2.5 py-0.5 rounded-full border border-slate-200 font-medium">
                                                Rango: {{ number_format($minVal, 2) }} - {{ number_format($maxVal, 2) }}
                                            </span>
                                            <span class="text-[11px] font-bold text-[#5c0618] bg-rose-50 px-2.5 py-0.5 rounded-full border border-rose-100">
                                                Nota: <span id="badge_nota_{{ $idParam }}">{{ number_format($valorPrevio, 1) }}</span>
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Fila de Controles: Slider Gradiente + Input Numérico Sincronizado -->
                                    <div class="flex items-center gap-4">
                                        <!-- Slider -->
                                        <div class="flex-1 space-y-1">
                                            <input type="range"
                                                   id="slider_{{ $idParam }}"
                                                   min="{{ $minVal }}"
                                                   max="{{ $maxVal }}"
                                                   step="0.5"
                                                   value="{{ $valorPrevio }}"
                                                   class="festival-slider w-full"
                                                   oninput="sincronizarDesdeSlider({{ $idParam }})">
                                            
                                            <div class="flex justify-between text-[10px] text-slate-400 font-bold px-0.5">
                                                <span>{{ number_format($minVal, 2) }}</span>
                                                <span id="label_center_{{ $idParam }}" class="text-[#8b1528]">{{ number_format($valorPrevio, 1) }}</span>
                                                <span>{{ number_format($maxVal, 2) }}</span>
                                            </div>
                                        </div>

                                        <!-- Caja de Texto Numérica -->
                                        <div class="shrink-0 w-24">
                                            <input type="number"
                                                   name="valores[{{ $idParam }}]"
                                                   id="input_{{ $idParam }}"
                                                   min="{{ $minVal }}"
                                                   max="{{ $maxVal }}"
                                                   step="0.5"
                                                   value="{{ $valorPrevio }}"
                                                   required
                                                   class="w-full px-3 py-2 text-center text-base font-black text-slate-900 bg-white border-2 border-slate-300 rounded-xl focus:ring-2 focus:ring-[#5c0618] focus:border-[#5c0618] shadow-sm transition tabular-nums"
                                                   oninput="sincronizarDesdeInput({{ $idParam }})">
                                        </div>
                                    </div>
                                    
                                    @error("valores.{$idParam}")
                                        <p class="text-xs text-rose-600 font-semibold mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                            @endforeach
                        </div>

                        <!-- Botón Principal de Envío -->
                        <div class="pt-4 border-t border-slate-100">
                            <button type="button"
                                    id="btnConfirmarCalificacion"
                                    onclick="abrirModalConfirmacion()"
                                    class="w-full py-4 px-6 rounded-2xl bg-[#5c0618] hover:bg-[#720a20] text-white font-black text-base tracking-wide shadow-xl hover:shadow-2xl transition-all duration-200 flex items-center justify-center gap-2 transform hover:-translate-y-0.5">
                                <span>✓</span>
                                <span>Registrar calificación</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación Oficial -->
    <div id="modalConfirmacion" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/70 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full mx-4 shadow-2xl border border-slate-100 space-y-5">
            <div class="flex items-center gap-3">
                <div class="h-12 w-12 rounded-2xl bg-amber-100 text-amber-800 flex items-center justify-center font-black text-2xl shrink-0">
                    !
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">¿Confirmar emisión de voto?</h3>
                    <p class="text-xs text-slate-500">XXXIII Festival Aquí Canta Bolivia</p>
                </div>
            </div>

            <p class="text-sm text-slate-600 leading-relaxed">
                Está registrando una calificación total de <strong id="modalNotaTotal" class="text-[#5c0618] font-black text-base">0.00</strong> puntos para la presentación <strong class="text-slate-900">{{ $nombrePrincipal }}</strong>.
            </p>

            <div class="p-3.5 bg-slate-50 rounded-2xl text-xs text-slate-500 border border-slate-200/80">
                Al confirmar, su calificación quedará asentada oficialmente en el sistema.
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button"
                        onclick="cerrarModalConfirmacion()"
                        class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition">
                    Revisar notas
                </button>
                <button type="button"
                        onclick="document.getElementById('formCalificacion').submit()"
                        class="px-6 py-2.5 bg-[#5c0618] hover:bg-[#720a20] text-white text-xs font-black rounded-xl shadow-lg transition">
                    Sí, registrar calificación
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function sincronizarDesdeSlider(idParam) {
            const slider = document.getElementById('slider_' + idParam);
            const input = document.getElementById('input_' + idParam);
            const badge = document.getElementById('badge_nota_' + idParam);
            const centerLabel = document.getElementById('label_center_' + idParam);

            if (slider && input) {
                const val = parseFloat(slider.value) || 0;
                input.value = val;
                if (badge) badge.textContent = val.toFixed(1);
                if (centerLabel) centerLabel.textContent = val.toFixed(1);
                recalcularTotal();
            }
        }

        function sincronizarDesdeInput(idParam) {
            const slider = document.getElementById('slider_' + idParam);
            const input = document.getElementById('input_' + idParam);
            const badge = document.getElementById('badge_nota_' + idParam);
            const centerLabel = document.getElementById('label_center_' + idParam);

            if (slider && input) {
                const val = parseFloat(input.value) || 0;
                slider.value = val;
                if (badge) badge.textContent = val.toFixed(1);
                if (centerLabel) centerLabel.textContent = val.toFixed(1);
                recalcularTotal();
            }
        }

        function recalcularTotal() {
            let total = 0;
            document.querySelectorAll('input[type="number"][name^="valores"]').forEach(inp => {
                const val = parseFloat(inp.value) || 0;
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
            recalcularTotal();
            document.getElementById('modalConfirmacion').classList.remove('hidden');
        }

        function cerrarModalConfirmacion() {
            document.getElementById('modalConfirmacion').classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', () => {
            recalcularTotal();
        });
    </script>
    @endpush
</x-layouts.jurado>
