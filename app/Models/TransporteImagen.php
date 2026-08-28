<?php

namespace App\Models;

use App\Concerns\HasGalleryImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransporteImagen extends Model
{
    use HasGalleryImageUrl;

    protected $table = 'transporte_imagenes';

    protected $fillable = [
        'transporte_id',
        'ruta',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function transporte(): BelongsTo
    {
        return $this->belongsTo(Transporte::class);
    }
}
