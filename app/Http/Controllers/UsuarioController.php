<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    public function index()
    {
        return view('usuarios.index', [
            'usuarios' => User::with('role')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return view('usuarios.form', [
            'usuario' => new User(['activo' => true]),
            'roles'   => Role::orderBy('id')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'name'     => 'required|max:100',
            'email'    => 'required|email|max:120|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role_id'  => 'required|exists:roles,id',
        ]);

        $datos['password'] = Hash::make($datos['password']);   // la contraseña se guarda cifrada
        $datos['activo'] = $request->boolean('activo');

        User::create($datos);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario creado correctamente.');
    }

    public function edit(User $usuario)
    {
        return view('usuarios.form', [
            'usuario' => $usuario,
            'roles'   => Role::orderBy('id')->get(),
        ]);
    }

    public function update(Request $request, User $usuario)
    {
        $datos = $request->validate([
            'name'     => 'required|max:100',
            'email'    => ['required', 'email', 'max:120', Rule::unique('users', 'email')->ignore($usuario->id)],
            'password' => 'nullable|min:8|confirmed',   // si se deja vacía, no cambia
            'role_id'  => 'required|exists:roles,id',
        ]);

        if (! empty($datos['password'])) {
            $datos['password'] = Hash::make($datos['password']);
        } else {
            unset($datos['password']);
        }

        $datos['activo'] = $request->boolean('activo');

        // Evita que el administrador se quite el acceso a sí mismo
        if ($usuario->id === $request->user()->id) {
            $datos['activo'] = true;
            $datos['role_id'] = $usuario->role_id;
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')->with('exito', 'Usuario actualizado correctamente.');
    }

    public function destroy(Request $request, User $usuario)
    {
        if ($usuario->id === $request->user()->id) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }
        if ($usuario->mantenimientos()->exists()) {
            return back()->with('error', 'No se puede eliminar: el usuario registró mantenimientos. Desactívalo en su lugar.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')->with('exito', 'Usuario eliminado.');
    }
}
