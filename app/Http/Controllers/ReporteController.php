<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\TipoEquipo;

class ReporteController extends Controller
{
    public function index()
    {
        // Existencias por ubicación y estado
        $existencias = Equipo::selectRaw('ubicaciones.sede, ubicaciones.nombre as ubicacion, equipos.estado, COUNT(*) as total')
            ->join('ubicaciones', 'ubicaciones.id', '=', 'equipos.ubicacion_id')
            ->groupBy('ubicaciones.sede', 'ubicaciones.nombre', 'equipos.estado')
            ->orderBy('ubicaciones.sede')
            ->orderBy('ubicaciones.nombre')
            ->get();

        // Existencias por tipo de equipo
        $porTipo = TipoEquipo::withCount('equipos')->orderBy('nombre')->get();

        return view('reportes.index', [
            'existencias' => $existencias,
            'porTipo'     => $porTipo,
            'total'       => Equipo::count(),
        ]);
    }

    /** Descarga el inventario completo en CSV (se abre con Excel). */
    public function exportarCsv()
    {
        $nombreArchivo = 'inventario_equipos_' . now()->format('Ymd') . '.csv';

        return response()->streamDownload(function () {
            $salida = fopen('php://output', 'w');
            fwrite($salida, "\xEF\xBB\xBF");   // para que Excel respete las tildes

            fputcsv($salida, [
                'Código', 'Serie', 'Marca', 'Modelo', 'Tipo', 'Ubicación', 'Sede',
                'Responsable', 'Fecha de adquisición', 'Estado',
            ], ';', '"', '\\');

            Equipo::with(['tipo', 'ubicacion', 'responsable'])
                ->orderBy('codigo_inventario')
                ->chunk(100, function ($equipos) use ($salida) {
                    foreach ($equipos as $e) {
                        fputcsv($salida, [
                            $e->codigo_inventario,
                            $e->numero_serie,
                            $e->marca,
                            $e->modelo,
                            $e->tipo->nombre,
                            $e->ubicacion->nombre,
                            $e->ubicacion->sede,
                            $e->responsable->nombre_completo ?? '',
                            $e->fecha_adquisicion->format('Y-m-d'),
                            $e->estado,
                        ], ';', '"', '\\');
                    }
                });

            fclose($salida);
        }, $nombreArchivo, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
