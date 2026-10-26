<?php

namespace App\Http\Controllers;

use App\Models\Parametro;
use Illuminate\Http\Request;

class ParametroController extends Controller
{
    public function index()
    {
        return view('parametros.index', [
            'parametros' => Parametro::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request)
    {
        $datos = $request->validate([
            'vida_util_anios' => 'required|integer|min:1|max:50',
            'limite_fallas'   => 'required|integer|min:1|max:50',
            'ventana_meses'   => 'required|integer|min:1|max:120',
        ]);

        foreach ($datos as $clave => $valor) {
            Parametro::where('clave', $clave)->update(['valor' => $valor]);
        }
        Parametro::olvidar();

        return back()->with('exito', 'Parámetros actualizados. Las alertas ya usan los nuevos valores.');
    }
}
