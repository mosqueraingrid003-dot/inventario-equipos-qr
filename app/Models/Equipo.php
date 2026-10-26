<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipo extends Model
{
    public const ESTADOS = ['Activo', 'En mantenimiento', 'Dado de baja'];

    protected $table = 'equipos';

    protected $fillable = [
        'codigo_inventario', 'numero_serie', 'marca', 'modelo',
        'tipo_equipo_id', 'ubicacion_id', 'responsable_id',
        'fecha_adquisicion', 'estado', 'especificaciones', 'observaciones',
    ];

    protected $casts = ['fecha_adquisicion' => 'date'];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoEquipo::class, 'tipo_equipo_id');
    }

    public function ubicacion(): BelongsTo
    {
        return $this->belongsTo(Ubicacion::class, 'ubicacion_id');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(Responsable::class, 'responsable_id');
    }

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class, 'equipo_id');
    }

    /** Años completos desde la fecha de adquisición. */
    public function antiguedadAnios(): int
    {
        return $this->fecha_adquisicion->diff(now())->y;
    }

    /** Número de fallas registradas dentro de la ventana de meses configurada. */
    public function fallasRecientes(): int
    {
        $meses = Parametro::numero('ventana_meses', 12);

        return $this->mantenimientos()
            ->where('es_falla', 1)
            ->where('fecha', '>=', now()->subMonths($meses)->toDateString())
            ->count();
    }

    /**
     * Regla de obsolescencia (sin inteligencia artificial):
     * un equipo no dado de baja es obsoleto si su antigüedad >= vida útil
     * o si tiene >= N fallas en la ventana de meses. Los límites están en la tabla parametros.
     */
    public function alertaObsolescencia(): array
    {
        $vidaUtil = Parametro::numero('vida_util_anios', 5);
        $limiteFallas = Parametro::numero('limite_fallas', 3);
        $ventana = Parametro::numero('ventana_meses', 12);

        $antiguedad = $this->antiguedadAnios();
        $fallas = $this->fallasRecientes();
        $motivos = [];

        if ($this->estado !== 'Dado de baja') {
            if ($antiguedad >= $vidaUtil) {
                $motivos[] = "Antigüedad de {$antiguedad} años (límite: {$vidaUtil})";
            }
            if ($fallas >= $limiteFallas) {
                $motivos[] = "{$fallas} fallas en los últimos {$ventana} meses (límite: {$limiteFallas})";
            }
        }

        return [
            'obsoleto'   => count($motivos) > 0,
            'motivos'    => $motivos,
            'antiguedad' => $antiguedad,
            'fallas'     => $fallas,
        ];
    }
}
