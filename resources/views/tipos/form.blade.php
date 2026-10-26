@extends('layouts.app')
@section('titulo', $tipo->exists ? 'Editar tipo' : 'Nuevo tipo')

@section('contenido')
<h1 class="h3 mb-3">{{ $tipo->exists ? 'Editar tipo de equipo' : 'Nuevo tipo de equipo' }}</h1>

<form method="POST" class="card card-body shadow-sm"
      action="{{ $tipo->exists ? route('tipos.update', $tipo) : route('tipos.store') }}">
    @csrf
    @if($tipo->exists) @method('PUT') @endif

    <label class="form-label">Nombre *</label>
    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $tipo->nombre) }}" required>

    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <a class="btn btn-outline-secondary" href="{{ route('tipos.index') }}">Cancelar</a>
    </div>
</form>
@endsection
