@extends('layouts.app')

@section('title', 'Guías Turísticos - TurismoHuila')

@section('content')
<div class="container py-5">
    <p class="text-uppercase small fw-medium mb-2" style="letter-spacing: 0.08em; color: var(--color-muted-fg);">
        — TurismoHuila
    </p>
    <h1 class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: clamp(1.75rem, 4vw, 2.5rem); color: var(--color-fg);">
        Guías turísticos
    </h1>
    <p class="mb-4" style="max-width: 640px; color: var(--color-muted-fg);">
        Expertos locales que harán de tu visita una experiencia inolvidable.
    </p>

    @auth
        @if(auth()->user()->esAdmin())
            <a href="{{ route('guias.create') }}" class="btn border-0 mb-4"
               style="background-color: var(--color-primary); color:#fff; border-radius: 2rem; padding: .6rem 1.5rem;">
                + Nuevo Guía
            </a>
        @endif
    @endauth

    <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4 mt-1">
        @forelse($guias as $guia)
            <div class="col tarjeta-item">
                <x-card-turismo
                    :imagen="$guia->foto ? asset($guia->foto) : null"
                    :titulo="$guia->nombre"
                    :ubicacion="$guia->zonas ?? 'Guía turístico certificado'"
                    :rating="$guia->calificacion_promedio"
                    :totalResenas="$guia->total_resenas"
                    :descripcion="Str::limit($guia->bio ?? $guia->experiencia_anios . ' años de experiencia · ' . $guia->idiomas, 110)"
                    :url="route('guias.show', $guia)"
                    :editUrl="auth()->check() && auth()->user()->esAdmin() ? route('guias.edit', $guia) : null"
                    :deleteUrl="auth()->check() && auth()->user()->esAdmin() ? route('guias.destroy', $guia) : null"
                />
            </div>
        @empty
            <div class="col-12">
                <div class="alert" style="background-color: var(--color-muted); color: var(--color-muted-fg); border: none;">
                    No hay guías registrados todavía.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection
