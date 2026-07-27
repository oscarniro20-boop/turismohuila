<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/test/admin-check', function () {
    if (!Auth::check()) {
        return response()->json([
            'error' => 'No autenticado',
            'message' => 'Debes iniciar sesión para ver esta información'
        ], 401);
    }

    $user = Auth::user();
    
    return response()->json([
        'usuario' => [
            'id' => $user->id,
            'nombre' => $user->name,
            'email' => $user->email,
            'rol' => $user->rol,
        ],
        'verificaciones' => [
            'auth()->user()->rol' => $user->rol,
            'auth()->user()->esAdmin()' => $user->esAdmin(),
            'es_admin' => $user->esAdmin() ? 'SÍ - Es admin' : 'NO - No es admin',
        ],
        'rutas_accesibles' => $user->esAdmin() ? [
            'hoteles.create' => route('hoteles.create'),
            'restaurantes.create' => route('restaurantes.create'),
            'tours.create' => route('tours.create'),
        ] : 'No accesible',
        'botones_visibles' => $user->esAdmin() ? [
            '+ Nuevo Hotel',
            '+ Nuevo Restaurante',
            '+ Nuevo Tour',
        ] : 'No visibles',
    ]);
})->middleware('auth')->name('test.admin-check');