<?php

use App\Http\Controllers\AgendaController;
use App\Http\Controllers\Api\CorreoController;
use App\Http\Controllers\DifuntoController;
use App\Http\Controllers\HistorialController;
use App\Http\Controllers\MapaController;
use App\Http\Controllers\PanteoneroController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicoController;
use App\Http\Controllers\UbicacionController;
use App\Http\Controllers\UsuarioController;
use Illuminate\Support\Facades\Route;

// ===== RUTAS PUBLICAS (sin autenticacion) =====
Route::get('/', [PublicoController::class, 'inicio'])->name('publico.inicio');
Route::get('/consulta', [PublicoController::class, 'buscar'])->name('publico.buscar');
Route::get('/mapa-publico', [PublicoController::class, 'mapa'])->name('publico.mapa');
Route::get('/difuntos-publico/{id}', [PublicoController::class, 'ficha'])->name('publico.ficha');

// ===== RUTAS ADMINISTRATIVAS (con autenticacion) =====
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth'])->name('dashboard');

// Rutas de gestion de difuntos (Administrador y Personal administrativo)
Route::middleware(['auth'])->group(function () {
    Route::get('/difuntos', [DifuntoController::class, 'index'])->name('difuntos.index');
    Route::get('/difuntos/crear', [DifuntoController::class, 'create'])->name('difuntos.create');
    Route::post('/difuntos', [DifuntoController::class, 'store'])->name('difuntos.store');
    Route::get('/difuntos/{difunto}', [DifuntoController::class, 'show'])->name('difuntos.show');
    Route::get('/difuntos/{difunto}/editar', [DifuntoController::class, 'edit'])->name('difuntos.edit');
    Route::put('/difuntos/{difunto}', [DifuntoController::class, 'update'])->name('difuntos.update');
    Route::patch('/difuntos/{difunto}/activar', [DifuntoController::class, 'toggleActivo'])->name('difuntos.toggle-activo');
});

// ---- Modulo 3: Mapa y Ubicaciones ----

// El mapa y la consulta de ubicaciones son accesibles por Admin y Personal administrativo
Route::middleware(['auth'])->group(function () {
    Route::get('/mapa', [MapaController::class, 'index'])->name('mapa.index');

    // Nivel 2: lados disponibles de un bloque (JSON)
    Route::get('/mapa/bloque/{bloque}', [MapaController::class, 'porBloque'])->name('mapa.porBloque');

    // Nivel 3: grid de ubicaciones de un bloque + lado, con estado (JSON)
    Route::get('/mapa/bloque/{bloque}/lado/{lado}', [MapaController::class, 'porBloqueYLado'])->name('mapa.porBloqueYLado');

    // Detalle de una ubicacion (devuelve JSON para el panel lateral del mapa)
    Route::get('/ubicaciones/{ubicacion}', [UbicacionController::class, 'show'])->name('ubicaciones.show');
});

// Edicion y gestion de asociaciones: solo Administrador
Route::middleware(['auth', 'admin'])->group(function () {
    Route::put('/ubicaciones/{ubicacion}', [UbicacionController::class, 'update'])->name('ubicaciones.update');
    Route::post('/ubicaciones/{ubicacion}/asociar', [UbicacionController::class, 'asociar'])->name('ubicaciones.asociar');
    Route::post('/ubicaciones/{ubicacion}/retirar', [UbicacionController::class, 'retirar'])->name('ubicaciones.retirar');
});

// Rutas de administracion de usuarios (solo accesibles por Administrador activo)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/crear', [UsuarioController::class, 'create'])->name('usuarios.create');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{usuario}/editar', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::patch('/usuarios/{usuario}/activar', [UsuarioController::class, 'toggleActivo'])->name('usuarios.toggle-activo');
});

// Rutas de gestion de panteoneros (solo accesibles por Administrador activo)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/panteoneros', [PanteoneroController::class, 'index'])->name('panteoneros.index');
    Route::get('/panteoneros/crear', [PanteoneroController::class, 'create'])->name('panteoneros.create');
    Route::post('/panteoneros', [PanteoneroController::class, 'store'])->name('panteoneros.store');
    Route::get('/panteoneros/{panteonero}/editar', [PanteoneroController::class, 'edit'])->name('panteoneros.edit');
    Route::put('/panteoneros/{panteonero}', [PanteoneroController::class, 'update'])->name('panteoneros.update');
    Route::patch('/panteoneros/{panteonero}/activar', [PanteoneroController::class, 'toggleActivo'])->name('panteoneros.toggle-activo');
});

// Rutas de historial de auditoria (solo accesibles por Administrador activo)
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/historial', [HistorialController::class, 'index'])->name('historial.index');
});

// ---- Modulo 5: Agenda (Administrador y Personal administrativo) ----
// IMPORTANTE: /agenda/eventos, /agenda/lista, /agenda/completo,
// /agenda/por-dia y /agenda/semana deben definirse ANTES de
// /agenda/{agenda} para que Laravel no los interprete como IDs.
// La creacion de actividades ya no tiene una pagina propia: se hace
// desde el grid de la vista Semana (ver agenda.store, llamado por fetch).
Route::middleware(['auth'])->group(function () {
    Route::get('/agenda', [AgendaController::class, 'index'])->name('agenda.index');
    Route::get('/agenda/eventos', [AgendaController::class, 'eventos'])->name('agenda.eventos');
    Route::get('/agenda/lista', [AgendaController::class, 'lista'])->name('agenda.lista');
    Route::get('/agenda/completo', [AgendaController::class, 'completo'])->name('agenda.completo');
    Route::get('/agenda/por-dia', [AgendaController::class, 'porDia'])->name('agenda.porDia');
    Route::get('/agenda/semana', [AgendaController::class, 'semana'])->name('agenda.semana');
    Route::post('/agenda', [AgendaController::class, 'store'])->name('agenda.store');
    Route::get('/agenda/{agenda}', [AgendaController::class, 'show'])->name('agenda.show');
    Route::get('/agenda/{agenda}/editar', [AgendaController::class, 'edit'])->name('agenda.edit');
    Route::put('/agenda/{agenda}', [AgendaController::class, 'update'])->name('agenda.update');
    Route::patch('/agenda/{agenda}/estado', [AgendaController::class, 'cambiarEstado'])->name('agenda.cambiarEstado');
});

// ---- Envio de correo con ubicacion (Brevo) ----
Route::middleware('auth')->group(function () {
    Route::post('/api/correos/enviar-ubicacion', [CorreoController::class, 'enviarUbicacion'])
        ->name('correos.enviarUbicacion');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';