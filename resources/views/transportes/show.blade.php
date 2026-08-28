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
    >
        <x-slot:meta>
            @if($transporte->capacidad)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">👥 Capacidad {{ $transporte->capacidad }}</span>@endif
            @if($transporte->telefono)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📞 {{ $transporte->telefono }}</span>@endif
        </x-slot:meta>
    </x-cabecera-detalle>

    <div class="row g-4" style="min-height: calc(100vh - var(--navbar-height, 90px));">
        <div class="col-lg-8">
            @if($transporte->precio)
                <div class="rounded-4 p-3 mb-4" style="background: var(--color-muted);">
                    <span class="small" style="color: var(--color-muted-fg);">Precio</span>
                    <div class="fw-bold" style="font-family: 'Fraunces', Georgia, serif; font-size: 1.6rem; color: var(--color-primary);">${{ number_format($transporte->precio, 0, ',', '.') }}</div>
                </div>
            @endif

            @if($transporte->descripcion)
                <p class="mb-4" style="font-family: 'Fraunces', Georgia, serif; font-weight: 500; font-size: 1.3rem; line-height: 1.4;">
                    <span style="float: left; font-family: 'Fraunces', Georgia, serif; font-size: 3.3rem; line-height: 0.8; padding-right: 0.5rem; color: var(--color-primary); font-weight: 600;">{{ mb_substr($transporte->descripcion, 0, 1) }}</span>{{ mb_substr($transporte->descripcion, 1) }}
                </p>
            @endif

            @if($transporte->imagenes->count() > 0)
                <x-galeria-grid :imagenes="$transporte->imagenes" :alt="$transporte->nombre" />
            @endif

            @if($transporte->resenas->count() > 0)
                <div class="mt-4" style="clear: both;">
                    <h3 class="mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.1rem;">⭐ Lo que dicen</h3>
                    <div class="d-flex flex-column gap-2">
                        @foreach($transporte->resenas as $resena)
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
                <x-resena-formulario tipo="transporte" :referenciaId="$transporte->id" :nombre="$transporte->nombre" />
            @else
                <p class="small mt-3">🔒 <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Inicia sesión</a> <span style="color: var(--color-muted-fg);">para dejar una reseña.</span></p>
            @endauth

            <div class="d-flex gap-2 mt-4 pt-3 flex-wrap" style="border-top: 1px solid var(--color-border);">
                <a href="{{ route('transportes.index') }}" class="btn rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">← Volver</a>
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
                <x-contacto-servicio
                    :telefono="$transporte->telefono"
                    texto="Contacta directamente con {{ $transporte->nombre }} para reservar o preguntar disponibilidad."
                />
                <x-tarjeta-compartir :titulo="$transporte->nombre" tipo="transporte" />
            </div>
        </div>
    </div>
</div>
@endsection
