<?php

namespace Database\Seeders;

use App\Models\Bloque;
use Illuminate\Database\Seeder;

class BloqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Carga los bloques A1 y A2, verificados en docs/datos reales/bloques_a1_a2.md.
     * El Bloque 18 fue descartado (ya no existe en el plano real) y no se siembra más.
     * No se crean ni inventan bloques adicionales (A3, A5, A6 aparecen en el plano
     * pero todavía no tienen relevamiento de filas/columnas).
     */
    public function run(): void
    {
        Bloque::updateOrCreate(
            ['codigo' => 'A1'],
            [
                'nombre' => 'Bloque A1',
                'descripcion' => 'Bloque A1 - Nichos (Norte/Sur) y Mausoleos (Este) - Datos reales verificados',
                'activo' => true,
            ]
        );

        Bloque::updateOrCreate(
            ['codigo' => 'A2'],
            [
                'nombre' => 'Bloque A2',
                'descripcion' => 'Bloque A2 - Nichos (Norte/Sur) y Mausoleos (Este/Oeste) - Datos reales verificados',
                'activo' => true,
            ]
        );
    }
}
