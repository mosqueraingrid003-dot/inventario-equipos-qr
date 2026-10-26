@extends('layouts.app')
@section('titulo', 'Tipos de equipo')

@section('contenido')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Tipos de equipo</h1>
    @if(auth()->user()->esAdmin())
        <a href="{{ route('tipos.create') }}" class="btn btn-primary">+ Nuevo tipo</a>
    @endif
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Nombre</th><th class="text-end">Equipos</th><th></th></tr></thead>
            <tbody>
            @forelse($tipos as $t)
                <tr>
                    <td class="fw-semibold">{{ $t->nombre }}</td>
                    <td class="text-end">{{ $t->equipos_count }}</td>
                    <td class="text-end text-nowrap">
                        @if(auth()->user()->esAdmin())
                            <a href="{{ route('tipos.edit', $t) }}" class="btn btn-sm btn-outline-secondary">Editar</a>
                            <form method="POST" action="{{ route('tipos.destroy', $t) }}" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar este tipo?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-center text-muted py-4">No hay tipos registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
