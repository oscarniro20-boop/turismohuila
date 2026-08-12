<?php

namespace App\Services;

use App\Models\Lugar;
use App\Models\Hotel;
use App\Models\Restaurante;
use App\Models\Tour;
use App\Models\Guia;
use App\Models\Agencia;
use Illuminate\Support\Collection;

class MapaService
{
    /**
     * Obtiene todos los puntos para el mapa.
     *
     * @return Collection
     */
    public function obtenerTodosLosPuntos(): Collection
    {
        $config = [
            'lugar' => ['modelo' => Lugar::class, 'sub' => 'municipio', 'ruta' => 'lugares.show', 'icono' => '📍'],
            'hotel' => ['modelo' => Hotel::class, 'sub' => 'municipio', 'ruta' => 'hoteles.show', 'icono' => '🏨'],
            'restaurante' => ['modelo' => Restaurante::class, 'sub' => 'municipio', 'ruta' => 'restaurantes.show', 'icono' => '🍽️'],
            'tour' => ['modelo' => Tour::class, 'sub' => 'municipio', 'ruta' => 'tours.show', 'icono' => '🚌'],
            'guia' => ['modelo' => Guia::class, 'sub' => 'zonas', 'ruta' => 'guias.show', 'icono' => '🎯'],
            'agencia' => ['modelo' => Agencia::class, 'sub' => 'municipio', 'ruta' => 'agencias.show', 'icono' => '🧳'],
        ];

        $puntos = collect();

        foreach ($config as $tipo => $c) {
            $registros = $c['modelo']::whereNotNull('latitud')
                ->whereNotNull('longitud')
                ->get(['id', 'nombre', 'latitud', 'longitud', $c['sub']]);

            $puntos = $puntos->merge($registros->map(function ($item) use ($c, $tipo) {
                return [
                    'nombre' => $item->nombre,
                    'municipio' => $item->{$c['sub']} ?? '',
                    'lat' => (float) $item->latitud,
                    'lng' => (float) $item->longitud,
                    'url' => route($c['ruta'], $item),
                    'icono' => $c['icono'],
                    'tipo' => $tipo,
                ];
            }));
        }

        return $puntos;
    }
}