<?php

namespace App\Models;

use App\Concerns\HasGalleryImages;
use App\Concerns\HasUbicacion;
use App\Concerns\HasRating;
use App\Concerns\HasUrlImagen;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transporte extends Model
{
    use HasFactory, HasGalleryImages, HasRating, HasUrlImagen, HasUbicacion;

    protected $table = 'transportes';

    protected $fillable = [
        'nombre',
        'descripcion',
        'departamento',
        'municipio',
        'tipo',
        'precio',
        'capacidad',
        'imagen',
        'telefono',
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
