<?php

namespace App\Modules\Usuarios\Controllers;

use App\Modules\Usuarios\Models\Usuario;
use App\Support\Enums\RolEnum;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UsuariosController
{
    public function index(Request $request): View
    {
        $busqueda = $request->string('busqueda')->trim()->toString();

        $usuarios = Usuario::query()
            ->when($busqueda !== '', fn ($q) => $q->where(function ($q) use ($busqueda) {
                $q->where('nombre', 'like', "%{$busqueda}%")
                    ->orWhere('paterno', 'like', "%{$busqueda}%")
                    ->orWhere('materno', 'like', "%{$busqueda}%")
                    ->orWhere('username', 'like', "%{$busqueda}%")
                    ->orWhere('email', 'like', "%{$busqueda}%")
                    ->orWhere('ci', 'like', "%{$busqueda}%");
            }))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.usuarios.index', [
            'usuarios' => $usuarios,
            'busqueda' => $busqueda,
        ]);
    }

    public function create(): View
    {
        return view('admin.usuarios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $passwordPlano = $request->string('password')->toString();
        $datos = $this->validar($request, crear: true);

        $usuario = Usuario::create([
            ...$datos,
            'email_verified_at' => now(),
        ]);

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', 'Usuario creado correctamente.')
            ->with('credenciales_generadas', [
                'nombre' => $usuario->nombreCompleto(),
                'email' => $usuario->email,
                'password' => $passwordPlano,
            ]);
    }

    public function edit(Usuario $usuario): View
    {
        return view('admin.usuarios.edit', ['usuario' => $usuario]);
    }

    public function update(Request $request, Usuario $usuario): RedirectResponse
    {
        $passwordPlano = $request->string('password')->toString();
        $datos = $this->validar($request, crear: false, usuario: $usuario);

        $usuario->fill($datos);
        $usuario->email_verified_at = now();
        $usuario->save();

        $redirect = redirect()
            ->route('admin.usuarios.index')
            ->with('status', 'Usuario actualizado correctamente.');

        if ($passwordPlano !== '') {
            $redirect->with('credenciales_generadas', [
                'nombre' => $usuario->nombreCompleto(),
                'email' => $usuario->email,
                'password' => $passwordPlano,
            ]);
        }

        return $redirect;
    }

    public function destroy(Request $request, Usuario $usuario): RedirectResponse
    {
        abort_unless($usuario->id !== $request->user()->id, 403, 'No puedes eliminar tu propia cuenta.');

        if ($usuario->esAdmin() && Usuario::where('rol', RolEnum::ADMIN->value)->where('id', '!=', $usuario->id)->doesntExist()) {
            abort(403, 'Debe existir al menos un administrador.');
        }

        $usuario->delete();

        return redirect()
            ->route('admin.usuarios.index')
            ->with('status', 'Usuario eliminado.');
    }

    /**
     * @throws ValidationException
     */
    protected function validar(Request $request, bool $crear, ?Usuario $usuario = null): array
    {
        $id = $usuario?->id;
        $usaNombreCompleto = $request->has('nombre_completo');
        $reglasPassword = $crear
            ? ['required', Rules\Password::defaults()]
            : ['nullable', Rules\Password::defaults()];

        if ($request->has('password_confirmation')) {
            array_splice($reglasPassword, 1, 0, ['confirmed']);
        }

        $reglas = [
            'username' => ['nullable', 'string', 'max:60', Rule::unique('users', 'username')->ignore($id)],
            'ci' => ['nullable', 'string', 'max:20'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'rol' => ['required', Rule::enum(RolEnum::class)],
            'puede_controlar_tiempo' => ['sometimes', 'boolean'],
            'password' => $reglasPassword,
        ];

        if ($usaNombreCompleto) {
            $reglas['nombre_completo'] = ['required', 'string', 'max:255'];
        } else {
            $reglas['nombre'] = ['required', 'string', 'max:255'];
            $reglas['paterno'] = ['nullable', 'string', 'max:255'];
            $reglas['materno'] = ['nullable', 'string', 'max:255'];
        }

        $datos = $request->validate($reglas);

        if ($usaNombreCompleto) {
            $separado = $this->separarNombreCompleto($datos['nombre_completo']);
            unset($datos['nombre_completo']);
            $datos['nombre'] = $separado['nombre'];
            $datos['paterno'] = $separado['paterno'];
            $datos['materno'] = $separado['materno'];
        } elseif (! $request->has('paterno') && ! $request->has('materno')) {
            $separado = $this->separarNombreCompleto($datos['nombre']);
            $datos['nombre'] = $separado['nombre'];
            $datos['paterno'] = $separado['paterno'];
            $datos['materno'] = $separado['materno'];
        }

        if (array_key_exists('username', $datos)) {
            $datos['username'] = blank($datos['username']) ? null : $datos['username'];
        }

        if (array_key_exists('ci', $datos)) {
            $datos['ci'] = blank($datos['ci']) ? null : $datos['ci'];
        }

        $datos['puede_controlar_tiempo'] = $request->boolean('puede_controlar_tiempo');

        if ($crear === false) {
            if (blank($request->input('password'))) {
                unset($datos['password']);
            }

            // Protección: el administrador no puede cambiar su propio rol.
            if ($usuario->id === $request->user()->id && $datos['rol'] !== $request->user()->rol?->value) {
                throw ValidationException::withMessages([
                    'rol' => 'No puedes cambiar tu propio rol.',
                ]);
            }

            // Protección: no se puede degradar al último administrador.
            if ($usuario->esAdmin()
                && $datos['rol'] !== RolEnum::ADMIN->value
                && Usuario::where('rol', RolEnum::ADMIN->value)->where('id', '!=', $usuario->id)->doesntExist()
            ) {
                throw ValidationException::withMessages([
                    'rol' => 'Debe existir al menos un administrador.',
                ]);
            }
        }

        return $datos;
    }

    /**
     * Separa un nombre completo por espacios en blanco:
     * - 1 palabra:  nombre = P1, paterno = null, materno = null
     * - 2 palabras: nombre = P1, paterno = P2, materno = null
     * - 3+ palabras: últimas dos son paterno y materno; el resto es nombre.
     *
     * @return array{nombre: string, paterno: ?string, materno: ?string}
     */
    public function separarNombreCompleto(string $nombreCompleto): array
    {
        $partes = preg_split('/\s+/u', trim($nombreCompleto), -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $total = count($partes);

        if ($total === 0) {
            return ['nombre' => '', 'paterno' => null, 'materno' => null];
        }

        if ($total === 1) {
            return ['nombre' => $partes[0], 'paterno' => null, 'materno' => null];
        }

        if ($total === 2) {
            return ['nombre' => $partes[0], 'paterno' => $partes[1], 'materno' => null];
        }

        $materno = array_pop($partes);
        $paterno = array_pop($partes);
        $nombre = implode(' ', $partes);

        return [
            'nombre' => $nombre,
            'paterno' => $paterno,
            'materno' => $materno,
        ];
    }
}