<?php

namespace App\Http\Controllers;

use App\Http\Requests\PanteoneroRequest;
use App\Models\Historial;
use App\Models\Panteonero;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PanteoneroController extends Controller
{
    /**
     * Muestra el listado de panteoneros con búsqueda y filtro de estado.
     */
    public function index(Request $request): View
    {
        $query = Panteonero::query();

        // Filtro por término de búsqueda (nombre, apellido paterno, apellido materno o CI)
        if ($request->filled('buscar')) {
            $buscar = trim($request->input('buscar'));
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre', 'LIKE', "%{$buscar}%")
                  ->orWhere('apellido_paterno', 'LIKE', "%{$buscar}%")
                  ->orWhere('apellido_materno', 'LIKE', "%{$buscar}%")
                  ->orWhere('ci', 'LIKE', "%{$buscar}%");
            });
        }

        // Por defecto muestra solo panteoneros activos, salvo que se solicite incluir inactivos
        if (!$request->boolean('mostrar_inactivos')) {
            $query->where('activo', true);
        }

        $panteoneros = $query->orderBy('apellido_paterno', 'asc')
            ->orderBy('nombre', 'asc')
            ->paginate(15)
            ->withQueryString();

        return view('panteoneros.index', compact('panteoneros'));
    }

    /**
     * Muestra el formulario para registrar un nuevo panteonero.
     */
    public function create(): View
    {
        return view('panteoneros.create');
    }

    /**
     * Guarda un nuevo panteonero en la base de datos.
     */
    public function store(PanteoneroRequest $request): RedirectResponse
    {
        $panteonero = Panteonero::create([
            'nombre' => $request->validated('nombre'),
            'apellido_paterno' => $request->validated('apellido_paterno'),
            'apellido_materno' => $request->validated('apellido_materno'),
            'ci' => $request->validated('ci'),
            'activo' => true,
        ]);

        Historial::registrar(
            'panteoneros',
            $panteonero->id,
            'Crear',
            "Se registró al panteonero '{$panteonero->nombre} {$panteonero->apellido_paterno}' con CI {$panteonero->ci}",
            null,
            [
                'nombre' => $panteonero->nombre,
                'apellido_paterno' => $panteonero->apellido_paterno,
                'apellido_materno' => $panteonero->apellido_materno,
                'ci' => $panteonero->ci,
                'activo' => true,
            ]
        );

        return redirect()->route('panteoneros.index')
            ->with('success', 'Panteonero registrado correctamente.');
    }

    /**
     * Muestra el formulario para editar los datos de un panteonero.
     */
    public function edit(Panteonero $panteonero): View
    {
        return view('panteoneros.edit', compact('panteonero'));
    }

    /**
     * Actualiza la información del panteonero en la base de datos.
     */
    public function update(PanteoneroRequest $request, Panteonero $panteonero): RedirectResponse
    {
        $campos = ['nombre', 'apellido_paterno', 'apellido_materno', 'ci'];
        $original = $panteonero->only($campos);

        $panteonero->update($request->validated());

        $datosAnteriores = [];
        $datosNuevos = [];
        foreach ($original as $campo => $valorAnterior) {
            if ((string)$valorAnterior !== (string)$panteonero->$campo) {
                $datosAnteriores[$campo] = $valorAnterior;
                $datosNuevos[$campo] = $panteonero->$campo;
            }
        }

        if (!empty($datosNuevos)) {
            Historial::registrar(
                'panteoneros',
                $panteonero->id,
                'Editar',
                "Se actualizaron los datos del panteonero '{$panteonero->nombre} {$panteonero->apellido_paterno}'",
                $datosAnteriores,
                $datosNuevos
            );
        }

        return redirect()->route('panteoneros.index')
            ->with('success', 'Panteonero actualizado correctamente.');
    }

    /**
     * Alterna el estado (activo/inactivo) de un panteonero sin eliminar el registro.
     */
    public function toggleActivo(Panteonero $panteonero): RedirectResponse
    {
        $anterior = $panteonero->activo;
        $panteonero->activo = !$panteonero->activo;
        $panteonero->save();

        $accion = $panteonero->activo ? 'Activar' : 'Desactivar';
        $estadoTexto = $panteonero->activo ? 'activado' : 'desactivado';

        Historial::registrar(
            'panteoneros',
            $panteonero->id,
            $accion,
            "Se ha {$estadoTexto} al panteonero '{$panteonero->nombre} {$panteonero->apellido_paterno}'",
            ['activo' => $anterior],
            ['activo' => $panteonero->activo]
        );

        return redirect()->route('panteoneros.index')
            ->with('success', "El panteonero '{$panteonero->nombre} {$panteonero->apellido_paterno}' ha sido {$estadoTexto} correctamente.");
    }
}