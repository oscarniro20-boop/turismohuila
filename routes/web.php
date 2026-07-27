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

// Página de inicio
Route::get('/', function () {
    $heroLugar = \App\Models\Lugar::whereNotNull('imagen')->orderByDesc('calificacion')->first();
    return view('home', compact('heroLugar'));
})->name('home');

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

    Route::resource('resenas', ResenaController::class)
        ->parameters(['resenas' => 'resena'])
        ->only(['index']);
});

// ── Rutas TURISTA (dejar reseñas) ─────────────────────────────────────────
// IMPORTANTE: debe ir ANTES del resource público de abajo, para que
// /resenas/create no sea interceptada por /resenas/{resena} (show)
Route::middleware(['auth'])->group(function () {
    Route::resource('resenas', ResenaController::class)
        ->parameters(['resenas' => 'resena'])
        ->except(['index', 'show']);
});

// ── Rutas PÚBLICAS (todos pueden ver) ─────────────────────────────────────

// ── Rutas PÚBLICAS (todos pueden ver) ─────────────────────────────────────
Route::resource('lugares', LugarController::class)
    ->parameters(['lugares' => 'lugar'])
    ->only(['index', 'show']);

Route::get('/mapa', function () {
    $lugares = \App\Models\Lugar::whereNotNull('latitud')->whereNotNull('longitud')->get();

    $lugaresMapa = $lugares->map(function ($lugar) {
        return [
            'nombre'    => $lugar->nombre,
            'municipio' => $lugar->municipio,
            'lat'       => (float) $lugar->latitud,
            'lng'       => (float) $lugar->longitud,
            'url'       => route('lugares.show', $lugar),
        ];
    });

    return view('mapa', compact('lugaresMapa'));
})->name('mapa');

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

// ── Rutas públicas de Guías (ver listado y perfil) ─────────────────────────
Route::get('/guias', [GuiaController::class, 'index'])->name('guias.index');
Route::get('/guias/{guia}', [GuiaController::class, 'show'])->name('guias.show');

// ── Pantalla de bienvenida ────────────────────────────────────────────────
Route::get('/bienvenida', function () {
    if (!auth()->check()) return redirect()->route('login');
    return view('bienvenida');
})->middleware('auth')->name('bienvenida');

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
    Route::get('/resenas/nombre-servicio', [ResenaController::class, 'nombreServicio'])->name('resenas.nombre-servicio');
});

// Ruta de prueba JSON para verificar acceso de admin
Route::get('/test/admin-check', function () {
    if (!auth()->check()) {
        return response()->json([
            'error' => 'No autenticado',
            'message' => 'Debes iniciar sesión para ver esta información'
        ], 401);
    }

    $user = auth()->user();
    
    return response()->json([
        'usuario' => [
            'id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'rol' => $user->rol,
        ],
        'verificaciones' => [
            'auth()->user()->rol' => auth()->user()->rol,
            'auth()->user()->esAdmin()' => auth()->user()->esAdmin(),
            'es_admin' => auth()->user()->esAdmin() ? 'SÍ - Es admin' : 'NO - No es admin',
        ],
        'rutas_accesibles' => auth()->user()->esAdmin() ? [
            'hoteles.create' => route('hoteles.create'),
            'restaurantes.create' => route('restaurantes.create'),
            'tours.create' => route('tours.create'),
        ] : 'No accesible',
        'botones_visibles' => auth()->user()->esAdmin() ? [
            '+ Nuevo Hotel',
            '+ Nuevo Restaurante',
            '+ Nuevo Tour',
        ] : 'No visibles',
    ]);
})->middleware('auth')->name('test.admin-check');

// Ruta de prueba VISUAL para verificar acceso de admin
Route::get('/test/admin-visual', function () {
    return view('test-admin');
})->middleware('auth')->name('test.admin-visual');

require __DIR__.'/auth.php';
