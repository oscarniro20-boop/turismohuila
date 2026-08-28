@extends('layouts.app')

@section('title', 'Transporte - TurismoHuila')

@section('content')
<x-page-header titulo="Transporte" descripcion="Buses, vans y servicios de transporte turístico verificados en el Huila.">
    @auth
        @if(auth()->user()->esAdmin())
            <x-slot:actions>
                <a href="{{ route('transportes.create') }}" class="btn border-0"
                   style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1.5rem;">
                    + Nuevo Transporte
                </a>
            </x-slot:actions>
        @endif
    @endauth
</x-page-header>

<div class="container pb-5">
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($transportes as $transporte)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$transporte->url_imagen"
                    :titulo="$transporte->nombre"
                    :ubicacion="$transporte->municipio"
                    :rating="$transporte->resenas_avg_calificacion"
                    :descripcion="Str::limit($transporte->descripcion, 110)"
                    :precio="$transporte->precio"
                    precioLabel="{{ $transporte->tipo }}"
                    :url="route('transportes.show', $transporte)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('transportes.edit', $transporte) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('transportes.destroy', $transporte) : null"
                    favoritoTipo="transporte"
                    :favoritoId="$transporte->id"
                    :esFavorito="in_array($transporte->id, $favoritosIds ?? [])"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    No hay servicios de transporte registrados todavía.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
