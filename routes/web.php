<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LugarController;
use App\Http\Controllers\HotelController;
use App\Http\Controllers\RestauranteController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\ResenaController;
use App\Http\Controllers\GuiaController;
use App\Http\Controllers\AgenciaController;
use App\Http\Controllers\FavoritoController;
use App\Http\Controllers\TransporteController;
use App\Http\Controllers\BusquedaController;
use App\Http\Controllers\ContactoController;
use App\Http\Controllers\BienvenidaController;
use Illuminate\Support\Facades\Mail;

// Página de inicio
// "/" y "/bienvenida" comparten BienvenidaController::index() (vista 'bienvenida')
Route::get('/', [BienvenidaController::class, 'index'])->name('home');

Route::get('/buscar', [BusquedaController::class, 'buscar'])
    ->middleware('throttle:30,1')
    ->name('busqueda');

Route::view('/nosotros', 'nosotros')->name('nosotros');
Route::view('/privacidad', 'privacidad')->name('privacidad');
Route::view('/terminos', 'terminos')->name('terminos');
Route::get('/contacto', [ContactoController::class, 'mostrar'])->name('contacto');
Route::post('/contacto', [ContactoController::class, 'enviar'])
    ->middleware('throttle:5,1')
    ->name('contacto.enviar');

// ── Rutas ADMIN (gestión total) ───────────────────────────────────────────
// IMPORTANTE: Estas rutas deben ir ANTES que las públicas para evitar conflictos
Route::middleware(['auth', 'rol:admin'])->group(function () {
    Route::resource('lugares', LugarController::class)
        ->parameters(['lugares' => 'lugar'])
        ->except(['index', 'show']);

    Route::resource('hoteles', HotelController::class)
        ->parameters(['hoteles' => 'hotel'])
        ->except(['index', 'show']);

    Route::resource('restaurantes', RestauranteController::class)
        ->parameters(['restaurantes' => 'restaurante'])
        ->except(['index', 'show']);

    Route::resource('tours', TourController::class)
        ->parameters(['tours' => 'tour'])
        ->except(['index', 'show']);

    Route::resource('guias', GuiaController::class)
        ->except(['index', 'show']);

    Route::resource('agencias', AgenciaController::class)
        ->except(['index', 'show']);

    Route::resource('transportes', TransporteController::class)
        ->parameters(['transportes' => 'transporte'])
        ->except(['index', 'show']);

    Route::resource('resenas', ResenaController::class)
        ->parameters(['resenas' => 'resena'])
        ->only(['index']);
});

// ── Rutas TURISTA (dejar reseñas) ─────────────────────────────────────────
// IMPORTANTE: debe ir ANTES del resource público de abajo, para que
// /resenas/create no sea interceptada por /resenas/{resena} (show)
Route::middleware(['auth'])->group(function () {
    // H08: rate limiting anti-spam en escritura de reseñas (creación, edición,
    // borrado). Límite generoso para usuarios legítimos, duro contra abuso.
    Route::resource('resenas', ResenaController::class)
        ->parameters(['resenas' => 'resena'])
        ->except(['index', 'show'])
        ->middleware('throttle:10,1');

    // IMPORTANTE: debe ir antes del resource público de abajo, para que
    // /resenas/nombre-servicio no sea interceptada por /resenas/{resena} (show)
    Route::get('/resenas/nombre-servicio', [ResenaController::class, 'nombreServicio'])
        ->middleware('throttle:30,1')
        ->name('resenas.nombre-servicio');
});

// ── Rutas PÚBLICAS (todos pueden ver) ─────────────────────────────────────
Route::resource('lugares', LugarController::class)
    ->parameters(['lugares' => 'lugar'])
    ->only(['index', 'show']);

Route::get('/mapa', [LugarController::class, 'mapa'])->name('mapa');

Route::resource('hoteles', HotelController::class)
    ->parameters(['hoteles' => 'hotel'])
    ->only(['index', 'show']);

Route::resource('restaurantes', RestauranteController::class)
    ->parameters(['restaurantes' => 'restaurante'])
    ->only(['index', 'show']);

Route::resource('tours', TourController::class)
    ->parameters(['tours' => 'tour'])
    ->only(['index', 'show']);

Route::resource('resenas', ResenaController::class)
    ->parameters(['resenas' => 'resena'])
    ->only(['show']);

Route::resource('agencias', AgenciaController::class)
    ->only(['index', 'show']);

Route::resource('transportes', TransporteController::class)
    ->parameters(['transportes' => 'transporte'])
    ->only(['index', 'show']);

// ── Rutas públicas de Guías (ver listado y perfil) ─────────────────────────
Route::get('/guias', [GuiaController::class, 'index'])->name('guias.index');
Route::get('/guias/{guia}', [GuiaController::class, 'show'])->name('guias.show');

// ── Pantalla de bienvenida ────────────────────────────────────────────────
Route::get('/bienvenida', [BienvenidaController::class, 'index'])
    ->name('bienvenida');

// ── Sala de Visita ───────────────────────────────────────────────────────
Route::get('/sala-visita', function () {
    return view('sala-visita');
})->middleware('auth')->name('sala-visita');

// ── Dashboard y Perfil ────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/mis-resenas', [ResenaController::class, 'misResenas'])->name('resenas.mis');
    Route::post('/favoritos/toggle', [FavoritoController::class, 'toggle'])
        ->middleware('throttle:30,1')
        ->name('favoritos.toggle');
    Route::get('/mis-favoritos', [FavoritoController::class, 'index'])->name('favoritos.index');
});

require __DIR__.'/auth.php';
