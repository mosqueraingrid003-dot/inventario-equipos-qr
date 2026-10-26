<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    {{-- viewport: necesario para que la página sea responsive en el celular --}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('titulo', 'Inventario TI') · Inventario TI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f7fb; }
        .navbar-brand small { font-weight: 400; opacity: .75; }
        .tarjeta-dato { border-left: 4px solid #0d6efd; }
        @media print { .no-imprimir { display: none !important; } body { background: #fff; } }
    </style>
</head>
<body>
@auth
<nav class="navbar navbar-expand-lg navbar-dark bg-primary no-imprimir">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('dashboard') }}">Inventario TI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Inicio</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('equipos.index') }}">Equipos</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('mantenimientos.index') }}">Mantenimientos</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('alertas.index') }}">Alertas</a></li>
                <li class="nav-item"><a class="nav-link" href="{{ route('reportes.index') }}">Reportes</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Catálogos</a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('tipos.index') }}">Tipos de equipo</a></li>
                        <li><a class="dropdown-item" href="{{ route('ubicaciones.index') }}">Ubicaciones</a></li>
                        @if(auth()->user()->puedeEscribir())
                            <li><a class="dropdown-item" href="{{ route('responsables.index') }}">Responsables</a></li>
                        @endif
                    </ul>
                </li>

                @if(auth()->user()->esAdmin())
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">Administración</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('usuarios.index') }}">Usuarios</a></li>
                            <li><a class="dropdown-item" href="{{ route('parametros.index') }}">Parámetros de obsolescencia</a></li>
                        </ul>
                    </li>
                @endif
            </ul>
            <div class="d-flex align-items-center text-white gap-2">
                <span class="small">{{ auth()->user()->name }} <span class="badge bg-light text-primary">{{ auth()->user()->role->nombre }}</span></span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-light" type="submit">Salir</button>
                </form>
            </div>
        </div>
    </div>
</nav>
@endauth

<main class="container py-4">
    @if(session('exito'))
        <div class="alert alert-success alert-dismissible fade show no-imprimir">{{ session('exito') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show no-imprimir">{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($errors->any() && ! request()->routeIs('login'))
        <div class="alert alert-danger no-imprimir">
            <strong>Revisa los datos:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $mensaje)
                    <li>{{ $mensaje }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('contenido')
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
