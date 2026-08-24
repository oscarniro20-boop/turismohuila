@extends('layouts.app')

@section('title', 'Tours - TurismoHuila')

@section('content')
<x-page-header titulo="Tours" descripcion="Experiencias guiadas por todo el Huila, con paquetes y agencias verificadas.">
    @auth
        @if(auth()->user()->esAdmin())
            <x-slot:actions>
                <a href="{{ route('tours.create') }}" class="btn border-0"
                   style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1.5rem;">
                    + Nuevo Tour
                </a>
            </x-slot:actions>
        @endif
    @endauth
</x-page-header>

<div class="container pb-5">

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($tours as $tour)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$tour->url_imagen"
                    :titulo="$tour->nombre"
                    :ubicacion="$tour->municipio"
                    :rating="$tour->resenas_avg_calificacion"
                    :descripcion="Str::limit($tour->descripcion, 110)"
                    :precio="$tour->precio"
                    :precioLabel="$tour->duracion_dias . ' día(s)'"
                    :url="route('tours.show', $tour)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('tours.edit', $tour) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('tours.destroy', $tour) : null"
                    favoritoTipo="tour"
                    :favoritoId="$tour->id"
                    :esFavorito="in_array($tour->id, $favoritosIds ?? [])"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    No hay tours registrados todavía.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
