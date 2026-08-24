<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use App\Models\Hotel;
use App\Models\Restaurante;
use App\Models\Tour;
use App\Models\Guia;
use App\Models\Agencia;
use App\Models\LugarImagen;
use App\Models\Resena;

class BienvenidaController extends Controller
{
    /**
     * Display the welcome page.
     */
    public function index()
    {
        $lugaresCount = Lugar::count();
        $hotelesCount = Hotel::count();
        $restaurantesCount = Restaurante::count();
        $toursCount = Tour::count();
        $guiasCount = Guia::count();
        $agenciasCount = Agencia::count();

        $galeria = LugarImagen::inRandomOrder()->take(8)->get();

        $testimonios = Resena::with('user', 'servicio')
            ->where('calificacion', '>=', 4)
            ->whereNotNull('comentario')
            ->latest()
            ->take(3)
            ->get();

        if ($galeria->count() == 0) {
            $galeria = collect([
                (object)['ruta' => 'imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg', 'lugar' => 'Parque Arqueológico San Agustín'],
                (object)['ruta' => 'imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg', 'lugar' => 'Parque Arqueológico San Agustín'],
                (object)['ruta' => 'imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg', 'lugar' => 'Parque Arqueológico San Agustín'],
                (object)['ruta' => 'imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg', 'lugar' => 'Parque Arqueológico San Agustín'],
                (object)['ruta' => 'imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg', 'lugar' => 'Parque Arqueológico San Agustín'],
                (object)['ruta' => 'imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg', 'lugar' => 'Parque Arqueológico San Agustín'],
                (object)['ruta' => 'imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg', 'lugar' => 'Parque Arqueológico San Agustín'],
                (object)['ruta' => 'imagenes/lugares/1783842423_Parque Arqueológico San Agustín.jpg', 'lugar' => 'Parque Arqueológico San Agustín'],
            ]);
        }

        return view('bienvenida', compact(
            'lugaresCount',
            'hotelesCount',
            'restaurantesCount',
            'toursCount',
            'guiasCount',
            'agenciasCount',
            'galeria',
            'testimonios'
        ));
    }
}
