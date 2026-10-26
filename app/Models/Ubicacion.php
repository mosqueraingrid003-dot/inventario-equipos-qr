<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ubicacion extends Model
{
    protected $table = 'ubicaciones';

    protected $fillable = ['nombre', 'sede', 'descripcion'];

    public function equipos(): HasMany
    {
        return $this->hasMany(Equipo::class, 'ubicacion_id');
    }
}
