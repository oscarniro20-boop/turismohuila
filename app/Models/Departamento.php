<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    protected $table = 'departamentos';

    protected $primaryKey = 'cod_dpto';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'cod_dpto',
        'nombre',
        'slug',
        'region',
        'capital',
        'destacado',
        'orden',
        'descripcion',
        'imagen',
        'latitud',
        'longitud',
    ];

    protected $casts = [
        'destacado' => 'boolean',
        'orden' => 'integer',
        'latitud' => 'decimal:4',
        'longitud' => 'decimal:4',
    ];

    public function lugares(): HasMany
    {
        return $this->hasMany(Lugar::class, 'departamento_id', 'cod_dpto');
    }

    public function hoteles(): HasMany
    {
        return $this->hasMany(Hotel::class, 'departamento_id', 'cod_dpto');
    }

    public function restaurantes(): HasMany
    {
        return $this->hasMany(Restaurante::class, 'departamento_id', 'cod_dpto');
    }

    public function tours(): HasMany
    {
        return $this->hasMany(Tour::class, 'departamento_id', 'cod_dpto');
    }

    public function agencias(): HasMany
    {
        return $this->hasMany(Agencia::class, 'departamento_id', 'cod_dpto');
    }

    public function guias(): HasMany
    {
        return $this->hasMany(Guia::class, 'departamento_id', 'cod_dpto');
    }

    public function transportes(): HasMany
    {
        return $this->hasMany(Transporte::class, 'departamento_id', 'cod_dpto');
    }

    public function scopeDestacados($query)
    {
        return $query->where('destacado', true)->orderBy('orden')->orderBy('nombre');
    }

    public function scopePorRegion($query, string $region)
    {
        return $query->where('region', $region)->orderBy('orden')->orderBy('nombre');
    }
}