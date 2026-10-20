@extends('layouts.app')

@section('title', $departamento . ' - ' . __('ui.destinos.title'))

@section('content')
<x-page-header
    :titulo="$departamento"
    :descripcion="$region !== null ? __('ui.destinos.detail_description', ['region' => $region, 'total' => $totalServicios]) : null"
>
    <x-slot:actions>
        <a href="{{ $regionSlug !== null ? route('destinos.index') . '#' . $regionSlug : route('destinos.index') }}"
           class="btn border fw-medium"
           style="border-color: var(--color-border); color: var(--color-fg); background: #fff; border-radius: 2rem; padding: .6rem 1.5rem;">
            ← {{ __('ui.destinos.back') }}
        </a>
    </x-slot:actions>
</x-page-header>

<div class="container pb-5">
    {{-- Resumen del departamento --}}
    <div class="d-flex flex-wrap align-items-center gap-2 mb-4">
        @if($region !== null)
            <span class="badge rounded-pill px-3 py-2" style="background-color: var(--color-primary); color: #fff; font-size: .8rem;">
                {{ $infoRegion['icono'] ?? '📍' }} {{ __('ui.destinos.in_region', ['region' => $region]) }}
            </span>
        @endif
        <span class="badge rounded-pill px-3 py-2" style="background-color: var(--color-muted); color: var(--color-muted-fg); font-size: .8rem;">
            {{ $totalServicios }} {{ __('ui.destinos.services_label') }}
        </span>
    </div>

    {{-- Municipios del catálogo oficial (DIVIPOLA) --}}
    @if(count($municipios) > 0)
        <section class="mb-5">
            <div class="d-flex flex-wrap align-items-baseline justify-content-between gap-2 mb-2">
                <h2 class="h5 mb-0" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">
                    <span aria-hidden="true">🗺️</span> {{ __('ui.destinos.municipios_title') }}
                </h2>
                <span class="small" style="color: var(--color-muted-fg);">{{ __('ui.destinos.municipios_note', ['count' => count($municipios)]) }}</span>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @foreach($municipios as $municipio)
                    <span class="badge rounded-pill px-3 py-2" style="background: #fff; border: 1px solid var(--color-border); color: var(--color-fg); font-weight: 500; font-size: .8rem;">{{ $municipio }}</span>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Servicios turísticos por módulo --}}
    <section class="mb-5" id="lugares">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h2 class="h4 mb-0" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">
                <span aria-hidden="true">🏞️</span> {{ __('ui.nav.places') }}
                <span class="badge rounded-pill ms-2 align-middle" style="background-color: var(--color-muted); color: var(--color-muted-fg); font-size: .75rem;">{{ $servicios['lugar']->count() }}</span>
            </h2>
            <a href="{{ route('lugares.index', ['departamento' => $departamento]) }}" class="small fw-semibold text-decoration-none" style="color: var(--color-primary);">
                {{ __('ui.destinos.see_all') }} →
            </a>
        </div>
        @if($servicios['lugar']->isEmpty())
            <div class="alert mb-0" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                {{ __('ui.catalog.no_places') }}
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @foreach($servicios['lugar'] as $lugar)
                    <div class="col tarjeta-item">
                        <x-card-turismo
                            :imagen="$lugar->url_imagen"
                            :titulo="$lugar->nombre"
                            :ubicacion="$lugar->ubicacion_texto . ' · ' . $lugar->categoria"
                            :rating="$lugar->resenas_avg_calificacion"
                            :descripcion="Str::limit($lugar->descripcion, 110)"
                            :url="route('lugares.show', $lugar)"
                            favoritoTipo="lugar"
                            :favoritoId="$lugar->id"
                            :esFavorito="in_array($lugar->id, $favoritos['lugar'] ?? [])"
                            icono="🏞️"
                        />
                    </div>
                @endforeach
            </div>
        @endif
    </section>
    <section class="mb-5" id="hoteles">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h2 class="h4 mb-0" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">
                <span aria-hidden="true">🏨</span> {{ __('ui.nav.hotels') }}
                <span class="badge rounded-pill ms-2 align-middle" style="background-color: var(--color-muted); color: var(--color-muted-fg); font-size: .75rem;">{{ $servicios['hotel']->count() }}</span>
            </h2>
            <a href="{{ route('hoteles.index', ['departamento' => $departamento]) }}" class="small fw-semibold text-decoration-none" style="color: var(--color-primary);">
                {{ __('ui.destinos.see_all') }} →
            </a>
        </div>
        @if($servicios['hotel']->isEmpty())
            <div class="alert mb-0" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                {{ __('ui.catalog.no_hotels') }}
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @foreach($servicios['hotel'] as $hotel)
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
                            favoritoTipo="hotel"
                            :favoritoId="$hotel->id"
                            :esFavorito="in_array($hotel->id, $favoritos['hotel'] ?? [])"
                            icono="🏨"
                        />
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="mb-5" id="restaurantes">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h2 class="h4 mb-0" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">
                <span aria-hidden="true">🍽️</span> {{ __('ui.nav.restaurants') }}
                <span class="badge rounded-pill ms-2 align-middle" style="background-color: var(--color-muted); color: var(--color-muted-fg); font-size: .75rem;">{{ $servicios['restaurante']->count() }}</span>
            </h2>
            <a href="{{ route('restaurantes.index', ['departamento' => $departamento]) }}" class="small fw-semibold text-decoration-none" style="color: var(--color-primary);">
                {{ __('ui.destinos.see_all') }} →
            </a>
        </div>
        @if($servicios['restaurante']->isEmpty())
            <div class="alert mb-0" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                {{ __('ui.catalog.no_restaurants') }}
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @foreach($servicios['restaurante'] as $restaurante)
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
                            favoritoTipo="restaurante"
                            :favoritoId="$restaurante->id"
                            :esFavorito="in_array($restaurante->id, $favoritos['restaurante'] ?? [])"
                            icono="🍽️"
                        />
                    </div>
                @endforeach
            </div>
        @endif
    </section>
    <section class="mb-5" id="tours">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h2 class="h4 mb-0" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">
                <span aria-hidden="true">🥾</span> {{ __('ui.nav.tours') }}
                <span class="badge rounded-pill ms-2 align-middle" style="background-color: var(--color-muted); color: var(--color-muted-fg); font-size: .75rem;">{{ $servicios['tour']->count() }}</span>
            </h2>
            <a href="{{ route('tours.index', ['departamento' => $departamento]) }}" class="small fw-semibold text-decoration-none" style="color: var(--color-primary);">
                {{ __('ui.destinos.see_all') }} →
            </a>
        </div>
        @if($servicios['tour']->isEmpty())
            <div class="alert mb-0" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                {{ __('ui.catalog.no_tours') }}
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @foreach($servicios['tour'] as $tour)
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
                            favoritoTipo="tour"
                            :favoritoId="$tour->id"
                            :esFavorito="in_array($tour->id, $favoritos['tour'] ?? [])"
                            icono="🥾"
                        />
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="mb-5" id="guias">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h2 class="h4 mb-0" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">
                <span aria-hidden="true">🧭</span> {{ __('ui.nav.guides') }}
                <span class="badge rounded-pill ms-2 align-middle" style="background-color: var(--color-muted); color: var(--color-muted-fg); font-size: .75rem;">{{ $servicios['guia']->count() }}</span>
            </h2>
            <a href="{{ route('guias.index', ['departamento' => $departamento]) }}" class="small fw-semibold text-decoration-none" style="color: var(--color-primary);">
                {{ __('ui.destinos.see_all') }} →
            </a>
        </div>
        @if($servicios['guia']->isEmpty())
            <div class="alert mb-0" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                {{ __('ui.catalog.no_guides') }}
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @foreach($servicios['guia'] as $guia)
                    <div class="col tarjeta-item">
                        <x-card-turismo
                            :imagen="$guia->url_imagen"
                            :titulo="$guia->nombre"
                            :ubicacion="$guia->zonas ?? __('ui.catalog.certified_guide')"
                            :rating="$guia->resenas_avg_calificacion"
                            :descripcion="Str::limit($guia->bio ?? $guia->experiencia_anios . ' años de experiencia · ' . $guia->idiomas, 110)"
                            :url="route('guias.show', $guia)"
                            favoritoTipo="guia"
                            :favoritoId="$guia->id"
                            :esFavorito="in_array($guia->id, $favoritos['guia'] ?? [])"
                            icono="🧭"
                        />
                    </div>
                @endforeach
            </div>
        @endif
    </section>
    <section class="mb-5" id="agencias">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h2 class="h4 mb-0" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">
                <span aria-hidden="true">🏢</span> {{ __('ui.nav.agencies') }}
                <span class="badge rounded-pill ms-2 align-middle" style="background-color: var(--color-muted); color: var(--color-muted-fg); font-size: .75rem;">{{ $servicios['agencia']->count() }}</span>
            </h2>
            <a href="{{ route('agencias.index', ['departamento' => $departamento]) }}" class="small fw-semibold text-decoration-none" style="color: var(--color-primary);">
                {{ __('ui.destinos.see_all') }} →
            </a>
        </div>
        @if($servicios['agencia']->isEmpty())
            <div class="alert mb-0" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                {{ __('ui.catalog.no_agencies') }}
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @foreach($servicios['agencia'] as $agencia)
                    <div class="col tarjeta-item">
                        <x-card-turismo
                            :imagen="$agencia->url_imagen"
                            :titulo="$agencia->nombre"
                            :ubicacion="$agencia->ubicacion_texto . ' · ' . $agencia->tours_count . ' tours'"
                            :rating="$agencia->resenas_avg_calificacion"
                            :descripcion="Str::limit($agencia->descripcion, 110)"
                            :url="route('agencias.show', $agencia)"
                            favoritoTipo="agencia"
                            :favoritoId="$agencia->id"
                            :esFavorito="in_array($agencia->id, $favoritos['agencia'] ?? [])"
                            icono="🏢"
                        />
                    </div>
                @endforeach
            </div>
        @endif
    </section>

    <section class="mb-5" id="transportes">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
            <h2 class="h4 mb-0" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">
                <span aria-hidden="true">🚐</span> {{ __('ui.nav.transport') }}
                <span class="badge rounded-pill ms-2 align-middle" style="background-color: var(--color-muted); color: var(--color-muted-fg); font-size: .75rem;">{{ $servicios['transporte']->count() }}</span>
            </h2>
            <a href="{{ route('transportes.index', ['departamento' => $departamento]) }}" class="small fw-semibold text-decoration-none" style="color: var(--color-primary);">
                {{ __('ui.destinos.see_all') }} →
            </a>
        </div>
        @if($servicios['transporte']->isEmpty())
            <div class="alert mb-0" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                {{ __('ui.catalog.no_transport') }}
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                @foreach($servicios['transporte'] as $transporte)
                    <div class="col tarjeta-item">
                        <x-card-turismo
                            :imagen="$transporte->url_imagen"
                            :titulo="$transporte->nombre"
                            :ubicacion="$transporte->ubicacion_texto"
                            :rating="$transporte->resenas_avg_calificacion"
                            :descripcion="Str::limit($transporte->descripcion, 110)"
                            :precio="$transporte->precio"
                            :precioLabel="$transporte->tipo"
                            :url="route('transportes.show', $transporte)"
                            favoritoTipo="transporte"
                            :favoritoId="$transporte->id"
                            :esFavorito="in_array($transporte->id, $favoritos['transporte'] ?? [])"
                            icono="🚐"
                        />
                    </div>
                @endforeach
            </div>
        @endif
    </section>
</div>
@endsection
