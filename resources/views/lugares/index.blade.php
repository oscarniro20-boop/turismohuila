@extends('layouts.app')

@section('title', 'Lugares Turísticos - Turismo por Colombia')

@section('content')
<x-page-header :titulo="__('ui.catalog.places_title')" :descripcion="__('ui.catalog.places_description')">
    @auth
        @if(auth()->user()->esAdmin())
            <x-slot:actions>
                <a href="{{ route('lugares.create') }}" class="btn border-0" style="background-color: var(--color-primary); color:#fff;">+ {{ __('ui.catalog.new') }} {{ __('ui.nav.places') }}</a>
            </x-slot:actions>
        @endif
    @endauth
</x-page-header>

<div class="container pb-5">
    <x-filtro-ubicacion :accion="route('lugares.index')" />
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($lugares as $lugar)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$lugar->url_imagen"
                    :titulo="$lugar->nombre"
                    :ubicacion="$lugar->ubicacion_texto . ' · ' . $lugar->categoria"
                    :rating="$lugar->resenas_avg_calificacion"
                    :descripcion="Str::limit($lugar->descripcion, 110)"
                    :url="route('lugares.show', $lugar)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('lugares.edit', $lugar) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('lugares.destroy', $lugar) : null"
                    favoritoTipo="lugar"
                    :favoritoId="$lugar->id"
                    :esFavorito="in_array($lugar->id, $favoritosIds ?? [])"
                    icono="🏞️"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    {{ __('ui.catalog.no_places') }}
                </div>
            </div>
        @endforelse
    </div>
</div>

@endsection
