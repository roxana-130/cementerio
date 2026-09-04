<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bloque_id')->constrained('bloques')->cascadeOnDelete();
            $table->string('lado', 10); // Norte / Sur / Este / Oeste
            $table->integer('columna');
            $table->integer('fila');
            $table->string('tipo', 20); // Nicho / Mausoleo
            $table->integer('numero');
            $table->integer('capacidad'); // Autogenerado: 1 (Nicho), 5 (Mausoleo)
            $table->json('geom_geojson')->nullable();
            $table->decimal('centro_lat', 10, 7)->nullable();
            $table->decimal('centro_lng', 10, 7)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();

            // Restricción única compuesta para identificar unívocamente un espacio físico
            $table->unique(['bloque_id', 'lado', 'columna', 'fila', 'tipo', 'numero'], 'ubicaciones_fisicas_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubicaciones');
    }
};
