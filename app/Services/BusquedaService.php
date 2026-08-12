<?php

namespace App\Services;

use App\Models\Lugar;
use App\Models\Hotel;
use App\Models\Restaurante;
use App\Models\Tour;
use App\Models\Guia;
use App\Models\Agencia;
use Illuminate\Support\Collection;

class BusquedaService
{
    public function buscarEnTodos(string $query, int $limite = 4): Collection
    {
        $modelos = [
            ['modelo' => Lugar::class, 'tipo' => 'lugar', 'subtitulo' => 'municipio'],
            ['modelo' => Hotel::class, 'tipo' => 'hotel', 'subtitulo' => 'municipio'],
            ['modelo' => Restaurante::class, 'tipo' => 'restaurante', 'subtitulo' => 'municipio'],
            ['modelo' => Tour::class, 'tipo' => 'tour', 'subtitulo' => 'municipio'],
            ['modelo' => Guia::class, 'tipo' => 'guia', 'subtitulo' => 'zonas'],
            ['modelo' => Agencia::class, 'tipo' => 'agencia', 'subtitulo' => 'municipio'],
        ];

        $resultados = collect();

        foreach ($modelos as $config) {
            $items = $this->buscarEnModelo($config['modelo'], $query, $limite, $config['subtitulo']);

            foreach ($items as $item) {
                $resultados->push([
                    'tipo' => $config['tipo'],
                    'id' => $item->id,
                    'nombre' => $item->nombre,
                    'subtitulo' => $item->{$config['subtitulo']} ?? '',
                    'url' => route("{$config['tipo']}s.show", $item),
                ]);
            }
        }

        return $resultados;
    }

    private function buscarEnModelo(string $modelo, string $query, int $limite, string $campoSubtitulo): Collection
    {
        return $modelo::where('nombre', 'like', "%{$query}%")
            ->limit($limite)
            ->get(['id', 'nombre', $campoSubtitulo]);
    }
}