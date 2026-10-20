@extends('layouts.app')

@section('title', $lugar->nombre . ' - Lugares Turísticos')

@section('content')
<div class="container py-4">
    <x-cabecera-detalle
        :imagen="$lugar->imagen ? $lugar->url_imagen : null"
        :titulo="$lugar->nombre"
        categoria="{{ $lugar->categoria ? mb_strtoupper($lugar->categoria) . ' · ' : '' }}HUILA, COLOMBIA"
        favoritoTipo="lugar"
        :favoritoId="$lugar->id"
        :esFavorito="$esFav"
        icono="🏞️"
    >
        <x-slot:meta>
            @if($lugar->municipio)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📍 {{ $lugar->municipio }}</span>@endif
            @if($lugar->resenas->count() > 0)
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">⭐ {{ number_format($lugar->resenas->avg('calificacion'), 1) }} ({{ $lugar->resenas->count() }} {{ $lugar->resenas->count() === 1 ? __('ui.detail.review') : __('ui.detail.reviews') }})</span>
            @else
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📝 {{ __('ui.detail.no_reviews') }}</span>
            @endif
        </x-slot:meta>
    </x-cabecera-detalle>

    <div class="row g-4" style="min-height: calc(100vh - var(--navbar-height, 90px));">
        <div class="col-lg-8">
            @if($lugar->descripcion)
                <p class="mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 500; font-size: 1.3rem; line-height: 1.4;">
                    <span style="float: left; font-family: 'Fraunces', Georgia, serif; font-size: 3.3rem; line-height: 0.8; padding-right: 0.5rem; color: var(--color-primary); font-weight: 600;">{{ mb_substr($lugar->descripcion, 0, 1) }}</span>{{ mb_substr($lugar->descripcion, 1) }}
                </p>
            @endif

            @if($lugar->imagenes->count() > 0)
                <x-galeria-grid :imagenes="$lugar->imagenes" :alt="$lugar->nombre" />
            @endif

            @if($lugar->resenas->count() > 0)
                <div class="mt-4" style="clear: both;">
                    <h3 class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.1rem;">⭐ Lo que dicen</h3>
                    <div class="d-flex flex-column gap-2">
                        @foreach($lugar->resenas as $resena)
                            <div class="rounded-4 p-3 border" style="border-color: var(--color-border);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <strong>{{ $resena->user?->name ?? 'Usuario' }}</strong>
                                    <x-estrellas :calificacion="$resena->calificacion" />
                                </div>
                                @if($resena->comentario)
                                    <p class="mb-0 mt-1 small" style="color: var(--color-muted-fg);">{{ $resena->comentario }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @auth
                <x-resena-formulario tipo="lugar" :referenciaId="$lugar->id" :nombre="$lugar->nombre" />
            @else
                <p class="small mt-3">🔒 <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">{{ __('ui.detail.login') }}</a> <span style="color: var(--color-muted-fg);">{{ __('ui.detail.login_review') }}</span></p>
            @endauth


            <div class="d-flex gap-2 mt-4 pt-3 flex-wrap" style="border-top: 1px solid var(--color-border);">
                <a href="{{ route('lugares.index') }}" class="btn rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">← {{ __('ui.detail.back') }}</a>
                @auth
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('lugares.edit', $lugar) }}" class="btn btn-warning rounded-pill">✏️ Editar</a>
                        <form action="{{ route('lugares.destroy', $lugar) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill">🗑️ Eliminar</button>
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
