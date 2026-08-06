@extends('layouts.app')

@section('title', $agencia->nombre . ' - TurismoHuila')

@section('content')
<x-migas-pan :items="[
    ['label' => 'Agencias', 'url' => route('agencias.index')],
    ['label' => $agencia->nombre],
]" />
<div class="card shadow-sm border-0">
    @if($agencia->imagen)
        <img src="{{ asset($agencia->imagen) }}" alt="{{ $agencia->nombre }}" class="w-100" style="height: 320px; object-fit: cover;">
    @endif

    <div class="card-body">
        <div class="d-flex align-items-center gap-3">
            <h1 class="fw-bold mb-2" style="font-family: 'Fraunces', Georgia, serif;">{{ $agencia->nombre }}</h1>
            @auth
                @if(!auth()->user()->esAdmin())
                    @php $esFav = \App\Models\Favorito::where('user_id', auth()->id())->where('tipo', 'agencia')->where('referencia_id', $agencia->id)->exists(); @endphp
                    <x-favorito-boton tipo="agencia" :id="$agencia->id" :activo="$esFav" />
                @endif
            @endauth
            <x-boton-compartir titulo="{{ $agencia->nombre }}" />
        </div>
        <div class="d-flex flex-wrap gap-3 mb-3 text-muted">
            @if($agencia->municipio)<span>📍 {{ $agencia->municipio }}</span>@endif
            @if($agencia->telefono_contacto)<span>📞 {{ $agencia->telefono_contacto }}</span>@endif
            @if($agencia->email)<span>📧 {{ $agencia->email }}</span>@endif
            <span>⭐ {{ number_format($agencia->calificacion_promedio, 1) }}</span>
        </div>

        <hr class="my-4">

        <h5 style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">🧳 Tours ofrecidos</h5>
        @if($agencia->paquetes->count() > 0)
            <div class="row g-3">
                @foreach($agencia->paquetes as $paquete)
                    <div class="col-md-4">
                        <div class="card h-100 border-0 shadow-sm">
                            @if(optional($paquete->tour)->imagen)
                                <img src="{{ asset($paquete->tour->imagen) }}" alt="{{ $paquete->tour->nombre }}"
                                     class="card-img-top" style="height: 160px; object-fit: cover;">
                            @endif
                            <div class="card-body">
                                <h6 class="fw-bold mb-1">{{ optional($paquete->tour)->nombre ?? 'Tour eliminado' }}</h6>
                                <p class="text-muted small mb-1">
                                    @if($paquete->precio) ${{ number_format($paquete->precio, 0, ',', '.') }} @endif
                                    @if($paquete->duracion_dias) · {{ $paquete->duracion_dias }} día(s) @endif
                                    @if($paquete->cupo_maximo) · cupo {{ $paquete->cupo_maximo }} @endif
                                </p>
                                @if($paquete->descripcion)
                                    <p class="text-muted small mb-0">{{ $paquete->descripcion }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-muted">Aún no ofrece tours en la plataforma.</p>
        @endif

        <!-- Galería de Imágenes -->
        @if($agencia->imagenes->count() > 0)
            <x-galeria-grid :imagenes="$agencia->imagenes->sortBy('orden')" :alt="$agencia->nombre" />
        @endif

        <!-- Sección de Reseñas -->
        <div class="mt-4">
            <h5 style="font-family: 'Fraunces', Georgia, serif; font-weight: 600;">⭐ Reseñas</h5>
            @if($agencia->resenas->count() > 0)
                @foreach($agencia->resenas as $resena)
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
                <a href="{{ route('resenas.create', ['tipo' => 'agencia', 'referencia_id' => $agencia->id]) }}" class="btn btn-sm btn-success mt-2">✍️ Escribir Reseña</a>
            @else
                <a href="{{ route('login') }}" class="text-decoration-none">Inicia sesión</a> <span class="text-muted">para dejar una reseña.</span>
            @endauth
        </div>
    </div>

    <div class="card-footer d-flex gap-2">
        <a href="{{ route('agencias.index') }}" class="btn btn-secondary">← Volver</a>
        @auth
            @if(auth()->user()->esAdmin())
                <a href="{{ route('agencias.edit', $agencia) }}" class="btn btn-warning">✏️ Editar</a>
                <form action="{{ route('agencias.destroy', $agencia) }}" method="POST" onsubmit="return confirm('¿Eliminar esta agencia?')" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">🗑️ Eliminar</button>
                </form>
            @endif
        @endauth
    </div>
</div>
@endsection