@extends('layouts.app')
@section('titulo', $ubicacion->exists ? 'Editar ubicación' : 'Nueva ubicación')

@section('contenido')
<h1 class="h3 mb-3">{{ $ubicacion->exists ? 'Editar ubicación' : 'Nueva ubicación' }}</h1>

<form method="POST" class="card card-body shadow-sm"
      action="{{ $ubicacion->exists ? route('ubicaciones.update', $ubicacion) : route('ubicaciones.store') }}">
    @csrf
    @if($ubicacion->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Sede *</label>
            <input type="text" name="sede" class="form-control" value="{{ old('sede', $ubicacion->sede) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Nombre *</label>
            <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $ubicacion->nombre) }}" required>
        </div>
        <div class="col-12">
            <label class="form-label">Descripción</label>
            <input type="text" name="descripcion" class="form-control" value="{{ old('descripcion', $ubicacion->descripcion) }}">
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <a class="btn btn-outline-secondary" href="{{ route('ubicaciones.index') }}">Cancelar</a>
    </div>
</form>
@endsection
