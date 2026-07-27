@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Mis Reseñas</h1>
        <a href="{{ route('resenas.create') }}" class="btn btn-success">+ Nueva Reseña</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($resenas->isEmpty())
        <div class="alert alert-info">Aún no has escrito ninguna reseña.</div>
    @else
        <div class="row">
            @foreach($resenas as $resena)
            <div class="col-md-6 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span class="badge bg-success text-capitalize">{{ ucfirst($resena->tipo) }}</span>
                        <span class="text-warning fw-bold">
                            <x-estrellas :calificacion="$resena->calificacion" />
                        </span>
                    </div>
                    <div class="card-body">
                        <p class="card-text">{{ $resena->comentario }}</p>
<small class="text-muted">
                            {{ ucfirst($resena->tipo) }}: {{ $resena->nombre_servicio }} —
                            {{ $resena->created_at->diffForHumans() }}
                        </small>
                    </div>
                    <div class="card-footer d-flex gap-2">
                        <a href="{{ route('resenas.edit', $resena) }}" class="btn btn-sm btn-warning">Editar</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
</div>
@endsection