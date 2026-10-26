<?php

use App\Http\Controllers\AlertaController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipoController;
use App\Http\Controllers\MantenimientoController;
use App\Http\Controllers\ParametroController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\ResponsableController;
use App\Http\Controllers\TipoEquipoController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Middleware\RolMiddleware;
use Illuminate\Support\Facades\Route;

// ---------- Inicio de sesión ----------
Route::get('/login', [AuthController::class, 'mostrarLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.entrar')->middleware('guest');

// ---------- Todo lo demás exige haber iniciado sesión ----------
Route::middleware('auth')->group(function () {

    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    $soloAdmin = RolMiddleware::class . ':Administrador';
    $adminYTecnico = RolMiddleware::class . ':Administrador,Técnico';

    // ===== Administrador y Técnico: registrar y editar =====
    // (van ANTES de las rutas de lectura para que /equipos/create no se confunda con /equipos/{equipo})
    Route::middleware($adminYTecnico)->group(function () {
        Route::resource('equipos', EquipoController::class)->only(['create', 'store', 'edit', 'update']);
        Route::post('equipos/{equipo}/baja', [EquipoController::class, 'darDeBaja'])->name('equipos.baja');

        Route::resource('mantenimientos', MantenimientoController::class)->only(['create', 'store', 'edit', 'update']);

        Route::resource('responsables', ResponsableController::class)->only(['index', 'create', 'store', 'edit', 'update']);
    });

    // ===== Solo Administrador =====
    Route::middleware($soloAdmin)->group(function () {
        Route::delete('equipos/{equipo}', [EquipoController::class, 'destroy'])->name('equipos.destroy');
        Route::delete('mantenimientos/{mantenimiento}', [MantenimientoController::class, 'destroy'])->name('mantenimientos.destroy');
        Route::delete('responsables/{responsable}', [ResponsableController::class, 'destroy'])->name('responsables.destroy');

        Route::resource('ubicaciones', UbicacionController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy'])
            ->parameters(['ubicaciones' => 'ubicacion']);

        Route::resource('tipos-equipo', TipoEquipoController::class)
            ->only(['create', 'store', 'edit', 'update', 'destroy'])
            ->parameters(['tipos-equipo' => 'tipo'])
            ->names('tipos');

        Route::resource('usuarios', UsuarioController::class)
            ->except(['show'])
            ->parameters(['usuarios' => 'usuario']);

        Route::get('parametros', [ParametroController::class, 'index'])->name('parametros.index');
        Route::put('parametros', [ParametroController::class, 'update'])->name('parametros.update');
    });

    // ===== Lectura: todos los roles =====
    Route::resource('equipos', EquipoController::class)->only(['index', 'show']);
    Route::get('equipos/{equipo}/qr', [EquipoController::class, 'qr'])->name('equipos.qr');
    Route::resource('mantenimientos', MantenimientoController::class)->only(['index']);
    Route::get('ubicaciones', [UbicacionController::class, 'index'])->name('ubicaciones.index');
    Route::get('tipos-equipo', [TipoEquipoController::class, 'index'])->name('tipos.index');

    Route::get('alertas', [AlertaController::class, 'index'])->name('alertas.index');
    Route::get('reportes', [ReporteController::class, 'index'])->name('reportes.index');
    Route::get('reportes/equipos.csv', [ReporteController::class, 'exportarCsv'])->name('reportes.csv');
});
