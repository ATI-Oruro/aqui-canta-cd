<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acta Oficial de Escrutinio y Calificaciones — ACB 2026</title>
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
            .acta-card {
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
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-900 min-h-screen py-8 print:py-0 print:bg-white">

    <!-- Barra de acciones flotante (No se imprime) -->
    <div class="no-print max-w-4xl mx-auto mb-6 px-4 flex items-center justify-between gap-4">
        <a href="{{ route('admin.reportes.index', ['fase_id' => $reporte['fase']->id, 'categoria_id' => $reporte['categoria']->id]) }}"
           class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs inline-flex items-center gap-2 shadow">
            ← Volver al Centro de Reportes
        </a>

        <div class="flex items-center gap-3">
            <a href="{{ route('admin.reportes.exportar.resultados', ['fase_id' => $reporte['fase']->id, 'categoria_id' => $reporte['categoria']->id, 'departamento_id' => $reporte['departamento']?->id]) }}"
               class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-600 text-white font-bold text-xs inline-flex items-center gap-1.5 shadow">
                <span>📥 Descargar CSV</span>
            </a>
            <button onclick="window.print()"
                    class="px-5 py-2 rounded-xl bg-[#8b1528] hover:bg-[#5c0618] text-white font-black text-xs inline-flex items-center gap-2 shadow-lg transition transform hover:-translate-y-0.5">
                <span>🖨️ Imprimir / Guardar como PDF</span>
            </button>
        </div>
    </div>

    <!-- Documento Formal del Acta Notarial -->
    <div class="max-w-4xl mx-auto bg-white p-10 sm:p-12 shadow-2xl rounded-3xl border border-slate-200 acta-card print:border-none print:rounded-none print:shadow-none">
        
        <!-- Encabezado Oficial -->
        <div class="border-b-2 border-slate-900 pb-5 print:pb-3 text-center space-y-2 print:space-y-1.5">
            <!-- Franja Tricolor -->
            <div class="h-1.5 w-full flex mb-4 print:mb-2">
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
                        <span class="block">ACTA OFICIAL</span>
                        <span class="block">N.° {{ str_pad((string)$reporte['categoria']->id, 3, '0', STR_PAD_LEFT) }}-{{ now()->timezone('America/La_Paz')->format('Y') }}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-slate-100 p-2 print:py-1.5 rounded-lg mt-4 print:mt-2 border border-slate-300">
                <h2 class="font-black text-sm print:text-xs uppercase tracking-wide text-slate-900">
                    ACTA DE ESCRUTINIO Y CALIFICACIÓN FINAL
                </h2>
            </div>
        </div>

        <!-- Metadatos del Acta -->
        <div class="grid grid-cols-2 gap-4 py-4 print:py-2.5 text-xs border-b border-slate-200">
            <div>
                <p><span class="font-bold text-slate-700">Fase del Festival:</span> <span class="uppercase font-semibold text-slate-900">{{ $reporte['fase']->fase instanceof \App\Support\Enums\FaseEnum ? $reporte['fase']->fase->label() : $reporte['fase']->fase }}</span></p>
                <p class="mt-1"><span class="font-bold text-slate-700">Categoría Oficial:</span> <span class="font-bold text-[#5c0618] uppercase">{{ $reporte['categoria']->categoria }}</span></p>
                <p class="mt-1"><span class="font-bold text-slate-700">Ámbito / Departamento:</span> <span class="text-slate-900">{{ $reporte['departamento']?->nombre ?? 'Nacional (Todos los Departamentos)' }}</span></p>
            </div>
            <div class="text-right">
                <p><span class="font-bold text-slate-700">Lugar:</span> Teatro Internacional Oruro, Oruro - Bolivia</p>
                <p class="mt-1"><span class="font-bold text-slate-700">Fecha y Hora de Emisión:</span> <span id="fecha-hora-emision">{{ now()->timezone('America/La_Paz')->locale('es')->isoFormat('D [de] MMMM [de] YYYY, HH:mm') }}</span></p>
                <p class="mt-1"><span class="font-bold text-slate-700">Total Concursantes Evaluados:</span> {{ $reporte['total_participantes'] }}</p>
            </div>
        </div>

        <!-- Texto Declaratorio -->
        <div class="py-4 print:py-2.5 text-xs text-justify leading-relaxed text-slate-800">
            En la ciudad de Oruro, capital del folklore de Bolivia, reunidos los miembros del Jurado Calificador debidamente acreditados y en presencia del Notario de Fe Pública y el Comité Organizador del XXXIII Festival Nacional de la Canción Boliviana “Aquí… Canta Bolivia”, se procedió a la revisión, cómputo y consolidación de las calificaciones asignadas a los participantes en la categoría de referencia, con el siguiente resultado oficial:
        </div>

        <!-- Tabla Oficial de Puntuaciones y Puestos -->
        <div class="my-4 print:my-2 overflow-hidden border border-slate-800 rounded">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-800 text-white uppercase text-[10px] font-bold">
                        <th class="py-2.5 print:py-1.5 px-3 border-r border-slate-700 text-center w-12">Puesto</th>
                        <th class="py-2.5 print:py-1.5 px-3 border-r border-slate-700">Participante / Agrupación</th>
                        <th class="py-2.5 print:py-1.5 px-3 border-r border-slate-700">Departamento</th>
                        @foreach ($reporte['jurados_cabecera'] as $jurNom)
                            <th class="py-2.5 print:py-1.5 px-2 border-r border-slate-700 text-center font-mono">{{ $jurNom }}</th>
                        @endforeach
                        <th class="py-2.5 print:py-1.5 px-3 text-center bg-slate-900 font-mono">Nota Final</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-300">
                    @forelse ($reporte['items'] as $item)
                        <tr @class([
                            'bg-amber-50/60 font-bold' => $item['posicion'] === 1,
                            'bg-slate-50/50' => ! $reporte['categoria']->tieneUnSoloGanador() && ($item['posicion'] === 2 || $item['posicion'] === 3),
                        ])>
                            <!-- Puesto -->
                            <td class="py-2 print:py-1.5 px-3 border-r border-slate-300 text-center font-bold text-slate-900">
                                {{ $item['posicion'] !== null ? "{$item['posicion']}°" : 'S/C' }}
                            </td>

                            <!-- Participante -->
                            <td class="py-2 print:py-1.5 px-3 border-r border-slate-300">
                                <span class="font-bold text-slate-900 block">{{ $item['nombre_presentacion'] }}</span>
                                @if (!empty($item['integrantes']) && count($item['integrantes']) > 1)
                                    <span class="text-[9px] text-slate-600 block">({{ implode(', ', $item['integrantes']) }})</span>
                                @endif
                            </td>

                            <!-- Depto -->
                            <td class="py-2 print:py-1.5 px-3 border-r border-slate-300 text-slate-800">
                                {{ $item['departamento'] }}
                            </td>

                            <!-- Jurados -->
                            @foreach ($reporte['jurados_cabecera'] as $jurNom)
                                @php
                                    $v = $item['votos_jurados'][$jurNom] ?? null;
                                @endphp
                                <td class="py-2 print:py-1.5 px-2 border-r border-slate-300 text-center font-mono tabular-nums text-slate-800 text-[11px]">
                                    {{ $v && $v['nota_total'] !== null ? number_format($v['nota_total'], 2) : '-' }}
                                </td>
                            @endforeach

                            <!-- Nota Final -->
                            <td class="py-2 print:py-1.5 px-3 text-center font-mono font-black text-[#5c0618] text-sm tabular-nums bg-amber-100/30">
                                {{ $item['nota_final'] !== null ? number_format($item['nota_final'], 2) : 'S/C' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ 4 + count($reporte['jurados_cabecera']) }}" class="py-6 text-center text-slate-400">
                                No se registraron calificaciones en esta categoría.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Cláusula de Cierre -->
        <div class="py-3 print:py-2 text-xs text-justify leading-relaxed text-slate-700">
            Con lo que concluyó el acto, firmando la presente acta en constancia de conformidad y validez legal todos los miembros del Jurado Calificador, Comité Organizador y el Notario de Fe Pública que certifica la legalidad del certamen.
        </div>

        <!-- Bloque de Firmas: Jurados y Notaria de Gobierno (6 campos en conjunto) -->
        @php
            $juradosFirmas = !empty($reporte['jurados_cabecera'])
                ? array_values($reporte['jurados_cabecera'])
                : ['Jurado 1', 'Jurado 2', 'Jurado 3', 'Jurado 4', 'Jurado 5'];
            $juradosFila1 = array_slice($juradosFirmas, 0, 3);
            $juradosFila2 = array_slice($juradosFirmas, 3, 2);
        @endphp

        <div class="firmas-conjunto mt-6 print:mt-4 pt-8 print:pt-6 border-t border-slate-300 space-y-10 print:space-y-7 text-center text-xs">
            <!-- 1ra Fila: Jurado 1, Jurado 2 y Jurado 3 -->
            <div class="grid grid-cols-3 gap-8 print:gap-6">
                @foreach ($juradosFila1 as $jur)
                    <div class="space-y-0.5">
                        <div class="border-b border-slate-800 w-4/5 mx-auto mb-2 h-16 print:h-14"></div>
                        <p class="font-bold text-slate-900">{{ $jur }}</p>
                        <p class="text-[10px] text-slate-500">Jurado Calificador</p>
                    </div>
                @endforeach
            </div>

            <!-- 2da Fila: Jurado 4 y Jurado 5 -->
            @if (!empty($juradosFila2))
                <div class="grid grid-cols-2 gap-8 print:gap-6 max-w-xl mx-auto">
                    @foreach ($juradosFila2 as $jur)
                        <div class="space-y-0.5">
                            <div class="border-b border-slate-800 w-4/5 mx-auto mb-2 h-16 print:h-14"></div>
                            <p class="font-bold text-slate-900">{{ $jur }}</p>
                            <p class="text-[10px] text-slate-500">Jurado Calificador</p>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- 3ra Fila: Notaria de Gobierno -->
            <div class="max-w-xs mx-auto">
                <div class="space-y-0.5">
                    <div class="border-b border-slate-800 w-4/5 mx-auto mb-2 h-20 print:h-16"></div>
                    <p class="font-bold text-slate-900 uppercase">Notaria de Gobierno</p>
                    <p class="text-[10px] text-slate-500">Sello y Firma Notarial</p>
                </div>
            </div>

            <!-- Pie de Página de Seguridad -->
            <div class="mt-10 print:mt-6 pt-4 print:pt-3 border-t border-slate-200 text-[9px] text-slate-400 flex items-center justify-between font-mono">
                <span>Sistema Informático ACB — Generado automáticamente</span>
                <span>Código de Verificación: {{ strtoupper(substr(md5($reporte['categoria']->id.now()), 0, 16)) }}</span>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const el = document.getElementById('fecha-hora-emision');
            if (!el) return;

            const meses = [
                'enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio',
                'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre'
            ];

            const sincronizarHoraEquipo = () => {
                const ahora = new Date();
                const dia = ahora.getDate();
                const mes = meses[ahora.getMonth()];
                const anio = ahora.getFullYear();
                const horas = String(ahora.getHours()).padStart(2, '0');
                const minutos = String(ahora.getMinutes()).padStart(2, '0');

                el.textContent = `${dia} de ${mes} de ${anio}, ${horas}:${minutos}`;
            };

            sincronizarHoraEquipo();
            window.addEventListener('beforeprint', sincronizarHoraEquipo);
            setInterval(sincronizarHoraEquipo, 15000);
        })();
    </script>
</body>
</html>
