<?php

namespace Database\Seeders;

use App\Models\Bloque;
use Illuminate\Database\Seeder;

class BloqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Carga el Bloque 18 verificado en los datos reales del Cementerio General de Sacaba.
     * No se crean ni inventan bloques adicionales.
     */
    public function run(): void
    {
        Bloque::updateOrCreate(
            ['codigo' => '18'],
            [
                'nombre' => 'Bloque 18',
                'descripcion' => 'Bloque 18 - Nichos y Mausoleos (Datos reales verificados)',
                'activo' => true,
            ]
        );
    }
}
