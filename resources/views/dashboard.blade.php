@extends('layouts.app')
@section('titulo', 'Inicio')

@section('contenido')
<h1 class="h3 mb-4">Resumen del inventario</h1>

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card tarjeta-dato shadow-sm"><div class="card-body">
            <div class="text-muted small">Total de equipos</div><div class="fs-2 fw-bold">{{ $total }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card tarjeta-dato shadow-sm"><div class="card-body">
            <div class="text-muted small">Activos</div><div class="fs-2 fw-bold text-success">{{ $activos }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card tarjeta-dato shadow-sm"><div class="card-body">
            <div class="text-muted small">En mantenimiento</div><div class="fs-2 fw-bold text-warning">{{ $enMantenim }}</div>
        </div></div>
    </div>
    <div class="col-6 col-lg-3">
        <a href="{{ route('alertas.index') }}" class="text-decoration-none text-reset">
            <div class="card tarjeta-dato shadow-sm border-danger"><div class="card-body">
                <div class="text-muted small">Con alerta de obsolescencia</div><div class="fs-2 fw-bold text-danger">{{ $conAlerta }}</div>
            </div></div>
        </a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">Equipos por ubicación</div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light"><tr><th>Sede</th><th>Ubicación</th><th class="text-end">Equipos</th></tr></thead>
            <tbody>
            @forelse($ubicaciones as $u)
                <tr>
                    <td>{{ $u->sede }}</td>
                    <td><a href="{{ route('equipos.index', ['ubicacion_id' => $u->id]) }}">{{ $u->nombre }}</a></td>
                    <td class="text-end">{{ $u->equipos_count }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center text-muted">Aún no hay ubicaciones.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
<p class="text-muted small mt-3">Dados de baja: {{ $dadosDeBaja }}</p>
@endsection
