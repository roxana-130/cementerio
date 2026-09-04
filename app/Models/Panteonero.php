<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Panteonero extends Model
{
    use HasFactory;

    protected $table = 'panteoneros';

    protected $fillable = [
        'nombre',
        'apellido_paterno',
        'apellido_materno',
        'ci',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Agendas asignadas a este panteonero.
     */
    public function agendas(): HasMany
    {
        return $this->hasMany(Agenda::class);
    }
}
