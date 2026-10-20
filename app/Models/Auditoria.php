<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Registro inmutable de auditoría (Fase 6).
 *
 * Una fila por operación administrativa: quién (user_id, nullable para
 * conservar el rastro si el usuario se borra), qué (accion, entidad,
 * entidad_id) y un resumen estructurado en datos (json).
 */
class Auditoria extends Model
{
    protected $fillable = [
        'user_id',
        'accion',
        'entidad',
        'entidad_id',
        'datos',
        'ip',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'datos' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}