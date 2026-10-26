@extends('layouts.app')
@section('titulo', 'Iniciar sesión')

@section('contenido')
<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-6 col-lg-4">
        <div class="card shadow-sm mt-4">
            <div class="card-body p-4">
                <h1 class="h4 text-center mb-1">Inventario TI</h1>
                <p class="text-center text-muted">Gestión de equipos de cómputo</p>

                <form method="POST" action="{{ route('login.entrar') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="email">Correo</label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror" required autofocus>
                        @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Contraseña</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="recordar" name="recordar" value="1">
                        <label class="form-check-label" for="recordar">Mantener la sesión iniciada</label>
                    </div>
                    <button class="btn btn-primary w-100" type="submit">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
