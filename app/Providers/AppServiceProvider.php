<?php

namespace App\Providers;

use App\Models\Lugar;
use App\Models\Hotel;
use App\Models\Restaurante;
use App\Models\Tour;
use App\Models\Guia;
use App\Models\Transporte;
use App\Models\Agencia;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Mapeo de tipos para relaciones morfables (Resena, Favorito)
        Relation::morphMap([
            'lugar' => Lugar::class,
            'hotel' => Hotel::class,
            'restaurante' => Restaurante::class,
            'tour' => Tour::class,
            'guia' => Guia::class,
            'agencia' => Agencia::class,
            'transporte' => Transporte::class,
        ]);
    }
}
