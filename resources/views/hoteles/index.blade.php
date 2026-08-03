@extends('layouts.app')

@section('title', 'Hoteles - TurismoHuila')

@section('content')
<x-page-header titulo="Hoteles" descripcion="Alojamientos verificados en todo el departamento del Huila, de cabañas rurales a hoteles boutique.">
    @auth
        @if(auth()->user()->esAdmin())
            <x-slot:actions>
                <a href="{{ route('hoteles.create') }}" class="btn border-0"
                   style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1.5rem;">
                    + Nuevo Hotel
                </a>
            </x-slot:actions>
        @endif
    @endauth
</x-page-header>

<div class="container pb-5">

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($hoteles as $hotel)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$hotel->imagen ? asset($hotel->imagen) : null"
                    :titulo="$hotel->nombre"
                    :ubicacion="$hotel->municipio"
                    :rating="$hotel->resenas_avg_calificacion"
                    :descripcion="Str::limit($hotel->descripcion, 110)"
                    :precio="$hotel->precio_noche"
                    precioLabel="/ noche"
                    :url="route('hoteles.show', $hotel)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('hoteles.edit', $hotel) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('hoteles.destroy', $hotel) : null"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    No hay hoteles registrados todavía.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
