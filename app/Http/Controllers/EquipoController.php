<?php

namespace App\Http\Controllers;

use App\Models\Equipo;
use App\Models\Responsable;
use App\Models\TipoEquipo;
use App\Models\Ubicacion;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EquipoController extends Controller
{
    /** Listado con búsqueda y filtros. */
    public function index(Request $request)
    {
        $consulta = Equipo::with(['tipo', 'ubicacion', 'responsable']);

        if ($request->filled('q')) {
            $texto = $request->q;
            $consulta->where(function ($w) use ($texto) {
                $w->where('codigo_inventario', 'like', "%{$texto}%")
                  ->orWhere('numero_serie', 'like', "%{$texto}%")
                  ->orWhere('marca', 'like', "%{$texto}%")
                  ->orWhere('modelo', 'like', "%{$texto}%");
            });
        }
        if ($request->filled('ubicacion_id')) {
            $consulta->where('ubicacion_id', $request->ubicacion_id);
        }
        if ($request->filled('tipo_equipo_id')) {
            $consulta->where('tipo_equipo_id', $request->tipo_equipo_id);
        }
        if ($request->filled('estado')) {
            $consulta->where('estado', $request->estado);
        }

        return view('equipos.index', [
            'equipos'     => $consulta->orderBy('codigo_inventario')->paginate(10)->withQueryString(),
            'ubicaciones' => Ubicacion::orderBy('nombre')->get(),
            'tipos'       => TipoEquipo::orderBy('nombre')->get(),
            'estados'     => Equipo::ESTADOS,
        ]);
    }

    public function create()
    {
        // Sugerimos el siguiente código de inventario (EQ-0001, EQ-0002...)
        $siguiente = 'EQ-' . str_pad((string) ((Equipo::max('id') ?? 0) + 1), 4, '0', STR_PAD_LEFT);

        return view('equipos.form', $this->datosFormulario(
            new Equipo(['codigo_inventario' => $siguiente, 'estado' => 'Activo'])
        ));
    }

    public function store(Request $request)
    {
        $equipo = Equipo::create($this->validar($request));

        return redirect()->route('equipos.show', $equipo)
            ->with('exito', 'Equipo registrado correctamente.');
    }

    public function show(Equipo $equipo)
    {
        $equipo->load(['tipo', 'ubicacion', 'responsable']);

        return view('equipos.show', [
            'equipo'         => $equipo,
            'mantenimientos' => $equipo->mantenimientos()->with('tecnico')->orderByDesc('fecha')->get(),
            'alerta'         => $equipo->alertaObsolescencia(),
        ]);
    }

    public function edit(Equipo $equipo)
    {
        return view('equipos.form', $this->datosFormulario($equipo));
    }

    public function update(Request $request, Equipo $equipo)
    {
        $equipo->update($this->validar($request, $equipo));

        return redirect()->route('equipos.show', $equipo)
            ->with('exito', 'Equipo actualizado correctamente.');
    }

    /** Cambia el estado a "Dado de baja" (no borra el historial). */
    public function darDeBaja(Equipo $equipo)
    {
        $equipo->update(['estado' => 'Dado de baja']);

        return redirect()->route('equipos.show', $equipo)
            ->with('exito', 'El equipo fue dado de baja.');
    }

    public function destroy(Equipo $equipo)
    {
        $equipo->delete(); // también borra sus mantenimientos (ON DELETE CASCADE)

        return redirect()->route('equipos.index')
            ->with('exito', 'Equipo eliminado.');
    }

    /** Página imprimible con el código QR del equipo. */
    public function qr(Equipo $equipo)
    {
        $equipo->load(['tipo', 'ubicacion']);

        return view('equipos.qr', [
            'equipo' => $equipo,
            // El QR guarda la dirección de la ficha del equipo
            'url'    => route('equipos.show', $equipo),
        ]);
    }

    // ------------------------------------------------------------------

    private function datosFormulario(Equipo $equipo): array
    {
        return [
            'equipo'       => $equipo,
            'tipos'        => TipoEquipo::orderBy('nombre')->get(),
            'ubicaciones'  => Ubicacion::orderBy('sede')->orderBy('nombre')->get(),
            'responsables' => Responsable::where('activo', 1)->orderBy('nombre_completo')->get(),
            'estados'      => Equipo::ESTADOS,
        ];
    }

    private function validar(Request $request, ?Equipo $equipo = null): array
    {
        $id = $equipo?->id;

        return $request->validate([
            'codigo_inventario' => ['required', 'max:20', Rule::unique('equipos', 'codigo_inventario')->ignore($id)],
            'numero_serie'      => ['required', 'max:60', Rule::unique('equipos', 'numero_serie')->ignore($id)],
            'marca'             => 'required|max:50',
            'modelo'            => 'required|max:80',
            'tipo_equipo_id'    => 'required|exists:tipos_equipo,id',
            'ubicacion_id'      => 'required|exists:ubicaciones,id',
            'responsable_id'    => 'nullable|exists:responsables,id',
            'fecha_adquisicion' => 'required|date|before_or_equal:today',
            'estado'            => ['required', Rule::in(Equipo::ESTADOS)],
            'especificaciones'  => 'nullable|max:255',
            'observaciones'     => 'nullable',
        ]);
    }
}
