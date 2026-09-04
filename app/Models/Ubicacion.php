<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class Ubicacion extends Model
{
    use HasFactory;

    protected $table = 'ubicaciones';

    protected $fillable = [
        'bloque_id',
        'lado',
        'columna',
        'fila',
        'tipo',
        'numero',
        'capacidad',
        'geom_geojson',
        'centro_lat',
        'centro_lng',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'columna' => 'integer',
            'fila' => 'integer',
            'numero' => 'integer',
            'capacidad' => 'integer',
            'geom_geojson' => 'array',
            'centro_lat' => 'decimal:7',
            'centro_lng' => 'decimal:7',
            'activo' => 'boolean',
        ];
    }

    /**
     * Evento al guardar: autogenera la capacidad según el tipo de espacio.
     * Nicho = 1, Mausoleo = 5.
     */
    protected static function booted(): void
    {
        static::saving(function (Ubicacion $ubicacion) {
            if ($ubicacion->tipo === 'Nicho') {
                $ubicacion->capacidad = 1;
            } elseif ($ubicacion->tipo === 'Mausoleo') {
                $ubicacion->capacidad = 5;
            }
        });
    }

    /**
     * Bloque al que pertenece la ubicación.
     */
    public function bloque(): BelongsTo
    {
        return $this->belongsTo(Bloque::class);
    }

    /**
     * Difuntos asociados a la ubicación a través del pivote ubicacion_difunto.
     */
    public function difuntos(): BelongsToMany
    {
        return $this->belongsToMany(Difunto::class, 'ubicacion_difunto')
            ->withPivot(['id', 'fecha_ingreso', 'fecha_salida', 'activo'])
            ->withTimestamps();
    }

    /**
     * Registros de la tabla pivote explícita ubicacion_difunto.
     */
    public function ubicacionDifuntos(): HasMany
    {
        return $this->hasMany(UbicacionDifunto::class, 'ubicacion_id');
    }

    /**
     * Agendas asociadas a esta ubicación.
     */
    public function agendas(): HasMany
    {
        return $this->hasMany(Agenda::class);
    }

    /**
     * Accessor para el estado actual (calculado, no almacenado en base de datos).
     * 
     * Lógica:
     * 1. Si existe un registro activo (activo = true) en ubicacion_difunto -> "Ocupado"
     * 2. Si no existe registro activo, pero el último registro deshabilitado (activo = false)
     *    tiene fecha_salida de menos de 14 días atrás desde hoy -> "Mantenimiento"
     * 3. En cualquier otro caso -> "Disponible"
     */
    public function getEstadoActualAttribute(): string
    {
        // 1. Verificar si hay un registro de ocupación activo para esta ubicación
        $tieneOcupanteActivo = $this->ubicacionDifuntos()
            ->where('activo', true)
            ->exists();

        if ($tieneOcupanteActivo) {
            return 'Ocupado';
        }

        // 2. Buscar el último registro finalizado (activo = false) con fecha_salida
        $ultimoRegistro = $this->ubicacionDifuntos()
            ->where('activo', false)
            ->whereNotNull('fecha_salida')
            ->orderBy('fecha_salida', 'desc')
            ->first();

        if ($ultimoRegistro && $ultimoRegistro->fecha_salida) {
            $fechaSalida = Carbon::parse($ultimoRegistro->fecha_salida)->startOfDay();
            $hoy = Carbon::now()->startOfDay();

            // Días transcurridos desde la fecha de salida hasta hoy
            $diasDesdeSalida = $fechaSalida->diffInDays($hoy, false);

            // Si la fecha de salida ocurrió en los últimos 14 días (0 <= días < 14)
            if ($diasDesdeSalida >= 0 && $diasDesdeSalida < 14) {
                return 'Mantenimiento';
            }
        }

        // 3. En cualquier otro caso
        return 'Disponible';
    }
}
