<?php

namespace App\Http\Controllers;

use App\Models\Lugar;
use App\Models\Hotel;
use App\Models\Restaurante;
use App\Models\Tour;
use App\Models\Guia;
use App\Models\Agencia;
use App\Models\Transporte;
use App\Models\Resena;

class AdminController extends Controller
{
    /**
     * Dashboard de administración.
     *
     * Presenta KPIs, desglose por categoría, listado de servicios por módulo
     * y la actividad reciente real (reseñas y registros de usuarios).
     */
    public function dashboard()
    {
        $lugaresCount = Lugar::count();

        $modulos = [
            ['key' => 'lugares',      'label' => 'Lugares',      'modelo' => Lugar::class,      'ubicacion' => 'municipio'],
            ['key' => 'hoteles',      'label' => 'Hoteles',      'modelo' => Hotel::class,      'ubicacion' => 'municipio'],
            ['key' => 'restaurantes', 'label' => 'Restaurantes', 'modelo' => Restaurante::class, 'ubicacion' => 'municipio'],
            ['key' => 'tours',        'label' => 'Tours',        'modelo' => Tour::class,        'ubicacion' => 'municipio'],
            ['key' => 'guias',       'label' => 'Guías',       'modelo' => Guia::class,        'ubicacion' => 'zonas'],
            ['key' => 'agencias',     'label' => 'Agencias',     'modelo' => Agencia::class,     'ubicacion' => 'municipio'],
            ['key' => 'transportes',  'label' => 'Transporte',  'modelo' => Transporte::class, 'ubicacion' => 'municipio'],
        ];

        $serviciosActivos = 0;
        $porCategoria = [];
        $serviciosPorModulo = [];

        foreach ($modulos as $m) {
            $count = $m['modelo']::count();
            $serviciosActivos += $count;
            $porCategoria[$m['label']] = ['count' => $count, 'pct' => 0.0];

            $registros = $m['modelo']::orderBy('nombre')->get();
            $items = $registros->map(function ($r) use ($m) {
                $ubicacion = $r->{$m['ubicacion']} ?? '—';

                return [
                    'nombre'       => $r->nombre,
                    'ubicacion'    => $ubicacion,
                    'calificacion' => $r->calificacion_promedio,
                    'updated_at'   => $r->updated_at,
                    'edit_url'     => route($m['key'].'.edit', $r),
                ];
            })->all();

            $serviciosPorModulo[$m['label']] = $items;
        }

        // Porcentaje de cada categoría sobre el total de servicios activos
        if ($serviciosActivos > 0) {
            foreach ($porCategoria as $label => $datos) {
                $porCategoria[$label]['pct'] = (float) $datos['count'] / $serviciosActivos * 100;
            }
        }

        $resenasEsteMes = Resena::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $usuariosCount = \App\Models\User::count();
        $calidadMedia = Resena::avg('calificacion') ?? 0.0;

        // Actividad reciente real: última reseña y último usuario registrado
        $actividadReciente = [];

        $ultimaResena = Resena::with('servicio', 'user')->orderByDesc('created_at')->first();
        $ultimoUser = \App\Models\User::orderByDesc('created_at')->first();

        if ($ultimaResena) {
            $actividadReciente[] = [
                'tipo'  => 'resena',
                'icono' => '⭐',
                'titulo' => 'Nueva reseña',
                'detalle' => ($ultimaResena->user?->name ?? 'Usuario')
                    . ' valoró ' . ucfirst($ultimaResena->tipo)
                    . ($ultimaResena->servicio?->nombre ? ' "' . $ultimaResena->servicio->nombre . '"' : '')
                    . ' con ' . number_format($ultimaResena->calificacion, 1) . ' ★',
                'fecha' => $ultimaResena->created_at,
            ];
        }

        if ($ultimoUser) {
            $actividadReciente[] = [
                'tipo'  => 'usuario',
                'icono' => '👤',
                'titulo' => 'Usuario registrado',
                'detalle' => $ultimoUser->name . ' se unió a la plataforma.',
                'fecha' => $ultimoUser->created_at,
            ];
        }

        $actividadReciente = collect($actividadReciente)
            ->sortByDesc('fecha')
            ->take(2);

        return view('admin.dashboard', compact(
            'lugaresCount',
            'serviciosActivos',
            'resenasEsteMes',
            'usuariosCount',
            'calidadMedia',
            'porCategoria',
            'serviciosPorModulo',
            'actividadReciente',
        ));
    }
}