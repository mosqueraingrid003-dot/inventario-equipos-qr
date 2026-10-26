@extends('layouts.app')
@section('titulo', 'QR ' . $equipo->codigo_inventario)

@section('contenido')
<div class="row justify-content-center">
    <div class="col-12 col-md-6 col-lg-4">
        <div class="card shadow-sm text-center">
            <div class="card-body p-4">
                {{-- Aquí se dibuja el código QR --}}
                <div id="codigo-qr" class="d-flex justify-content-center mb-3"></div>

                <div class="h4 mb-0">{{ $equipo->codigo_inventario }}</div>
                <div>{{ $equipo->marca }} {{ $equipo->modelo }}</div>
                <div class="text-muted small">{{ $equipo->ubicacion->sede }} — {{ $equipo->ubicacion->nombre }}</div>
            </div>
        </div>

        <div class="d-flex gap-2 justify-content-center mt-3 no-imprimir">
            <button class="btn btn-primary" onclick="window.print()">Imprimir etiqueta</button>
            <a class="btn btn-outline-secondary" href="{{ route('equipos.show', $equipo) }}">Volver a la ficha</a>
        </div>
        <p class="small text-muted text-center mt-3 no-imprimir">
            Al escanear el código con el celular se abre: <br><span class="text-break">{{ $url }}</span>
        </p>
    </div>
</div>
@endsection

@push('scripts')
{{-- Librería que genera el QR en el navegador (no hay que instalar nada con Composer) --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById('codigo-qr'), {
        text: @json($url),
        width: 220,
        height: 220
    });
</script>
@endpush
