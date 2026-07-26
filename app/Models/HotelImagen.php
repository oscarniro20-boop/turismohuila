<?php

namespace App\Models;

use App\Concerns\HasGalleryImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HotelImagen extends Model
{
    use HasGalleryImageUrl;
    protected $table = 'hotel_imagenes';

    protected $fillable = [
        'hotel_id',
        'ruta',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    /**
     * Relación: Una imagen pertenece a un hotel
     */
    public function hotel(): BelongsTo
    {
        return $this->belongsTo(Hotel::class);
    }
}
