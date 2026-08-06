<?php

namespace App\Models;

use App\Concerns\HasGalleryImages;
use App\Concerns\HasRating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Agencia extends Model
{
    use HasGalleryImages, HasRating;
    protected $fillable = [
        'nombre',
        'municipio',
        'telefono_contacto',
        'whatsapp',
        'email',
        'imagen',
        'latitud',
        'longitud',
    ];

    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class, 'agencia_tour')
                    ->withPivot('precio')
                    ->withTimestamps();
    }

    public function resenas()
    {
        return $this->hasMany(Resena::class, 'referencia_id')->where('tipo', 'agencia');
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'referencia_id')->where('tipo', 'agencia');
    }

    public function paquetes()
    {
        return $this->hasMany(TourPaquete::class);
    }

    public function imagenes()
    {
        return $this->hasMany(AgenciaImagen::class)->orderBy('orden');
    }
}
