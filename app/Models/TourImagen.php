<?php

namespace App\Models;

use App\Concerns\HasGalleryImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourImagen extends Model
{
    use HasGalleryImageUrl;
    protected $table = 'tour_imagenes';

    protected $fillable = [
        'tour_id',
        'ruta',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function tour(): BelongsTo
    {
        return $this->belongsTo(Tour::class);
    }
}
