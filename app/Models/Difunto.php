<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Difunto extends Model
{
    use HasFactory;

    protected $table = 'difuntos';

    protected $fillable = [
        'codigo',
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'apellido_casada',
        'ci',
        'edad',
        'profesion_ocupacion',
        'causa_muerte',
        'fecha_fallecimiento',
        'hora_fallecimiento',
        'numero_certificado_defuncion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_fallecimiento' => 'date',
            'edad' => 'integer',
            'activo' => 'boolean',
        ];
    }

    /**
     * Ubicaciones asociadas a este difunto.
     */
    public function ubicaciones(): BelongsToMany
    {
        return $this->belongsToMany(Ubicacion::class, 'ubicacion_difunto')
            ->withPivot(['id', 'fecha_ingreso', 'fecha_salida', 'activo'])
            ->withTimestamps();
    }

    /**
     * Agendas o actividades programadas para este difunto.
     */
    public function agendas(): HasMany
    {
        return $this->hasMany(Agenda::class);
    }
}
