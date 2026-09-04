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
        Schema::create('difuntos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('nombre', 100);
            $table->string('apellido_paterno', 100);
            $table->string('apellido_materno', 100);
            $table->string('apellido_casada', 100)->nullable();
            $table->string('ci', 20); // Uso interno, no visible en interfaz pública
            $table->integer('edad')->nullable();
            $table->string('profesion_ocupacion', 150)->nullable();
            $table->text('causa_muerte')->nullable();
            $table->date('fecha_fallecimiento');
            $table->time('hora_fallecimiento')->nullable();
            $table->string('numero_certificado_defuncion', 50)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('difuntos');
    }
};
