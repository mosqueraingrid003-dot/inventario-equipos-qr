@extends('layouts.app')
@section('titulo', $mantenimiento->exists ? 'Editar mantenimiento' : 'Registrar mantenimiento')

@section('contenido')
<h1 class="h3 mb-3">{{ $mantenimiento->exists ? 'Editar mantenimiento' : 'Registrar mantenimiento' }}</h1>

<form method="POST" class="card card-body shadow-sm"
      action="{{ $mantenimiento->exists ? route('mantenimientos.update', $mantenimiento) : route('mantenimientos.store') }}">
    @csrf
    @if($mantenimiento->exists) @method('PUT') @endif

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Equipo *</label>
            <select name="equipo_id" class="form-select" required>
                <option value="">Seleccione...</option>
                @foreach($equipos as $e)
                    <option value="{{ $e->id }}" @selected(old('equipo_id', $mantenimiento->equipo_id) == $e->id)>{{ $e->codigo_inventario }} — {{ $e->marca }} {{ $e->modelo }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label">Fecha *</label>
            <input type="date" name="fecha" class="form-control"
                   value="{{ old('fecha', optional($mantenimiento->fecha)->format('Y-m-d')) }}" required>
        </div>
        <div class="col-md-3">
            <label class="form-label">Tipo *</label>
            <select name="tipo" class="form-select" required>
                @foreach($tipos as $t)
                    <option value="{{ $t }}" @selected(old('tipo', $mantenimiento->tipo) == $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-12">
            <label class="form-label">Descripción del trabajo *</label>
            <textarea name="descripcion" rows="3" class="form-control" required>{{ old('descripcion', $mantenimiento->descripcion) }}</textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label">Costo (COP)</label>
            <input type="number" step="0.01" min="0" name="costo" class="form-control"
                   value="{{ old('costo', $mantenimiento->costo) }}">
        </div>
        <div class="col-md-8 d-flex align-items-end">
            <div class="form-check">
                {{-- El campo oculto envía 0 cuando la casilla está sin marcar --}}
                <input type="hidden" name="es_falla" value="0">
                <input class="form-check-input" type="checkbox" id="es_falla" name="es_falla" value="1"
                       @checked(old('es_falla', $mantenimiento->es_falla))>
                <label class="form-check-label" for="es_falla">Este evento fue una falla del equipo (cuenta para la regla de obsolescencia)</label>
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-2">
        <button class="btn btn-primary" type="submit">Guardar</button>
        <a class="btn btn-outline-secondary" href="{{ route('mantenimientos.index') }}">Cancelar</a>
    </div>
</form>
@endsection
