<?php

namespace App\Http\Controllers;

use App\Models\Bloque;
use App\Models\Ubicacion;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MapaController extends Controller
{
    /**
     * Nivel 1 — Vista general del plano.
     *
     * Dibuja un rectangulo clicable por cada bloque que tiene AMBAS cosas:
     * 1. al menos una ubicacion sembrada en la base de datos;
     * 2. una posicion definida en config/mapa_posiciones.php.
     *
     * No se inventan posiciones para bloques sin datos reales.
     */
    public function index(): View
    {
        $posiciones = config('mapa_posiciones');

        // Bloques con al menos una ubicacion activa sembrada
        $bloquesConDatos = Bloque::whereHas('ubicaciones', function ($query) {
            $query->where('activo', true);
        })->get();

        // Cruzar contra el config: solo pasan los que tienen posicion definida
        $bloques = $bloquesConDatos
            ->filter(fn (Bloque $bloque) => isset($posiciones[$bloque->codigo]))
            ->map(function (Bloque $bloque) use ($posiciones) {
                return [
                    'codigo' => $bloque->codigo,
                    'nombre' => $bloque->nombre,
                    'top'    => $posiciones[$bloque->codigo]['top'],
                    'left'   => $posiciones[$bloque->codigo]['left'],
                    'width'  => $posiciones[$bloque->codigo]['width'],
                    'height' => $posiciones[$bloque->codigo]['height'],
                ];
            })
            ->values();

        return view('mapa.index', [
            'bloquesJson' => $bloques->toJson(),
        ]);
    }

    /**
     * Nivel 2 — Lados disponibles de un bloque.
     *
     * Devuelve en JSON los lados que tienen ubicaciones reales para el
     * bloque indicado, con su tipo y cuantas ubicaciones tiene cada uno.
     */
    public function porBloque(string $bloque): JsonResponse
    {
        $bloqueModelo = Bloque::where('codigo', $bloque)->firstOrFail();

        $lados = Ubicacion::where('bloque_id', $bloqueModelo->id)
            ->where('activo', true)
            ->selectRaw('lado, tipo, count(*) as cantidad')
            ->groupBy('lado', 'tipo')
            ->orderBy('lado')
            ->get();

        return response()->json([
            'bloque' => [
                'codigo' => $bloqueModelo->codigo,
                'nombre' => $bloqueModelo->nombre,
            ],
            'lados' => $lados,
        ]);
    }

    /**
     * Nivel 3 — Grid de ubicaciones de un bloque + lado.
     *
     * Devuelve en JSON todas las ubicaciones de ese lado, con su estado
     * calculado, para pintar la cuadricula de filas x columnas.
     */
    public function porBloqueYLado(string $bloque, string $lado): JsonResponse
    {
        $bloqueModelo = Bloque::where('codigo', $bloque)->firstOrFail();

        $ubicaciones = Ubicacion::where('bloque_id', $bloqueModelo->id)
            ->where('lado', $lado)
            ->where('activo', true)
            ->orderBy('fila')
            ->orderBy('columna')
            ->get();

        if ($ubicaciones->isEmpty()) {
            abort(404, 'No hay ubicaciones para ese bloque y lado.');
        }

        $datos = $ubicaciones->map(function (Ubicacion $u) {
            return [
                'id'      => $u->id,
                'columna' => $u->columna,
                'fila'    => $u->fila,
                'numero'  => $u->numero,
                'estado'  => $u->estado_actual,
            ];
        });

        return response()->json([
            'bloque'   => $bloqueModelo->codigo,
            'lado'     => $lado,
            'tipo'     => $ubicaciones->first()->tipo,
            'filas'    => $ubicaciones->max('fila'),
            'columnas' => $ubicaciones->max('columna'),
            'ubicaciones' => $datos,
        ]);
    }
}
