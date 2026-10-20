<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'rol' => \App\Http\Middleware\VerificarRol::class,
        ]);

        // Cabeceras de seguridad (FASE 4). Sin CSP restrictiva: las vistas usan
        // scripts/styles inline y CDNs (Leaflet, OSM, MarkerCluster, Cloudinary,
        // Google Fonts); una CSP estricta rompería el mapa y el frontend.
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\CabecerasSeguridad::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // JSON para api/* y para cualquier cliente que lo pida explícitamente
        // (fetch/AJAX del frontend, ej. toggle de favoritos).
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();