@extends('layouts.app')

@section('title', 'Tours - Turismo por Colombia')

@section('content')
<x-page-header :titulo="__('ui.catalog.tours_title')" :descripcion="__('ui.catalog.tours_description')">
    @auth
        @if(auth()->user()->esAdmin())
            <x-slot:actions>
                <a href="{{ route('tours.create') }}" class="btn border-0"
                   style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1.5rem;">
                    + {{ __('ui.catalog.new') }} {{ __('ui.nav.tours') }}
                </a>
            </x-slot:actions>
        @endif
    @endauth
</x-page-header>

<div class="container pb-5">
    <x-filtro-ubicacion :accion="route('tours.index')" />

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($tours as $tour)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$tour->url_imagen"
                    :titulo="$tour->nombre"
                    :ubicacion="$tour->ubicacion_texto"
                    :rating="$tour->resenas_avg_calificacion"
                    :descripcion="Str::limit($tour->descripcion, 110)"
                    :precio="$tour->precio"
                    :precioLabel="$tour->duracion_dias . ' ' . __('ui.catalog.day')"
                    :url="route('tours.show', $tour)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('tours.edit', $tour) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('tours.destroy', $tour) : null"
                    favoritoTipo="tour"
                    :favoritoId="$tour->id"
                    :esFavorito="in_array($tour->id, $favoritosIds ?? [])"
                    icono="🥾"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    {{ __('ui.catalog.no_tours') }}
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
