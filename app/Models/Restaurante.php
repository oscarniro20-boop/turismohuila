<?php

namespace App\Models;

use App\Concerns\HasGalleryImages;
use App\Concerns\HasUbicacion;
use App\Concerns\HasUrlImagen;
use App\Concerns\HasRating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restaurante extends Model
{
    use HasFactory, HasGalleryImages, HasRating, HasUrlImagen, HasUbicacion;

    protected $table = 'restaurantes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'departamento',
        'municipio',
        'tipo_cocina',
        'precio_promedio',
        'imagen',
        'telefono',
        'direccion',
        'latitud',
        'longitud',
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id', 'cod_dpto');
    }

    public function resenas()
    {
        return $this->hasMany(Resena::class, 'referencia_id')
                    ->where('tipo', 'restaurante');
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'referencia_id')
                    ->where('tipo', 'restaurante');
    }

    public function imagenes()
    {
        return $this->hasMany(RestauranteImagen::class)->orderBy('orden');
    }
}
