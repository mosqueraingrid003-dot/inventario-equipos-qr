@extends('layouts.app')
@section('titulo', 'Parámetros de obsolescencia')

@section('contenido')
<h1 class="h3 mb-3">Parámetros de obsolescencia</h1>
<p class="text-muted">Un equipo (que no esté dado de baja) genera alerta cuando su antigüedad alcanza la vida útil
    <strong>o</strong> cuando acumula el límite de fallas dentro de la ventana de meses.</p>

<form method="POST" action="{{ route('parametros.update') }}" class="card card-body shadow-sm">
    @csrf @method('PUT')
    <div class="row g-3">
        @foreach($parametros as $p)
            <div class="col-md-4">
                <label class="form-label fw-semibold">{{ $p->clave }}</label>
                <input type="number" min="1" name="{{ $p->clave }}" class="form-control"
                       value="{{ old($p->clave, $p->valor) }}" required>
                <div class="form-text">{{ $p->descripcion }}</div>
            </div>
        @endforeach
    </div>
    <div class="mt-4"><button class="btn btn-primary" type="submit">Guardar parámetros</button></div>
</form>
@endsection
