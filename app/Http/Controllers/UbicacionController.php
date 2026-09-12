<?php

namespace App\Http\Controllers;

use App\Models\Difunto;
use App\Models\Historial;
use App\Models\Ubicacion;
use App\Models\UbicacionDifunto;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class UbicacionController extends Controller
{
    /**
     * Devuelve el detalle de una ubicación en formato JSON.
     *
     * Se usa mediante fetch() desde el JS del mapa al hacer clic en un marcador.
     * Incluye: datos de la ubicación, estado calculado, difuntos activos en ella,
     * y lista de difuntos disponibles para asociar (activos sin ubicación actual).
     *
     * Acceso: Administrador y Personal administrativo (cualquier usuario autenticado).
     */
    public function show(Ubicacion $ubicacion): JsonResponse
    {
        // Cargar los difuntos activos actualmente en esta ubicación
        $difuntosActivos = UbicacionDifunto::where('ubicacion_id', $ubicacion->id)
            ->where('activo', true)
            ->with('difunto')
            ->get()
            ->map(function (UbicacionDifunto $ud) {
                return [
                    'pivot_id'     => $ud->id,
                    'difunto_id'   => $ud->difunto_id,
                    'nombre'       => $ud->difunto->nombre . ' ' . $ud->difunto->apellido_paterno . ' ' . $ud->difunto->apellido_materno,
                    'codigo'       => $ud->difunto->codigo,
                    'fecha_ingreso' => $ud->fecha_ingreso ? Carbon::parse($ud->fecha_ingreso)->format('d/m/Y') : null,
                ];
            });

        // Difuntos disponibles para asociar:
        // Activos (activo = true) y sin ningún registro activo en ubicacion_difunto
        $difuntosDisponibles = Difunto::where('activo', true)
            ->whereDoesntHave('ubicaciones', function ($query) {
                // Dentro de whereDoesntHave, el closure recibe el query builder
                // de la tabla relacionada (ya unida a la tabla pivote), por lo
                // que la columna de la pivote se referencia por su nombre real
                // en vez de con wherePivot() (que aquí no está disponible).
                $query->where('ubicacion_difunto.activo', true);
            })
            ->orderBy('apellido_paterno')
            ->orderBy('nombre')
            ->get(['id', 'codigo', 'nombre', 'apellido_paterno', 'apellido_materno']);

        return response()->json([
            'id'                   => $ubicacion->id,
            'bloque'               => $ubicacion->bloque->codigo ?? 'N/A',
            'bloque_nombre'        => $ubicacion->bloque->nombre ?? '',
            'lado'                 => $ubicacion->lado,
            'columna'              => $ubicacion->columna,
            'fila'                 => $ubicacion->fila,
            'tipo'                 => $ubicacion->tipo,
            'numero'               => $ubicacion->numero,
            'capacidad'            => $ubicacion->capacidad,
            'estado'               => $ubicacion->estado_actual,  // Accessor del modelo
            'activo'               => $ubicacion->activo,
            'difuntos_activos'     => $difuntosActivos,
            'difuntos_disponibles' => $difuntosDisponibles,
        ]);
    }

    /**
     * Actualiza los campos editables de una ubicación: geom_geojson, centro_lat,
     * centro_lng y activo.
     *
     * Los campos físicos (bloque, lado, columna, fila, tipo, numero, capacidad)
     * NUNCA se modifican desde la interfaz — solo se cargan por seeder.
     *
     * Acceso: solo Administrador (middleware 'admin' en las rutas).
     */
    public function update(Request $request, Ubicacion $ubicacion): RedirectResponse
    {
        $validated = $request->validate([
            'geom_geojson' => ['nullable', 'json'],
            'centro_lat'   => ['nullable', 'numeric', 'between:-9999,9999'],
            'centro_lng'   => ['nullable', 'numeric', 'between:-9999,9999'],
            'activo'       => ['required', 'boolean'],
        ]);

        $campos = ['geom_geojson', 'centro_lat', 'centro_lng', 'activo'];
        $original = $ubicacion->only($campos);

        $ubicacion->update($validated);

        // Comparar campos modificados
        $datosAnteriores = [];
        $datosNuevos = [];
        foreach ($campos as $campo) {
            if ((string)$original[$campo] !== (string)$ubicacion->$campo) {
                $datosAnteriores[$campo] = $original[$campo];
                $datosNuevos[$campo] = $ubicacion->$campo;
            }
        }

        if (!empty($datosNuevos)) {
            $codigoBloque = $ubicacion->bloque->codigo ?? 'B?';
            $descUbicacion = "Bloque {$codigoBloque}-{$ubicacion->lado}-C{$ubicacion->columna}-F{$ubicacion->fila}-{$ubicacion->tipo}-{$ubicacion->numero}";

            Historial::registrar(
                'ubicaciones',
                $ubicacion->id,
                'Editar',
                "Se actualizaron los datos técnicos/geográficos de la ubicación {$descUbicacion}",
                $datosAnteriores,
                $datosNuevos
            );
        }

        return redirect()->route('mapa.index')
            ->with('success', 'Ubicación actualizada correctamente.');
    }

    /**
     * Asocia un difunto a una ubicación creando un registro en ubicacion_difunto.
     *
     * Valida que:
     * 1. El difunto_id fue enviado y el difunto existe.
     * 2. La ubicación no haya alcanzado su capacidad máxima.
     * 3. El difunto no tenga ya una ubicación activa (unicidad).
     *
     * Acceso: solo Administrador.
     */
    public function asociar(Request $request, Ubicacion $ubicacion): RedirectResponse
    {
        $validated = $request->validate([
            'difunto_id' => ['required', 'exists:difuntos,id'],
        ]);

        // Contar cuántos difuntos activos hay en esta ubicación ahora mismo
        $ocupantesActuales = UbicacionDifunto::where('ubicacion_id', $ubicacion->id)
            ->where('activo', true)
            ->count();

        // Verificar que no se haya alcanzado la capacidad máxima
        if ($ocupantesActuales >= $ubicacion->capacidad) {
            return back()->with(
                'error',
                "La ubicación ya está al tope de su capacidad ({$ubicacion->capacidad}). No se puede asociar otro difunto."
            );
        }

        // Verificar que el difunto no tenga ya una ubicación activa
        $yaTieneUbicacion = UbicacionDifunto::where('difunto_id', $validated['difunto_id'])
            ->where('activo', true)
            ->exists();

        if ($yaTieneUbicacion) {
            return back()->with('error', 'El difunto ya tiene una ubicación activa asignada.');
        }

        // Crear el registro de asociación
        UbicacionDifunto::create([
            'ubicacion_id' => $ubicacion->id,
            'difunto_id'   => $validated['difunto_id'],
            'fecha_ingreso' => Carbon::today(),
            'activo'       => true,
        ]);

        $difunto = Difunto::find($validated['difunto_id']);
        $codigoBloque = $ubicacion->bloque->codigo ?? 'B?';
        $descUbicacion = "Bloque {$codigoBloque}-{$ubicacion->lado}-C{$ubicacion->columna}-F{$ubicacion->fila}-{$ubicacion->tipo}-{$ubicacion->numero}";
        $nombreDifunto = "{$difunto->nombre} {$difunto->apellido_paterno} {$difunto->apellido_materno}";

        Historial::registrar(
            'ubicaciones',
            $ubicacion->id,
            'Asociar',
            "Se asoció al difunto {$nombreDifunto} a la ubicación {$descUbicacion}",
            null,
            [
                'difunto_id'     => $difunto->id,
                'codigo_difunto' => $difunto->codigo,
                'difunto'        => $nombreDifunto,
                'fecha_ingreso'  => Carbon::today()->toDateString(),
                'ubicacion'      => $descUbicacion,
            ]
        );

        return redirect()->route('mapa.index')
            ->with('success', 'Difunto asociado a la ubicación correctamente.');
    }

    /**
     * Retira un difunto de una ubicación cerrando su registro activo en ubicacion_difunto.
     *
     * Establece fecha_salida = hoy y activo = false, lo que dispara el cálculo de
     * estado "Mantenimiento" en el accessor por los siguientes 14 días.
     *
     * Acceso: solo Administrador.
     */
    public function retirar(Request $request, Ubicacion $ubicacion): RedirectResponse
    {
        $validated = $request->validate([
            'difunto_id' => ['required', 'exists:difuntos,id'],
        ]);

        // Buscar el registro activo de ese difunto en esta ubicación
        $registro = UbicacionDifunto::where('ubicacion_id', $ubicacion->id)
            ->where('difunto_id', $validated['difunto_id'])
            ->where('activo', true)
            ->first();

        if (!$registro) {
            return back()->with('error', 'No se encontró un registro activo de ese difunto en esta ubicación.');
        }

        // Cerrar el registro: fecha_salida = hoy, activo = false
        $registro->fecha_salida = Carbon::today();
        $registro->activo = false;
        $registro->save();

        $difunto = Difunto::find($validated['difunto_id']);
        $codigoBloque = $ubicacion->bloque->codigo ?? 'B?';
        $descUbicacion = "Bloque {$codigoBloque}-{$ubicacion->lado}-C{$ubicacion->columna}-F{$ubicacion->fila}-{$ubicacion->tipo}-{$ubicacion->numero}";
        $nombreDifunto = $difunto ? "{$difunto->nombre} {$difunto->apellido_paterno} {$difunto->apellido_materno}" : "ID {$validated['difunto_id']}";

        Historial::registrar(
            'ubicaciones',
            $ubicacion->id,
            'Retirar',
            "Se retiró al difunto {$nombreDifunto} de la ubicación {$descUbicacion}",
            [
                'activo'       => true,
                'fecha_salida' => null,
            ],
            [
                'activo'       => false,
                'fecha_salida' => Carbon::today()->toDateString(),
                'difunto'      => $nombreDifunto,
                'ubicacion'    => $descUbicacion,
            ]
        );

        return redirect()->route('mapa.index')
            ->with('success', 'Difunto retirado de la ubicación. La ubicación queda en período de mantenimiento (14 días).');
    }
}