<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Mantenimiento;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MantenimientoController extends Controller
{
    public function index(Request $request)
    {
        $consulta = Mantenimiento::with(['equipo', 'tecnico']);

        if ($request->filled('equipo_id')) {
            $consulta->where('equipo_id', $request->equipo_id);
        }
        if ($request->filled('tipo')) {
            $consulta->where('tipo', $request->tipo);
        }

        return view('mantenimientos.index', [
            'mantenimientos' => $consulta->orderByDesc('fecha')->orderByDesc('id')->paginate(10)->withQueryString(),
            'equipos'        => Equipo::orderBy('codigo_inventario')->get(),
            'tipos'          => Mantenimiento::TIPOS,
        ]);
    }

    public function create(Request $request)
    {
        // Si venimos desde la ficha de un equipo, llega ?equipo_id=...
        $mantenimiento = new Mantenimiento([
            'equipo_id' => $request->query('equipo_id'),
            'fecha'     => now()->toDateString(),
            'tipo'      => 'Preventivo',
        ]);

        return view('mantenimientos.form', $this->datosFormulario($mantenimiento));
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $datos['user_id'] = $request->user()->id;   // técnico que lo registra

        Mantenimiento::create($datos);

        return redirect()->route('equipos.show', $datos['equipo_id'])
            ->with('exito', 'Mantenimiento registrado correctamente.');
    }

    public function edit(Mantenimiento $mantenimiento)
    {
        return view('mantenimientos.form', $this->datosFormulario($mantenimiento));
    }

    public function update(Request $request, Mantenimiento $mantenimiento)
    {
        $mantenimiento->update($this->validar($request));

        return redirect()->route('equipos.show', $mantenimiento->equipo_id)
            ->with('exito', 'Mantenimiento actualizado correctamente.');
    }

    public function destroy(Mantenimiento $mantenimiento)
    {
        $equipoId = $mantenimiento->equipo_id;
        $mantenimiento->delete();

        return redirect()->route('equipos.show', $equipoId)
            ->with('exito', 'Mantenimiento eliminado.');
    }

    private function datosFormulario(Mantenimiento $mantenimiento): array
    {
        return [
            'mantenimiento' => $mantenimiento,
            'equipos'       => Equipo::orderBy('codigo_inventario')->get(),
            'tipos'         => Mantenimiento::TIPOS,
        ];
    }

    private function validar(Request $request): array
    {
        $datos = $request->validate([
            'equipo_id'   => 'required|exists:equipos,id',
            'fecha'       => 'required|date|before_or_equal:today',
            'tipo'        => ['required', Rule::in(Mantenimiento::TIPOS)],
            'descripcion' => 'required',
            'costo'       => 'nullable|numeric|min:0',
        ]);

        $datos['es_falla'] = $request->boolean('es_falla');   // casilla marcada = 1
        $datos['costo'] = $datos['costo'] ?? 0;

        return $datos;
    }
}
