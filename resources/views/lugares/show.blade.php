@extends('layouts.app')

@section('title', $lugar->nombre . ' - Lugares Turísticos')

@section('content')
<div class="card shadow-sm border-0">
    @if($lugar->imagen)
        <div class="position-relative">
            <img src="{{ asset($lugar->imagen) }}" alt="{{ $lugar->nombre }}" class="w-100" style="height: 380px; object-fit: cover;">
            <div class="position-absolute bottom-0 start-0 w-100" style="background: linear-gradient(to top, rgba(0,0,0,0.7), rgba(0,0,0,0));">
                <div class="container py-4">
                    <span class="badge bg-warning text-dark mb-2">HUILA, COLOMBIA</span>
                    <h1 class="text-white fw-bold mb-1" style="font-family: 'Fraunces', Georgia, serif;">{{ $lugar->nombre }}</h1>
                    <div class="d-flex align-items-center gap-3 text-white">
                        <span>📍 {{ $lugar->municipio }}</span>
                        <span>⭐ {{ number_format($lugar->calificacion_promedio, 1) }}</span>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="card-header bg-success text-white">
            <h4 class="mb-0">🏞️ {{ $lugar->nombre }}</h4>
        </div>
    @endif

    <div class="card-body">
        <p class="text-muted mb-1">Categoría: {{ $lugar->categoria }}</p>
        <hr>
        <p>{{ $lugar->descripcion }}</p>

        <!-- Galería de imágenes múltiples -->
        @if($lugar->imagenes->count() > 0)
            <x-galeria-grid :imagenes="$lugar->imagenes" :alt="$lugar->nombre" />
        @endif

        <!-- Sección de Reseñas -->
        <div class="mt-4">
            <h5 style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">⭐ Reseñas</h5>
            @if($lugar->resenas->count() > 0)
                @foreach($lugar->resenas as $resena)
                    <div class="card mb-2">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>{{ $resena->user->name }}</strong>
                                <span class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        {{ $i <= $resena->calificacion ? '★' : '☆' }}
                                    @endfor
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
                <a href="{{ route('resenas.create', ['tipo' => 'lugar', 'referencia_id' => $lugar->id]) }}" class="btn btn-sm btn-success mt-2">✍️ Escribir Reseña</a>
            @else
                <a href="{{ route('login') }}" class="text-decoration-none">Inicia sesión</a> <span class="text-muted">para dejar una reseña.</span>
            @endauth
        </div>
    </div>

    <div class="card-footer">
        <a href="{{ route('lugares.index') }}" class="btn btn-secondary">← Volver</a>
        @auth
            @if(auth()->user()->esAdmin())
                <a href="{{ route('lugares.edit', $lugar) }}" class="btn btn-warning">✏️ Editar</a>
                <form action="{{ route('lugares.destroy', $lugar) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
                </form>
            @endif
        @endauth
    </div>
</div>
@endsection
