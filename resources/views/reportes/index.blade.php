@extends('layouts.app')
@section('titulo', 'Reportes')

@section('contenido')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h3 mb-0">Reportes</h1>
    <div class="d-flex gap-2 no-imprimir">
        <a href="{{ route('reportes.csv') }}" class="btn btn-outline-success">Descargar inventario (CSV)</a>
        <button class="btn btn-outline-secondary" onclick="window.print()">Imprimir</button>
    </div>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-header bg-white fw-semibold">Existencias por ubicación y estado (total: {{ $total }} equipos)</div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Sede</th><th>Ubicación</th><th>Estado</th><th class="text-end">Cantidad</th></tr></thead>
            <tbody>
            @forelse($existencias as $fila)
                <tr>
                    <td>{{ $fila->sede }}</td>
                    <td>{{ $fila->ubicacion }}</td>
                    <td>{{ $fila->estado }}</td>
                    <td class="text-end">{{ $fila->total }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Sin datos.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white fw-semibold">Existencias por tipo de equipo</div>
    <div class="table-responsive">
        <table class="table table-sm table-hover mb-0">
            <thead class="table-light"><tr><th>Tipo</th><th class="text-end">Cantidad</th></tr></thead>
            <tbody>
            @foreach($porTipo as $t)
                <tr><td>{{ $t->nombre }}</td><td class="text-end">{{ $t->equipos_count }}</td></tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
