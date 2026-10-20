@extends('layouts.app')

@section('title', $transporte->nombre . ' - Transporte')

@section('content')
<div class="container py-4">
    <x-cabecera-detalle
        :imagen="$transporte->imagen ? $transporte->url_imagen : null"
        :titulo="$transporte->nombre"
        categoria="{{ mb_strtoupper($transporte->tipo) }}{{ $transporte->municipio ? ' · ' . mb_strtoupper($transporte->municipio) : '' }}"
        favoritoTipo="transporte"
        :favoritoId="$transporte->id"
        :esFavorito="$esFav"
        icono="🚐"
    >
        <x-slot:meta>
            @if($transporte->capacidad)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">👥 {{ __('ui.detail.capacity') }} {{ $transporte->capacidad }}</span>@endif
            @if($transporte->resenas->count() > 0)
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">⭐ {{ number_format($transporte->resenas->avg('calificacion'), 1) }} ({{ $transporte->resenas->count() }} {{ $transporte->resenas->count() === 1 ? __('ui.detail.review') : __('ui.detail.reviews') }})</span>
            @else
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📝 {{ __('ui.detail.no_reviews') }}</span>
            @endif
            @if($transporte->telefono)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📞 {{ $transporte->telefono }}</span>@endif
        </x-slot:meta>
    </x-cabecera-detalle>

    <div class="row g-4" style="min-height: calc(100vh - var(--navbar-height, 90px));">
        <div class="col-lg-8">
            @if($transporte->imagenes->count() > 0)
                <x-galeria-grid :imagenes="$transporte->imagenes" :alt="$transporte->nombre" />
            @endif

            @if($transporte->descripcion)
                <span class="d-block mt-4 mb-2 fw-semibold small" style="color: var(--color-primary); letter-spacing: 0.05em;">LA EXPERIENCIA</span>
                <p class="mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 500; font-size: 1.3rem; line-height: 1.4;">
                    <span style="float: left; font-family: 'Fraunces', Georgia, serif; font-size: 3.3rem; line-height: 0.8; padding-right: 0.5rem; color: var(--color-primary); font-weight: 600;">{{ mb_substr($transporte->descripcion, 0, 1) }}</span>{{ mb_substr($transporte->descripcion, 1) }}
                </p>
            @endif

            @if($transporte->precio)
                <div class="rounded-4 p-3 mb-4" style="background: var(--color-muted);">
                    <span class="small" style="color: var(--color-muted-fg);">{{ __('ui.detail.price') }}</span>
                    <div class="fw-bold" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.6rem; color: var(--color-primary);">${{ number_format($transporte->precio, 0, ',', '.') }}</div>
                </div>
            @endif

            <div class="row row-cols-1 row-cols-sm-3 g-3 mb-4" style="clear: both;">
                <div class="col">
                    <div class="rounded-4 p-3 h-100" style="background: var(--color-muted);">
                        <div class="mb-1">📍</div>
                        <div class="fw-semibold small">Municipio</div>
                        <div class="small" style="color: var(--color-muted-fg);">{{ $transporte->municipio ?: 'No especificado' }}</div>
                    </div>
                </div>
                @if($transporte->capacidad)
                <div class="col">
                    <div class="rounded-4 p-3 h-100" style="background: var(--color-muted);">
                        <div class="mb-1">✨</div>
                        <div class="fw-semibold small">Destacado</div>
                        <div class="small" style="color: var(--color-muted-fg);">{{ __('ui.detail.up_to_passengers', ['count' => $transporte->capacidad]) }}</div>
                    </div>
                </div>
                @endif
                <div class="col">
                    <div class="rounded-4 p-3 h-100" style="background: var(--color-muted);">
                        <div class="mb-1">🛡️</div>
                        <div class="fw-semibold small">Confianza</div>
                        <div class="small" style="color: var(--color-muted-fg);">{{ __('ui.detail.verified_reviews') }}</div>
                    </div>
                </div>
            </div>

            <x-contacto-servicio
                :telefono="$transporte->telefono"
                :texto="__('ui.contact.direct', ['name' => $transporte->nombre])"
            />

            @if($transporte->resenas->count() > 0)
                <div class="mt-4" style="clear: both;">
                    <div class="d-flex justify-content-between align-items-end mb-3">
                        <div>
                            <span class="d-block mb-1 fw-semibold small" style="color: var(--color-primary); letter-spacing: 0.05em;">COMUNIDAD</span>
                            <h3 class="mb-0" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.3rem;">Lo que dicen los viajeros</h3>
                        </div>
                        <span class="fw-semibold" style="color: var(--color-primary);">⭐ {{ number_format($transporte->resenas->avg('calificacion'), 1) }}</span>
                    </div>
                    <div class="row row-cols-1 row-cols-md-2 g-3">
                        @foreach($transporte->resenas as $resena)
                            <div class="col">
                                <div class="rounded-4 p-3 border h-100" style="border-color: var(--color-border);">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <strong>{{ $resena->user?->name ?? 'Usuario' }}</strong>
                                        <x-estrellas :calificacion="$resena->calificacion" />
                                    </div>
                                    @if($resena->comentario)
                                        <p class="mb-0 small fst-italic" style="color: var(--color-muted-fg);">"{{ $resena->comentario }}"</p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @auth
                <x-resena-formulario tipo="transporte" :referenciaId="$transporte->id" :nombre="$transporte->nombre" />
            @else
                <p class="small mt-3">🔒 <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">{{ __('ui.detail.login') }}</a> <span style="color: var(--color-muted-fg);">{{ __('ui.detail.login_review') }}</span></p>
            @endauth

            <div class="d-flex gap-2 mt-4 pt-3 flex-wrap" style="border-top: 1px solid var(--color-border);">
                <a href="{{ route('transportes.index') }}" class="btn rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">← {{ __('ui.detail.back') }}</a>
                @auth
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('transportes.edit', $transporte) }}" class="btn btn-warning rounded-pill">✏️ Editar</a>
                        <form action="{{ route('transportes.destroy', $transporte) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')">
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
                <x-tarjeta-compartir :titulo="$transporte->nombre" tipo="transporte" />
            </div>
        </div>
    </div>
</div>
@endsection
