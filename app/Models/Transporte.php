<?php

namespace App\Models;

use App\Concerns\HasGalleryImages;
use App\Concerns\HasRating;
use App\Concerns\HasUrlImagen;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transporte extends Model
{
    use HasFactory, HasGalleryImages, HasRating, HasUrlImagen;

    protected $table = 'transportes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'municipio',
        'tipo',
        'precio',
        'capacidad',
        'imagen',
        'telefono',
        'latitud',
        'longitud',
    ];

    public function resenas()
    {
        return $this->hasMany(Resena::class, 'referencia_id')
                    ->where('tipo', 'transporte');
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'referencia_id')
                    ->where('tipo', 'transporte');
    }

    public function imagenes()
    {
        return $this->hasMany(TransporteImagen::class)->orderBy('orden');
    }
}
