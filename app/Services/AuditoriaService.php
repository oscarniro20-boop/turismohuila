<?php

namespace App\Services;

use App\Models\Auditoria;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Servicio de auditoría central (Fase 6).
 *
 * Registra operaciones administrativas en la tabla `auditorias`. Nunca
 * interrumpe la operación principal: si el registro falla, se avisa por
 * log y la acción del usuario continúa.
 */
class AuditoriaService
{
    /**
     * @param  array<string, mixed>  $datos  Resumen estructurado (contadores, cambios, etc.).
     */
    public function registrar(string $accion, ?string $entidad = null, ?int $entidadId = null, array $datos = []): void
    {
        try {
            $request = app()->bound('request') ? request() : null;

            Auditoria::create([
                'user_id' => $request?->user()?->id,
                'accion' => $accion,
                'entidad' => $entidad,
                'entidad_id' => $entidadId,
                'datos' => $datos === [] ? null : $datos,
                'ip' => $request?->ip(),
                'user_agent' => Str::limit((string) $request?->userAgent(), 250, ''),
            ]);
        } catch (\Throwable $e) {
            // La auditoría es best-effort: no debe tumbar la operación auditada.
            Log::warning('auditoria_no_registrada', [
                'accion' => $accion,
                'motivo' => $e->getMessage(),
            ]);
        }
    }
}