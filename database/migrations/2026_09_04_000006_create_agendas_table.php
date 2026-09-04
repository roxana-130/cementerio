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
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 30)->unique();
            $table->string('tipo', 20); // Inhumación / Exhumación / Cremación / Anexión
            $table->foreignId('difunto_id')->constrained('difuntos')->cascadeOnDelete();
            $table->foreignId('ubicacion_id')->nullable()->constrained('ubicaciones')->nullOnDelete();
            $table->foreignId('panteonero_id')->nullable()->constrained('panteoneros')->nullOnDelete();
            $table->date('fecha');
            $table->time('hora');
            $table->string('estado', 20)->default('Pendiente'); // Pendiente / Realizado / Cancelado
            $table->text('observaciones')->nullable();
            $table->foreignId('usuario_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
