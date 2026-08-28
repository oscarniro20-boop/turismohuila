@extends('layouts.app')

@section('title', $tour->nombre . ' - Tours')

@section('content')
<div class="container py-4">
    <x-cabecera-detalle
        :imagen="$tour->url_imagen"
        :titulo="$tour->nombre"
        :favoritoTipo="'tour'"
        :favoritoId="$tour->id"
        :esFavorito="$esFav">

        <x-slot:meta>
            @if($tour->municipio)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📍 {{ $tour->municipio }}</span>@endif
            @if($tour->duracion_dias)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">⏱️ {{ $tour->duracion_dias }} día(s)</span>@endif
            @if($tour->cupo_maximo)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">👥 Cupo {{ $tour->cupo_maximo }}</span>@endif
            @if($tour->precio)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">💲 ${{ number_format($tour->precio, 0, ',', '.') }}</span>@endif
            @if($tour->telefono_contacto)<span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">📞 {{ $tour->telefono_contacto }}</span>@endif
            <span class="badge rounded-pill px-3 py-2" style="background: rgba(255,255,255,0.92); color: var(--color-fg);">⭐ {{ number_format($tour->resenas->avg('calificacion') ?? 0, 1) }}</span>
        </x-slot:meta>
    </x-cabecera-detalle>

    <div class="row g-4" style="min-height: calc(100vh - var(--navbar-height, 90px));">
        <div class="col-lg-8">
            @if($tour->incluye)
                <p class="fw-bold mb-1" style="color: var(--color-primary);">Incluye:</p>
                <p class="mb-3">{{ $tour->incluye }}</p>
            @endif
            <p class="mb-3">@if($tour->descripcion)<span style="float: left; font-family: 'Fraunces', Georgia, serif; font-size: 3.5rem; line-height: 0.8; padding-right: 0.5rem; color: var(--color-primary); font-weight: 600;">{{ mb_substr($tour->descripcion, 0, 1) }}</span>{{ mb_substr($tour->descripcion, 1) }}@else Sin descripción. @endif</p>

            {{-- Agencias que ofrecen este tour --}}
            <h3 class="mt-4 mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">💬 Reserva con nuestras agencias</h3>
            @forelse($tour->paquetes as $paquete)
                <div class="rounded-4 p-3 border mb-2" style="border-color: var(--color-border); box-shadow: var(--shadow-ring);">
                    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
                        <div class="flex-grow-1">
                            <strong>{{ optional($paquete->agencia)->nombre ?? 'Agencia eliminada' }}</strong>
                            <div class="text-muted small">{{ optional($paquete->agencia)->municipio }}</div>
                            <div class="text-muted small mt-1">
                                @if($paquete->precio) <span class="text-success fw-bold">${{ number_format($paquete->precio, 0, ',', '.') }}</span> @endif
                                @if($paquete->duracion_dias) · {{ $paquete->duracion_dias }} día(s) @endif
                                @if($paquete->cupo_maximo) · cupo {{ $paquete->cupo_maximo }} @endif
                            </div>
                            @if($paquete->descripcion)
                                <p class="mb-0 mt-1">{{ Str::limit($paquete->descripcion, 100) }}</p>
                            @endif
                        </div>
                        @if(optional($paquete->agencia)->telefono_contacto)
                            @php $numAg = preg_replace('/\D/', '', $paquete->agencia->telefono_contacto); @endphp
                            <a href="https://wa.me/57{{ $numAg }}" target="_blank" rel="noopener"
                               class="btn btn-sm rounded-pill"
                               style="background: var(--color-primary); color: #fff;">💬 WhatsApp</a>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted small">Ninguna agencia registrada todavía.</p>
            @endforelse

            {{-- Galería --}}
            @if($tour->imagenes->count() > 0)
                <x-galeria-grid :imagenes="$tour->imagenes->sortBy('orden')" :alt="$tour->nombre" />
            @endif

            <h3 class="mt-4 mb-3" style="font-family: 'Fraunces', Georgia, serif; font-weight: 600; font-size: 1.2rem;">⭐ Reseñas</h3>
            @if($tour->resenas->count() > 0)
                @foreach($tour->resenas as $resena)
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
                <x-resena-formulario tipo="tour" :referenciaId="$tour->id" :nombre="$tour->nombre" />
            @else
                <p class="small mt-3">🔒 <a href="{{ route('login') }}" class="text-decoration-none fw-semibold">Inicia sesión</a> <span style="color: var(--color-muted-fg);">para dejar una reseña.</span></p>
            @endauth

            {{-- Acciones --}}
            <div class="d-flex flex-wrap gap-2 mt-4 pt-2">
                <a href="{{ route('tours.index') }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-border); color: var(--color-fg);">← Volver</a>
                @auth
                    @if(auth()->user()->esAdmin())
                        <a href="{{ route('tours.edit', $tour) }}" class="btn btn-sm rounded-pill" style="border: 1px solid var(--color-secondary); color: var(--color-secondary);">✏️ Editar</a>
                        <form action="{{ route('tours.destroy', $tour) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')" class="d-inline">
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
                <x-contacto-servicio :telefono="$tour->telefono_contacto" />
                <x-tarjeta-compartir :titulo="$tour->nombre" tipo="tour" />
            </div>
        </div>
    </div>
</div>
@endsection
