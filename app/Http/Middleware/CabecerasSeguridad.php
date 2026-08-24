<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Cabeceras de seguridad HTTP (OWASP Security Misconfiguration / Clickjacking /
 * MIME sniffing / Referrer leakage).
 *
 * Nota sobre CSP: NO se implementa script-src restrictivo porque las vistas
 * Blade usan <script> inline (mapa con Leaflet, formularios de tours/agencias)
 * y CDNs externos (unpkg Leaflet/MarkerCluster, Google Fonts). Forzar
 * 'unsafe-inline' en script-src aportaria poco valor; se documenta como
 * mejora futura (nonces) si se eliminan los inline scripts.
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

        // HSTS solo tiene sentido bajo HTTPS; se activa por .env en produccion.
        if (config('app.env') === 'production' && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        return $response;
    }
}
