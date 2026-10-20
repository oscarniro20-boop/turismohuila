@extends('layouts.app')

@section('title', $tour->nombre . ' - ' . __('ui.catalog.tours_title'))

@section('content')
<div class="container py-4">
    <x-cabecera-detalle
        :imagen="$tour->imagen ? $tour->url_imagen : null"
        :titulo="$tour->nombre"
        :categoria="mb_strtoupper(__('ui.catalog.tours_title')) . ($tour->municipio ? ' · ' . mb_strtoupper($tour->municipio) : '')"
        favoritoTipo="tour"
        :favoritoId="$tour->id"
        :esFavorito="$esFav"
        icono="🥾"
    >
        <x-slot:meta>
            <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">⏱️ {{ $tour->duracion_dias }} {{ __('ui.detail.duration') }}</span>
            <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">👥 {{ __('ui.detail.quota') }} {{ $tour->cupo_maximo }}</span>
            @if($tour->resenas->count() > 0)
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">⭐ {{ number_format($tour->resenas->avg('calificacion'), 1) }} ({{ $tour->resenas->count() }} {{ $tour->resenas->count() === 1 ? __('ui.detail.review') : __('ui.detail.reviews') }})</span>
            @else
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📝 {{ __('ui.detail.no_reviews') }}</span>
            @endif
        </x-slot:meta>
    </x-cabecera-detalle>

    <div class="row g-4" style="min-height: calc(100vh - var(--navbar-height, 90px));">
        <div class="col-lg-8">
            @if($tour->descripcion)
                <p class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 500; font-size: 1.3rem; line-height: 1.4;">
                    <span style="float: left; font-family: 'Fraunces', Georgia, serif; font-size: 3.3rem; line-height: 0.8; padding-right: 0.5rem; color: var(--color-primary); font-weight: 600;">{{ mb_substr($tour->descripcion, 0, 1) }}</span>{{ mb_substr($tour->descripcion, 1) }}
                </p>
            @endif
            @if($tour->incluye)
                <p class="small mb-4" style="color: var(--color-muted-fg); clear: both;"><strong>{{ __('ui.detail.include') }}</strong> {{ $tour->incluye }}</p>
            @endif

            <div class="mb-4">
                <h2 class="mb-1" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.3rem;">{{ __('ui.detail.book_agencies') }}</h2>
                <p class="small mb-3" style="color: var(--color-muted-fg);">{{ __('ui.detail.compare_booking') }}</p>
                @forelse($tour->paquetes as $paquete)
                    <div class="rounded-4 p-3 mb-2 border" style="border-color: var(--color-border);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ optional($paquete->agencia)->nombre ?? __('ui.detail.deleted_agency') }}</strong>
                                <div class="small" style="color: var(--color-muted-fg);">{{ optional($paquete->agencia)->municipio }}</div>
                            </div>
                            @if($paquete->agencia)
                                <a href="{{ route('agencias.show', $paquete->agencia) }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">{{ __('ui.detail.view_agency') }}</a>
                            @endif
                        </div>
                        <p class="small mb-0 mt-1" style="color: var(--color-muted-fg);">
                            @if($paquete->precio) <span class="fw-semibold" style="color: var(--color-primary);">${{ number_format($paquete->precio, 0, ',', '.') }}</span> @endif
                            @if($paquete->duracion_dias) · {{ $paquete->duracion_dias }} {{ __('ui.detail.duration') }} @endif
                            @if($paquete->cupo_maximo) · {{ __('ui.detail.quota') }} {{ $paquete->cupo_maximo }} @endif
                        </p>
                    </div>
                @empty
                    <div class="rounded-4 p-4 text-center" style="background: var(--color-muted); color: var(--color-muted-fg);">
                        {{ __('ui.detail.no_agencies') }}
                    </div>
                @endforelse
            </div>

            @if($tour->imagenes->count() > 0)
                <x-galeria-grid :imagenes="$tour->imagenes->sortBy('orden')" :alt="$tour->nombre" />
            @endif

            <x-contacto-servicio
                :telefono="$tour->telefono_contacto"
                :texto="__('ui.contact.direct', ['name' => $tour->nombre])"
            />

            @if($tour->resenas->count() > 0)
                <div class="mt-4">
                    <h3 class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.1rem;">⭐ {{ __('ui.detail.what_they_say') }}</h3>
                    <div class="d-flex flex-column gap-2">
                        @foreach($tour->resenas as $resena)
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
                <x-resena-formulario tipo="tour" :referenciaId="$tour->id" :nombre="$tour->nombre" />
            @else
                <p class="small mt-3">🔒 <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">{{ __('ui.detail.login') }}</a> <span style="color: var(--color-muted-fg);">{{ __('ui.detail.login_review') }}</span></p>
            @endauth

            <div class="d-flex gap-2 mt-4 pt-3 flex-wrap" style="border-top: 1px solid var(--color-border);">
                <a href="{{ route('tours.index') }}" class="btn rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">← {{ __('ui.detail.back') }}</a>
                @auth
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('tours.edit', $tour) }}" class="btn btn-warning rounded-pill">✏️ {{ __('ui.detail.edit') }}</a>
                        <form action="{{ route('tours.destroy', $tour) }}" method="POST" data-confirm="{{ __('ui.detail.delete_confirm') }}" onsubmit="return confirm(this.dataset.confirm)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill">🗑️ {{ __('ui.detail.delete') }}</button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-top" style="top: calc(var(--navbar-height, 90px) + 16px);">
                <x-tarjeta-compartir :titulo="$tour->nombre" tipo="tour" />
            </div>
        </div>
    </div>
</div>
@endsection
