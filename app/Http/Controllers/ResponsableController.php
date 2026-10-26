<?php

namespace App\Http\Controllers;

use App\Models\Responsable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResponsableController extends Controller
{
    public function index()
    {
        return view('responsables.index', [
            'responsables' => Responsable::withCount('equipos')->orderBy('nombre_completo')->get(),
        ]);
    }

    public function create()
    {
        return view('responsables.form', ['responsable' => new Responsable(['activo' => true])]);
    }

    public function store(Request $request)
    {
        Responsable::create($this->validar($request));

        return redirect()->route('responsables.index')->with('exito', 'Responsable creado correctamente.');
    }

    public function edit(Responsable $responsable)
    {
        return view('responsables.form', ['responsable' => $responsable]);
    }

    public function update(Request $request, Responsable $responsable)
    {
        $responsable->update($this->validar($request, $responsable));

        return redirect()->route('responsables.index')->with('exito', 'Responsable actualizado correctamente.');
    }

    public function destroy(Responsable $responsable)
    {
        // Los equipos que tenía quedan sin responsable (ON DELETE SET NULL)
        $responsable->delete();

        return redirect()->route('responsables.index')->with('exito', 'Responsable eliminado.');
    }

    private function validar(Request $request, ?Responsable $responsable = null): array
    {
        $datos = $request->validate([
            'nombre_completo' => 'required|max:120',
            'identificacion'  => ['required', 'max:20', Rule::unique('responsables', 'identificacion')->ignore($responsable?->id)],
            'cargo'           => 'nullable|max:80',
            'dependencia'     => 'nullable|max:80',
            'correo'          => 'nullable|email|max:120',
            'telefono'        => 'nullable|max:20',
        ]);

        $datos['activo'] = $request->boolean('activo');

        return $datos;
    }
}
