<?php

namespace App\Models;

use App\Concerns\HasGalleryImages;
use App\Concerns\HasUrlImagen;
use App\Concerns\HasRating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lugar extends Model
{
    use HasFactory, HasGalleryImages, HasRating, HasUrlImagen;

    protected $table = 'lugares';

    protected $fillable = [
        'user_id',
        'nombre',
        'municipio',
        'categoria',
        'descripcion',
        'imagen',
        'latitud',
        'longitud',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function resenas()
    {
        return $this->hasMany(Resena::class, 'referencia_id')->where('tipo', 'lugar');
    }

    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'referencia_id')->where('tipo', 'lugar');
    }

    public function imagenes()
    {
        return $this->hasMany(LugarImagen::class)->orderBy('orden');
    }

    public function guias()
    {
        return $this->belongsToMany(Guia::class, 'guia_lugar')
                    ->withPivot('id')
                    ->withTimestamps();
    }
}
