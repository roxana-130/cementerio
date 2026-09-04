<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Historial extends Model
{
    use HasFactory;

    protected $table = 'historial';

    // Tabla inmutable, solo registra created_at (sin updated_at)
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'tabla',
        'registro_id',
        'accion',
        'descripcion',
        'datos_anteriores',
        'datos_nuevos',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'datos_anteriores' => 'array',
            'datos_nuevos' => 'array',
            'created_at' => 'datetime',
        ];
    }

    /**
     * Usuario que ejecutó la acción registrada.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
