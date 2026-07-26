@extends('layouts.app')

@section('title', 'Agencias de Viaje - TurismoHuila')

@section('content')
<div class="container py-5">
    <p class="text-uppercase small fw-medium mb-2" style="letter-spacing: 0.08em; color: var(--color-muted-fg);">
        — TurismoHuila
    </p>
    <h1 class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: clamp(1.75rem, 4vw, 2.5rem); color: var(--color-fg);">
        Agencias de viaje
    </h1>
    <p class="mb-4" style="max-width: 640px; color: var(--color-muted-fg);">
        Operadores turísticos certificados para planificar tu viaje por el Huila.
    </p>

    @auth
        @if(auth()->user()->esAdmin())
            <a href="{{ route('agencias.create') }}" class="btn border-0 mb-4"
               style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1.5rem;">
                + Nueva Agencia
            </a>
        @endif
    @endauth

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($agencias as $agencia)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$agencia->imagen ? asset($agencia->imagen) : null"
                    :titulo="$agencia->nombre"
                    :ubicacion="$agencia->municipio . ' · ' . $agencia->tours_count . ' tours'"
                    :rating="$agencia->calificacion_promedio"
                    :totalResenas="$agencia->total_resenas"
                    :descripcion="Str::limit($agencia->descripcion, 110)"
                    :url="route('agencias.show', $agencia)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('agencias.edit', $agencia) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('agencias.destroy', $agencia) : null"
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
