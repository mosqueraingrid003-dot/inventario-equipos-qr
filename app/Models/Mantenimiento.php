<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mantenimiento extends Model
{
    public const TIPOS = ['Preventivo', 'Correctivo'];

    protected $table = 'mantenimientos';

    protected $fillable = [
        'equipo_id', 'user_id', 'fecha', 'tipo', 'es_falla', 'descripcion', 'costo',
    ];

    protected $casts = [
        'fecha'    => 'date',
        'es_falla' => 'boolean',
        'costo'    => 'decimal:2',
    ];

    public function equipo(): BelongsTo
    {
        return $this->belongsTo(Equipo::class, 'equipo_id');
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
