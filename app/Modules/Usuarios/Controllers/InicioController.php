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
        return view('admin.inicio');
    }

    public function jurado(Request $request): View
    {
        return view('jurado.inicio');
    }

    public function publico(Request $request): View
    {
        $categorias = Schema::hasTable('categorias') ? Categoria::orderBy('id')->get() : collect();
        
        $tabActiva = $request->string('tab', 'ranking')->toString();
        if (! in_array($tabActiva, ['ranking', 'escenario', 'participantes', 'categorias'], true)) {
            $tabActiva = 'ranking';
        }

        $categoriaId = $request->has('categoria') ? $request->integer('categoria') : null;
        $departamentoId = $request->has('departamento') ? $request->integer('departamento') : null;
        $modoVista = $request->string('vista', 'tarjetas')->toString();

        $categoriaSeleccionada = $categoriaId ? $categorias->firstWhere('id', $categoriaId) : null;

        $ranking = collect();
        $conteoDepartamentos = collect();
        $conteoCategorias = collect();
        $resumenCategorias = collect();

        if ($this->rankingService) {
            $ranking = $this->rankingService->rankingPorCategoria($categoriaSeleccionada, null, true, $departamentoId);
            $conteoDepartamentos = $this->rankingService->conteoDepartamentos($categoriaId, null);
            $conteoCategorias = $this->rankingService->conteoCategorias($departamentoId, null);
            $resumenCategorias = $this->rankingService->resumenCategoriasEtapaFinal();
        }

        // Presentación en escenario
        $presentacionEnEscenario = \App\Modules\Presentaciones\Models\Presentacion::query()
            ->with(['inscripcion.categoria', 'inscripcion.departamento', 'inscripcion.participantes'])
            ->whereIn('estado', [
                \App\Support\Enums\PresentacionEstadoEnum::EN_ESCENARIO->value,
                \App\Support\Enums\PresentacionEstadoEnum::PARTICIPANDO->value,
            ])
            ->first();

        // Total general de participantes/presentaciones en etapa final
        $totalGeneral = \App\Modules\Presentaciones\Models\Presentacion::query()
            ->join('inscripciones', 'presentaciones.id_inscripcion', '=', 'inscripciones.id')
            ->join('fases', 'inscripciones.id_fase', '=', 'fases.id')
            ->where('fases.fase', \App\Support\Enums\FaseEnum::FINAL->value)
            ->count();

        return view('publico.inicio', [
            'categorias' => $categorias,
            'categoriaSeleccionada' => $categoriaSeleccionada,
            'departamentoSeleccionadoId' => $departamentoId,
            'ranking' => $ranking,
            'conteoDepartamentos' => $conteoDepartamentos,
            'conteoCategorias' => $conteoCategorias,
            'resumenCategorias' => $resumenCategorias,
            'presentacionEnEscenario' => $presentacionEnEscenario,
            'totalGeneral' => $totalGeneral,
            'tabActiva' => $tabActiva,
            'modoVista' => $modoVista,
        ]);
    }
}