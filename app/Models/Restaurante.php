<?php

namespace App\Models;

use App\Concerns\HasGalleryImages;
use App\Concerns\HasRating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restaurante extends Model
{
    use HasFactory, HasGalleryImages, HasRating;

    protected $table = 'restaurantes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'municipio',
        'tipo_cocina',
        'precio_promedio',
        'imagen',
        'telefono',
        'direccion',
    ];

    public function resenas()
    {
        return $this->hasMany(Resena::class, 'referencia_id')
                    ->where('tipo', 'restaurante');
    }

    public function imagenes()
    {
        return $this->hasMany(RestauranteImagen::class)->orderBy('orden');
    }
}
