<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Responsable extends Model
{
    protected $table = 'responsables';

    protected $fillable = [
        'nombre_completo', 'identificacion', 'cargo',
        'dependencia', 'correo', 'telefono', 'activo',
    ];

    protected $casts = ['activo' => 'boolean'];

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'responsable_id');
    }
}
