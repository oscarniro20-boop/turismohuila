<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cabeceras de seguridad HTTP (OWASP Security Misconfiguration / Clickjacking /
 * MIME sniffing / Referrer leakage).
 *
 * CSP (H05): politica basada en el inventario REAL de recursos externos:
 *  - unpkg.com            -> Leaflet / MarkerCluster (scripts)
 *  - tile.openstreetmap.org -> tiles del mapa (img)
 *  - res.cloudinary.com   -> imagenes principales/galeria (img)
 *  - fonts.googleapis.com / fonts.gstatic.com -> Google Fonts (style/font)
 * 'unsafe-inline' en script-src es temporal: las vistas Blade usan <script>
 * inline (mapa, formularios tours/agencias). Pendiente migrar a nonces.
 */
class CabecerasSeguridad
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Content-Security-Policy (H05). Sin unsafe-eval. Endurecer con nonces
        // cuando se eliminen los scripts inline de las vistas.
        $response->headers->set('Content-Security-Policy',
            "default-src 'self'; " .
            "script-src 'self' 'unsafe-inline' https://unpkg.com; " .
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://unpkg.com; " .
            "font-src 'self' https://fonts.gstatic.com data:; " .
            "img-src 'self' data: blob: https://*.tile.openstreetmap.org https://res.cloudinary.com; " .
            "connect-src 'self'; " .
            "frame-src 'none'; " .
            "object-src 'none'; " .
            "base-uri 'self'"
        );

        // HSTS solo tiene sentido bajo HTTPS; se activa por .env en produccion.
        if (config('app.env') === 'production' && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        return $response;
    }
}
