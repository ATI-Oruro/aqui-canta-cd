<?php

namespace App\Modules\Jurados\Services;

use App\Modules\Categorias\Events\CategoriaFinalizada;
use App\Modules\Categorias\Models\Categoria;
use App\Modules\Jurados\Events\JuradoSecretoRevelado;
use App\Modules\Jurados\Models\JuradoPresentacion;
use App\Modules\Presentaciones\Models\Presentacion;
use App\Modules\Rankings\Events\RankingActualizado;
use App\Modules\Rankings\Services\CalcularRankingService;
use App\Modules\Usuarios\Models\Usuario;
use App\Support\Enums\FaseEnum;
use App\Support\Enums\RolEnum;
use Illuminate\Support\Facades\DB;
use UnexpectedValueException;

class GestionarJuradoSecretoService
{
    public function __construct(
        private readonly AsignarJuradosService $asignarJuradosService,
        private readonly CalcularRankingService $calcularRankingService
    ) {}

    /**
     * Designa un jurado secreto para todas las presentaciones de una categoría en la Etapa Final.
     */
    public function designarJuradoSecretoCategoria(Categoria $categoria, int $usuarioId): Usuario
    {
        $jurado = Usuario::where('rol', RolEnum::JURADO)->find($usuarioId);

        if (! $jurado) {
            throw new UnexpectedValueException('El usuario especificado no es un jurado válido.');
        }

        DB::transaction(function () use ($categoria, $jurado) {
            $categoria->update([
                'jurado_secreto_usuario_id' => $jurado->id,
            ]);

            $presentacionesFinal = Presentacion::whereHas('inscripcion', function ($query) use ($categoria) {
                $query->where('id_categoria', $categoria->id)
                    ->whereHas('fase', fn ($f) => $f->where('fase', FaseEnum::FINAL->value));
            })->get();

            foreach ($presentacionesFinal as $presentacion) {
                // Desmarcar otros jurados secretos en la presentación
                JuradoPresentacion::where('id_presentacion', $presentacion->id)
                    ->where('id_usuario', '!=', $jurado->id)
                    ->update(['es_secreto' => false]);

                $asignacion = JuradoPresentacion::where('id_presentacion', $presentacion->id)
                    ->where('id_usuario', $jurado->id)
                    ->first();

                if ($asignacion) {
                    $asignacion->update(['es_secreto' => true]);
                } else {
                    $totalAsignados = JuradoPresentacion::where('id_presentacion', $presentacion->id)->count();
                    if ($totalAsignados < 5) {
                        JuradoPresentacion::create([
                            'id_presentacion' => $presentacion->id,
                            'id_usuario' => $jurado->id,
                            'es_secreto' => true,
                            'es_controlador' => $jurado->puedeControlarTiempo(),
                            'estado' => \App\Support\Enums\JuradoPresentacionEstadoEnum::ASIGNADO,
                        ]);
                    } else {
                        $aReemplazar = JuradoPresentacion::where('id_presentacion', $presentacion->id)
                            ->where('es_controlador', false)
                            ->first() ?? JuradoPresentacion::where('id_presentacion', $presentacion->id)->first();

                        if ($aReemplazar) {
                            $aReemplazar->delete();
                            JuradoPresentacion::create([
                                'id_presentacion' => $presentacion->id,
                                'id_usuario' => $jurado->id,
                                'es_secreto' => true,
                                'es_controlador' => $jurado->puedeControlarTiempo(),
                                'estado' => \App\Support\Enums\JuradoPresentacionEstadoEnum::ASIGNADO,
                            ]);
                        }
                    }
                }
            }
        });

        return $jurado;
    }

    /**
     * Revela el jurado secreto de una categoría en la etapa final.
     */
    public function revelarJuradoSecreto(Categoria $categoria): void
    {
        $categoria->marcarRevelada();

        $juradoSecreto = $this->asignarJuradosService->juradoSecretoDeCategoria($categoria);

        // Despachar eventos en tiempo real
        event(new JuradoSecretoRevelado($categoria, $juradoSecreto));

        $ranking = $this->calcularRankingService->rankingPorCategoria($categoria, null, true);
        event(new RankingActualizado($categoria, $ranking));
    }

    /**
     * Oculta nuevamente el jurado secreto de una categoría (si no está finalizada).
     */
    public function ocultarJuradoSecreto(Categoria $categoria): void
    {
        if ($categoria->estaFinalizada()) {
            throw new UnexpectedValueException('No se puede ocultar el jurado secreto de una categoría ya finalizada.');
        }

        $categoria->marcarOculta();

        $ranking = $this->calcularRankingService->rankingPorCategoria($categoria, null, true);
        event(new RankingActualizado($categoria, $ranking));
    }

    /**
     * Finaliza oficialmente la categoría, revelando automáticamente el jurado secreto.
     */
    public function finalizarCategoria(Categoria $categoria): void
    {
        $categoria->marcarFinalizada();

        $juradoSecreto = $this->asignarJuradosService->juradoSecretoDeCategoria($categoria);

        event(new CategoriaFinalizada($categoria));
        event(new JuradoSecretoRevelado($categoria, $juradoSecreto));

        $ranking = $this->calcularRankingService->rankingPorCategoria($categoria, null, true);
        event(new RankingActualizado($categoria, $ranking));
    }

    /**
     * Reabre una categoría finalizada.
     */
    public function reabrirCategoria(Categoria $categoria): void
    {
        $categoria->marcarReabierta();

        $ranking = $this->calcularRankingService->rankingPorCategoria($categoria, null, true);
        event(new RankingActualizado($categoria, $ranking));
    }

    public function obtenerJuradoSecreto(Categoria $categoria): ?Usuario
    {
        return $this->asignarJuradosService->juradoSecretoDeCategoria($categoria);
    }

    public function estaRevelado(Categoria $categoria): bool
    {
        return $categoria->estaRevelada();
    }

    public function estaFinalizada(Categoria $categoria): bool
    {
        return $categoria->estaFinalizada();
    }
}
