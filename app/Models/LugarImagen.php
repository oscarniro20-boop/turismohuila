<?php

namespace App\Models;

use App\Concerns\HasGalleryImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LugarImagen extends Model
{
    use HasGalleryImageUrl;
    protected $table = 'lugar_imagenes';

    protected $fillable = [
        'lugar_id',
        'ruta',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    /**
     * Relación: Una imagen pertenece a un lugar
     */
    public function lugar(): BelongsTo
    {
        return $this->belongsTo(Lugar::class);
    }
}
