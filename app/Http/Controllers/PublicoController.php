<?php

namespace App\Http\Controllers;

use App\Models\Difunto;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PublicoController extends Controller
{
    /**
     * Muestra la página principal pública de bienvenida e información institucional.
     */
    public function inicio(): View
    {
        return view('publico.inicio');
    }

    /**
     * Muestra el buscador público y procesa las consultas por término (código, CI, nombre, apellidos).
     * El CI se usa como criterio de búsqueda interna, pero NUNCA se renderiza en la vista pública.
     */
    public function buscar(Request $request): View
    {
        $query = trim($request->input('q', ''));
        $difuntos = null;

        if ($query !== '') {
            $term = '%' . mb_strtolower($query, 'UTF-8') . '%';

            $difuntos = Difunto::where('activo', true)
                ->where(function ($q) use ($term) {
                    $q->whereRaw('LOWER(codigo) LIKE ?', [$term])
                      ->orWhereRaw('LOWER(ci) LIKE ?', [$term])
                      ->orWhereRaw('LOWER(nombre) LIKE ?', [$term])
                      ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', [$term])
                      ->orWhereRaw('LOWER(apellido_materno) LIKE ?', [$term]);
                })
                ->orderBy('nombre', 'asc')
                ->paginate(10)
                ->withQueryString();
        }

        return view('publico.buscar', compact('difuntos', 'query'));
    }

    /**
     * Muestra la ficha pública de un difunto.
     * ÚNICAMENTE renderiza el nombre completo y su ubicación física activa.
     * NUNCA muestra el CI, fecha de fallecimiento, causa de muerte ni estados internos de disponibilidad.
     */
    public function ficha($id): View
    {
        $difunto = Difunto::where('id', $id)
            ->where('activo', true)
            ->first();

        if (!$difunto) {
            abort(404);
        }

        // Obtener la ubicación activa del difunto en la tabla pivote ubicacion_difunto
        $ubicacion = $difunto->ubicaciones()
            ->wherePivot('activo', true)
            ->first();

        return view('publico.ficha', compact('difunto', 'ubicacion'));
    }

    /**
     * Muestra la vista del mapa público interactivo.
     */
    public function mapa(): View
    {
        return view('publico.mapa');
    }
}
