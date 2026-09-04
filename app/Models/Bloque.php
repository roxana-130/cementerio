<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Bloque extends Model
{
    use HasFactory;

    protected $table = 'bloques';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'activo',
    ];

    protected function casts(): array
    {
        return [
            'activo' => 'boolean',
        ];
    }

    /**
     * Ubicaciones físicas pertenecientes a este bloque.
     */
    public function ubicaciones(): HasMany
    {
        return $this->hasMany(Ubicacion::class);
    }
}
