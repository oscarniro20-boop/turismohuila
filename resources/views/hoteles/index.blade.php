@extends('layouts.app')

@section('title', 'Hoteles - Turismo por Colombia')

@section('content')
<x-page-header :titulo="__('ui.catalog.hotels_title')" :descripcion="__('ui.catalog.hotels_description')">
    @auth
        @if(auth()->user()->esAdmin())
            <x-slot:actions>
                <a href="{{ route('hoteles.create') }}" class="btn border-0"
                   style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1.5rem;">
                    + {{ __('ui.catalog.new') }} {{ __('ui.nav.hotels') }}
                </a>
            </x-slot:actions>
        @endif
    @endauth
</x-page-header>

<div class="container pb-5">
    <x-filtro-ubicacion :accion="route('hoteles.index')" />

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($hoteles as $hotel)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$hotel->url_imagen"
                    :titulo="$hotel->nombre"
                    :ubicacion="$hotel->ubicacion_texto"
                    :rating="$hotel->resenas_avg_calificacion"
                    :descripcion="Str::limit($hotel->descripcion, 110)"
                    :precio="$hotel->precio_noche"
                    :precioLabel="__('ui.catalog.night')"
                    :url="route('hoteles.show', $hotel)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('hoteles.edit', $hotel) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('hoteles.destroy', $hotel) : null"
                    favoritoTipo="hotel"
                    :favoritoId="$hotel->id"
                    :esFavorito="in_array($hotel->id, $favoritosIds ?? [])"
                    icono="🏨"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    {{ __('ui.catalog.no_hotels') }}
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
