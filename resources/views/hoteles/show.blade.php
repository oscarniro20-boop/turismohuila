@extends('layouts.app')

@section('title', $hotel->nombre . ' - Hoteles')

@section('content')
<x-migas-pan :items="[
    ['label' => 'Hoteles', 'url' => route('hoteles.index')],
    ['label' => $hotel->nombre],
]" />
<div class="card shadow-sm border-0">
    @if($hotel->imagen)
        <img src="{{ asset($hotel->imagen) }}" alt="{{ $hotel->nombre }}" class="w-100" style="height: 320px; object-fit: cover;">
    @endif

    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <h1 class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif;">{{ $hotel->nombre }}</h1>
            @auth
                @if(!auth()->user()->esAdmin())
                    @php $esFav = \App\Models\Favorito::where('user_id', auth()->id())->where('tipo', 'hotel')->where('referencia_id', $hotel->id)->exists(); @endphp
                    <x-favorito-boton tipo="hotel" :id="$hotel->id" :activo="$esFav" />
                @endif
            @endauth
            <x-boton-compartir titulo="{{ $hotel->nombre }}" />
        </div>
        <div class="d-flex flex-wrap gap-3 mb-3 text-muted">
            <span>📍 {{ $hotel->municipio }}</span>
            @if($hotel->telefono)<span>📞 {{ $hotel->telefono }}</span>@endif
            <span>⭐ {{ number_format($hotel->calificacion_promedio, 1) }}</span>
        </div>
        <p class="fw-bold mb-3">
            <span class="badge rounded-pill px-3 py-2" style="background-color: var(--color-secondary); color:#fff;">
                ${{ number_format($hotel->precio_noche, 0, ',', '.') }} <span class="fw-normal" style="font-size: 0.85em;">/ noche</span>
            </span>
        </p>
        <hr>
        <p>{{ $hotel->descripcion }}</p>

        <!-- Galería de Imágenes -->
        @if($hotel->imagenes->count() > 0)
            <x-galeria-grid :imagenes="$hotel->imagenes->sortBy('orden')" :alt="$hotel->nombre" />
        @endif

        <!-- Sección de Reseñas -->
        <div class="mt-4">
            <h5 style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">⭐ Reseñas</h5>
            @if($hotel->resenas->count() > 0)
                @foreach($hotel->resenas as $resena)
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
                <a href="{{ route('resenas.create', ['tipo' => 'hotel', 'referencia_id' => $hotel->id]) }}" class="btn btn-sm btn-success mt-2">✍️ Escribir Reseña</a>
            @else
                <a href="{{ route('login') }}" class="text-decoration-none">Inicia sesión</a> <span class="text-muted">para dejar una reseña.</span>
            @endauth
        </div>
    </div>

    <div class="card-footer">
        <a href="{{ route('hoteles.index') }}" class="btn btn-secondary">← Volver</a>
        @auth
            @if(auth()->user()->esAdmin())
                <a href="{{ route('hoteles.edit', $hotel) }}" class="btn btn-warning">✏️ Editar</a>
                <form action="{{ route('hoteles.destroy', $hotel) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
                </form>
            @endif
        @endauth
    </div>
</div>
@endsection
