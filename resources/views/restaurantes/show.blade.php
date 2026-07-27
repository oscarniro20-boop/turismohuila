@extends('layouts.app')

@section('title', $restaurante->nombre . ' - Restaurantes')

@section('content')
<div class="card shadow-sm border-0">
    @if($restaurante->imagen)
        <img src="{{ asset($restaurante->imagen) }}" alt="{{ $restaurante->nombre }}" class="w-100" style="height: 320px; object-fit: cover;">
    @endif

    <div class="card-body">
        <h1 class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif;">{{ $restaurante->nombre }}</h1>
        <div class="d-flex flex-wrap gap-3 mb-3 text-muted">
            <span>📍 {{ $restaurante->municipio }}</span>
            @if($restaurante->telefono)<span>📞 {{ $restaurante->telefono }}</span>@endif
            <span>🍽️ {{ $restaurante->tipo_cocina }}</span>
            <span>⭐ {{ number_format($restaurante->calificacion_promedio, 1) }}</span>
        </div>
        <p class="fw-bold mb-3" style="color: var(--color-primary); font-size: 1.1rem;">
            Precio promedio: ${{ number_format($restaurante->precio_promedio, 0, ',', '.') }}
        </p>
        <hr>
        <p>{{ $restaurante->descripcion }}</p>

        <!-- Galería de Imágenes -->
        @if($restaurante->imagenes->count() > 0)
            <x-galeria-grid :imagenes="$restaurante->imagenes->sortBy('orden')" :alt="$restaurante->nombre" />
        @endif

        <!-- Sección de Reseñas -->
        <div class="mt-4">
            <h5 style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">⭐ Reseñas</h5>
            @if($restaurante->resenas->count() > 0)
                @foreach($restaurante->resenas as $resena)
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
                <a href="{{ route('resenas.create', ['tipo' => 'restaurante', 'referencia_id' => $restaurante->id]) }}" class="btn btn-sm btn-success mt-2">✍️ Escribir Reseña</a>
            @else
                <a href="{{ route('login') }}" class="text-decoration-none">Inicia sesión</a> <span class="text-muted">para dejar una reseña.</span>
            @endauth
        </div>
    </div>

    <div class="card-footer">
        <a href="{{ route('restaurantes.index') }}" class="btn btn-secondary">← Volver</a>
        @auth
            @if(auth()->user()->esAdmin())
                <a href="{{ route('restaurantes.edit', $restaurante) }}" class="btn btn-warning">✏️ Editar</a>
                <form action="{{ route('restaurantes.destroy', $restaurante) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
                </form>
            @endif
        @endauth
    </div>
</div>
@endsection
