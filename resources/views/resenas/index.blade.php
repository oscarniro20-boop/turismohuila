@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-4">
        <h1 class="h3">Reseñas</h1>
        <a href="{{ route('resenas.create') }}" class="btn btn-success">+ Nueva Reseña</a>
    </div>
    @if($resenas->count() > 0)
    <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 row-cols-xl-4 g-4">
        @foreach($resenas as $resena)
        <div class="col tarjeta-item">
            <div class="card h-100">
                <div class="card-body">
<h5 class="card-title">{{ ucfirst($resena->tipo) }}: {{ $resena->nombre_servicio }}</h5>
                    <p class="card-text">{{ $resena->comentario }}</p>
                    <p class="card-text"><strong>Calificación:</strong> {{ $resena->calificacion }} ⭐</p>
                </div>
                <div class="card-footer d-flex gap-2">
                    <a href="{{ route('resenas.show', $resena) }}" class="btn btn-sm btn-info">Ver</a>
                    @if($resena->user_id === auth()->id())
                        <a href="{{ route('resenas.edit', $resena) }}" class="btn btn-sm btn-warning">Editar</a>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="alert alert-info text-center py-4">
        <div class="display-4 mb-2">📝</div>
        <h5>Aún no hay reseñas para este lugar.</h5>
        <p class="text-muted mb-0">Sé el primero en compartir tu experiencia.</p>
    </div>
    @endif
</div>
@endsection