<?php

namespace App\Models;

use App\Concerns\HasGalleryImages;
use App\Concerns\HasUrlImagen;
use App\Concerns\HasRating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory, HasGalleryImages, HasRating, HasUrlImagen;

    protected $table = 'hoteles';

    protected $fillable = [
        'nombre',
        'descripcion',
        'municipio',
        'precio_noche',
        'estrellas',
        'imagen',
        'telefono',
        'direccion',
        'latitud',
        'longitud',
    ];

    public function resenas()
    {
        return $this->hasMany(Resena::class, 'referencia_id')
                    ->where('tipo', 'hotel');
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'referencia_id')
                    ->where('tipo', 'hotel');
    }

    public function imagenes()
    {
        return $this->hasMany(HotelImagen::class)->orderBy('orden');
    }
}
