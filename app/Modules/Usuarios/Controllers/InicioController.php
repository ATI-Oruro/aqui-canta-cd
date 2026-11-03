<?php

namespace App\Modules\Usuarios\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Categorias\Models\Categoria;
use App\Modules\Rankings\Services\CalcularRankingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class InicioController extends Controller
{
    public function __construct(
        private readonly CalcularRankingService $rankingService
    ) {}

    public function admin(): View
    {
        $totalCategorias = Schema::hasTable('categorias') ? Categoria::count() : 0;
        $categoriasFinalizadas = Schema::hasTable('categorias') ? Categoria::where('finalizada', true)->count() : 0;
        $categoriasReveladas = Schema::hasTable('categorias') ? Categoria::where('jurado_secreto_revelado', true)->count() : 0;
        $categoriaActiva = Schema::hasTable('categorias') ? Categoria::categoriaActiva() : null;

        $totalUsuarios = Schema::hasTable('users') ? \App\Modules\Usuarios\Models\Usuario::count() : 0;
        $totalJurados = Schema::hasTable('users')
            ? \App\Modules\Usuarios\Models\Usuario::where('rol', \App\Support\Enums\RolEnum::JURADO->value)->count()
            : 0;
        $juradosControladores = Schema::hasTable('users')
            ? \App\Modules\Usuarios\Models\Usuario::where('rol', \App\Support\Enums\RolEnum::JURADO->value)
                ->where('puede_controlar_tiempo', true)
                ->count()
            : 0;

        $totalParticipantes = Schema::hasTable('participantes')
            ? \App\Modules\Participantes\Models\Participante::count()
            : 0;
        $totalInscripciones = Schema::hasTable('inscripciones')
            ? \App\Modules\Inscripciones\Models\Inscripcion::count()
            : 0;

        $totalPresentaciones = 0;
        $presentacionesPorParticipar = 0;
        $presentacionesEnCurso = 0;
        $presentacionesFinalizadas = 0;
        $presentacionEnVivo = null;
        $ultimasEvaluadas = collect();

        if (Schema::hasTable('presentaciones')) {
            $totalPresentaciones = \App\Modules\Presentaciones\Models\Presentacion::count();
            $presentacionesPorParticipar = \App\Modules\Presentaciones\Models\Presentacion::where(
                'estado',
                \App\Support\Enums\PresentacionEstadoEnum::POR_PARTICIPAR->value
            )->count();
            $presentacionesEnCurso = \App\Modules\Presentaciones\Models\Presentacion::whereIn('estado', [
                \App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO->value,
                \App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO->value,
            ])->count();
            $presentacionesFinalizadas = \App\Modules\Presentaciones\Models\Presentacion::where(
                'estado',
                \App\Support\Enums\PresentacionEstadoEnum::FINALIZADO->value
            )->count();

            $presentacionEnVivo = \App\Modules\Presentaciones\Models\Presentacion::query()
                ->with(['inscripcion.categoria', 'inscripcion.departamento', 'inscripcion.fase', 'jurados.calificacion'])
                ->whereIn('estado', [
                    \App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO->value,
                    \App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO->value,
                ])
                ->first();

            $ultimasEvaluadas = \App\Modules\Presentaciones\Models\Presentacion::query()
                ->with(['inscripcion.categoria', 'inscripcion.departamento'])
                ->whereNotNull('nota_final')
                ->orderByDesc('updated_at')
                ->limit(5)
                ->get();
        }

        $totalCalificacionesEmitidas = Schema::hasTable('calificaciones')
            ? \App\Modules\Calificaciones\Models\Calificacion::whereIn('estado', [
                \App\Support\Enums\CalificacionEstadoEnum::EMITIDO->value,
                \App\Support\Enums\CalificacionEstadoEnum::CORREGIDO->value,
            ])->count()
            : 0;

        $totalCorrecciones = Schema::hasTable('calificacion_historial')
            ? \App\Modules\Calificaciones\Models\CalificacionHistorial::count()
            : 0;

        $ultimasCorrecciones = Schema::hasTable('calificacion_historial')
            ? \App\Modules\Calificaciones\Models\CalificacionHistorial::query()
                ->with(['usuario', 'calificacion.juradoPresentacion.presentacion.inscripcion.categoria'])
                ->orderByDesc('fecha')
                ->limit(4)
                ->get()
            : collect();

        $resumenCategorias = Schema::hasTable('categorias')
            ? $this->rankingService->resumenCategoriasEtapaFinal()
            : collect();

        return view('admin.inicio', [
            'totalCategorias' => $totalCategorias,
            'categoriasFinalizadas' => $categoriasFinalizadas,
            'categoriasReveladas' => $categoriasReveladas,
            'categoriaActiva' => $categoriaActiva,
            'totalUsuarios' => $totalUsuarios,
            'totalJurados' => $totalJurados,
            'juradosControladores' => $juradosControladores,
            'totalParticipantes' => $totalParticipantes,
            'totalInscripciones' => $totalInscripciones,
            'totalPresentaciones' => $totalPresentaciones,
            'presentacionesPorParticipar' => $presentacionesPorParticipar,
            'presentacionesEnCurso' => $presentacionesEnCurso,
            'presentacionesFinalizadas' => $presentacionesFinalizadas,
            'presentacionEnVivo' => $presentacionEnVivo,
            'ultimasEvaluadas' => $ultimasEvaluadas,
            'totalCalificacionesEmitidas' => $totalCalificacionesEmitidas,
            'totalCorrecciones' => $totalCorrecciones,
            'ultimasCorrecciones' => $ultimasCorrecciones,
            'resumenCategorias' => $resumenCategorias,
        ]);
    }

    public function jurado(Request $request): View
    {
        $usuario = $request->user();
        $esDelegadoMesa = $usuario && ($usuario->esAdmin() || $usuario->puedeControlarTiempo() || \App\Modules\Jurados\Models\JuradoPresentacion::where('id_usuario', $usuario->id)->where('es_controlador', true)->exists());

        $categorias = collect();
        $categoriaActiva = null;
        $presentacionesCategoria = collect();
        $resumenCategorias = collect();

        if (Schema::hasTable('categorias')) {
            $categorias = Categoria::orderBy('id')->get();
            $categoriaIdReq = $request->has('cat') ? $request->integer('cat') : null;
            $categoriaActiva = Categoria::categoriaActiva($categoriaIdReq);

            if ($categoriaActiva) {
                $presentacionesCategoria = \App\Modules\Presentaciones\Models\Presentacion::query()
                    ->select('presentaciones.*')
                    ->join('inscripciones', 'presentaciones.id_inscripcion', '=', 'inscripciones.id')
                    ->join('fases', 'inscripciones.id_fase', '=', 'fases.id')
                    ->where('fases.fase', \App\Support\Enums\FaseEnum::FINAL->value)
                    ->where('inscripciones.id_categoria', $categoriaActiva->id)
                    ->with([
                        'inscripcion.categoria',
                        'inscripcion.departamento',
                        'inscripcion.participantes',
                        'jurados.usuario',
                        'jurados.calificacion.detalles.parametro',
                    ])
                    ->orderBy('inscripciones.fecha_presentacion')
                    ->orderBy('inscripciones.hora_presentacion')
                    ->orderBy('presentaciones.id')
                    ->get();
            }

            $resumenCategorias = $this->rankingService->resumenCategoriasEtapaFinal();
        }

        $presentacionEnVivo = $presentacionesCategoria->first(fn ($p) => in_array($p->estado, [
            \App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO,
            \App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO,
        ], true));

        $cronometroEnVivo = $presentacionEnVivo
            ? app(\App\Modules\Presentaciones\Services\GestionarCronometroService::class)->obtenerEstado($presentacionEnVivo)
            : null;

        return view('jurado.inicio', [
            'esDelegadoMesa' => $esDelegadoMesa,
            'categorias' => $categorias,
            'categoriaActiva' => $categoriaActiva,
            'presentacionesCategoria' => $presentacionesCategoria,
            'resumenCategorias' => $resumenCategorias,
            'presentacionEnVivo' => $presentacionEnVivo,
            'cronometroEnVivo' => $cronometroEnVivo,
        ]);
    }

    public function publico(Request $request): View
    {
        $categorias = Schema::hasTable('categorias') ? Categoria::orderBy('id')->get() : collect();
        
        $categoriaId = $request->has('categoria') ? $request->integer('categoria') : null;
        $categoriaSeleccionada = Categoria::categoriaActiva($categoriaId);

        $ranking = collect();
        if ($this->rankingService && $categoriaSeleccionada) {
            $ranking = $this->rankingService->rankingPorCategoria($categoriaSeleccionada, null, true);
        }

        $esPodio = (bool) ($categoriaSeleccionada?->estaRevelada() ?? false);
        $podioPaso = $categoriaSeleccionada?->podioPasoActual();
        $lugar1Revelado = $categoriaSeleccionada?->esPodioLugarRevelado(1) ?? false;
        $lugar2Revelado = $categoriaSeleccionada?->esPodioLugarRevelado(2) ?? false;
        $lugar3Revelado = $categoriaSeleccionada?->esPodioLugarRevelado(3) ?? false;
        $mostrarGeneral = $categoriaSeleccionada?->debeMostrarListadoGeneral() ?? false;

        $primerLugar = null;
        $segundoLugar = null;
        $tercerLugar = null;
        $restoParticipantes = collect();

        if ($esPodio && $ranking->isNotEmpty()) {
            $primerLugar = $ranking->firstWhere('posicion', 1);

            if ($categoriaSeleccionada?->tieneUnSoloGanador()) {
                $segundoLugar = null;
                $tercerLugar = null;
                $idsPodio = array_filter([$primerLugar['id'] ?? null]);
            } else {
                $segundoLugar = $ranking->firstWhere('posicion', 2);
                $tercerLugar = $ranking->firstWhere('posicion', 3);
                $idsPodio = array_filter([
                    $primerLugar['id'] ?? null,
                    $segundoLugar['id'] ?? null,
                    $tercerLugar['id'] ?? null,
                ]);
            }

            $restoParticipantes = $ranking->reject(fn ($item) => in_array($item['id'], $idsPodio, true))->values();
        }

        // Presentación en escenario
        $presentacionEnEscenario = \App\Modules\Presentaciones\Models\Presentacion::query()
            ->with(['inscripcion.categoria', 'inscripcion.departamento', 'inscripcion.participantes'])
            ->whereIn('estado', [
                \App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO->value,
                \App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO->value,
            ])
            ->first();

        return view('publico.inicio', [
            'categorias' => $categorias,
            'categoriaSeleccionada' => $categoriaSeleccionada,
            'ranking' => $ranking,
            'esPodio' => $esPodio,
            'podioPaso' => $podioPaso,
            'lugar1Revelado' => $lugar1Revelado,
            'lugar2Revelado' => $lugar2Revelado,
            'lugar3Revelado' => $lugar3Revelado,
            'mostrarGeneral' => $mostrarGeneral,
            'primerLugar' => $primerLugar,
            'segundoLugar' => $segundoLugar,
            'tercerLugar' => $tercerLugar,
            'restoParticipantes' => $restoParticipantes,
            'presentacionEnEscenario' => $presentacionEnEscenario,
        ]);
    }
}