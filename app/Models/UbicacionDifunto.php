<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UbicacionDifunto extends Model
{
    use HasFactory;

    protected $table = 'ubicacion_difunto';

    protected $fillable = [
        'ubicacion_id',
        'difunto_id',
        'fecha_ingreso',
        'fecha_salida',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'fecha_ingreso' => 'date',
            'fecha_salida' => 'date',
            'activo' => 'boolean',
        ];
    }

    /**
     * Ubicación asociada.
     */
    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class);
    }

    /**
     * Difunto asociado.
     */
    public function difunto(): BelongsTo
    {
        return $this->belongsTo(Difunto::class);
    }
}
