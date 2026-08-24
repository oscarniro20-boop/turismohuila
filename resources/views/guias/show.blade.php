@extends('layouts.app')

@section('title', $guia->nombre . ' - Guía Turístico')

@section('content')
    <div class="container py-4">
        {{-- Header: foto circular + nombre + badge --}}
        <div class="rounded-4 p-4 p-md-5 mb-4 d-flex align-items-center gap-4" style="background: var(--color-muted); box-shadow: var(--shadow-ring);">
            @if($guia->foto)
                <img src="{{ $guia->url_imagen }}" alt="{{ $guia->nombre }}" class="rounded-circle object-fit-cover flex-shrink-0" style="width: 108px; height: 108px;">
            @else
                <span class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 108px; height: 108px; background: var(--color-border); font-size: 3rem;">🏞️</span>
            @endif
            <div class="d-flex flex-column">
                <h1 class="mb-1" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: clamp(1.4rem, 3vw, 2rem);">
                    {{ $guia->nombre }}
                </h1>
                <span class="badge rounded-pill" style="background: var(--color-primary); color: #fff; font-size: .8rem; width: max-content;">✔️ Guía local verificado</span>
            </div>
        </div>

        <div class="row g-4" style="min-height: calc(100vh - var(--navbar-height, 90px));">
            <div class="col-lg-8">
                @if($guia->bio)
                    <p class="mb-3">{{ $guia->bio }}</p>
                @endif

                {{-- Lugares que acompaña --}}
                @if($guia->lugares->count() > 0)
                    <div class="mb-4">
                        <h3 class="mb-2" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">🏞️ Lugares que acompaña</h3>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($guia->lugares as $lugar)
                                <a href="{{ route('lugares.show', $lugar) }}" class="badge rounded-pill px-3 py-2" style="background: var(--color-muted); color: var(--color-fg); border: 1px solid var(--color-border);">
                                    {{ $lugar->nombre }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Galería --}}
                @if($guia->imagenes->count() > 0)
                    <x-galeria-grid :imagenes="$guia->imagenes->sortBy('orden')" :alt="$guia->nombre" />
                @endif

                <x-barra-contacto :telefono="$guia->telefono" texto="Contacta directamente a {{ $guia->nombre }} para reservar o preguntar disponibilidad." />

                {{-- Reseñas --}}
                <h3 class="mt-4 mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">⭐ Reseñas</h3>
                @if($guia->resenas->count() > 0)
                    @foreach($guia->resenas as $resena)
                        <div class="rounded-4 p-3 border mb-2" style="border-color: var(--color-border);">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>{{ $resena->user->name }}</strong>
                                <span style="color: var(--color-gold);"><x-estrellas :calificacion="$resena->calificacion" /></span>
                            </div>
                            <p class="mb-0 mt-1">{{ $resena->comentario }}</p>
                        </div>
                    @endforeach
                @else
                    <p class="text-muted small">Aún no hay reseñas. ¡Sé el primero!</p>
                @endif
                @auth
                    <a href="{{ route('resenas.create', ['tipo' => 'guia', 'referencia_id' => $guia->id]) }}" class="btn btn-sm rounded-pill mt-2" style="background: var(--color-primary); color: #fff;">✍️ Escribir Reseña</a>
                @else
                    <a href="{{ route('login') }}" class="text-decoration-none small">Inicia sesión</a> <span class="text-muted small">para dejar una reseña.</span>
                @endauth

                {{-- Acciones --}}
                <div class="d-flex flex-wrap gap-2 mt-4 pt-2">
                    <a href="{{ route('guias.index') }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">← Volver</a>
                    @auth
                        @if(auth()->user()->esAdmin())
                            <a href="{{ route('guias.edit', $guia) }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-secondary); color: var(--color-secondary);">✏️ Editar</a>
                            <form action="{{ route('guias.destroy', $guia) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm rounded-pill" style="background: var(--color-danger); color: #fff;">🗑️ Eliminar</button>
                            </form>
                        @endif
                    @endauth
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-top" style="top: calc(var(--navbar-height, 90px) + 16px);">
                    <x-tarjeta-compartir :titulo="$guia->nombre" tipo="guia" />
                </div>
            </div>
        </div>
    </div>
@endsection
