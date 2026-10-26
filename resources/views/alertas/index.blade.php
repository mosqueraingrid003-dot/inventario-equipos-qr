@extends('layouts.app')
@section('titulo', 'Alertas de obsolescencia')

@section('contenido')
<h1 class="h3 mb-1">Alertas de obsolescencia</h1>
<p class="text-muted">Regla vigente: antigüedad de <strong>{{ $vidaUtil }}</strong> años o más,
    o <strong>{{ $fallas }}</strong> fallas o más en los últimos <strong>{{ $ventana }}</strong> meses.</p>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Código</th><th>Equipo</th><th>Ubicación</th><th>Antigüedad</th><th>Fallas recientes</th><th>Motivo</th><th></th></tr></thead>
            <tbody>
            @forelse($alertas as $fila)
                @php($e = $fila['equipo'])
                <tr>
                    <td class="fw-semibold">{{ $e->codigo_inventario }}</td>
                    <td>{{ $e->marca }} {{ $e->modelo }}<div class="small text-muted">{{ $e->tipo->nombre }}</div></td>
                    <td>{{ $e->ubicacion->nombre }}</td>
                    <td>{{ $fila['alerta']['antiguedad'] }} años</td>
                    <td>{{ $fila['alerta']['fallas'] }}</td>
                    <td>
                        @foreach($fila['alerta']['motivos'] as $motivo)
                            <span class="badge text-bg-danger d-block text-start mb-1">{{ $motivo }}</span>
                        @endforeach
                    </td>
                    <td class="text-end"><a href="{{ route('equipos.show', $e) }}" class="btn btn-sm btn-outline-primary">Ver</a></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No hay equipos con alerta de obsolescencia.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
