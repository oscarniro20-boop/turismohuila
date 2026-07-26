<?php

namespace App\Models;

use App\Concerns\HasGalleryImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestauranteImagen extends Model
{
    use HasGalleryImageUrl;
    protected $table = 'restaurante_imagenes';

    protected $fillable = [
        'restaurante_id',
        'ruta',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function restaurante(): BelongsTo
    {
        return $this->belongsTo(Restaurante::class);
    }
}
