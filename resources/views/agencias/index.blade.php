@extends('layouts.app')

@section('title', 'Agencias de Viaje - TurismoHuila')

@section('content')
<x-page-header titulo="Agencias de viaje" descripcion="Operadores turísticos certificados para planificar tu viaje por el Huila.">
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
    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($agencias as $agencia)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$agencia->imagen ? asset($agencia->imagen) : null"
                    :titulo="$agencia->nombre"
                    :ubicacion="$agencia->municipio . ' · ' . $agencia->tours_count . ' tours'"
                    :rating="$agencia->resenas_avg_calificacion"
                    :totalResenas="$agencia->total_resenas"
                    :descripcion="Str::limit($agencia->descripcion, 110)"
                    :url="route('agencias.show', $agencia)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('agencias.edit', $agencia) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('agencias.destroy', $agencia) : null"
                    favoritoTipo="agencia"
                    :favoritoId="$agencia->id"
                    :esFavorito="in_array($agencia->id, $favoritosIds ?? [])"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    No hay agencias registradas todavía.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
