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
        private readonly ?CalcularRankingService $rankingService = null
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
        $categoriaId = $request->integer('categoria', $categorias->first()?->id ?? 0);
        $categoriaSeleccionada = $categorias->firstWhere('id', $categoriaId) ?? $categorias->first();

        $ranking = collect();
        if ($categoriaSeleccionada && $this->rankingService) {
            $ranking = $this->rankingService->rankingPorCategoria($categoriaSeleccionada, null, true);
        }

        return view('publico.inicio', [
            'categorias' => $categorias,
            'categoriaSeleccionada' => $categoriaSeleccionada,
            'ranking' => $ranking,
        ]);
    }
}