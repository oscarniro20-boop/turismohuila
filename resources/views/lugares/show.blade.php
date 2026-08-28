@extends('layouts.app')

@section('title', $lugar->nombre . ' - Lugares Turísticos')

@section('content')
    <div class="container py-4">
        <x-cabecera-detalle
            :imagen="$lugar->url_imagen"
            :titulo="$lugar->nombre"
            :categoria="$lugar->categoria"
            :favoritoTipo="'lugar'"
            :favoritoId="$lugar->id"
            :esFavorito="$esFav">

            <x-slot:meta>
                @if($lugar->municipio)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📍 {{ $lugar->municipio }}</span>@endif
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">⭐ {{ number_format($lugar->resenas->avg('calificacion') ?? 0, 1) }}</span>
            </x-slot:meta>
        </x-cabecera-detalle>

        <div class="row g-4" style="min-height: calc(100vh - var(--navbar-height, 90px));">
            <div class="col-lg-8">
                <p class="mb-3">@if($lugar->descripcion)<span style="float: left; font-family: 'Fraunces', Georgia, serif; font-size: 3.5rem; line-height: 0.8; padding-right: 0.5rem; color: var(--color-primary); font-weight: 600;">{{ mb_substr($lugar->descripcion, 0, 1) }}</span>{{ mb_substr($lugar->descripcion, 1) }}@else Sin descripción. @endif</p>

                {{-- Galería --}}
                @if($lugar->imagenes->count() > 0)
                    <x-galeria-grid :imagenes="$lugar->imagenes" :alt="$lugar->nombre" />
                @endif

                {{-- Reseñas --}}
                <h3 class="mt-4 mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">⭐ Reseñas</h3>
                @if($lugar->resenas->count() > 0)
                    @foreach($lugar->resenas as $resena)
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
                    <x-resena-formulario tipo="lugar" :referenciaId="$lugar->id" :nombre="$lugar->nombre" />
                @else
                    <p class="small mt-3">🔒 <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Inicia sesión</a> <span style="color: var(--color-muted-fg);">para dejar una reseña.</span></p>
                @endauth

                {{-- Acciones --}}
                <div class="d-flex flex-wrap gap-2 mt-4 pt-2">
                    <a href="{{ route('lugares.index') }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">← Volver</a>
                    @auth
                        @if(auth()->user()->esAdmin())
                            <a href="{{ route('lugares.edit', $lugar) }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-secondary); color: var(--color-secondary);">✏️ Editar</a>
                            <form action="{{ route('lugares.destroy', $lugar) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')" class="d-inline">
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
                    <x-tarjeta-compartir :titulo="$lugar->nombre" tipo="lugar" />
                </div>
            </div>
        </div>
    </div>
@endsection
