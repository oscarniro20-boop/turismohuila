<?php

namespace App\Models;

use App\Concerns\HasGalleryImages;
use App\Concerns\HasUbicacion;
use App\Concerns\HasUrlImagen;
use App\Concerns\HasRating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Agencia extends Model
{
    use HasGalleryImages, HasRating, HasUrlImagen, HasUbicacion;
    protected $fillable = [
        'nombre',
        'departamento',
        'municipio',
        'telefono_contacto',
        'whatsapp',
        'email',
        'imagen',
        'latitud',
        'longitud',
    ];

    public function tours(): HasManyThrough
    {
        // Fuente oficial de verdad: tour_paquetes (no agencia_tour, tabla legacy).
        return $this->hasManyThrough(
            Tour::class,
            TourPaquete::class,
            'agencia_id',   // FK en tour_paquetes -> agencias.id
            'id',           // FK en tours -> tour_paquetes.tour_id
            'id',           // PK de agencias
            'tour_id'       // FK en tour_paquetes -> tours.id
        );
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
