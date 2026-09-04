<?php

namespace Database\Seeders;

use App\Models\Bloque;
use App\Models\Ubicacion;
use Illuminate\Database\Seeder;

class UbicacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Carga las 130 ubicaciones reales del Bloque 18 / Lado Norte.
     * Se genera mediante un bucle aplicando la regla de numeración verificada.
     */
    public function run(): void
    {
        $bloque18 = Bloque::where('codigo', '18')->firstOrFail();

        // Parámetros verificados para Bloque 18, Lado Norte
        $totalFilas = 5;
        $columnasPorFila = 26;
        $lado = 'Norte';
        $tipo = 'Nicho';

        // Recorrer filas (1 a 5) y columnas (1 a 26)
        for ($fila = 1; $fila <= $totalFilas; $fila++) {
            for ($columna = 1; $columna <= $columnasPorFila; $columna++) {
                
                /*
                 * Fórmula de numeración oficial del Bloque 18 (Lado Norte):
                 * La numeración comienza en la fila 5 (nichos 1 a 26) y sube hacia la fila 1 (nichos 105 a 130).
                 * numero = (total_filas - fila) * columnas_por_fila + columna
                 */
                $numero = ($totalFilas - $fila) * $columnasPorFila + $columna;

                Ubicacion::updateOrCreate(
                    [
                        'bloque_id' => $bloque18->id,
                        'lado' => $lado,
                        'columna' => $columna,
                        'fila' => $fila,
                        'tipo' => $tipo,
                        'numero' => $numero,
                    ],
                    [
                        'capacidad' => 1, // Nicho = 1 (se autogenera también en el modelo)
                        'activo' => true,
                    ]
                );
            }
        }
    }
}
