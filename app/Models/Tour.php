<?php

namespace App\Models;

use App\Concerns\HasGalleryImages;
use App\Concerns\HasUbicacion;
use App\Concerns\HasUrlImagen;
use App\Concerns\HasRating;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Tour extends Model
{
    use HasFactory, HasGalleryImages, HasRating, HasUrlImagen, HasUbicacion;

    protected $table = 'tours';

    protected $fillable = [
        'nombre',
        'descripcion',
        'departamento',
        'departamento_id',
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

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id', 'cod_dpto');
    }

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

    public function agencias(): HasManyThrough
    {
        // Fuente oficial de verdad: tour_paquetes (no agencia_tour, tabla legacy).
        return $this->hasManyThrough(
            Agencia::class,
            TourPaquete::class,
            'tour_id',      // FK en tour_paquetes -> tours.id
            'id',           // FK en agencias -> tour_paquetes.agencia_id
            'id',           // PK de tours
            'agencia_id'    // FK en tour_paquetes -> agencias.id
        );
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
