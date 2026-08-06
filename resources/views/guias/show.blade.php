@extends('layouts.app')

@section('title', $guia->nombre . ' - Guía Turístico')

@section('content')
<div class="card shadow-sm border-0">
    @if($guia->foto)
        <img src="{{ asset($guia->foto) }}" alt="{{ $guia->nombre }}" class="w-100" style="height: 320px; object-fit: cover;">
    @endif

    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <h1 class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif;">{{ $guia->nombre }}</h1>
            @auth
                @if(!auth()->user()->esAdmin())
                    @php $esFav = \App\Models\Favorito::where('user_id', auth()->id())->where('tipo', 'guia')->where('referencia_id', $guia->id)->exists(); @endphp
                    <x-favorito-boton tipo="guia" :id="$guia->id" :activo="$esFav" />
                @endif
            @endauth
        </div>
        <div class="d-flex flex-wrap gap-3 mb-3 text-muted">
            @if($guia->experiencia_anios)<span>⭐ {{ $guia->experiencia_anios }} años de experiencia</span>@endif
            @if($guia->telefono)<span>📞 {{ $guia->telefono }}</span>@endif
            @if($guia->idiomas)<span>🗣️ {{ $guia->idiomas }}</span>@endif
            <span>⭐ {{ number_format($guia->calificacion_promedio, 1) }}</span>
        </div>

        @if($guia->bio)
            <p>{{ $guia->bio }}</p>
        @endif
        @if($guia->zonas)
            <p class="text-muted mb-0">📍 {{ $guia->zonas }}</p>
        @endif

        <hr class="my-4">

        @if($guia->lugares->count() > 0)
            <div class="mb-4">
                <h5 style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">🏞️ Lugares que acompaña</h5>
                <div class="d-flex flex-wrap gap-2">
                    @foreach($guia->lugares as $lugar)
                        <span class="badge rounded-pill px-3 py-2" style="background-color: var(--color-muted); color: var(--color-fg); border: 1px solid var(--color-border);">
                            {{ $lugar->nombre }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Galería de Imágenes -->
        @if($guia->imagenes->count() > 0)
            <x-galeria-grid :imagenes="$guia->imagenes->sortBy('orden')" :alt="$guia->nombre" />
        @endif

        <!-- Sección de Reseñas -->
        <div class="mt-4">
            <h5 style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">⭐ Reseñas</h5>
            @if($guia->resenas->count() > 0)
                @foreach($guia->resenas as $resena)
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
                <a href="{{ route('resenas.create', ['tipo' => 'guia', 'referencia_id' => $guia->id]) }}" class="btn btn-sm btn-success mt-2">✍️ Escribir Reseña</a>
            @else
                <a href="{{ route('login') }}" class="text-decoration-none">Inicia sesión</a> <span class="text-muted">para dejar una reseña.</span>
            @endauth
        </div>
    </div>

    <div class="card-footer">
        <a href="{{ route('guias.index') }}" class="btn btn-secondary">← Volver</a>
        @auth
            @if(auth()->user()->esAdmin())
                <a href="{{ route('guias.edit', $guia) }}" class="btn btn-warning">✏️ Editar</a>
                <form action="{{ route('guias.destroy', $guia) }}" method="POST" onsubmit="return confirm('¿Eliminar este registro?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
                </form>
            @endif
        @endauth
    </div>
</div>
@endsection