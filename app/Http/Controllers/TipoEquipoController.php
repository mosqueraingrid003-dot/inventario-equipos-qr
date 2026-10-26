<?php

namespace App\Http\Controllers;

use App\Models\TipoEquipo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TipoEquipoController extends Controller
{
    public function index()
    {
        return view('tipos.index', [
            'tipos' => TipoEquipo::withCount('equipos')->orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        return view('tipos.form', ['tipo' => new TipoEquipo()]);
    }

    public function store(Request $request)
    {
        TipoEquipo::create($this->validar($request));

        return redirect()->route('tipos.index')->with('exito', 'Tipo de equipo creado correctamente.');
    }

    public function edit(TipoEquipo $tipo)
    {
        return view('tipos.form', ['tipo' => $tipo]);
    }

    public function update(Request $request, TipoEquipo $tipo)
    {
        $tipo->update($this->validar($request, $tipo));

        return redirect()->route('tipos.index')->with('exito', 'Tipo de equipo actualizado correctamente.');
    }

    public function destroy(TipoEquipo $tipo)
    {
        if ($tipo->equipos()->exists()) {
            return back()->with('error', 'No se puede eliminar: hay equipos de este tipo.');
        }

        $tipo->delete();

        return redirect()->route('tipos.index')->with('exito', 'Tipo de equipo eliminado.');
    }

    private function validar(Request $request, ?TipoEquipo $tipo = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'max:50', Rule::unique('tipos_equipo', 'nombre')->ignore($tipo?->id)],
        ]);
    }
}
