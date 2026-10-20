@extends('layouts.app')

@section('title', __('ui.destinos.title') . ' - Turismo por Colombia')

@section('content')
<x-page-header :titulo="__('ui.destinos.title')" :descripcion="__('ui.destinos.description')" />

<div class="container pb-5">
    {{-- Navegación rápida a las seis regiones naturales --}}
    <nav class="d-flex flex-wrap gap-2 mb-4" aria-label="{{ __('ui.destinos.regions_nav') }}">
        @foreach($regiones as $region)
            <a href="#{{ $region['slug'] }}" class="btn btn-sm rounded-pill px-3 py-2 border fw-medium text-nowrap"
               style="border-color: var(--color-border); background: #fff; color: var(--color-fg);">
                <span aria-hidden="true">{{ $region['icono'] }}</span> {{ $region['nombre'] }}
                <span class="ms-1" style="color: var(--color-muted-fg);">({{ count($region['departamentos']) }})</span>
            </a>
        @endforeach
    </nav>

    @foreach($regiones as $region)
        <section id="{{ $region['slug'] }}" class="mb-5">
            <div class="d-flex align-items-start gap-3 mb-3">
                <span aria-hidden="true" style="font-size: 2rem; line-height: 1;">{{ $region['icono'] }}</span>
                <div>
                    <h2 class="h4 mb-1" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg);">{{ $region['nombre'] }}</h2>
                    @if($region['descripcion'])
                        <p class="mb-0 small" style="color: var(--color-muted-fg);">{{ $region['descripcion'] }}</p>
                    @endif
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-3">
                @foreach($region['departamentos'] as $departamento)
                    <div class="col">
                        <a href="{{ route('destinos.show', $departamento['slug']) }}"
                           class="d-flex align-items-center justify-content-between gap-3 h-100 rounded-4 text-decoration-none hover-lift p-3"
                           style="box-shadow: var(--shadow-ring); background: #fff; border: 1px solid var(--color-border);">
                            <span>
                                <span class="d-block fw-semibold" style="font-family: 'Fraunces', Georgia, serif; color: var(--color-fg); font-size: 1.05rem;">{{ $departamento['nombre'] }}</span>
                                <small style="color: var(--color-muted-fg);">
                                    @if($departamento['servicios'] > 0)
                                        {{ $departamento['servicios'] }} {{ __('ui.destinos.services_label') }}
                                    @else
                                        {{ __('ui.destinos.no_services_short') }}
                                    @endif
                                </small>
                            </span>
                            <span aria-hidden="true" style="color: var(--color-primary);">→</span>
                        </a>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach
</div>
@endsection
