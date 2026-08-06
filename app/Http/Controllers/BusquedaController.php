<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lugar;
use App\Models\Hotel;
use App\Models\Restaurante;
use App\Models\Tour;
use App\Models\Guia;
use App\Models\Agencia;

class BusquedaController extends Controller
{
    public function buscar(Request $request)
    {
        $q = $request->query('q', '');

        if (mb_strlen(trim($q)) < 2) {
            return response()->json(['resultados' => []]);
        }

        $resultados = [];

        // Lugares
        $lugares = Lugar::where('nombre', 'like', "%{$q}%")
            ->limit(4)
            ->get(['id', 'nombre', 'municipio']);
        foreach ($lugares as $lugar) {
            $resultados[] = [
                'tipo' => 'lugar',
                'id' => $lugar->id,
                'nombre' => $lugar->nombre,
                'subtitulo' => $lugar->municipio,
                'url' => route('lugares.show', $lugar),
            ];
        }

        // Hoteles
        $hoteles = Hotel::where('nombre', 'like', "%{$q}%")
            ->limit(4)
            ->get(['id', 'nombre', 'municipio']);
        foreach ($hoteles as $hotel) {
            $resultados[] = [
                'tipo' => 'hotel',
                'id' => $hotel->id,
                'nombre' => $hotel->nombre,
                'subtitulo' => $hotel->municipio,
                'url' => route('hoteles.show', $hotel),
            ];
        }

        // Restaurantes
        $restaurantes = Restaurante::where('nombre', 'like', "%{$q}%")
            ->limit(4)
            ->get(['id', 'nombre', 'municipio']);
        foreach ($restaurantes as $restaurante) {
            $resultados[] = [
                'tipo' => 'restaurante',
                'id' => $restaurante->id,
                'nombre' => $restaurante->nombre,
                'subtitulo' => $restaurante->municipio,
                'url' => route('restaurantes.show', $restaurante),
            ];
        }

        // Tours
        $tours = Tour::where('nombre', 'like', "%{$q}%")
            ->limit(4)
            ->get(['id', 'nombre', 'municipio']);
        foreach ($tours as $tour) {
            $resultados[] = [
                'tipo' => 'tour',
                'id' => $tour->id,
                'nombre' => $tour->nombre,
                'subtitulo' => $tour->municipio,
                'url' => route('tours.show', $tour),
            ];
        }

        // Guías
        $guias = Guia::where('nombre', 'like', "%{$q}%")
            ->limit(4)
            ->get(['id', 'nombre', 'zonas']);
        foreach ($guias as $guia) {
            $resultados[] = [
                'tipo' => 'guia',
                'id' => $guia->id,
                'nombre' => $guia->nombre,
                'subtitulo' => $guia->zonas,
                'url' => route('guias.show', $guia),
            ];
        }

        // Agencias
        $agencias = Agencia::where('nombre', 'like', "%{$q}%")
            ->limit(4)
            ->get(['id', 'nombre', 'municipio']);
        foreach ($agencias as $agencia) {
            $resultados[] = [
                'tipo' => 'agencia',
                'id' => $agencia->id,
                'nombre' => $agencia->nombre,
                'subtitulo' => $agencia->municipio,
                'url' => route('agencias.show', $agencia),
            ];
        }

        return response()->json(['resultados' => $resultados]);
    }
}