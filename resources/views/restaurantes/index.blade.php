@extends('layouts.app')

@section('title', 'Restaurantes - TurismoHuila')

@section('content')
<x-page-header titulo="Restaurantes" descripcion="Sabores del Huila: cocina tradicional huilense, internacional y todo lo que se sirve en el departamento.">
    @auth
        @if(auth()->user()->esAdmin())
            <x-slot:actions>
                <a href="{{ route('restaurantes.create') }}" class="btn border-0"
                   style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1.5rem;">
                    + Nuevo Restaurante
                </a>
            </x-slot:actions>
        @endif
    @endauth
</x-page-header>

<div class="container pb-5">

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($restaurantes as $restaurante)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$restaurante->imagen ? asset($restaurante->imagen) : null"
                    :titulo="$restaurante->nombre"
                    :ubicacion="$restaurante->municipio . ' · ' . $restaurante->tipo_cocina"
                    :rating="$restaurante->resenas_avg_calificacion"
                    :descripcion="Str::limit($restaurante->descripcion, 110)"
                    :precio="$restaurante->precio_promedio"
                    precioLabel="promedio / persona"
                    :url="route('restaurantes.show', $restaurante)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('restaurantes.edit', $restaurante) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('restaurantes.destroy', $restaurante) : null"
                    favoritoTipo="restaurante"
                    :favoritoId="$restaurante->id"
                    :esFavorito="in_array($restaurante->id, $favoritosIds ?? [])"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    No hay restaurantes registrados todavía.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
