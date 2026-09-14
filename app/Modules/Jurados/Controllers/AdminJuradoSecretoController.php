<?php

namespace App\Modules\Jurados\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Categorias\Models\Categoria;
use App\Modules\Jurados\Services\GestionarJuradoSecretoService;
use App\Modules\Rankings\Services\CalcularRankingService;
use App\Modules\Usuarios\Models\Usuario;
use App\Support\Enums\RolEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;
use UnexpectedValueException;

class AdminJuradoSecretoController extends Controller
{
    public function __construct(
        private readonly GestionarJuradoSecretoService $juradoSecretoService,
        private readonly CalcularRankingService $rankingService
    ) {}

    public function index(): View
    {
        Gate::authorize('asignarJurados');

        $resumenCategorias = $this->rankingService->resumenCategoriasEtapaFinal();
        $jurados = Usuario::where('rol', RolEnum::JURADO)->orderBy('nombre')->get();

        return view('admin.jurados.secreto', [
            'resumenCategorias' => $resumenCategorias,
            'jurados' => $jurados,
        ]);
    }

    public function designar(Request $request): RedirectResponse
    {
        Gate::authorize('asignarJurados');

        $validados = $request->validate([
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'usuario_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $categoria = Categoria::findOrFail((int) $validados['categoria_id']);

        try {
            $jurado = $this->juradoSecretoService->designarJuradoSecretoCategoria($categoria, (int) $validados['usuario_id']);
        } catch (UnexpectedValueException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', sprintf('Jurado secreto para "%s" asignado a %s %s.', $categoria->categoria, $jurado->nombre, $jurado->paterno));
    }

    public function revelar(Categoria $categoria): RedirectResponse
    {
        Gate::authorize('asignarJurados');

        $this->juradoSecretoService->revelarJuradoSecreto($categoria);

        return back()->with('status', sprintf('¡Jurado secreto de la categoría "%s" revelado con éxito!', $categoria->categoria));
    }

    public function ocultar(Categoria $categoria): RedirectResponse
    {
        Gate::authorize('asignarJurados');

        try {
            $this->juradoSecretoService->ocultarJuradoSecreto($categoria);
        } catch (UnexpectedValueException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', sprintf('Jurado secreto de la categoría "%s" ocultado nuevamente.', $categoria->categoria));
    }

    public function finalizar(Categoria $categoria): RedirectResponse
    {
        Gate::authorize('asignarJurados');

        $this->juradoSecretoService->finalizarCategoria($categoria);

        return back()->with('status', sprintf('Categoría "%s" FINALIZADA oficialmente. Jurado secreto revelado.', $categoria->categoria));
    }

    public function reabrir(Categoria $categoria): RedirectResponse
    {
        Gate::authorize('asignarJurados');

        $this->juradoSecretoService->reabrirCategoria($categoria);

        return back()->with('status', sprintf('Categoría "%s" reabierta.', $categoria->categoria));
    }
}
