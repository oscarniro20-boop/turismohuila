@extends('layouts.app')

@section('title', $agencia->nombre . ' - ' . __('ui.catalog.agencies_title'))

@section('content')
<div class="container py-4">
    <x-cabecera-detalle
        :imagen="$agencia->imagen ? $agencia->url_imagen : null"
        :titulo="$agencia->nombre"
        :categoria="mb_strtoupper(__('ui.catalog.agencies_title')) . ($agencia->municipio ? ' · ' . mb_strtoupper($agencia->municipio) : '')"
        favoritoTipo="agencia"
        :favoritoId="$agencia->id"
        :esFavorito="$esFav"
        icono="🏢"
    >
        <x-slot:meta>
            @if($agencia->resenas->count() > 0)
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">⭐ {{ number_format($agencia->resenas->avg('calificacion'), 1) }} ({{ $agencia->resenas->count() }} {{ $agencia->resenas->count() === 1 ? __('ui.detail.review') : __('ui.detail.reviews') }})</span>
            @else
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📝 {{ __('ui.detail.no_reviews') }}</span>
            @endif
            @if($agencia->telefono_contacto)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📞 {{ $agencia->telefono_contacto }}</span>@endif
        </x-slot:meta>
    </x-cabecera-detalle>

    <div class="row g-4" style="min-height: calc(100vh - var(--navbar-height, 90px));">
        <div class="col-lg-8">
            @if($agencia->descripcion)
                <p class="mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 500; font-size: 1.3rem; line-height: 1.4;">
                    <span style="float: left; font-family: 'Fraunces', Georgia, serif; font-size: 3.3rem; line-height: 0.8; padding-right: 0.5rem; color: var(--color-primary); font-weight: 600;">{{ mb_substr($agencia->descripcion, 0, 1) }}</span>{{ mb_substr($agencia->descripcion, 1) }}
                </p>
            @endif

            <h2 class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.3rem; clear: both;">🧳 {{ __('ui.detail.book_agencies') }}</h2>

            @if($agencia->paquetes->count() > 0)
                <div class="row g-3 mb-4">
                    @foreach($agencia->paquetes as $paquete)
                        <div class="col-sm-6">
                            <div class="h-100 rounded-4 overflow-hidden hover-lift" style="box-shadow: var(--shadow-ring); background: #fff;">
                                @if(optional($paquete->tour)->imagen)
                                    <img src="{{ $paquete->tour->url_imagen }}" alt="{{ $paquete->tour->nombre }}" class="w-100" style="height: 150px; object-fit: cover;">
                                @else
                                    <div class="w-100 d-flex align-items-center justify-content-center" style="height: 150px; background: var(--color-muted); font-size: 1.75rem;">🧳</div>
                                @endif
                                <div class="p-3">
                                    <h3 class="mb-1" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.05rem;">
                                        {{ optional($paquete->tour)->nombre ?? 'Tour eliminado' }}
                                    </h3>
                                    <p class="small mb-1" style="color: var(--color-muted-fg);">
                                        @if($paquete->duracion_dias) {{ $paquete->duracion_dias }} {{ __('ui.detail.duration') }} @endif
                                        @if($paquete->cupo_maximo) · {{ __('ui.detail.quota') }} {{ $paquete->cupo_maximo }} @endif
                                    </p>
                                    @if($paquete->precio)
                                        <span class="fw-semibold" style="color: var(--color-primary);">$ {{ number_format($paquete->precio, 0, ',', '.') }}</span>
                                    @endif
                                    @if($paquete->tour)
                                        <div class="mt-2">
                                            <a href="{{ route('tours.show', $paquete->tour) }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">{{ __('ui.detail.view_tour') }}</a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="rounded-4 p-4 mb-4 text-center" style="background: var(--color-muted); color: var(--color-muted-fg);">
                    {{ __('ui.detail.no_tours') }}
                </div>
            @endif

            @if($agencia->imagenes->count() > 0)
                <x-galeria-grid :imagenes="$agencia->imagenes->sortBy('orden')" :alt="$agencia->nombre" />
            @endif

            <x-contacto-servicio
                :telefono="$agencia->telefono_contacto"
                :email="$agencia->email"
                :texto="__('ui.contact.direct', ['name' => $agencia->nombre])"
            />

            @if($agencia->resenas->count() > 0)
                <div class="mt-4">
                    <h3 class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.1rem;">⭐ {{ __('ui.detail.what_they_say') }}</h3>
                    <div class="d-flex flex-column gap-2">
                        @foreach($agencia->resenas as $resena)
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
                <x-resena-formulario tipo="agencia" :referenciaId="$agencia->id" :nombre="$agencia->nombre" />
            @else
                <p class="small mt-3">🔒 <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">{{ __('ui.detail.login') }}</a> <span style="color: var(--color-muted-fg);">{{ __('ui.detail.login_review') }}</span></p>
            @endauth

            <div class="d-flex gap-2 mt-4 pt-3 flex-wrap" style="border-top: 1px solid var(--color-border);">
                <a href="{{ route('agencias.index') }}" class="btn rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">← {{ __('ui.detail.back') }}</a>
                @auth
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('agencias.edit', $agencia) }}" class="btn btn-warning rounded-pill">✏️ {{ __('ui.detail.edit') }}</a>
                        <form action="{{ route('agencias.destroy', $agencia) }}" method="POST" data-confirm="{{ __('ui.detail.delete_confirm') }}" onsubmit="return confirm(this.dataset.confirm)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill">🗑️ {{ __('ui.detail.delete') }}</button>
                        </form>
                    @endif
                @endauth
            </div>
        </div>

        <div class="col-lg-4">
            <div class="sticky-top d-flex flex-column gap-3" style="top: calc(var(--navbar-height, 90px) + 16px);">
                <x-tarjeta-compartir :titulo="$agencia->nombre" tipo="agencia" />
            </div>
        </div>
    </div>
</div>
@endsection
