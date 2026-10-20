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
        // Si la relación ya fue precargada, usarla
        if ($this->relationLoaded('servicio')) {
            return $this->getRelation('servicio');
        }

        $modelo = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($this->tipo);

        if (!$modelo) {
            return null;
        }

        return $modelo::find($this->referencia_id);
    }

    /**
     * Relación morfable hacia el servicio referenciado.
     */
    public function servicio()
    {
        return $this->morphTo(
            __FUNCTION__,
            'tipo',
            'referencia_id'
        );
    }
}