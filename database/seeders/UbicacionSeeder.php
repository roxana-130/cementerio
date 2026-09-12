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
     * Carga las ubicaciones reales de los Bloques A1 (160 ubicaciones) y A2
     * (232 ubicaciones), verificadas en docs/datos reales/bloques_a1_a2.md.
     * El Bloque 18 fue descartado y ya no se siembra.
     *
     * La numeración es INDEPENDIENTE por lado: cada lado empieza su propia
     * numeración en 1 (ej. A1-Sur y A1-Norte ambos tienen números 1 a 76).
     * La combinación Bloque+Lado+Columna+Fila+Tipo+Numero sigue siendo única
     * porque el Lado es distinto.
     */
    public function run(): void
    {
        $a1 = Bloque::where('codigo', 'A1')->firstOrFail();
        $a2 = Bloque::where('codigo', 'A2')->firstOrFail();

        // ---- Bloque A1 ----
        $this->sembrarLado($a1, 'Sur', columnas: 19, filas: 4, tipo: 'Nicho');
        $this->sembrarLado($a1, 'Norte', columnas: 19, filas: 4, tipo: 'Nicho');
        $this->sembrarLado($a1, 'Este', columnas: 4, filas: 2, tipo: 'Mausoleo');
        // A1-Oeste: sin datos reales todavia, no se siembra (ver bloques_a1_a2.md).

        // ---- Bloque A2 ----
        $this->sembrarLado($a2, 'Sur', columnas: 22, filas: 5, tipo: 'Nicho');
        $this->sembrarLado($a2, 'Norte', columnas: 22, filas: 5, tipo: 'Nicho');
        $this->sembrarLado($a2, 'Este', columnas: 4, filas: 2, tipo: 'Mausoleo');
        $this->sembrarLado($a2, 'Oeste', columnas: 2, filas: 2, tipo: 'Mausoleo');
    }

    /**
     * Siembra todas las ubicaciones de un lado de un bloque, aplicando la
     * formula de numeracion verificada (igual que en el Bloque 18):
     *
     *   numero = (total_filas - fila) * columnas + columna
     *
     * La numeracion comienza en la ultima fila (numeros mas bajos) y sube
     * hacia la fila 1.
     */
    private function sembrarLado(Bloque $bloque, string $lado, int $columnas, int $filas, string $tipo): void
    {
        $capacidad = $tipo === 'Mausoleo' ? 5 : 1;

        for ($fila = 1; $fila <= $filas; $fila++) {
            for ($columna = 1; $columna <= $columnas; $columna++) {
                $numero = ($filas - $fila) * $columnas + $columna;

                Ubicacion::updateOrCreate(
                    [
                        'bloque_id' => $bloque->id,
                        'lado' => $lado,
                        'columna' => $columna,
                        'fila' => $fila,
                        'tipo' => $tipo,
                        'numero' => $numero,
                    ],
                    [
                        'capacidad' => $capacidad,
                        'activo' => true,
                    ]
                );
            }
        }
    }
}
