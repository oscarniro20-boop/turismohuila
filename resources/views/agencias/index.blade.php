@extends('layouts.app')

@section('title', 'Agencias de Viaje - Turismo por Colombia')

@section('content')
<x-page-header :titulo="__('ui.catalog.agencies_title')" :descripcion="__('ui.catalog.agencies_description')">
    @auth
        @if(auth()->user()->esAdmin())
            <x-slot:actions>
                <a href="{{ route('agencias.create') }}" class="btn border-0"
                   style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1.5rem;">
                    + Nueva Agencia
                </a>
            </x-slot:actions>
        @endif
    @endauth
</x-page-header>

<div class="container pb-5">
    <x-filtro-ubicacion :accion="route('agencias.index')" />
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($agencias as $agencia)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$agencia->url_imagen"
                    :titulo="$agencia->nombre"
                    :ubicacion="$agencia->ubicacion_texto . ' · ' . $agencia->tours_count . ' tours'"
                    :rating="$agencia->resenas_avg_calificacion"
                                    :descripcion="Str::limit($agencia->descripcion, 110)"
                    :url="route('agencias.show', $agencia)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('agencias.edit', $agencia) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('agencias.destroy', $agencia) : null"
                    favoritoTipo="agencia"
                    :favoritoId="$agencia->id"
                    :esFavorito="in_array($agencia->id, $favoritosIds ?? [])"
                    icono="🏢"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    {{ __('ui.catalog.no_agencies') }}
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
