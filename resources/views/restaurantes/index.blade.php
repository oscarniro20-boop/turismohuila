@extends('layouts.app')

@section('title', 'Restaurantes - Turismo por Colombia')

@section('content')
<x-page-header :titulo="__('ui.catalog.restaurants_title')" :descripcion="__('ui.catalog.restaurants_description')">
    @auth
        @if(auth()->user()->esAdmin())
            <x-slot:actions>
                <a href="{{ route('restaurantes.create') }}" class="btn border-0"
                   style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1.5rem;">
                    + {{ __('ui.catalog.new') }} {{ __('ui.nav.restaurants') }}
                </a>
            </x-slot:actions>
        @endif
    @endauth
</x-page-header>

<div class="container pb-5">
    <x-filtro-ubicacion :accion="route('restaurantes.index')" />

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($restaurantes as $restaurante)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$restaurante->url_imagen"
                    :titulo="$restaurante->nombre"
                    :ubicacion="$restaurante->ubicacion_texto . ' · ' . $restaurante->tipo_cocina"
                    :rating="$restaurante->resenas_avg_calificacion"
                    :descripcion="Str::limit($restaurante->descripcion, 110)"
                    :precio="$restaurante->precio_promedio"
                    :precioLabel="__('ui.catalog.per_person')"
                    :url="route('restaurantes.show', $restaurante)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('restaurantes.edit', $restaurante) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('restaurantes.destroy', $restaurante) : null"
                    favoritoTipo="restaurante"
                    :favoritoId="$restaurante->id"
                    :esFavorito="in_array($restaurante->id, $favoritosIds ?? [])"
                    icono="🍽️"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    {{ __('ui.catalog.no_restaurants') }}
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
