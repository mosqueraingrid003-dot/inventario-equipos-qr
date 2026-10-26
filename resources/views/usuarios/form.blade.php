@extends('layouts.app')
@section('titulo', $usuario->exists ? 'Editar usuario' : 'Nuevo usuario')

@section('contenido')
<h1 class="h3 mb-3">{{ $usuario->exists ? 'Editar usuario' : 'Nuevo usuario' }}</h1>

<form method="POST" class="card card-body shadow-sm"
      action="{{ $usuario->exists ? route('usuarios.update', $usuario) : route('usuarios.store') }}">
    @csrf
    @if($usuario->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $usuario->name) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Correo *</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $usuario->email) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Contraseña {{ $usuario->exists ? '(déjala vacía para no cambiarla)' : '*' }}</label>
            <input type="password" name="password" class="form-control" minlength="8" {{ $usuario->exists ? '' : 'required' }}>
        </div>
        <div class="col-md-6">
            <label class="form-label">Confirmar contraseña</label>
            <input type="password" name="password_confirmation" class="form-control" minlength="8">
        </div>
        <div class="col-md-6">
            <label class="form-label">Rol *</label>
            <select name="role_id" class="form-select" required>
                @foreach($roles as $r)
                    <option value="{{ $r->id }}" @selected(old('role_id', $usuario->role_id) == $r->id)>{{ $r->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-6 d-flex align-items-end">
            <div class="form-check">
                <input type="hidden" name="activo" value="0">
                <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" @checked(old('activo', $usuario->activo))>
                <label class="form-check-label" for="activo">Usuario activo (puede iniciar sesión)</label>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <a class="btn btn-outline-secondary" href="{{ route('usuarios.index') }}">Cancelar</a>
    </div>
</form>
@endsection
