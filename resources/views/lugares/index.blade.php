@extends('layouts.app')

@section('title', 'Lugares Turísticos - TurismoHuila')

@section('content')
<x-page-header titulo="Lugares turísticos" descripcion="Desde el desierto rojizo hasta el macizo colombiano: los rincones más icónicos del Huila.">
    @auth
        @if(auth()->user()->esAdmin())
            <x-slot:actions>
                <a href="{{ route('lugares.create') }}" class="btn border-0"
                   style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1.5rem;">
                    + Nuevo Lugar
                </a>
            </x-slot:actions>
        @endif
    @endauth
</x-page-header>

<div class="container pb-5">
    @include('partials.filtro-municipio', ['rutaIndex' => 'lugares.index', 'municipios' => $municipios])

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($lugares as $lugar)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$lugar->imagen ? asset($lugar->imagen) : null"
                    :titulo="$lugar->nombre"
                    :ubicacion="$lugar->municipio . ' · ' . $lugar->categoria"
                    :rating="$lugar->calificacion"
                    :descripcion="Str::limit($lugar->descripcion, 110)"
                    :url="route('lugares.show', $lugar)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('lugares.edit', $lugar) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('lugares.destroy', $lugar) : null"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    No hay lugares registrados todavía.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
