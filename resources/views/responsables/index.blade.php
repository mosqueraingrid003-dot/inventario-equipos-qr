@extends('layouts.app')
@section('titulo', 'Responsables')

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Responsables</h1>
    <a href="{{ route('responsables.create') }}" class="btn btn-primary">+ Nuevo responsable</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Nombre</th><th>Identificación</th><th>Cargo</th><th>Dependencia</th><th>Activo</th><th class="text-end">Equipos</th><th></th></tr></thead>
            <tbody>
            @forelse($responsables as $r)
                <tr>
                    <td class="fw-semibold">{{ $r->nombre_completo }}</td>
                    <td>{{ $r->identificacion }}</td>
                    <td>{{ $r->cargo ?: '—' }}</td>
                    <td>{{ $r->dependencia ?: '—' }}</td>
                    <td>{!! $r->activo ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>' !!}</td>
                    <td class="text-end">{{ $r->equipos_count }}</td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('responsables.edit', $r) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                        @if(auth()->user()->esAdmin())
                            <form method="POST" action="{{ route('responsables.destroy', $r) }}" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este responsable? Sus equipos quedarán sin responsable.')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">No hay responsables registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
