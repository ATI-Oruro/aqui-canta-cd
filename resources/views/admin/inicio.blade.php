@php
    $porcentajeAvance = ($totalPresentaciones ?? 0) > 0
        ? round((($presentacionesFinalizadas ?? 0) / $totalPresentaciones) * 100)
        : 0;
@endphp

<x-layouts.admin>
    <x-slot:header>
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Panel de Administración</h2>
                <p class="text-xs text-slate-500 mt-0.5">Panel administrativo del XXXIII Festival Nacional «Aquí… Canta Bolivia»</p>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.presentaciones.escenario') }}" target="_blank"
                   class="inline-flex items-center px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Monitor de Escenario
                </a>
                <a href="{{ route('admin.presentaciones.index') }}"
                   class="inline-flex items-center px-3.5 py-2 bg-[#5c0618] hover:bg-[#7a0920] text-white text-xs font-semibold rounded-lg shadow-sm transition">
                    Gestionar Presentaciones
                </a>
            </div>
        </div>
    </x-slot:header>

    <x-slot:sidebar>
        <x-menus.admin />
    </x-slot:sidebar>

    <div class="space-y-6">
        <!-- 2. TARJETAS KPI OPERATIVAS -->
        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <!-- KPI 1: Avance de Presentaciones -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Presentaciones</span>
                        <span class="text-xs font-bold text-[#5c0618] bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">
                            {{ $porcentajeAvance }}% completado
                        </span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900 tabular-nums">{{ $presentacionesFinalizadas ?? 0 }}</span>
                        <span class="text-sm text-slate-400 font-medium">/ {{ $totalPresentaciones ?? 0 }} finalizadas</span>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-[#5c0618] rounded-full transition-all" style="width: {{ $porcentajeAvance }}%"></div>
                    </div>
                    <div class="mt-2 flex items-center justify-between text-xs text-slate-500">
                        <span>Pendientes: <strong class="text-slate-700">{{ $presentacionesPorParticipar ?? 0 }}</strong></span>
                        <span>En curso: <strong class="text-amber-700">{{ $presentacionesEnCurso ?? 0 }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- KPI 2: Participantes e Inscripciones -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Inscripciones</span>
                        <a href="{{ route('admin.inscripciones.index') }}" class="text-xs font-semibold text-[#5c0618] hover:text-[#8b1528]">
                            Ver listado →
                        </a>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900 tabular-nums">{{ $totalInscripciones ?? 0 }}</span>
                        <span class="text-sm text-slate-400 font-medium">registros</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Participantes individuales:</span>
                    <strong class="text-slate-800 font-bold tabular-nums">{{ $totalParticipantes ?? 0 }}</strong>
                </div>
            </div>

            <!-- KPI 3: Cuerpo de Jurados -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Mesa de Jurados</span>
                        <a href="{{ route('admin.jurados.index') }}" class="text-xs font-semibold text-[#5c0618] hover:text-[#8b1528]">
                            Asignaciones →
                        </a>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900 tabular-nums">{{ $totalJurados ?? 0 }}</span>
                        <span class="text-sm text-slate-400 font-medium">jurados habilitados</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Controladores de tiempo:</span>
                    <strong class="text-slate-800 font-bold tabular-nums">{{ $juradosControladores ?? 0 }}</strong>
                </div>
            </div>

            <!-- KPI 4: Calificaciones & Auditoría -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Evaluaciones</span>
                        <a href="{{ route('admin.auditoria.index') }}" class="text-xs font-semibold text-[#5c0618] hover:text-[#8b1528]">
                            Auditoría →
                        </a>
                    </div>
                    <div class="mt-2 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-slate-900 tabular-nums">{{ $totalCalificacionesEmitidas ?? 0 }}</span>
                        <span class="text-sm text-slate-400 font-medium">votos emitidos</span>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                    <span>Correcciones auditadas:</span>
                    <strong class="text-slate-800 font-bold tabular-nums">{{ $totalCorrecciones ?? 0 }}</strong>
                </div>
            </div>
        </div>

        <!-- 3. ACCESOS RÁPIDOS DE OPERACIÓN -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Acciones rápidas de administración</h3>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-6">
                <a href="{{ route('admin.usuarios.create') }}"
                   class="px-3.5 py-2.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-[#5c0618] border border-rose-200 text-xs font-semibold text-center transition">
                    + Nuevo Usuario / Jurado
                </a>
                <a href="{{ route('admin.inscripciones.create') }}"
                   class="px-3.5 py-2.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-[#5c0618] border border-rose-200 text-xs font-semibold text-center transition">
                    + Registrar Inscripción
                </a>
                <a href="{{ route('admin.importaciones.index') }}"
                   class="px-3.5 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold text-center transition">
                    Importar XLSX / CSV / JSON
                </a>
                <a href="{{ route('admin.jurados.index') }}"
                   class="px-3.5 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold text-center transition">
                    Asignar Jurados
                </a>
                <a href="{{ route('admin.jurados.secreto') }}"
                   class="px-3.5 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold text-center transition">
                    Jurado Secreto y Podio
                </a>
                <a href="{{ route('admin.reportes.index') }}"
                   class="px-3.5 py-2.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-semibold text-center transition">
                    Reportes y Actas
                </a>
            </div>
        </div>

        <!-- 4. ESTADO DE CATEGORÍAS (ETAPA FINAL) Y ACTIVIDAD RECIENTE -->
        <div class="grid gap-6 lg:grid-cols-12">
            <!-- Tabla de Estado de las 13 Categorías -->
            <div class="lg:col-span-8 bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm">Estado de Categorías — Etapa Final ({{ $totalCategorias ?? 13 }})</h3>
                        <p class="text-xs text-slate-500 mt-0.5">
                            Finalizadas: <strong>{{ $categoriasFinalizadas ?? 0 }}</strong> ·
                            Con podio revelado: <strong>{{ $categoriasReveladas ?? 0 }}</strong>
                        </p>
                    </div>
                    <a href="{{ route('admin.jurados.secreto') }}" class="text-xs font-semibold text-[#5c0618] hover:text-[#8b1528]">
                        Control de cierre y podio →
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left">
                        <thead class="bg-slate-50 text-slate-500 uppercase tracking-wider border-b border-slate-200">
                            <tr>
                                <th class="py-2.5 px-4">Categoría</th>
                                <th class="py-2.5 px-3 text-center">Avance</th>
                                <th class="py-2.5 px-3">Jurado Secreto</th>
                                <th class="py-2.5 px-3 text-center">Líder Actual</th>
                                <th class="py-2.5 px-4 text-right">Estado</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($resumenCategorias as $item)
                                @php
                                    $secreto = $item['jurado_secreto'];
                                    $lider = $item['top3'][0] ?? null;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition">
                                    <td class="py-2.5 px-4 font-semibold text-slate-800">
                                        {{ $item['nombre'] }}
                                    </td>
                                    <td class="py-2.5 px-3 text-center font-mono tabular-nums">
                                        <span @class([
                                            'font-bold',
                                            'text-emerald-600' => $item['todas_evaluadas'] && $item['total_presentaciones'] > 0,
                                            'text-slate-600' => ! $item['todas_evaluadas'] || $item['total_presentaciones'] === 0,
                                        ])>
                                            {{ $item['presentaciones_finalizadas'] }}/{{ $item['total_presentaciones'] }}
                                        </span>
                                    </td>
                                    <td class="py-2.5 px-3">
                                        @if ($secreto)
                                            <span class="text-slate-700 font-medium">{{ $secreto->nombre }} {{ $secreto->paterno }}</span>
                                        @else
                                            <span class="text-amber-700 font-semibold">Sin designar</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-3 text-center">
                                        @if ($lider && $lider['nota_final'] !== null)
                                            <span class="font-mono font-bold text-[#5c0618]">{{ number_format($lider['nota_final'], 2) }}</span>
                                        @else
                                            <span class="text-slate-300">—</span>
                                        @endif
                                    </td>
                                    <td class="py-2.5 px-4 text-right whitespace-nowrap">
                                        @if ($item['finalizada'])
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                Finalizada
                                            </span>
                                        @elseif ($item['jurado_secreto_revelado'])
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-[#5c0618]">
                                                Podio Activo
                                            </span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-600">
                                                En curso
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400">Sin categorías configuradas.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Columna Derecha: Últimas Evaluaciones y Auditoría -->
            <div class="lg:col-span-4 space-y-6">
                <!-- Últimas presentaciones calificadas -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-slate-800 text-sm">Últimas Notas Oficiales</h3>
                        <a href="{{ route('admin.presentaciones.index') }}" class="text-xs font-semibold text-[#5c0618] hover:text-[#8b1528]">
                            Ver todas
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse ($ultimasEvaluadas as $pres)
                            <div class="py-2.5 flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <a href="{{ route('admin.calificaciones.presentacion', $pres) }}"
                                       class="text-xs font-bold text-slate-800 hover:text-[#5c0618] truncate block">
                                        {{ $pres->nombreMostrado() }}
                                    </a>
                                    <span class="text-[11px] text-slate-400 truncate block">
                                        {{ $pres->inscripcion?->categoria?->categoria ?? '—' }}
                                        @if ($pres->inscripcion?->departamento)
                                            · {{ $pres->inscripcion->departamento->nombre }}
                                        @endif
                                    </span>
                                </div>
                                <span class="font-mono font-black text-sm text-[#5c0618] bg-rose-50 px-2.5 py-1 rounded-lg border border-rose-200 shrink-0 tabular-nums">
                                    {{ number_format($pres->nota_final, 2) }}
                                </span>
                            </div>
                        @empty
                            <p class="py-4 text-xs text-slate-400 text-center">
                                Aún no se registran presentaciones con nota final calculada.
                            </p>
                        @endforelse
                    </div>
                </div>

                <!-- Últimas correcciones en Auditoría -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-5">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="font-bold text-slate-800 text-sm">Correcciones Recientes</h3>
                        <a href="{{ route('admin.auditoria.index') }}" class="text-xs font-semibold text-[#5c0618] hover:text-[#8b1528]">
                            Historial
                        </a>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @forelse ($ultimasCorrecciones as $corr)
                            @php
                                $presCorr = $corr->calificacion?->juradoPresentacion?->presentacion;
                                $nAnt = (float) ($corr->datos_anteriores['nota_total'] ?? 0);
                                $nNue = (float) ($corr->datos_nuevos['nota_total'] ?? 0);
                            @endphp
                            <div class="py-2.5 flex items-center justify-between gap-2 text-xs">
                                <div class="min-w-0">
                                    <a href="{{ route('admin.auditoria.show', $corr) }}"
                                       class="font-bold text-slate-800 hover:text-[#5c0618] truncate block">
                                        {{ $presCorr ? $presCorr->nombreMostrado() : "Calificación #{$corr->id_calificacion}" }}
                                    </a>
                                    <span class="text-[11px] text-slate-400 block truncate">
                                        Por {{ $corr->usuario?->nombreCompleto() ?? 'Usuario' }} · {{ $corr->fecha?->format('d/m H:i') }}
                                    </span>
                                </div>
                                <span class="font-mono text-xs font-bold text-slate-700 shrink-0 tabular-nums">
                                    {{ number_format($nAnt, 1) }} → <strong class="text-[#5c0618]">{{ number_format($nNue, 1) }}</strong>
                                </span>
                            </div>
                        @empty
                            <p class="py-4 text-xs text-slate-400 text-center">
                                Sin correcciones de calificación registradas.
                            </p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
