<?php

namespace App\Http\Controllers;

use App\Http\Requests\UsuarioRequest;
use App\Models\Historial;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UsuarioController extends Controller
{
    /**
     * Muestra el listado de usuarios registrados en el sistema.
     */
    public function index(): View
    {
        $usuarios = User::orderBy('name', 'asc')->paginate(10);

        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function create(): View
    {
        return view('usuarios.create');
    }

    /**
     * Guarda un nuevo usuario en la base de datos.
     */
    public function store(UsuarioRequest $request): RedirectResponse
    {
        $usuario = User::create([
            'name' => $request->validated('name'),
            'email' => $request->validated('email'),
            'password' => Hash::make($request->validated('password')),
            'rol' => $request->validated('rol'),
            'activo' => true,
        ]);

        // Registrar en historial (nunca incluir la contraseña en datos_nuevos)
        Historial::registrar(
            'usuarios',
            $usuario->id,
            'Crear',
            "Se registró al usuario '{$usuario->name}' con rol {$usuario->rol}",
            null,
            [
                'name'   => $usuario->name,
                'email'  => $usuario->email,
                'rol'    => $usuario->rol,
                'activo' => true,
            ]
        );

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    /**
     * Muestra el formulario para editar un usuario existente.
     */
    public function edit(User $usuario): View
    {
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Actualiza los datos del usuario en la base de datos.
     * Permite el reseteo manual de contraseña si el Administrador la proporciona.
     */
    public function update(UsuarioRequest $request, User $usuario): RedirectResponse
    {
        // Guardar valores originales antes de la actualización
        $original = [
            'name'  => $usuario->name,
            'email' => $usuario->email,
            'rol'   => $usuario->rol,
        ];

        $datos = [
            'name'  => $request->validated('name'),
            'email' => $request->validated('email'),
            'rol'   => $request->validated('rol'),
        ];

        // Si se proporcionó una nueva contraseña, la actualizamos
        $passwordActualizado = false;
        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->validated('password'));
            $passwordActualizado = true;
        }

        $usuario->update($datos);

        // Comparar cambios reales
        $datosAnteriores = [];
        $datosNuevos = [];
        foreach ($original as $campo => $valorAnterior) {
            if ($usuario->$campo !== $valorAnterior) {
                $datosAnteriores[$campo] = $valorAnterior;
                $datosNuevos[$campo] = $usuario->$campo;
            }
        }

        if ($passwordActualizado) {
            $datosAnteriores['password'] = '(sin cambios)';
            $datosNuevos['password'] = '(contraseña actualizada)';
        }

        if (!empty($datosNuevos)) {
            Historial::registrar(
                'usuarios',
                $usuario->id,
                'Editar',
                "Se actualizaron los datos del usuario '{$usuario->name}'",
                $datosAnteriores,
                $datosNuevos
            );
        }

        return redirect()->route('usuarios.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * Alterna el estado activo/inactivo de un usuario.
     * Previene que un Administrador desactive su propia cuenta.
     */
    public function toggleActivo(User $usuario): RedirectResponse
    {
        // Regla obligatoria: No permitir desactivar la propia cuenta logueada
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $anterior = $usuario->activo;
        $usuario->activo = !$usuario->activo;
        $usuario->save();

        $accion = $usuario->activo ? 'Activar' : 'Desactivar';
        $estadoTexto = $usuario->activo ? 'activado' : 'desactivado';

        Historial::registrar(
            'usuarios',
            $usuario->id,
            $accion,
            "Se ha {$estadoTexto} al usuario '{$usuario->name}'",
            ['activo' => $anterior],
            ['activo' => $usuario->activo]
        );

        return redirect()->route('usuarios.index')
            ->with('success', "El usuario '{$usuario->name}' ha sido {$estadoTexto} correctamente.");
    }
}