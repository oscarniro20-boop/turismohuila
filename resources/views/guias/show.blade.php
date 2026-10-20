@extends('layouts.app')

@section('title', $guia->nombre . ' - ' . __('ui.catalog.guides_title'))

@section('content')
<div class="container py-4">
    <x-cabecera-detalle
        :imagen="$guia->foto ? $guia->url_imagen : null"
        :titulo="$guia->nombre"
        :categoria="mb_strtoupper(__('ui.catalog.guides_title')) . ($guia->zonas ? ' · ' . mb_strtoupper($guia->zonas) : '')"
        favoritoTipo="guia"
        :favoritoId="$guia->id"
        :esFavorito="$esFav"
        icono="🧑‍🏫"
    >
        <x-slot:meta>
            @if($guia->experiencia_anios)
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">🕐 {{ $guia->experiencia_anios }} {{ __('ui.detail.years_experience') }}</span>
            @endif
            @if($guia->idiomas)
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">🌐 {{ $guia->idiomas }}</span>
            @endif
            @if($guia->resenas->count() > 0)
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">⭐ {{ number_format($guia->resenas->avg('calificacion'), 1) }} ({{ $guia->resenas->count() }} {{ $guia->resenas->count() === 1 ? __('ui.detail.review') : __('ui.detail.reviews') }})</span>
            @else
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📝 {{ __('ui.detail.no_reviews') }}</span>
            @endif
            @if($guia->telefono)
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📞 {{ $guia->telefono }}</span>
            @endif
        </x-slot:meta>
    </x-cabecera-detalle>

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
                    <h3 class="mb-2" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">🏞️ {{ __('ui.detail.places_guides') }}</h3>

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
                    <span class="badge rounded-pill mt-1" style="background: var(--color-primary); color: #fff; font-size: .75rem;">✔️ {{ __('ui.detail.verified_guide') }}</span>
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

            <x-contacto-servicio
                :telefono="$guia->telefono"
                :whatsapp="$guia->whatsapp"
                :email="$guia->email"
                :texto="__('ui.contact.direct', ['name' => $guia->nombre])"
            />


            {{-- Reseñas --}}
            <h3 class="mt-4 mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">⭐ {{ __('ui.detail.reviews_title') }}</h3>
            @if($guia->resenas->count() > 0)
                @foreach($guia->resenas as $resena)
                    <div class="rounded-4 p-3 border mb-2" style="border-color: var(--color-border);">
                        <div class="d-flex justify-content-between align-items-center">
                            <strong>{{ $resena->user?->name ?? 'Usuario' }}</strong>
                            <span style="color: var(--color-gold);"><x-estrellas :calificacion="$resena->calificacion" /></span>
                        </div>
                        @if($resena->comentario)<p class="mb-0 mt-1">{{ $resena->comentario }}</p>@endif
                    </div>
                @endforeach
            @else
                <p class="text-muted small">{{ __('ui.detail.no_reviews_cta') }}</p>
            @endif
            @auth
                <x-resena-formulario tipo="guia" :referenciaId="$guia->id" :nombre="$guia->nombre" />
            @else
                <p class="small mt-3">🔒 <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">{{ __('ui.detail.login') }}</a> <span style="color: var(--color-muted-fg);">{{ __('ui.detail.login_review') }}</span></p>
            @endauth

            {{-- Acciones --}}
            <div class="d-flex flex-wrap gap-2 mt-4 pt-2">
                <a href="{{ route('guias.index') }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">← {{ __('ui.detail.back') }}</a>
                @auth
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('guias.edit', $guia) }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-secondary); color: var(--color-secondary);">✏️ {{ __('ui.detail.edit') }}</a>
                        <form action="{{ route('guias.destroy', $guia) }}" method="POST" data-confirm="{{ __('ui.detail.delete_confirm') }}" onsubmit="return confirm(this.dataset.confirm)" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm rounded-pill" style="background: var(--color-danger); color: #fff;">🗑️ {{ __('ui.detail.delete') }}</button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-top d-flex flex-column gap-3" style="top: calc(var(--navbar-height, 90px) + 16px);">
                <x-tarjeta-compartir :titulo="$guia->nombre" tipo="guia" />
            </div>
        </div>
    </div>
</div>
@endsection
