@extends('layouts.app')
@section('titulo', 'Equipos')

@section('contenido')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 mb-0">Equipos</h1>
    @if(auth()->user()->puedeEscribir())
        <a href="{{ route('equipos.create') }}" class="btn btn-primary">+ Registrar equipo</a>
    @endif
</div>

{{-- Búsqueda y filtros --}}
<form method="GET" action="{{ route('equipos.index') }}" class="card card-body shadow-sm mb-3">
    <div class="row g-2">
        <div class="col-12 col-md-4">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control"
                   placeholder="Buscar por código, serie, marca o modelo">
        </div>
        <div class="col-6 col-md-2">
            <select name="ubicacion_id" class="form-select">
                <option value="">Ubicación</option>
                @foreach($ubicaciones as $u)
                    <option value="{{ $u->id }}" @selected(request('ubicacion_id') == $u->id)>{{ $u->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="tipo_equipo_id" class="form-select">
                <option value="">Tipo</option>
                @foreach($tipos as $t)
                    <option value="{{ $t->id }}" @selected(request('tipo_equipo_id') == $t->id)>{{ $t->nombre }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2">
            <select name="estado" class="form-select">
                <option value="">Estado</option>
                @foreach($estados as $e)
                    <option value="{{ $e }}" @selected(request('estado') == $e)>{{ $e }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-2 d-flex gap-2">
            <button class="btn btn-outline-primary flex-fill" type="submit">Filtrar</button>
            <a class="btn btn-outline-secondary" href="{{ route('equipos.index') }}">Limpiar</a>
        </div>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr><th>Código</th><th>Equipo</th><th>Tipo</th><th>Ubicación</th><th>Responsable</th><th>Estado</th><th class="text-end">Acciones</th></tr>
            </thead>
            <tbody>
            @forelse($equipos as $e)
                <tr>
                    <td class="fw-semibold">{{ $e->codigo_inventario }}</td>
                    <td>{{ $e->marca }} {{ $e->modelo }}<div class="small text-muted">{{ $e->numero_serie }}</div></td>
                    <td>{{ $e->tipo->nombre }}</td>
                    <td>{{ $e->ubicacion->nombre }}</td>
                    <td>{{ $e->responsable->nombre_completo ?? '—' }}</td>
                    <td>
                        @php
                            $color = ['Activo' => 'success', 'En mantenimiento' => 'warning', 'Dado de baja' => 'secondary'][$e->estado];
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ $e->estado }}</span>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('equipos.show', $e) }}" class="btn btn-sm btn-outline-primary">Ver</a>
                        @if(auth()->user()->puedeEscribir())
                            <a href="{{ route('equipos.edit', $e) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No se encontraron equipos.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $equipos->links('pagination::bootstrap-5') }}</div>
@endsection
