<?php

namespace App\Models;

use App\Concerns\HasGalleryImages;
use App\Concerns\HasRating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tour extends Model
{
    use HasFactory, HasGalleryImages, HasRating;

    protected $table = 'tours';

    protected $fillable = [
        'nombre',
        'descripcion',
        'municipio',
        'precio',
        'duracion_dias',
        'incluye',
        'cupo_maximo',
        'telefono_contacto',
        'imagen',
        'latitud',
        'longitud',
    ];

    public function resenas()
    {
        return $this->hasMany(Resena::class, 'referencia_id')
                    ->where('tipo', 'tour');
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'referencia_id')
                    ->where('tipo', 'tour');
    }

    public function agencias(): BelongsToMany
    {
        return $this->belongsToMany(Agencia::class, 'agencia_tour')
                    ->withPivot('precio')
                    ->withTimestamps();
    }

    public function guias(): BelongsToMany
    {
        return $this->belongsToMany(Guia::class, 'tour_guia')
                    ->withTimestamps();
    }

    public function paquetes()
    {
        return $this->hasMany(TourPaquete::class);
    }

    public function imagenes()
    {
        return $this->hasMany(TourImagen::class)->orderBy('orden');
    }
}
