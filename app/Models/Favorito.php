<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorito extends Model
{
    use HasFactory;

    protected $table = 'favoritos';

    protected $fillable = [
        'user_id',
        'tipo',
        'referencia_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getServicioAttribute()
    {
        $modelo = match ($this->tipo) {
            'lugar'       => \App\Models\Lugar::class,
            'hotel'       => \App\Models\Hotel::class,
            'restaurante' => \App\Models\Restaurante::class,
            'tour'        => \App\Models\Tour::class,
            'guia'        => \App\Models\Guia::class,
            'agencia'     => \App\Models\Agencia::class,
            default       => null,
        };

        if (!$modelo) {
            return null;
        }

        return $modelo::find($this->referencia_id);
    }
}