@extends('layouts.app')
@section('titulo', 'Mantenimientos')

@section('contenido')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 mb-0">Mantenimientos</h1>
    @if(auth()->user()->puedeEscribir())
        <a href="{{ route('mantenimientos.create') }}" class="btn btn-primary">+ Registrar mantenimiento</a>
    @endif
</div>

<form method="GET" action="{{ route('mantenimientos.index') }}" class="card card-body shadow-sm mb-3">
    <div class="row g-2">
        <div class="col-12 col-md-5">
            <select name="equipo_id" class="form-select">
                <option value="">Todos los equipos</option>
                @foreach($equipos as $e)
                    <option value="{{ $e->id }}" @selected(request('equipo_id') == $e->id)>{{ $e->codigo_inventario }} — {{ $e->marca }} {{ $e->modelo }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <select name="tipo" class="form-select">
                <option value="">Todos los tipos</option>
                @foreach($tipos as $t)
                    <option value="{{ $t }}" @selected(request('tipo') == $t)>{{ $t }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-4 d-flex gap-2">
            <button class="btn btn-outline-primary flex-fill" type="submit">Filtrar</button>
            <a class="btn btn-outline-secondary" href="{{ route('mantenimientos.index') }}">Limpiar</a>
        </div>
    </div>
</form>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Fecha</th><th>Equipo</th><th>Tipo</th><th>Falla</th><th>Descripción</th><th>Costo</th><th>Técnico</th><th></th></tr></thead>
            <tbody>
            @forelse($mantenimientos as $m)
                <tr>
                    <td>{{ $m->fecha->format('d/m/Y') }}</td>
                    <td><a href="{{ route('equipos.show', $m->equipo_id) }}">{{ $m->equipo->codigo_inventario }}</a></td>
                    <td>{{ $m->tipo }}</td>
                    <td>{!! $m->es_falla ? '<span class="badge bg-danger">Sí</span>' : 'No' !!}</td>
                    <td>{{ \Illuminate\Support\Str::limit($m->descripcion, 60) }}</td>
                    <td>${{ number_format($m->costo, 0, ',', '.') }}</td>
                    <td>{{ $m->tecnico->name ?? '—' }}</td>
                    <td class="text-end">
                        @if(auth()->user()->puedeEscribir())
                            <a href="{{ route('mantenimientos.edit', $m) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">No hay mantenimientos registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<div class="mt-3">{{ $mantenimientos->links('pagination::bootstrap-5') }}</div>
@endsection
