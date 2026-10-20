<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resena extends Model
{
    use HasFactory;

    protected $table = 'resenas';

    protected $fillable = [
        'user_id',
        'tipo',
        'referencia_id',
        'comentario',
        'calificacion',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getNombreServicioAttribute()
    {
        // Si la relación ya fue precargada, usarla
        if ($this->relationLoaded('servicio')) {
            $item = $this->getRelation('servicio');
            
            return $item
                ? $item->nombre
                : ucfirst($this->tipo) . ' #' . $this->referencia_id . ' (eliminado)';
        }

        $modelo = \Illuminate\Database\Eloquent\Relations\Relation::getMorphedModel($this->tipo);

        if (!$modelo) {
            return ucfirst($this->tipo) . ' #' . $this->referencia_id;
        }

        $item = $modelo::find($this->referencia_id);

        return $item
            ? $item->nombre
            : ucfirst($this->tipo) . ' #' . $this->referencia_id . ' (eliminado)';
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