@extends('layouts.app')

@section('title', $agencia->nombre . ' - Agencias')

@section('content')
<div class="container py-4">
    <x-cabecera-detalle
        :imagen="$agencia->url_imagen"
        :titulo="$agencia->nombre"
        :favoritoTipo="'agencia'"
        :favoritoId="$agencia->id"
        :esFavorito="$esFav">

        <x-slot:meta>
            @if($agencia->municipio)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📍 {{ $agencia->municipio }}</span>@endif
            @if($agencia->telefono_contacto)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📞 {{ $agencia->telefono_contacto }}</span>@endif
            @if($agencia->email)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📧 {{ $agencia->email }}</span>@endif
            <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">⭐ {{ number_format($agencia->resenas->avg('calificacion') ?? 0, 1) }}</span>
        </x-slot:meta>
    </x-cabecera-detalle>

    <div class="row g-4" style="min-height: calc(100vh - var(--navbar-height, 90px));">
        <div class="col-lg-8">
            <p class="mb-3">@if($agencia->descripcion)<span style="float: left; font-family: 'Fraunces', Georgia, serif; font-size: 3.5rem; line-height: 0.8; padding-right: 0.5rem; color: var(--color-primary); font-weight: 600;">{{ mb_substr($agencia->descripcion, 0, 1) }}</span>{{ mb_substr($agencia->descripcion, 1) }}@else Sin descripción. @endif</p>

            {{-- Tours ofrecidos --}}
            <h3 class="mt-4 mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">🧳 Tours ofrecidos</h3>
            @if($agencia->paquetes->count() > 0)
                <div class="row g-3">
                    @foreach($agencia->paquetes as $paquete)
                        <div class="col-sm-6">
                            <div class="h-100 rounded-4 border bg-white p-3 hover-lift" style="border-color: var(--color-border); box-shadow: var(--shadow-ring);">
                                @if(optional($paquete->tour)->imagen)
                                    <img src="{{ $paquete->tour->url_imagen }}" alt="{{ $paquete->tour->nombre ?? 'Tour' }}" class="w-100 rounded-3 mb-2" style="height: 140px; object-fit: cover;">
                                @endif
                                <h6 class="fw-bold mb-1">{{ optional($paquete->tour)->nombre ?? 'Tour eliminado' }}</h6>
                                <p class="small text-muted mb-1">
                                    @if($paquete->precio) <span class="text-success fw-bold">${{ number_format($paquete->precio, 0, ',', '.') }}</span> @endif
                                    @if($paquete->duracion_dias) · {{ $paquete->duracion_dias }} día(s) @endif
                                    @if($paquete->cupo_maximo) · cupo {{ $paquete->cupo_maximo }} @endif
                                </p>
                                @if($paquete->descripcion)
                                    <p class="small text-muted mb-0">{{ Str::limit($paquete->descripcion, 90) }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted small">Aún no ofrece tours en la plataforma.</p>
            @endif

            {{-- Galería --}}
            @if($agencia->imagenes->count() > 0)
                <x-galeria-grid :imagenes="$agencia->imagenes->sortBy('orden')" :alt="$agencia->nombre" />
            @endif

            <x-contacto-servicio :telefono="$agencia->telefono_contacto" :email="$agencia->email" texto="Contacta directamente con {{ $agencia->nombre }} para reservar un tour o preguntar disponibilidad." />

            <h3 class="mt-4 mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">⭐ Reseñas</h3>
            @if($agencia->resenas->count() > 0)
                @foreach($agencia->resenas as $resena)
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
                <x-resena-formulario tipo="agencia" :referenciaId="$agencia->id" :nombre="$agencia->nombre" />
            @else
                <p class="small mt-3">🔒 <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Inicia sesión</a> <span style="color: var(--color-muted-fg);">para dejar una reseña.</span></p>
            @endauth

            {{-- Acciones --}}
            <div class="d-flex flex-wrap gap-2 mt-4 pt-2">
                <a href="{{ route('agencias.index') }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">← Volver</a>
                @auth
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('agencias.edit', $agencia) }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-secondary); color: var(--color-secondary);">✏️ Editar</a>
                        <form action="{{ route('agencias.destroy', $agencia) }}" method="POST" onsubmit="return confirm('¿Eliminar esta agencia?')" class="d-inline">
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
                <x-tarjeta-compartir :titulo="$agencia->nombre" tipo="agencia" />
            </div>
        </div>
    </div>
</div>
@endsection
