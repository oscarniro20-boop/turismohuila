@extends('layouts.app')

@section('title', $tour->nombre . ' - Tours')

@section('content')
<x-migas-pan :items="[
    ['label' => 'Tours', 'url' => route('tours.index')],
    ['label' => $tour->nombre],
]" />
<div class="card shadow-sm border-0">
    <div class="card-body">

        @if($tour->imagen)
            <img src="{{ asset($tour->imagen) }}" alt="{{ $tour->nombre }}"
                 class="rounded mb-3"
                 style="width: 100%; height: 420px; object-fit: cover;">
        @endif

        <div class="d-flex align-items-center gap-3">
            <h1 class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif;">{{ $tour->nombre }}</h1>
            @auth
                @if(!auth()->user()->esAdmin())
                    @php $esFav = \App\Models\Favorito::where('user_id', auth()->id())->where('tipo', 'tour')->where('referencia_id', $tour->id)->exists(); @endphp
                    <x-favorito-boton tipo="tour" :id="$tour->id" :activo="$esFav" />
                @endif
            @endauth
            <x-boton-compartir titulo="{{ $tour->nombre }}" />
        </div>
        <div class="d-flex flex-wrap gap-3 mb-3 text-muted">
            <span>📍 {{ $tour->municipio }}</span>
            <span>⏱️ {{ $tour->duracion_dias }} día(s)</span>
            <span>👥 Cupo máximo: {{ $tour->cupo_maximo }} personas</span>
            <span>⭐ {{ number_format($tour->calificacion_promedio, 1) }}</span>
        </div>
        <p class="fw-bold mb-1">Incluye:</p>
        <p>{{ $tour->incluye }}</p>
        <hr>
        <p>{{ $tour->descripcion }}</p>

        <div class="mt-4">
            <h5 style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">Reserva con nuestras agencias</h5>
            <p class="text-muted small mb-3">Compara precios y condiciones. Reserva directamente con la agencia.</p>
            @forelse($tour->paquetes as $paquete)
                <div class="card mb-2">
                    <div class="card-body py-2">
                        <strong>{{ optional($paquete->agencia)->nombre ?? 'Agencia eliminada' }}</strong>
                        <div class="text-muted small">{{ optional($paquete->agencia)->municipio }}</div>
                        <p class="text-muted small mb-0 mt-1">
                            @if($paquete->precio) <span class="text-success fw-bold">${{ number_format($paquete->precio, 0, ',', '.') }}</span> @endif
                            @if($paquete->duracion_dias) · {{ $paquete->duracion_dias }} día(s) @endif
                            @if($paquete->cupo_maximo) · cupo {{ $paquete->cupo_maximo }} @endif
                        </p>
                        @if($paquete->descripcion)
                            <p class="mb-0 mt-1">{{ $paquete->descripcion }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-muted">Ninguna agencia registrada todavía.</p>
            @endforelse
        </div>

        <!-- Galería de Imágenes -->
        @if($tour->imagenes->count() > 0)
            <x-galeria-grid :imagenes="$tour->imagenes->sortBy('orden')" :alt="$tour->nombre" />
        @endif

        <!-- Sección de Reseñas -->
        <div class="mt-4">
            <h5 style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">⭐ Reseñas</h5>
            @if($tour->resenas->count() > 0)
                @foreach($tour->resenas as $resena)
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>{{ $resena->user->name }}</strong>
                                <span class="text-warning">
                                    <x-estrellas :calificacion="$resena->calificacion" />
                                </span>
                            </div>
                            <p class="mb-0 mt-1">{{ $resena->comentario }}</p>
                        </div>
                    </div>
                @endforeach
            @else
                <p class="text-muted">Aún no hay reseñas. ¡Sé el primero!</p>
            @endif
            @auth
                <a href="{{ route('resenas.create', ['tipo' => 'tour', 'referencia_id' => $tour->id]) }}" class="btn btn-sm btn-success mt-2">✍️ Escribir Reseña</a>
            @else
                <a href="{{ route('login') }}" class="text-decoration-none">Inicia sesión</a> <span class="text-muted">para dejar una reseña.</span>
            @endauth
        </div>
    </div>

    <div class="card-footer">
        <a href="{{ route('tours.index') }}" class="btn btn-secondary">← Volver</a>
        @auth
            @if(auth()->user()->esAdmin())
                <a href="{{ route('tours.edit', $tour) }}" class="btn btn-warning">✏️ Editar</a>
                <form action="{{ route('tours.destroy', $tour) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
                </form>
            @endif
        @endauth
    </div>
</div>
@endsection
