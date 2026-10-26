<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Ubicacion;

class DashboardController extends Controller
{
    public function index()
    {
        $equipos = Equipo::where('estado', '!=', 'Dado de baja')->get();
        $conAlerta = $equipos->filter(fn ($e) => $e->alertaObsolescencia()['obsoleto'])->count();

        return view('dashboard', [
            'total'        => Equipo::count(),
            'activos'      => Equipo::where('estado', 'Activo')->count(),
            'enMantenim'   => Equipo::where('estado', 'En mantenimiento')->count(),
            'dadosDeBaja'  => Equipo::where('estado', 'Dado de baja')->count(),
            'conAlerta'    => $conAlerta,
            'ubicaciones'  => Ubicacion::withCount('equipos')->orderBy('sede')->orderBy('nombre')->get(),
        ]);
    }
}
