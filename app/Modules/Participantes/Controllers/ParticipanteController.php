<?php

namespace App\Modules\Participantes\Controllers;

use App\Modules\Participantes\Models\Participante;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ParticipanteController
{
    public function index(Request $request): View
    {
        $busqueda = $request->string('busqueda')->trim()->toString();

        $participantes = Participante::query()
            ->withCount('inscripciones')
            ->when($busqueda !== '', fn ($q) => $q->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                    ->orWhere('paterno', 'like', "%{$busqueda}%")
                    ->orWhere('materno', 'like', "%{$busqueda}%")
                    ->orWhere('pseudonimo', 'like', "%{$busqueda}%");
            }))
            ->orderBy('paterno')
            ->orderBy('nombre')
            ->paginate(15)
            ->withQueryString();

        return view('admin.participantes.index', [
            'participantes' => $participantes,
            'busqueda' => $busqueda,
        ]);
    }

    public function create(): View
    {
        return view('admin.participantes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $datos = $this->validar($request);

        $this->comprobarDuplicado($datos, null);

        Participante::create($datos);

        return redirect()
            ->route('admin.participantes.index')
            ->with('status', 'Participante registrado correctamente.');
    }

    public function edit(Participante $participante): View
    {
        return view('admin.participantes.edit', [
            'participante' => $participante->load('inscripciones.categoria', 'inscripciones.fase', 'inscripciones.departamento'),
        ]);
    }

    public function update(Request $request, Participante $participante): RedirectResponse
    {
        $datos = $this->validar($request);

        $this->comprobarDuplicado($datos, $participante->id);

        $participante->fill($datos)->save();

        return redirect()
            ->route('admin.participantes.index')
            ->with('status', 'Participante actualizado correctamente.');
    }

    public function destroy(Participante $participante): RedirectResponse
    {
        abort_if($participante->inscripciones()->exists(), 403, 'No se puede eliminar un participante que ya figura en inscripciones.');

        $participante->delete();

        return redirect()
            ->route('admin.participantes.index')
            ->with('status', 'Participante eliminado.');
    }

    /**
     * @throws ValidationException
     */
    protected function validar(Request $request): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'paterno' => ['nullable', 'string', 'max:255'],
            'materno' => ['nullable', 'string', 'max:255'],
            'pseudonimo' => ['nullable', 'string', 'max:255'],
        ]);
    }

    /**
     * @param  array{nombre:string,paterno:?string,materno:?string,pseudonimo:?string}  $datos
     *
     * @throws ValidationException
     */
    protected function comprobarDuplicado(array $datos, ?int $ignorarId): void
    {
        $existente = Participante::query()
            ->where('nombre', $datos['nombre'])
            ->where('paterno', $datos['paterno'] ?? null)
            ->where('materno', $datos['materno'] ?? null)
            ->when($ignorarId !== null, fn ($q) => $q->where('id', '!=', $ignorarId))
            ->first();

        if ($existente !== null) {
            throw ValidationException::withMessages([
                'nombre' => sprintf('Ya existe un participante con estos datos (id %d): %s.', $existente->id, $existente->nombreCompleto()),
            ]);
        }
    }
}
