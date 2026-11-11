<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planillas de Calificación por Jurado — {{ $sabana['categoria']->categoria }} — ACB 2026</title>
    <link rel="icon" type="image/png" href="/images/logo-oficial.png">
    <link rel="shortcut icon" href="/images/favicon.ico">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cinzel:700,900|figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css'])

    <style>
        @page {
            size: letter portrait;
            margin: 1cm 1.2cm;
        }

        .firmas-conjunto {
            break-inside: avoid;
            page-break-inside: avoid;
        }

        @media print {
            html, body {
                background: white !important;
                color: black !important;
                font-size: 10pt;
                margin: 0 !important;
                padding: 0 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .no-print {
                display: none !important;
            }
            .planilla-card {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0.2cm 0.3cm !important;
            }
            .firmas-conjunto {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }
            .page-break {
                page-break-after: always;
                break-after: page;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 min-h-screen py-8 print:py-0 print:bg-white">

    @php
        $faseLabel = $sabana['fase']->fase instanceof \App\Support\Enums\FaseEnum
            ? $sabana['fase']->fase->label()
            : $sabana['fase']->fase;

        $planillas = !empty($sabana['planillas_jurados'])
            ? $sabana['planillas_jurados']
            : [
                [
                    'numero' => 1,
                    'jurado_id' => 0,
                    'jurado_nombre' => 'Jurado Calificador',
                    'ci' => null,
                    'es_secreto' => false,
                    'evaluados_count' => 0,
                    'total_participantes' => count($sabana['filas']),
                    'participantes' => collect($sabana['filas'])->values()->map(fn ($f, $i) => [
                        'nro' => $i + 1,
                        'id_presentacion' => $f['presentacion']->id,
                        'nombre' => $f['nombre'],
                        'integrantes' => $f['integrantes'] ?? [],
                        'departamento' => $f['departamento'],
                        'parametros_valores' => [],
                        'nota_total' => null,
                    ])->all(),
                ],
            ];
    @endphp

    <!-- Barra de acciones flotante (No se imprime) -->
    <div class="no-print max-w-5xl mx-auto mb-6 px-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reportes.index', ['fase_id' => $sabana['fase']->id, 'categoria_id' => $sabana['categoria']->id, 'departamento_id' => $sabana['departamento']?->id]) }}"
               class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs inline-flex items-center gap-2 shadow">
                ← Volver al Centro de Reportes
            </a>
            <span class="text-xs font-bold text-slate-600 hidden md:inline">
                Sábana de Calificación por Parámetros — {{ count($planillas) }} {{ count($planillas) === 1 ? 'Planilla de Jurado' : 'Planillas de Jurados' }}
            </span>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            @if (count($planillas) > 1)
                <div class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-slate-300 shadow-sm">
                    <label for="filtro-jurado" class="text-[11px] font-bold text-slate-600 uppercase">Ver / Imprimir:</label>
                    <select id="filtro-jurado" onchange="filtrarPlanillaJurado(this.value)" class="text-xs font-bold text-slate-900 border-none focus:ring-0 py-0.5 pl-1 pr-6 bg-transparent">
                        <option value="todos">Todos los Jurados ({{ count($planillas) }})</option>
                        @foreach ($planillas as $pl)
                            <option value="jurado-{{ $pl['numero'] }}">Jurado {{ $pl['numero'] }}: {{ $pl['jurado_nombre'] }}</option>
                        @endforeach
                    </select>
                </div>
            @endif

            <a href="{{ route('admin.reportes.exportar.sabana', ['fase_id' => $sabana['fase']->id, 'categoria_id' => $sabana['categoria']->id, 'departamento_id' => $sabana['departamento']?->id]) }}"
               class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow">
                <span>📥 Descargar CSV</span>
            </a>

            <button onclick="window.print()"
                    class="px-5 py-2 rounded-xl bg-[#8b1528] hover:bg-[#5c0618] text-white font-black text-xs inline-flex items-center gap-2 shadow-lg transition transform hover:-translate-y-0.5">
                <span>🖨️ Imprimir / Guardar como PDF</span>
            </button>
        </div>
    </div>

    <!-- Contenedor de Planillas por Jurado -->
    <div class="space-y-10 print:space-y-0">
        @foreach ($planillas as $index => $planilla)
            <div data-planilla="jurado-{{ $planilla['numero'] }}"
                 @class([
                     'planilla-item max-w-5xl mx-auto bg-white p-10 sm:p-12 shadow-2xl rounded-3xl border border-slate-200 planilla-card print:border-none print:rounded-none print:shadow-none',
                     'page-break' => ! $loop->last,
                 ])>

                <!-- Encabezado Oficial -->
                <div class="border-b-2 border-slate-900 pb-4 print:pb-3 text-center space-y-2 print:space-y-1.5">
                    <!-- Franja Tricolor -->
                    <div class="h-1.5 w-full flex mb-3 print:mb-2">
                        <div class="w-1/3 bg-[#dc2626]"></div>
                        <div class="w-1/3 bg-[#facc15]"></div>
                        <div class="w-1/3 bg-[#16a34a]"></div>
                    </div>

                    <div class="flex items-center justify-between gap-4">
                        <img src="/images/logo-oficial.png" alt="Logo ACB" class="h-16 w-16 print:h-14 print:w-14 object-contain">
                        <div>
                            <h3 class="font-bold text-xs uppercase tracking-widest text-slate-600">Gobierno Autónomo Departamental de Oruro — GADOR</h3>
                            <h1 class="font-black text-lg sm:text-xl print:text-lg text-[#5c0618] tracking-tight uppercase" style="font-family: 'Cinzel', serif;">
                                XXXIII Festival Nacional de la Canción Boliviana
                            </h1>
                            <h2 class="font-bold text-base print:text-sm text-slate-800">
                                «AQUÍ… CANTA BOLIVIA» — EDICIÓN 2026
                            </h2>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="inline-block px-3 py-1.5 bg-slate-100 border border-slate-300 rounded text-[10px] font-mono font-bold text-center leading-tight">
                                <span class="block">PLANILLA OFICIAL</span>
                                <span class="block">JURADO N.° {{ str_pad((string)$planilla['numero'], 2, '0', STR_PAD_LEFT) }}-{{ now()->timezone('America/La_Paz')->format('Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-100 p-2 print:py-1.5 rounded-lg mt-3 print:mt-2 border border-slate-300">
                        <h2 class="font-black text-sm print:text-xs uppercase tracking-wide text-slate-900">
                            PLANILLA INDIVIDUAL DE CALIFICACIÓN POR PARÁMETROS
                        </h2>
                    </div>
                </div>

                <!-- Metadatos de la Planilla del Jurado -->
                <div class="grid grid-cols-2 gap-4 py-3.5 print:py-2.5 text-xs border-b border-slate-200">
                    <div>
                        <p>
                            <span class="font-bold text-slate-700">Jurado Calificador:</span>
                            <span class="font-black text-slate-900 uppercase">{{ $planilla['jurado_nombre'] }}</span>
                            @if ($planilla['es_secreto'])
                                <span class="ml-1 px-1.5 py-0.5 rounded bg-amber-100 text-amber-900 border border-amber-300 text-[10px] font-bold">Jurado Secreto</span>
                            @endif
                        </p>
                        <p class="mt-1">
                            <span class="font-bold text-slate-700">Categoría Oficial:</span>
                            <span class="font-bold text-[#5c0618] uppercase">{{ $sabana['categoria']->categoria }}</span>
                        </p>
                        <p class="mt-1">
                            <span class="font-bold text-slate-700">Fase / Ámbito:</span>
                            <span class="text-slate-900 uppercase font-semibold">{{ $faseLabel }}</span>
                            <span class="text-slate-700">— {{ $sabana['departamento']?->nombre ?? 'Nacional (Todos los Departamentos)' }}</span>
                        </p>
                    </div>
                    <div class="text-right">
                        <p><span class="font-bold text-slate-700">Lugar:</span> Teatro Internacional Oruro, Oruro - Bolivia</p>
                        <p class="mt-1">
                            <span class="font-bold text-slate-700">Fecha y Hora de Emisión:</span>
                            <span class="fecha-hora-emision">{{ now()->timezone('America/La_Paz')->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm') }}</span>
                        </p>
                        <p class="mt-1">
                            <span class="font-bold text-slate-700">Participantes Evaluados:</span>
                            {{ $planilla['evaluados_count'] }} de {{ $planilla['total_participantes'] }}
                        </p>
                    </div>
                </div>

                <!-- Resumen de Parámetros de la Categoría -->
                <div class="py-2.5 print:py-2 text-[11px] text-slate-700 flex flex-wrap items-center justify-between gap-2 border-b border-slate-200 bg-slate-50/70 px-3 rounded-b">
                    <span class="font-bold uppercase text-slate-800">Parámetros Evaluados (Total 100 pts):</span>
                    <div class="flex flex-wrap items-center gap-3">
                        @foreach ($sabana['parametros'] as $param)
                            <span class="inline-flex items-center gap-1">
                                <strong class="text-slate-900">{{ $param->orden }}. {{ $param->parametro?->parametro }}:</strong>
                                <span class="font-mono font-bold text-[#5c0618]">{{ number_format($param->valor_maximo, 0) }} pts</span>
                            </span>
                        @endforeach
                    </div>
                </div>

                <!-- Tabla de Calificaciones del Jurado por Participante y Parámetro -->
                <div class="my-4 print:my-2.5 overflow-hidden border border-slate-800 rounded">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-800 text-white uppercase text-[10px] font-bold">
                                <th class="py-2.5 print:py-1.5 px-2.5 border-r border-slate-700 text-center w-10">N.°</th>
                                <th class="py-2.5 print:py-1.5 px-3 border-r border-slate-700">Participante / Agrupación</th>
                                <th class="py-2.5 print:py-1.5 px-2.5 border-r border-slate-700">Departamento</th>
                                @foreach ($sabana['parametros'] as $p)
                                    <th class="py-2 print:py-1.5 px-2 border-r border-slate-700 text-center">
                                        <span class="block leading-tight">{{ $p->parametro?->parametro }}</span>
                                        <span class="text-[9px] text-amber-300 font-mono block mt-0.5">(Máx {{ number_format($p->valor_maximo, 0) }} pts)</span>
                                    </th>
                                @endforeach
                                <th class="py-2.5 print:py-1.5 px-3 text-center bg-slate-900 font-mono w-24">
                                    <span class="block leading-tight">Nota Jurado</span>
                                    <span class="text-[9px] text-amber-300 font-normal block">(100 pts)</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-300">
                            @forelse ($planilla['participantes'] as $part)
                                <tr class="odd:bg-white even:bg-slate-50/50">
                                    <!-- Nro -->
                                    <td class="py-2 print:py-1.5 px-2.5 border-r border-slate-300 text-center font-bold text-slate-800">
                                        {{ $part['nro'] }}
                                    </td>

                                    <!-- Participante -->
                                    <td class="py-2 print:py-1.5 px-3 border-r border-slate-300">
                                        <span class="font-bold text-slate-900 block">{{ $part['nombre'] }}</span>
                                        @if (!empty($part['integrantes']) && count($part['integrantes']) > 1)
                                            <span class="text-[9px] text-slate-600 block">({{ implode(', ', $part['integrantes']) }})</span>
                                        @endif
                                    </td>

                                    <!-- Departamento -->
                                    <td class="py-2 print:py-1.5 px-2.5 border-r border-slate-300 text-slate-800">
                                        {{ $part['departamento'] }}
                                    </td>

                                    <!-- Valores por Parámetro -->
                                    @foreach ($sabana['parametros'] as $param)
                                        @php
                                            $val = $part['parametros_valores'][$param->id] ?? null;
                                        @endphp
                                        <td class="py-2 print:py-1.5 px-2 border-r border-slate-300 text-center font-mono tabular-nums text-slate-900 text-[11px]">
                                            {{ $val !== null ? number_format($val, 2) : '—' }}
                                        </td>
                                    @endforeach

                                    <!-- Total otorgado por este Jurado -->
                                    <td class="py-2 print:py-1.5 px-3 text-center font-mono font-black text-[#5c0618] text-sm tabular-nums bg-amber-100/30">
                                        {{ $part['nota_total'] !== null ? number_format($part['nota_total'], 2) : 'S/C' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ 4 + count($sabana['parametros']) }}" class="py-6 text-center text-slate-400">
                                        No hay participaciones registradas en esta categoría.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Constancia y Bloque de Firmas de la Planilla -->
                <div class="py-2 text-xs text-justify leading-relaxed text-slate-700">
                    Por la presente planilla individual, el Jurado Calificador abajo firmante certifica que las calificaciones consignadas en cada parámetro corresponden fielmente a la evaluación realizada durante el certamen en la categoría <strong>{{ $sabana['categoria']->categoria }}</strong>.
                </div>

                <div class="firmas-conjunto mt-6 print:mt-4 pt-8 print:pt-6 border-t border-slate-300 text-center text-xs">
                    <div class="grid grid-cols-2 gap-12 max-w-2xl mx-auto items-end">
                        <!-- Firma del Jurado Calificador -->
                        <div class="space-y-0.5">
                            <div class="border-b border-slate-800 w-4/5 mx-auto mb-2 h-20 print:h-16"></div>
                            <p class="font-bold text-slate-900 uppercase">{{ $planilla['jurado_nombre'] }}</p>
                            <p class="text-[10px] text-slate-500">
                                Jurado Calificador{{ $planilla['ci'] ? ' — CI: '.$planilla['ci'] : '' }}
                            </p>
                        </div>

                        <!-- Firma y Sello de Notaria de Gobierno -->
                        <div class="space-y-0.5">
                            <div class="border-b border-slate-800 w-4/5 mx-auto mb-2 h-20 print:h-16"></div>
                            <p class="font-bold text-slate-900 uppercase">Notaria de Gobierno</p>
                            <p class="text-[10px] text-slate-500">Sello y Firma Notarial</p>
                        </div>
                    </div>

                    <!-- Pie de Página de Seguridad -->
                    <div class="mt-10 print:mt-6 pt-4 print:pt-3 border-t border-slate-200 text-[9px] text-slate-400 flex items-center justify-between font-mono">
                        <span>Sistema Informático ACB — Planilla Oficial de Jurado ({{ $planilla['numero'] }}/{{ count($planillas) }})</span>
                        <span>Código de Verificación: {{ strtoupper(substr(md5($sabana['categoria']->id.'-'.$planilla['jurado_id'].'-'.now()), 0, 16)) }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <script>
        function filtrarPlanillaJurado(valor) {
            const items = document.querySelectorAll('.planilla-item');
            const visibles = [];

            items.forEach((el) => {
                if (valor === 'todos' || el.getAttribute('data-planilla') === valor) {
                    el.style.display = '';
                    visibles.push(el);
                } else {
                    el.style.display = 'none';
                }
            });

            visibles.forEach((el, idx) => {
                if (idx < visibles.length - 1) {
                    el.classList.add('page-break');
                } else {
                    el.classList.remove('page-break');
                }
            });
        }

        (function () {
            const meses = [
                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
            ];

            const sincronizarHoraEquipo = () => {
                const elementos = document.querySelectorAll('.fecha-hora-emision');
                if (!elementos.length) return;

                const ahora = new Date();
                const dia = ahora.getDate();
                const mes = meses[ahora.getMonth()];
                const anio = ahora.getFullYear();
                const horas = String(ahora.getHours()).padStart(2, '0');
                const minutos = String(ahora.getMinutes()).padStart(2, '0');
                const texto = `${dia} de ${mes} de ${anio}, ${horas}:${minutos}`;

                elementos.forEach((el) => {
                    el.textContent = texto;
                });
            };

            sincronizarHoraEquipo();
            window.addEventListener('beforeprint', sincronizarHoraEquipo);
            setInterval(sincronizarHoraEquipo, 15000);
        })();
    </script>
</body>
</html>
