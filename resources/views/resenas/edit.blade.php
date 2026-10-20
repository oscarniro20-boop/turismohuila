@extends('layouts.app')

@section('title', 'Editar Reseña - Turismo por Colombia')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h4 class="mb-0">✏️ Editar Reseña</h4>
            </div>
            <div class="card-body">

                <form action="{{ route('resenas.update', $resena) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Calificación</label>
                        <select name="calificacion" class="form-select">
                            <option value="5" {{ old('calificacion', $resena->calificacion) == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ Excelente</option>
                            <option value="4" {{ old('calificacion', $resena->calificacion) == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ Muy bueno</option>
                            <option value="3" {{ old('calificacion', $resena->calificacion) == 3 ? 'selected' : '' }}>⭐⭐⭐ Bueno</option>
                            <option value="2" {{ old('calificacion', $resena->calificacion) == 2 ? 'selected' : '' }}>⭐⭐ Regular</option>
                            <option value="1" {{ old('calificacion', $resena->calificacion) == 1 ? 'selected' : '' }}>⭐ Malo</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Comentario</label>
                        <textarea name="comentario" class="form-control" rows="4">{{ old('comentario', $resena->comentario) }}</textarea>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route(auth()->user()->esAdmin() ? 'resenas.index' : 'resenas.mis') }}" class="btn btn-secondary">← Volver</a>
                        <button type="submit" class="btn btn-warning">Actualizar Reseña</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection