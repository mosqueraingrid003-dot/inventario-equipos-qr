@extends('layouts.app')
@section('titulo', $responsable->exists ? 'Editar responsable' : 'Nuevo responsable')

@section('contenido')
<h1 class="h3 mb-3">{{ $responsable->exists ? 'Editar responsable' : 'Nuevo responsable' }}</h1>

<form method="POST" class="card card-body shadow-sm"
      action="{{ $responsable->exists ? route('responsables.update', $responsable) : route('responsables.store') }}">
    @csrf
    @if($responsable->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-8">
            <label class="form-label">Nombre completo *</label>
            <input type="text" name="nombre_completo" class="form-control" value="{{ old('nombre_completo', $responsable->nombre_completo) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Identificación *</label>
            <input type="text" name="identificacion" class="form-control" value="{{ old('identificacion', $responsable->identificacion) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Cargo</label>
            <input type="text" name="cargo" class="form-control" value="{{ old('cargo', $responsable->cargo) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Dependencia</label>
            <input type="text" name="dependencia" class="form-control" value="{{ old('dependencia', $responsable->dependencia) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" class="form-control" value="{{ old('correo', $responsable->correo) }}">
        </div>
        <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $responsable->telefono) }}">
        </div>
        <div class="col-12">
            <div class="form-check">
                <input type="hidden" name="activo" value="0">
                <input class="form-check-input" type="checkbox" id="activo" name="activo" value="1" @checked(old('activo', $responsable->activo))>
                <label class="form-check-label" for="activo">Responsable activo</label>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <a class="btn btn-outline-secondary" href="{{ route('responsables.index') }}">Cancelar</a>
    </div>
</form>
@endsection
