@extends('layouts.app')

@section('title', $restaurante->nombre . ' - Restaurantes')

@section('content')
<div class="container py-4">
    <x-cabecera-detalle
        :imagen="$restaurante->imagen ? $restaurante->url_imagen : null"
        :titulo="$restaurante->nombre"
        categoria="RESTAURANTE{{ $restaurante->municipio ? ' · ' . mb_strtoupper($restaurante->municipio) : '' }}"
        favoritoTipo="restaurante"
        :favoritoId="$restaurante->id"
        :esFavorito="$esFav"
        icono="🍽️"
    >
        <x-slot:meta>
            @if($restaurante->resenas->count() > 0)
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">⭐ {{ number_format($restaurante->resenas->avg('calificacion'), 1) }} ({{ $restaurante->resenas->count() }} {{ $restaurante->resenas->count() === 1 ? __('ui.detail.review') : __('ui.detail.reviews') }})</span>
            @else
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📝 {{ __('ui.detail.no_reviews') }}</span>
            @endif
            @if($restaurante->tipo_cocina)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">🍳 {{ $restaurante->tipo_cocina }}</span>@endif
            @if($restaurante->telefono)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📞 {{ $restaurante->telefono }}</span>@endif
        </x-slot:meta>
    </x-cabecera-detalle>

    <div class="row g-4" style="min-height: calc(100vh - var(--navbar-height, 90px));">
        <div class="col-lg-8">
            <p class="fw-semibold mb-3" style="color: var(--color-primary); font-size: 1.1rem;">
                Precio promedio: ${{ number_format($restaurante->precio_promedio, 0, ',', '.') }}
            </p>

            @if($restaurante->descripcion)
                <p class="mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 500; font-size: 1.3rem; line-height: 1.4;">
                    <span style="float: left; font-family: 'Fraunces', Georgia, serif; font-size: 3.3rem; line-height: 0.8; padding-right: 0.5rem; color: var(--color-primary); font-weight: 600;">{{ mb_substr($restaurante->descripcion, 0, 1) }}</span>{{ mb_substr($restaurante->descripcion, 1) }}
                </p>
            @endif

            @if($restaurante->imagenes->count() > 0)
                <x-galeria-grid :imagenes="$restaurante->imagenes->sortBy('orden')" :alt="$restaurante->nombre" />
            @endif

            <x-contacto-servicio
                :telefono="$restaurante->telefono"
                :texto="__('ui.contact.direct', ['name' => $restaurante->nombre])"
            />

            @if($restaurante->resenas->count() > 0)
                <div class="mt-4" style="clear: both;">
                    <h3 class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.1rem;">⭐ Lo que dicen</h3>
                    <div class="d-flex flex-column gap-2">
                        @foreach($restaurante->resenas as $resena)
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
                <x-resena-formulario tipo="restaurante" :referenciaId="$restaurante->id" :nombre="$restaurante->nombre" />
            @else
                <p class="small mt-3">🔒 <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">{{ __('ui.detail.login') }}</a> <span style="color: var(--color-muted-fg);">{{ __('ui.detail.login_review') }}</span></p>
            @endauth

            <div class="d-flex gap-2 mt-4 pt-3 flex-wrap" style="border-top: 1px solid var(--color-border);">
                <a href="{{ route('restaurantes.index') }}" class="btn rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">← {{ __('ui.detail.back') }}</a>
                @auth
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('restaurantes.edit', $restaurante) }}" class="btn btn-warning rounded-pill">✏️ Editar</a>
                        <form action="{{ route('restaurantes.destroy', $restaurante) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill">🗑️ Eliminar</button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-top d-flex flex-column gap-3" style="top: calc(var(--navbar-height, 90px) + 16px);">
                <x-tarjeta-compartir :titulo="$restaurante->nombre" tipo="restaurante" />
            </div>
        </div>
    </div>
</div>
@endsection
