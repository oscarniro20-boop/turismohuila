<?php

namespace App\Models;

use App\Concerns\HasGalleryImages;
use App\Concerns\HasUbicacion;
use App\Concerns\HasUrlImagen;
use App\Concerns\HasRating;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guia extends Model
{
    use HasGalleryImages, HasRating, HasUrlImagen, HasUbicacion;
    protected $fillable = [
        'nombre',
        'foto',
        'bio',
        'idiomas',
        'experiencia_anios',
        'zonas',
        'departamento',
        'whatsapp',
        'email',
        'telefono',
        'activo',
        'latitud',
        'longitud',
    ];

    protected $casts = [
        'experiencia_anios' => 'integer',
        'activo' => 'boolean',
    ];

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id', 'cod_dpto');
    }

    /**
     * Relación muchos a muchos: Un guía puede estar en muchos lugares
     */
    public function lugares(): BelongsToMany
    {
        return $this->belongsToMany(Lugar::class, 'guia_lugar')
                    ->withPivot('id')
                    ->withTimestamps();
    }

    /**
     * Relación muchos a muchos: Un guía puede estar en muchos tours
     */
    public function tours(): BelongsToMany
    {
        return $this->belongsToMany(Tour::class, 'tour_guia')
                    ->withTimestamps();
    }

    /**
     * Relación con reseñas
     */
    public function resenas()
    {
        return $this->hasMany(Resena::class, 'referencia_id')
                    ->where('tipo', 'guia');
    }

    /**
     * Relación con favoritos
     */
    public function favoritos()
    {
        return $this->hasMany(Favorito::class, 'referencia_id')
                    ->where('tipo', 'guia');
    }

    /**
     * Relación con imágenes de la galería
     */
    public function imagenes()
    {
        return $this->hasMany(GuiaImagen::class)->orderBy('orden');
    }

    protected function columnaImagen(): string
    {
        return 'foto';
    }
}
