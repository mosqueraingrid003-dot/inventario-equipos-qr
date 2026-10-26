@extends('layouts.app')
@section('titulo', $equipo->exists ? 'Editar equipo' : 'Registrar equipo')

@section('contenido')
<h1 class="h3 mb-3">{{ $equipo->exists ? 'Editar equipo ' . $equipo->codigo_inventario : 'Registrar equipo' }}</h1>

<form method="POST" class="card card-body shadow-sm"
      action="{{ $equipo->exists ? route('equipos.update', $equipo) : route('equipos.store') }}">
    @csrf
    @if($equipo->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Código de inventario *</label>
            <input type="text" name="codigo_inventario" class="form-control"
                   value="{{ old('codigo_inventario', $equipo->codigo_inventario) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Número de serie *</label>
            <input type="text" name="numero_serie" class="form-control"
                   value="{{ old('numero_serie', $equipo->numero_serie) }}" required>
        </div>
        <div class="col-md-4">
            <label class="form-label">Fecha de adquisición *</label>
            <input type="date" name="fecha_adquisicion" class="form-control"
                   value="{{ old('fecha_adquisicion', optional($equipo->fecha_adquisicion)->format('Y-m-d')) }}" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Marca *</label>
            <input type="text" name="marca" class="form-control" value="{{ old('marca', $equipo->marca) }}" required>
        </div>
        <div class="col-md-6">
            <label class="form-label">Modelo *</label>
            <input type="text" name="modelo" class="form-control" value="{{ old('modelo', $equipo->modelo) }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">Tipo de equipo *</label>
            <select name="tipo_equipo_id" class="form-select" required>
                <option value="">Seleccione...</option>
                @foreach($tipos as $t)
                    <option value="{{ $t->id }}" @selected(old('tipo_equipo_id', $equipo->tipo_equipo_id) == $t->id)>{{ $t->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Ubicación *</label>
            <select name="ubicacion_id" class="form-select" required>
                <option value="">Seleccione...</option>
                @foreach($ubicaciones as $u)
                    <option value="{{ $u->id }}" @selected(old('ubicacion_id', $equipo->ubicacion_id) == $u->id)>{{ $u->sede }} — {{ $u->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Responsable</label>
            <select name="responsable_id" class="form-select">
                <option value="">Sin responsable</option>
                @foreach($responsables as $r)
                    <option value="{{ $r->id }}" @selected(old('responsable_id', $equipo->responsable_id) == $r->id)>{{ $r->nombre_completo }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Estado *</label>
            <select name="estado" class="form-select" required>
                @foreach($estados as $e)
                    <option value="{{ $e }}" @selected(old('estado', $equipo->estado) == $e)>{{ $e }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-8">
            <label class="form-label">Especificaciones</label>
            <input type="text" name="especificaciones" class="form-control" placeholder="Ej. Core i5, 8 GB RAM, 256 GB SSD"
                   value="{{ old('especificaciones', $equipo->especificaciones) }}">
        </div>
        <div class="col-12">
            <label class="form-label">Observaciones</label>
            <textarea name="observaciones" rows="3" class="form-control">{{ old('observaciones', $equipo->observaciones) }}</textarea>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <a class="btn btn-outline-secondary" href="{{ $equipo->exists ? route('equipos.show', $equipo) : route('equipos.index') }}">Cancelar</a>
    </div>
</form>
@endsection
