<?php

namespace App\Modules\Importaciones\Services;

use App\Modules\Categorias\Models\Categoria;
use App\Modules\Departamentos\Models\Departamento;
use App\Modules\Fases\Models\Fase;
use App\Modules\Importaciones\Exceptions\FormatoNoSoportadoException;
use App\Modules\Importaciones\Exceptions\LecturaException;
use App\Modules\Importaciones\Readers\FabricaLectores;
use App\Modules\Importaciones\ValueObjects\ResultadoImportacion;
use App\Modules\Inscripciones\Models\Inscripcion;
use App\Modules\Inscripciones\Models\InscripcionParticipante;
use App\Modules\Participantes\Models\Participante;
use App\Modules\Presentaciones\Models\Presentacion;
use App\Support\Enums\FaseEnum;
use App\Support\Enums\InscripcionEstadoEnum;
use App\Support\Enums\PresentacionEstadoEnum;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImportarInscripcionesService
{
    public function __construct(private readonly FabricaLectores $fabricaLectores) {}

    public function importar(UploadedFile|string $archivo): ResultadoImportacion
    {
        $resultado = new ResultadoImportacion;

        $ruta = $archivo instanceof UploadedFile ? $archivo->getPathname() : $archivo;
        $extension = $archivo instanceof UploadedFile
            ? $archivo->getClientOriginalExtension()
            : pathinfo($archivo, PATHINFO_EXTENSION);

        try {
            $lector = $this->fabricaLectores->crear($extension);
        } catch (FormatoNoSoportadoException $excepcion) {
            return $resultado->establecerErrorGeneral($excepcion->getMessage());
        }

        try {
            $filas = $lector->leer($ruta);
        } catch (LecturaException $excepcion) {
            return $resultado->establecerErrorGeneral($excepcion->getMessage());
        }

        $resultado->filasProcesadas = count($filas);

        $this->validarFilas($filas, $resultado);

        if ($resultado->tieneErrores()) {
            return $resultado;
        }

        try {
            DB::transaction(fn () => $this->persistir($filas, $resultado));
        } catch (\Throwable $excepcion) {
            $fallo = new ResultadoImportacion;

            $fallo->filasProcesadas = count($filas);

            return $fallo->establecerErrorGeneral('No se pudo guardar la importación: '.$excepcion->getMessage());
        }

        return $resultado->marcarExitoso();
    }

    /**
     * @param  list<array<string,mixed>>  $filas
     */
    private function validarFilas(array $filas, ResultadoImportacion $resultado): void
    {
        $categorias = $this->mapaNormalizado(Categoria::all(), 'categoria');
        $departamentos = $this->mapaNormalizado(Departamento::all(), 'nombre');

        $vistas = [];

        foreach ($filas as $indice => $fila) {
            $numero = $indice + 1;

            foreach ($this->validarFila($fila, $categorias, $departamentos) as $mensaje) {
                $resultado->agregarError($numero, $mensaje);
            }

            $clave = $this->claveInscripcion($fila);

            if (isset($vistas[$clave])) {
                $resultado->agregarError($numero, sprintf('Inscripción repetida (idéntica a la fila %d).', $vistas[$clave]));
            } else {
                $vistas[$clave] = $numero;
            }
        }
    }

    /**
     * @param  array<string,mixed>  $fila
     * @param  array<string,Categoria>  $categorias
     * @param  array<string,Departamento>  $departamentos
     * @return list<string>
     */
    private function validarFila(array $fila, array $categorias, array $departamentos): array
    {
        $errores = [];

        $categoria = $fila['categoria'];

        if ($categoria === null) {
            $errores[] = 'Falta la categoría.';
        } elseif (! isset($categorias[$this->clave($categoria)])) {
            $errores[] = sprintf('La categoría "%s" no existe (verifique el nombre oficial en la convocatoria).', $categoria);
        }

        $fase = strtoupper(trim($fila['fase'] ?? FaseEnum::PRESELECCION->value));

        if (! in_array($fase, [FaseEnum::PRESELECCION->value, FaseEnum::FINAL->value], true)) {
            $errores[] = sprintf('La fase "%s" no es válida (use PRESELECCION o FINAL).', $fila['fase']);
        }

        $departamento = $fila['departamento'];

        if ($fase === FaseEnum::PRESELECCION->value) {
            if ($departamento === null) {
                $errores[] = 'En preselección es obligatorio indicar el departamento.';
            } elseif (! isset($departamentos[$this->clave($departamento)])) {
                $errores[] = sprintf('El departamento "%s" no existe.', $departamento);
            }
        } elseif ($departamento !== null && ! isset($departamentos[$this->clave($departamento)])) {
            $errores[] = sprintf('El departamento "%s" no existe (en la final es opcional).', $departamento);
        }

        if ($fila['fecha_inscripcion'] !== null && ! $this->esFechaValida($fila['fecha_inscripcion'])) {
            $errores[] = sprintf('La fecha de inscripción "%s" no es válida (formato YYYY-MM-DD).', $fila['fecha_inscripcion']);
        }

        if ($fila['fecha_presentacion'] !== null && ! $this->esFechaValida($fila['fecha_presentacion'])) {
            $errores[] = sprintf('La fecha de presentación "%s" no es válida (formato YYYY-MM-DD).', $fila['fecha_presentacion']);
        }

        if ($fila['hora_presentacion'] !== null && ! $this->esHoraValida($fila['hora_presentacion'])) {
            $errores[] = sprintf('La hora de presentación "%s" no es válida (formato HH:MM).', $fila['hora_presentacion']);
        }

        $integrantes = $fila['integrantes'];

        if ($integrantes === []) {
            $errores[] = 'Debe indicar al menos un integrante.';
        }

        $categoriaModelo = $categoria !== null ? ($categorias[$this->clave($categoria)] ?? null) : null;

        if ($categoriaModelo !== null && count($integrantes) > $categoriaModelo->max_participantes) {
            $errores[] = sprintf(
                'La categoría "%s" admite máximo %d integrante(s); se indicaron %d.',
                $categoriaModelo->categoria,
                $categoriaModelo->max_participantes,
                count($integrantes)
            );
        }

        $sinNombre = [];

        foreach ($integrantes as $posicion => $integrante) {
            if (($integrante['nombre'] ?? null) === null) {
                $sinNombre[] = $posicion + 1;
            }
        }

        if ($sinNombre !== []) {
            $errores[] = 'Falta el nombre del integrante '.implode(', ', $sinNombre).'.';
        }

        $clavesIntegrantes = array_map(
            fn ($integrante) => $this->clave(implode('|', array_filter([
                $integrante['nombre'] ?? '',
                $integrante['paterno'] ?? '',
                $integrante['materno'] ?? '',
            ]))),
            $integrantes
        );

        if (array_diff_assoc($clavesIntegrantes, array_unique($clavesIntegrantes)) !== []) {
            $errores[] = 'Hay integrantes repetidos dentro de la misma inscripción.';
        }

        return $errores;
    }

    /**
     * @param  list<array<string,mixed>>  $filas
     */
    private function persistir(array $filas, ResultadoImportacion $resultado): void
    {
        $categorias = $this->mapaNormalizado(Categoria::all(), 'categoria');
        $departamentos = $this->mapaNormalizado(Departamento::all(), 'nombre');

        $fasesPorDepartamento = [];

        foreach (Fase::with('departamento')->where('fase', FaseEnum::PRESELECCION->value)->get() as $fase) {
            if ($fase->departamento !== null) {
                $fasesPorDepartamento[$this->clave($fase->departamento->nombre)] = $fase;
            }
        }

        $faseFinal = Fase::where('fase', FaseEnum::FINAL->value)->whereNull('departamento_id')->first();

        foreach ($filas as $fila) {
            $categoria = $categorias[$this->clave($fila['categoria'] ?? '')];

            $esFinal = strtoupper(trim($fila['fase'] ?? FaseEnum::PRESELECCION->value)) === FaseEnum::FINAL->value;
            $departamento = $esFinal ? null : ($departamentos[$this->clave($fila['departamento'] ?? '')] ?? null);
            $fase = $esFinal ? $faseFinal : ($fasesPorDepartamento[$this->clave($fila['departamento'] ?? '')] ?? null);

            $inscripcion = Inscripcion::create([
                'fecha_inscripcion' => $fila['fecha_inscripcion'] ?? now()->toDateString(),
                'hora_presentacion' => $fila['hora_presentacion'],
                'fecha_presentacion' => $fila['fecha_presentacion'],
                'id_fase' => $fase?->id,
                'id_categoria' => $categoria->id,
                'id_departamento' => $departamento?->id,
                'estado' => InscripcionEstadoEnum::REGISTRADA,
            ]);

            $resultado->inscripcionesCreadas++;

            foreach ($fila['integrantes'] as $integrante) {
                $participante = Participante::query()
                    ->where('nombre', $integrante['nombre'])
                    ->where('paterno', $integrante['paterno'] ?? null)
                    ->where('materno', $integrante['materno'] ?? null)
                    ->first();

                if ($participante === null) {
                    $participante = Participante::create([
                        'nombre' => $integrante['nombre'],
                        'paterno' => $integrante['paterno'] ?? null,
                        'materno' => $integrante['materno'] ?? null,
                        'pseudonimo' => $integrante['pseudonimo'] ?? null,
                    ]);

                    $resultado->participantesCreados++;
                }

                InscripcionParticipante::create([
                    'id_inscripcion' => $inscripcion->id,
                    'id_participante' => $participante->id,
                ]);
            }

            $primerIntegrante = $fila['integrantes'][0] ?? null;

            Presentacion::create([
                'id_inscripcion' => $inscripcion->id,
                'nombre_presentacion' => $fila['nombre_presentacion']
                    ?? $this->nombreCompleto($primerIntegrante),
                'estado' => PresentacionEstadoEnum::POR_PARTICIPAR,
                'tiempo_maximo' => $categoria->tiempo_presentacion,
            ]);

            $resultado->presentacionesCreadas++;
        }
    }

    /**
     * @param  iterable<object>  $items
     * @return array<string,object>
     */
    private function mapaNormalizado(iterable $items, string $campo): array
    {
        $mapa = [];

        foreach ($items as $item) {
            $mapa[$this->clave((string) $item->{$campo})] = $item;
        }

        return $mapa;
    }

    /**
     * @param  array<string,mixed>  $fila
     */
    private function claveInscripcion(array $fila): string
    {
        $integrantes = array_map(
            fn ($integrante) => implode('|', array_filter([
                $integrante['nombre'] ?? '',
                $integrante['paterno'] ?? '',
                $integrante['materno'] ?? '',
            ])),
            $fila['integrantes']
        );

        sort($integrantes);

        return $this->clave(implode('§', [
            $fila['categoria'] ?? '',
            $fila['departamento'] ?? '',
            $fila['fase'] ?? '',
            $fila['fecha_presentacion'] ?? '',
            $fila['hora_presentacion'] ?? '',
            $fila['nombre_presentacion'] ?? '',
            implode('§', $integrantes),
        ]));
    }

    private function clave(string $valor): string
    {
        $normalizado = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', mb_strtolower(trim($valor)));
        $normalizado = $normalizado === false ? mb_strtolower(trim($valor)) : $normalizado;

        return preg_replace('/\s+/', ' ', $normalizado) ?? $normalizado;
    }

    /**
     * @param  array{nombre:?string,paterno:?string,materno:?string,pseudonimo:?string}|null  $integrante
     */
    private function nombreCompleto(?array $integrante): ?string
    {
        if ($integrante === null) {
            return null;
        }

        $nombre = trim(implode(' ', array_filter([
            $integrante['nombre'] ?? '',
            $integrante['paterno'] ?? '',
            $integrante['materno'] ?? '',
        ])));

        return $nombre === '' ? null : $nombre;
    }

    private function esFechaValida(string $valor): bool
    {
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)) {
            return false;
        }

        [$anio, $mes, $dia] = explode('-', $valor);

        return checkdate((int) $mes, (int) $dia, (int) $anio);
    }

    private function esHoraValida(string $valor): bool
    {
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $valor) === 1;
    }
}
