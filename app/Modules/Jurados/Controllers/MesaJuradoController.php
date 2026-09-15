<?php

namespace App\Modules\Jurados\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Categorias\Events\CategoriaActivaCambiada;
use App\Modules\Categorias\Models\Categoria;
use App\Modules\Jurados\Services\GestionarJuradoSecretoService;
use App\Modules\Presentaciones\Models\Presentacion;
use App\Modules\Presentaciones\Services\GestionarPresentacionService;
use App\Modules\Rankings\Events\RankingActualizado;
use App\Modules\Rankings\Services\CalcularRankingService;
use App\Support\Enums\PresentacionEstadoEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use UnexpectedValueException;

class MesaJuradoController extends Controller
{
    public function __construct(
        private readonly GestionarJuradoSecretoService $juradoSecretoService,
        private readonly CalcularRankingService $rankingService,
        private readonly GestionarPresentacionService $presentacionesService,
    ) {}

    public function seleccionarCategoria(Request $request, Categoria $categoria): RedirectResponse
    {
        Gate::authorize('controlarMesa');

        $categoria->marcarActiva();

        event(new CategoriaActivaCambiada($categoria));

        $ranking = $this->rankingService->rankingPorCategoria($categoria, null, true);
        event(new RankingActualizado($categoria, $ranking));

        return back()->with('status', sprintf('Categoría "%s" seleccionada y fijada como activa en el festival.', $categoria->categoria));
    }

    public function cerrarCalificaciones(Categoria $categoria): RedirectResponse
    {
        Gate::authorize('controlarMesa');

        $this->juradoSecretoService->finalizarCategoria($categoria);

        return back()->with('status', sprintf('Calificaciones de la categoría "%s" cerradas exitosamente. Posiciones actualizadas.', $categoria->categoria));
    }

    public function revelarGanador(Categoria $categoria): RedirectResponse
    {
        Gate::authorize('controlarMesa');

        $this->juradoSecretoService->revelarJuradoSecreto($categoria);

        return back()->with('status', sprintf('¡Ganador y jurado secreto de "%s" revelados! Podio F1 habilitado en la vista pública.', $categoria->categoria));
    }

    public function transicion(Request $request, Presentacion $presentacion): RedirectResponse
    {
        Gate::authorize('controlarMesa');

        $estado = PresentacionEstadoEnum::tryFrom($request->string('estado')->toString());

        if ($estado === null) {
            return back()->with('error', 'Estado de destino no válido.');
        }

        try {
            $presentacion = match ($estado) {
                PresentacionEstadoEnum::EN_ESCENARIO => $this->presentacionesService->alEscenario($presentacion),
                PresentacionEstadoEnum::PARTICIPANDO => $this->presentacionesService->iniciarPresentacion($presentacion),
                PresentacionEstadoEnum::FINALIZADO => $this->presentacionesService->finalizarPresentacion($presentacion),
                default => throw new UnexpectedValueException('Estado no controlable.'),
            };

            // Asegurar que la categoría de esta presentación sea la activa
            if ($presentacion->inscripcion?->categoria) {
                $presentacion->inscripcion->categoria->marcarActiva();
                event(new CategoriaActivaCambiada($presentacion->inscripcion->categoria));
            }
        } catch (UnexpectedValueException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('status', sprintf('Presentación de "%s" marcada como "%s".', $presentacion->nombreMostrado(), $presentacion->estado?->label()));
    }
}
