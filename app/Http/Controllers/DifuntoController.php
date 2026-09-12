<?php

namespace App\Http\Controllers;

use App\Http\Requests\DifuntoRequest;
use App\Models\Difunto;
use App\Models\Historial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DifuntoController extends Controller
{
    /**
     * Muestra el listado de difuntos con opciones de búsqueda y filtrado por estado.
     */
    public function index(Request $request): View
    {
        $buscar = trim($request->input('buscar'));
        $incluirInactivos = $request->boolean('incluir_inactivos');

        $query = Difunto::query();

        // Si no se pide incluir inactivos explícitamente, por defecto muestra solo los activos
        if (!$incluirInactivos) {
            $query->where('activo', true);
        }

        // Búsqueda simple por texto libre (insensible a mayúsculas)
        if (!empty($buscar)) {
            $term = '%' . mb_strtolower($buscar) . '%';
            $query->where(function ($q) use ($term) {
                $q->whereRaw('LOWER(codigo) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(ci) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(nombre) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', [$term])
                  ->orWhereRaw('LOWER(apellido_materno) LIKE ?', [$term]);
            });
        }

        $difuntos = $query->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('difuntos.index', compact('difuntos', 'buscar', 'incluirInactivos'));
    }

    /**
     * Muestra el formulario para registrar un nuevo difunto.
     */
    public function create(): View
    {
        return view('difuntos.create');
    }

    /**
     * Guarda un nuevo difunto en la base de datos.
     */
    public function store(DifuntoRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Autogenerar código de difunto si no se ingresó manualmente (formato DIF-0001)
        if (empty($validated['codigo'])) {
            $maxId = Difunto::max('id') ?? 0;
            $validated['codigo'] = 'DIF-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);
        }

        $validated['activo'] = true;

        $difunto = Difunto::create($validated);

        // Registro en historial
        Historial::registrar(
            'difuntos',
            $difunto->id,
            'Crear',
            "Se registró al difunto '{$difunto->nombre} {$difunto->apellido_paterno}' con código {$difunto->codigo}",
            null,
            $difunto->only([
                'codigo', 'nombre', 'apellido_paterno', 'apellido_materno', 'apellido_casada',
                'ci', 'edad', 'profesion_ocupacion', 'causa_muerte', 'fecha_fallecimiento',
                'hora_fallecimiento', 'numero_certificado_defuncion', 'activo'
            ])
        );

        return redirect()->route('difuntos.index')
            ->with('success', 'Difunto registrado correctamente con el código ' . $validated['codigo'] . '.');
    }

    /**
     * Muestra la ficha de detalle administrativa de un difunto.
     */
    public function show(Difunto $difunto): View
    {
        // Consultar su ubicación actual activa si ya la tiene asociada
        $ubicacionActiva = $difunto->ubicaciones()
            ->wherePivot('activo', true)
            ->with('bloque')
            ->first();

        return view('difuntos.show', compact('difunto', 'ubicacionActiva'));
    }

    /**
     * Muestra el formulario para editar los datos de un difunto existente.
     */
    public function edit(Difunto $difunto): View
    {
        return view('difuntos.edit', compact('difunto'));
    }

    /**
     * Actualiza la información del difunto en la base de datos.
     */
    public function update(DifuntoRequest $request, Difunto $difunto): RedirectResponse
    {
        $validated = $request->validated();

        // Si se borra el código al editar, mantenemos el código que ya tenía
        if (empty($validated['codigo'])) {
            $validated['codigo'] = $difunto->codigo;
        }

        $camposRevisar = [
            'codigo', 'nombre', 'apellido_paterno', 'apellido_materno', 'apellido_casada',
            'ci', 'edad', 'profesion_ocupacion', 'causa_muerte', 'fecha_fallecimiento',
            'hora_fallecimiento', 'numero_certificado_defuncion'
        ];

        $original = $difunto->only($camposRevisar);

        $difunto->update($validated);

        // Comparar cambios reales
        $datosAnteriores = [];
        $datosNuevos = [];
        foreach ($original as $campo => $valorAnterior) {
            $valorNuevo = $difunto->$campo;
            if ((string)$valorAnterior !== (string)$valorNuevo) {
                $datosAnteriores[$campo] = $valorAnterior;
                $datosNuevos[$campo] = $valorNuevo;
            }
        }

        if (!empty($datosNuevos)) {
            Historial::registrar(
                'difuntos',
                $difunto->id,
                'Editar',
                "Se actualizaron datos del difunto '{$difunto->nombre} {$difunto->apellido_paterno}' ({$difunto->codigo})",
                $datosAnteriores,
                $datosNuevos
            );
        }

        return redirect()->route('difuntos.index')
            ->with('success', 'Información del difunto actualizada correctamente.');
    }

    /**
     * Alterna el estado activo/desactivado de un difunto (sin borrado físico).
     */
    public function toggleActivo(Difunto $difunto): RedirectResponse
    {
        $anterior = $difunto->activo;
        $difunto->activo = !$difunto->activo;
        $difunto->save();

        $accion = $difunto->activo ? 'Activar' : 'Desactivar';
        $estadoTexto = $difunto->activo ? 'activado' : 'desactivado';

        Historial::registrar(
            'difuntos',
            $difunto->id,
            $accion,
            "Se ha {$estadoTexto} al difunto '{$difunto->nombre} {$difunto->apellido_paterno}' ({$difunto->codigo})",
            ['activo' => $anterior],
            ['activo' => $difunto->activo]
        );

        return redirect()->route('difuntos.index')
            ->with('success', "El difunto '{$difunto->nombre} {$difunto->apellido_paterno}' ha sido {$estadoTexto} correctamente.");
    }
}