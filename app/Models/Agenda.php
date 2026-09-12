<?php
//agenda.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agenda extends Model
{
    use HasFactory;

    protected $table = 'agendas';

    protected $fillable = [
        'codigo',
        'tipo',
        'difunto_id',
        'ubicacion_id',
        'panteonero_id',
        'fecha',
        'hora',
        'estado',
        'observaciones',
        'usuario_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha' => 'date',
        ];
    }

    /**
     * Difunto asociado a la actividad de agenda.
     */
    public function difunto(): BelongsTo
    {
        return $this->belongsTo(Difunto::class);
    }

    /**
     * Ubicación asociada a la actividad de agenda (opcional/nullable).
     */
    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class);
    }

    /**
     * Panteonero asignado a la actividad (opcional/nullable).
     */
    public function panteonero(): BelongsTo
    {
        return $this->belongsTo(Panteonero::class);
    }

    /**
     * Usuario que registró o administra esta actividad.
     */
    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
