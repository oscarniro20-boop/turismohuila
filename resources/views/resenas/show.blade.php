@extends('layouts.app')

@section('title', 'Reseña #' . $resena->id)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">⭐ Reseña #{{ $resena->id }}</h4>
            </div>
            <div class="card-body">
                <p><strong>Tipo:</strong> {{ ucfirst($resena->tipo) }}</p>
                <p><strong>ID de referencia:</strong> #{{ $resena->referencia_id }}</p>
                <p><strong>Usuario:</strong> {{ $resena->user->name ?? 'Usuario #'.$resena->user_id }}</p>
                <p><strong>Calificación:</strong> 
                    <span class="text-warning">
                        <x-estrellas :calificacion="$resena->calificacion" />
                    </span>
                </p>
                <hr>
                <p>{{ $resena->comentario }}</p>
                <small class="text-muted">Creado: {{ $resena->created_at->format('d/m/Y H:i') }}</small>
            </div>
            <div class="card-footer">
                <a href="{{ route('resenas.index') }}" class="btn btn-secondary">← Volver</a>
                @if($resena->user_id === auth()->id())
                    <a href="{{ route('resenas.edit', $resena) }}" class="btn btn-warning">✏️ Editar</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection