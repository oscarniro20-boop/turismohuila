@extends('layouts.app')

@section('title', $guia->nombre . ' - Guía Turístico')

@section('content')
<div class="container py-4">

    {{-- Portada con badges superpuestos --}}
    <div class="position-relative rounded-4 overflow-hidden mb-4" style="box-shadow: var(--shadow-ring);">
        @if($guia->foto)
            <img src="{{ $guia->url_imagen }}" alt="{{ $guia->nombre }}" class="w-100" style="height: 280px; object-fit: cover;">
        @else
            <div class="w-100" style="height: 200px; background: var(--color-primary);"></div>
        @endif
        <div class="position-absolute bottom-0 start-0 w-100 p-4" style="background: linear-gradient(to top, rgba(20,18,12,0.75) 0%, rgba(20,18,12,0) 100%);">
            <div class="d-flex flex-wrap gap-2">
                @if($guia->experiencia_anios)
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">🕐 {{ $guia->experiencia_anios }} años de experiencia</span>
                @endif
                @if($guia->idiomas)
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">🌐 {{ $guia->idiomas }}</span>
                @endif
                @if($guia->telefono)
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📞 {{ $guia->telefono }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4" style="min-height: calc(100vh - var(--navbar-height, 90px));">
        <div class="col-lg-8">

            {{-- Bio como titular con capitular --}}
            @if($guia->bio)
                <p class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 500; font-size: 1.4rem; line-height: 1.35;">
                    <span style="float: left; font-family: 'Fraunces', Georgia, serif; font-size: 3.5rem; line-height: 0.8; padding-right: 0.5rem; color: var(--color-primary); font-weight: 600;">{{ mb_substr($guia->bio, 0, 1) }}</span>{{ mb_substr($guia->bio, 1) }}
                </p>
            @endif

            @if($guia->zonas)
                <p class="mb-4 small" style="color: var(--color-muted-fg); clear: both;">📍 {{ $guia->zonas }}</p>
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

            {{-- Byline: foto + nombre + verificado --}}
            <div class="d-flex align-items-center gap-3 mb-4 p-3 rounded-4" style="background: var(--color-muted);">
                @if($guia->foto)
                    <img src="{{ $guia->url_imagen }}" alt="{{ $guia->nombre }}" class="rounded-circle flex-shrink-0" style="width: 64px; height: 64px; object-fit: cover;">
                @else
                    <span class="rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px; background: var(--color-border); font-size: 1.75rem;">🏞️</span>
                @endif
                <div>
                    <div class="fw-semibold">{{ $guia->nombre }}</div>
                    <span class="badge rounded-pill mt-1" style="background: var(--color-primary); color: #fff; font-size: .75rem;">✔️ Guía local verificado</span>
                </div>
                @auth
                    @if(!auth()->user()->esAdmin())
                        <div class="ms-auto">
                            <x-favorito-boton tipo="guia" :id="$guia->id" :activo="$esFav" />
                        </div>
                    @endif
                @endauth
            </div>

            {{-- Galería --}}
            @if($guia->imagenes->count() > 0)
                <x-galeria-grid :imagenes="$guia->imagenes->sortBy('orden')" :alt="$guia->nombre" />
            @endif


            {{-- Reseñas --}}
            <h3 class="mt-4 mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">⭐ Reseñas</h3>
            @if($guia->resenas->count() > 0)
                @foreach($guia->resenas as $resena)
                    <div class="rounded-4 p-3 border mb-2" style="border-color: var(--color-border);">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>{{ $resena->user->name }}</strong>
                            <span style="color: var(--color-gold);"><x-estrellas :calificacion="$resena->calificacion" /></span>
                        </div>
                        @if($resena->comentario)<p class="mb-0 mt-1">{{ $resena->comentario }}</p>@endif
                    </div>
                @endforeach
            @else
                <p class="text-muted small">Aún no hay reseñas. ¡Sé el primero!</p>
            @endif
            @auth
                <x-resena-formulario tipo="guia" :referenciaId="$guia->id" :nombre="$guia->nombre" />
            @else
                <p class="small mt-3">🔒 <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Inicia sesión</a> <span style="color: var(--color-muted-fg);">para dejar una reseña.</span></p>
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
            <div class="sticky-top d-flex flex-column gap-3" style="top: calc(var(--navbar-height, 90px) + 16px);">
                <x-contacto-servicio :telefono="$guia->telefono" texto="Contacta directamente a {{ $guia->nombre }} para reservar o preguntar disponibilidad." />
                <x-tarjeta-compartir :titulo="$guia->nombre" tipo="guia" />
            </div>
        </div>
    </div>
</div>
@endsection
