<?php

namespace App\Modules\Rankings\Services;

use App\Modules\Categorias\Models\Categoria;
use App\Modules\Fases\Models\Fase;
use App\Modules\Jurados\Models\JuradoPresentacion;
use App\Modules\Presentaciones\Models\Presentacion;
use App\Modules\Usuarios\Models\Usuario;
use App\Support\Enums\CalificacionEstadoEnum;
use App\Support\Enums\FaseEnum;
use Illuminate\Support\Collection;

class CalcularRankingService
{
    /**
     * Calcula y retorna el ranking de una categoría, ordenado por nota final descendente.
     * Enmascara la identidad y calificaciones del jurado secreto para la vista pública
     * si la categoría no ha sido revelada.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function rankingPorCategoria(Categoria $categoria, ?Fase $fase = null, bool $esPublico = true): Collection
    {
        $faseEnum = $fase?->fase ?? FaseEnum::FINAL;
        $estaRevelada = $categoria->estaRevelada();

        $presentaciones = Presentacion::query()
            ->select('presentaciones.*')
            ->join('inscripciones', 'presentaciones.id_inscripcion', '=', 'inscripciones.id')
            ->join('fases', 'inscripciones.id_fase', '=', 'fases.id')
            ->where('inscripciones.id_categoria', $categoria->id)
            ->where('fases.fase', $faseEnum->value)
            ->with([
                'inscripcion.participantes',
                'inscripcion.departamento',
                'jurados.usuario',
                'jurados.calificacion.detalles.parametro',
            ])
            ->orderByRaw('presentaciones.nota_final IS NULL, presentaciones.nota_final DESC')
            ->orderBy('presentaciones.id')
            ->get();

        $ranking = collect();
        $posicion = 1;
        $notaAnterior = null;
        $posicionReal = 1;

        foreach ($presentaciones as $index => $presentacion) {
            $notaFinal = $presentacion->nota_final !== null ? (float) $presentacion->nota_final : null;

            if ($notaFinal !== null) {
                if ($notaAnterior !== null && $notaFinal < $notaAnterior) {
                    $posicion = $posicionReal;
                }
                $notaAnterior = $notaFinal;
                $posicionReal++;
            }

            // Desglose de jurados
            $juradosData = $presentacion->jurados->map(function (JuradoPresentacion $jp) use ($esPublico, $estaRevelada) {
                $usuario = $jp->usuario;
                $calificacion = $jp->calificacion;
                $esSecreto = (bool) $jp->es_secreto;
                $ocultar = $esPublico && $esSecreto && ! $estaRevelada;

                return [
                    'jurado_id' => $ocultar ? null : $usuario?->id,
                    'jurado_nombre' => $ocultar ? 'Jurado Secreto' : ($usuario ? "{$usuario->nombre} {$usuario->paterno}" : 'Jurado'),
                    'es_secreto' => $esSecreto,
                    'es_controlador' => (bool) $jp->es_controlador,
                    'es_revelado' => ! $ocultar,
                    'calificado' => $calificacion !== null && in_array($calificacion->estado, [CalificacionEstadoEnum::EMITIDO, CalificacionEstadoEnum::CORREGIDO], true),
                    'nota_total' => $ocultar ? null : ($calificacion?->nota_total !== null ? (float) $calificacion->nota_total : null),
                    'detalles' => $ocultar ? [] : ($calificacion?->detalles->map(fn ($d) => [
                        'parametro' => $d->parametro?->parametro,
                        'valor' => (float) $d->valor,
                    ])->all() ?? []),
                ];
            });

            $ranking->push([
                'id' => $presentacion->id,
                'posicion' => $notaFinal !== null ? $posicion : '—',
                'nombre' => $presentacion->nombreMostrado(),
                'departamento' => $presentacion->inscripcion?->departamento?->nombre,
                'participantes' => $presentacion->inscripcion?->participantes->pluck('nombre_completo')->all() ?? [],
                'foto_url' => $presentacion->foto_url ?? $presentacion->inscripcion?->participantes->first()?->foto_url,
                'estado' => $presentacion->estado?->value,
                'estado_label' => $presentacion->estado?->label(),
                'nota_final' => $notaFinal,
                'jurados' => $juradosData,
            ]);
        }

        return $ranking;
    }

    /**
     * Retorna el resumen del estado de todas las categorías en la Etapa Final.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function resumenCategoriasEtapaFinal(): Collection
    {
        $categorias = Categoria::with('juradoSecreto')->orderBy('id')->get();

        return $categorias->map(function (Categoria $cat) {
            $asignacionSecreta = JuradoPresentacion::where('es_secreto', true)
                ->whereHas('presentacion.inscripcion', function ($query) use ($cat) {
                    $query->where('id_categoria', $cat->id)
                        ->whereHas('fase', fn ($f) => $f->where('fase', FaseEnum::FINAL->value));
                })
                ->with('usuario')
                ->first();

            $juradoSecreto = $cat->juradoSecreto ?? $asignacionSecreta?->usuario;

            $presentaciones = Presentacion::whereHas('inscripcion', function ($query) use ($cat) {
                $query->where('id_categoria', $cat->id)
                    ->whereHas('fase', fn ($f) => $f->where('fase', FaseEnum::FINAL->value));
            })->get();

            $total = $presentaciones->count();
            $finalizadas = $presentaciones->where('estado', \App\Support\Enums\PresentacionEstadoEnum::FINALIZADO)->count();
            $conNotaFinal = $presentaciones->whereNotNull('nota_final')->count();

            return [
                'categoria' => $cat,
                'id' => $cat->id,
                'nombre' => $cat->categoria,
                'jurado_secreto' => $juradoSecreto,
                'jurado_secreto_revelado' => (bool) $cat->jurado_secreto_revelado,
                'revelado_at' => $cat->revelado_at,
                'finalizada' => (bool) $cat->finalizada,
                'finalizada_at' => $cat->finalizada_at,
                'total_presentaciones' => $total,
                'presentaciones_finalizadas' => $finalizadas,
                'presentaciones_con_nota' => $conNotaFinal,
                'todas_evaluadas' => $total > 0 && $finalizadas === $total,
            ];
        });
    }
}
