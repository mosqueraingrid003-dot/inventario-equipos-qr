@extends('layouts.app')
@section('titulo', 'Usuarios')

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Usuarios del sistema</h1>
    <a href="{{ route('usuarios.create') }}" class="btn btn-primary">+ Nuevo usuario</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Nombre</th><th>Correo</th><th>Rol</th><th>Activo</th><th></th></tr></thead>
            <tbody>
            @foreach($usuarios as $u)
                <tr>
                    <td class="fw-semibold">{{ $u->name }}</td>
                    <td>{{ $u->email }}</td>
                    <td><span class="badge bg-primary">{{ $u->role->nombre }}</span></td>
                    <td>{!! $u->activo ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('usuarios.edit', $u) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        @if($u->id !== auth()->id())
                            <form method="POST" action="{{ route('usuarios.destroy', $u) }}" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este usuario?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
