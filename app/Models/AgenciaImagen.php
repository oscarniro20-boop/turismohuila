<?php

namespace App\Models;

use App\Concerns\HasGalleryImageUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AgenciaImagen extends Model
{
    use HasGalleryImageUrl;
    protected $table = 'agencia_imagenes';

    protected $fillable = [
        'agencia_id',
        'ruta',
        'orden',
    ];

    protected $casts = [
        'orden' => 'integer',
    ];

    public function agencia(): BelongsTo
    {
        return $this->belongsTo(Agencia::class);
    }
}
