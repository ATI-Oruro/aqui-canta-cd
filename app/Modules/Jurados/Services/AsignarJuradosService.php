<?php

namespace App\Modules\Jurados\Services;

use App\Modules\Jurados\Models\JuradoPresentacion;
use App\Modules\Presentaciones\Models\Presentacion;
use App\Modules\Usuarios\Models\Usuario;
use App\Support\Enums\JuradoPresentacionEstadoEnum;
use App\Support\Enums\RolEnum;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Gestiona la asignación de jurados a las presentaciones.
 * - Preselección: exactamente 3 jurados.
 * - Final: exactamente 5 jurados.
 */
class AsignarJuradosService
{
    public function cantidadRequerida(Presentacion $presentacion): int
    {
        return $presentacion->esPreseleccion() ? 3 : 5;
    }

    public function cantidadAsignada(Presentacion $presentacion): int
    {
        return $this->asignaciones($presentacion)->count();
    }

    public function estaCompleta(Presentacion $presentacion): bool
    {
        return $this->cantidadAsignada($presentacion) === $this->cantidadRequerida($presentacion);
    }

    /**
     * @return Collection<int, JuradoPresentacion>
     */
    public function asignaciones(Presentacion $presentacion): Collection
    {
        return JuradoPresentacion::where('id_presentacion', $presentacion->id)
            ->with('usuario')
            ->get();
    }

    /**
     * Asigna automáticamente los puestos faltantes, rotando a los jurados
     * con menos asignaciones en el festival para distribuir la carga.
     *
     * @return list<JuradoPresentacion>
     */
    public function autoAsignar(Presentacion $presentacion): array
    {
        $requerido = $this->cantidadRequerida($presentacion);
        $faltantes = $requerido - $this->cantidadAsignada($presentacion);

        if ($faltantes <= 0) {
            return [];
        }

        $disponibles = $this->juradosDisponibles($presentacion);

        if ($disponibles->count() < $faltantes) {
            throw new \UnexpectedValueException(sprintf(
                'No hay suficientes jurados disponibles: se requieren %d y solo hay %d.',
                $faltantes,
                $disponibles->count()
            ));
        }

        $elegidos = $disponibles->take($faltantes);

        return DB::transaction(function () use ($presentacion, $elegidos) {
            $creadas = [];

            foreach ($elegidos as $jurado) {
                $creadas[] = $this->crearAsignacion($presentacion, $jurado);
            }

            return $creadas;
        });
    }

    /**
     * Reemplaza por completo la lista de jurados de una presentación.
     *
     * @param  list<int>  $usuarioIds
     * @return list<JuradoPresentacion>
     */
    public function asignar(Presentacion $presentacion, array $usuarioIds): array
    {
        $requerido = $this->cantidadRequerida($presentacion);

        if (count($usuarioIds) !== $requerido) {
            throw new \UnexpectedValueException(sprintf(
                'La presentación requiere exactamente %d jurados y se indicaron %d.',
                $requerido,
                count($usuarioIds)
            ));
        }

        $jurados = $this->juradosValidos($usuarioIds);

        if ($jurados->count() !== $requerido) {
            throw new \UnexpectedValueException('Algunos de los usuarios indicados no son jurados válidos.');
        }

        return DB::transaction(function () use ($presentacion, $jurados) {
            JuradoPresentacion::where('id_presentacion', $presentacion->id)->delete();

            $creadas = [];

            foreach ($jurados as $jurado) {
                $creadas[] = $this->crearAsignacion($presentacion, $jurado);
            }

            return $creadas;
        });
    }

    public function agregarJurado(Presentacion $presentacion, int $usuarioId): JuradoPresentacion
    {
        if ($this->estaCompleta($presentacion)) {
            throw new \UnexpectedValueException(sprintf(
                'La presentación ya tiene los %d jurados requeridos.',
                $this->cantidadRequerida($presentacion)
            ));
        }

        $jurado = $this->juradoValido($usuarioId);

        return $this->crearAsignacion($presentacion, $jurado);
    }

    public function removerJurado(Presentacion $presentacion, int $usuarioId): void
    {
        $cantidad = $this->cantidadAsignada($presentacion);

        if ($cantidad <= $this->cantidadRequerida($presentacion)) {
            throw new \UnexpectedValueException(sprintf(
                'La presentación debe mantener %d jurados; use la edición completa para sustituirlos.',
                $this->cantidadRequerida($presentacion)
            ));
        }

        JuradoPresentacion::where('id_presentacion', $presentacion->id)
            ->where('id_usuario', $usuarioId)
            ->delete();
    }

    private function crearAsignacion(Presentacion $presentacion, Usuario $jurado): JuradoPresentacion
    {
        return JuradoPresentacion::firstOrCreate([
            'id_presentacion' => $presentacion->id,
            'id_usuario' => $jurado->id,
        ], [
            'es_secreto' => false,
            'es_controlador' => $jurado->puedeControlarTiempo(),
            'estado' => JuradoPresentacionEstadoEnum::ASIGNADO,
        ]);
    }

    /**
     * @return Collection<int, Usuario>
     */
    private function juradosDisponibles(Presentacion $presentacion): Collection
    {
        return Usuario::where('rol', RolEnum::JURADO)
            ->whereDoesntHave('presentacionesJurado', fn ($q) => $q->where('jurado_presentacion.id_presentacion', $presentacion->id))
            ->get()
            ->sortBy(fn (Usuario $jurado) => $jurado->presentacionesJurado()->count())
            ->values();
    }

    /**
     * @param  list<int>  $usuarioIds
     * @return Collection<int, Usuario>
     */
    private function juradosValidos(array $usuarioIds): Collection
    {
        return Usuario::where('rol', RolEnum::JURADO)
            ->whereIn('id', array_map('intval', $usuarioIds))
            ->get();
    }

    private function juradoValido(int $usuarioId): Usuario
    {
        $jurado = Usuario::where('rol', RolEnum::JURADO)->find($usuarioId);

        if ($jurado === null) {
            throw new \UnexpectedValueException('El usuario indicado no es un jurado válido.');
        }

        return $jurado;
    }
}