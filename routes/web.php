<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicoController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// ===== RUTAS PÚBLICAS (sin autenticación) =====
Route::get('/', [PublicoController::class, 'inicio'])->name('publico.inicio');
Route::get('/consulta', [PublicoController::class, 'buscar'])->name('publico.buscar');
Route::get('/mapa-publico', [PublicoController::class, 'mapa'])->name('publico.mapa');
Route::get('/difuntos-publico/{id}', [PublicoController::class, 'ficha'])->name('publico.ficha');

// ===== RUTAS ADMINISTRATIVAS (con autenticación) =====
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Rutas de administración de usuarios (solo accesibles por Administrador activo)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/activar', [UsuarioController::class, 'toggleActivo'])->name('usuarios.toggle-activo');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
