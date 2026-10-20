@extends('layouts.app')

@section('title', 'Guías Turísticos - Turismo por Colombia')

@section('content')
<x-page-header :titulo="__('ui.catalog.guides_title')" :descripcion="__('ui.catalog.guides_description')">
    @auth
        @if(auth()->user()->esAdmin())
            <x-slot:actions>
                <a href="{{ route('guias.create') }}" class="btn border-0"
                   style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1.5rem;">
                    + {{ __('ui.catalog.new') }} {{ __('ui.nav.guides') }}
                </a>
            </x-slot:actions>
        @endif
    @endauth
</x-page-header>

<div class="container pb-5">
    <x-filtro-ubicacion :accion="route('guias.index')" :municipio="false" />
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($guias as $guia)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$guia->url_imagen"
                    :titulo="$guia->nombre"
                    :ubicacion="$guia->zonas ?? __('ui.catalog.certified_guide')"
                    :rating="$guia->resenas_avg_calificacion"
                                    :descripcion="Str::limit($guia->bio ?? $guia->experiencia_anios . ' años de experiencia · ' . $guia->idiomas, 110)"
                    :url="route('guias.show', $guia)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('guias.edit', $guia) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('guias.destroy', $guia) : null"
                    favoritoTipo="guia"
                    :favoritoId="$guia->id"
                    :esFavorito="in_array($guia->id, $favoritosIds ?? [])"
                    icono="🧭"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    {{ __('ui.catalog.no_guides') }}
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
