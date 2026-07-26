<?php

namespace App\Models;

use App\Concerns\HasGalleryImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuiaImagen extends Model
{
    use HasGalleryImageUrl;
    protected $table = 'guia_imagenes';

    protected $fillable = [
        'guia_id',
        'ruta',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function guia(): BelongsTo
    {
        return $this->belongsTo(Guia::class);
    }
}
