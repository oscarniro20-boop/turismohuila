<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourPaquete extends Model
{
    protected $fillable = [
        'agencia_id',
        'tour_id',
        'precio',
        'duracion_dias',
        'cupo_maximo',
        'descripcion',
    ];

    public function agencia(): BelongsTo
    {
        return $this->belongsTo(Agencia::class);
    }

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}