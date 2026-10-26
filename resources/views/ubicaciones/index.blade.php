@extends('layouts.app')
@section('titulo', 'Ubicaciones')

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Ubicaciones</h1>
    @if(auth()->user()->esAdmin())
        <a href="{{ route('ubicaciones.create') }}" class="btn btn-primary">+ Nueva ubicación</a>
    @endif
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Sede</th><th>Nombre</th><th>Descripción</th><th class="text-end">Equipos</th><th></th></tr></thead>
            <tbody>
            @forelse($ubicaciones as $u)
                <tr>
                    <td>{{ $u->sede }}</td>
                    <td class="fw-semibold">{{ $u->nombre }}</td>
                    <td>{{ $u->descripcion ?: '—' }}</td>
                    <td class="text-end">{{ $u->equipos_count }}</td>
                    <td class="text-end text-nowrap">
                        @if(auth()->user()->esAdmin())
                            <a href="{{ route('ubicaciones.edit', $u) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form method="POST" action="{{ route('ubicaciones.destroy', $u) }}" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar esta ubicación?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">No hay ubicaciones registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
