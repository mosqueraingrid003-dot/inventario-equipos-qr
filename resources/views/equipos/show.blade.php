@extends('layouts.app')
@section('titulo', $equipo->codigo_inventario)

@section('contenido')
@php
    $color = ['Activo' => 'success', 'En mantenimiento' => 'warning', 'Dado de baja' => 'secondary'][$equipo->estado];
@endphp

<div class="d-flex flex-wrap justify-content-between align-items-start mb-3 gap-2">
    <div>
        <h1 class="h3 mb-1">{{ $equipo->codigo_inventario }} · {{ $equipo->marca }} {{ $equipo->modelo }}</h1>
        <span class="badge bg-{{ $color }}">{{ $equipo->estado }}</span>
    </div>
    <div class="d-flex flex-wrap gap-2 no-imprimir">
        <a href="{{ route('equipos.qr', $equipo) }}" class="btn btn-outline-dark">Ver código QR</a>
        @if(auth()->user()->puedeEscribir())
            <a href="{{ route('equipos.edit', $equipo) }}" class="btn btn-outline-primary">Editar</a>
            <a href="{{ route('mantenimientos.create', ['equipo_id' => $equipo->id]) }}" class="btn btn-primary">+ Mantenimiento</a>
            @if($equipo->estado !== 'Dado de baja')
                <form method="POST" action="{{ route('equipos.baja', $equipo) }}" onsubmit="return confirm('¿Dar de baja este equipo?')">
                    @csrf
                    <button class="btn btn-outline-warning" type="submit">Dar de baja</button>
                </form>
            @endif
        @endif
        @if(auth()->user()->esAdmin())
            <form method="POST" action="{{ route('equipos.destroy', $equipo) }}"
                  onsubmit="return confirm('Se eliminará el equipo y todo su historial. ¿Continuar?')">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger" type="submit">Eliminar</button>
            </form>
        @endif
    </div>
</div>

@if($alerta['obsoleto'])
    <div class="alert alert-danger">
        <strong>Alerta de obsolescencia:</strong>
        <ul class="mb-0">
            @foreach($alerta['motivos'] as $motivo)
                <li>{{ $motivo }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-white fw-semibold">Datos del equipo</div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-4">Número de serie</dt><dd class="col-sm-8">{{ $equipo->numero_serie }}</dd>
                    <dt class="col-sm-4">Tipo</dt><dd class="col-sm-8">{{ $equipo->tipo->nombre }}</dd>
                    <dt class="col-sm-4">Ubicación</dt><dd class="col-sm-8">{{ $equipo->ubicacion->sede }} — {{ $equipo->ubicacion->nombre }}</dd>
                    <dt class="col-sm-4">Responsable</dt><dd class="col-sm-8">{{ $equipo->responsable->nombre_completo ?? 'Sin responsable' }}</dd>
                    <dt class="col-sm-4">Adquisición</dt><dd class="col-sm-8">{{ $equipo->fecha_adquisicion->format('d/m/Y') }} ({{ $alerta['antiguedad'] }} años)</dd>
                    <dt class="col-sm-4">Fallas recientes</dt><dd class="col-sm-8">{{ $alerta['fallas'] }}</dd>
                    <dt class="col-sm-4">Especificaciones</dt><dd class="col-sm-8">{{ $equipo->especificaciones ?: '—' }}</dd>
                    <dt class="col-sm-4">Observaciones</dt><dd class="col-sm-8">{{ $equipo->observaciones ?: '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm mt-3">
    <div class="card-header bg-white fw-semibold">Historial de mantenimientos</div>
    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead class="table-light"><tr><th>Fecha</th><th>Tipo</th><th>Falla</th><th>Descripción</th><th>Costo</th><th>Técnico</th><th></th></tr></thead>
            <tbody>
            @forelse($mantenimientos as $m)
                <tr>
                    <td>{{ $m->fecha->format('d/m/Y') }}</td>
                    <td>{{ $m->tipo }}</td>
                    <td>{!! $m->es_falla ? '<span class="badge bg-danger">Sí</span>' : 'No' !!}</td>
                    <td>{{ $m->descripcion }}</td>
                    <td>${{ number_format($m->costo, 0, ',', '.') }}</td>
                    <td>{{ $m->tecnico->name ?? '—' }}</td>
                    <td class="text-end text-nowrap">
                        @if(auth()->user()->puedeEscribir())
                            <a href="{{ route('mantenimientos.edit', $m) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        @endif
                        @if(auth()->user()->esAdmin())
                            <form method="POST" action="{{ route('mantenimientos.destroy', $m) }}" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este mantenimiento?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-3">Este equipo no tiene mantenimientos registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<a href="{{ route('equipos.index') }}" class="btn btn-link mt-3 no-imprimir">← Volver al listado</a>
@endsection
