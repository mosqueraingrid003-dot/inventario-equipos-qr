<?php

namespace App\Http\Controllers;

use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UbicacionController extends Controller
{
    public function index()
    {
        return view('ubicaciones.index', [
            'ubicaciones' => Ubicacion::withCount('equipos')->orderBy('sede')->orderBy('nombre')->get(),
        ]);
    }

    public function create()
    {
        return view('ubicaciones.form', ['ubicacion' => new Ubicacion()]);
    }

    public function store(Request $request)
    {
        Ubicacion::create($this->validar($request));

        return redirect()->route('ubicaciones.index')->with('exito', 'Ubicación creada correctamente.');
    }

    public function edit(Ubicacion $ubicacion)
    {
        return view('ubicaciones.form', ['ubicacion' => $ubicacion]);
    }

    public function update(Request $request, Ubicacion $ubicacion)
    {
        $ubicacion->update($this->validar($request, $ubicacion));

        return redirect()->route('ubicaciones.index')->with('exito', 'Ubicación actualizada correctamente.');
    }

    public function destroy(Ubicacion $ubicacion)
    {
        if ($ubicacion->equipos()->exists()) {
            return back()->with('error', 'No se puede eliminar: la ubicación tiene equipos asociados.');
        }

        $ubicacion->delete();

        return redirect()->route('ubicaciones.index')->with('exito', 'Ubicación eliminada.');
    }

    private function validar(Request $request, ?Ubicacion $ubicacion = null): array
    {
        return $request->validate([
            'nombre' => [
                'required', 'max:80',
                // No se repite el mismo nombre dentro de la misma sede
                Rule::unique('ubicaciones', 'nombre')
                    ->where('sede', $request->sede)
                    ->ignore($ubicacion?->id),
            ],
            'sede'        => 'required|max:80',
            'descripcion' => 'nullable|max:200',
        ]);
    }
}
