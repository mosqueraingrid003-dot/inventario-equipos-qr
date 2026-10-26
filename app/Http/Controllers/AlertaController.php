<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Parametro;

class AlertaController extends Controller
{
    /** Lista los equipos que cumplen la regla de obsolescencia. */
    public function index()
    {
        $alertas = Equipo::with(['tipo', 'ubicacion'])
            ->where('estado', '!=', 'Dado de baja')
            ->orderBy('fecha_adquisicion')
            ->get()
            ->map(fn ($equipo) => ['equipo' => $equipo, 'alerta' => $equipo->alertaObsolescencia()])
            ->filter(fn ($fila) => $fila['alerta']['obsoleto'])
            ->values();

        return view('alertas.index', [
            'alertas'   => $alertas,
            'vidaUtil'  => Parametro::numero('vida_util_anios', 5),
            'fallas'    => Parametro::numero('limite_fallas', 3),
            'ventana'   => Parametro::numero('ventana_meses', 12),
        ]);
    }
}
