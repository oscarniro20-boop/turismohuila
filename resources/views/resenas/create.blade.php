@extends('layouts.app')

@section('title', 'Nueva Reseña - TurismoHuila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">⭐ Nueva Reseña</h4>
                </div>
                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('resenas.store') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Tipo</label>
                            <select name="tipo" id="tipo-select" class="form-select">
                                <option value="">Seleccione...</option>
                                <option value="lugar" {{ old('tipo', $tipo) == 'lugar' ? 'selected' : '' }}>🏞️ Lugar</option>
                                <option value="hotel" {{ old('tipo', $tipo) == 'hotel' ? 'selected' : '' }}>🏨 Hotel</option>
                                <option value="restaurante" {{ old('tipo', $tipo) == 'restaurante' ? 'selected' : '' }}>🍽️ Restaurante</option>
                                <option value="tour" {{ old('tipo', $tipo) == 'tour' ? 'selected' : '' }}>🚌 Tour</option>
                                <option value="guia" {{ old('tipo', $tipo) == 'guia' ? 'selected' : '' }}>🗺️ Guía</option>
                                <option value="agencia" {{ old('tipo', $tipo) == 'agencia' ? 'selected' : '' }}>🧳 Agencia</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">ID del elemento</label>
                            <input type="number" name="referencia_id" id="referencia-id-input" class="form-control"
                                placeholder="ID del lugar, hotel, restaurante, tour o guía"
                                min="1" value="{{ old('referencia_id', $referenciaId) }}">
                            <div id="nombre-servicio-preview" class="form-text"></div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Calificación</label>
                            <select name="calificacion" class="form-select">
                                <option value="5">⭐⭐⭐⭐⭐ Excelente</option>
                                <option value="4">⭐⭐⭐⭐ Muy bueno</option>
                                <option value="3">⭐⭐⭐ Bueno</option>
                                <option value="2">⭐⭐ Regular</option>
                                <option value="1">⭐ Malo</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Comentario</label>
                            <textarea name="comentario" class="form-control" rows="4"
                                placeholder="Escribe tu experiencia...">{{ old('comentario') }}</textarea>
                        </div>
                        <div class="d-flex justify-content-between">
                        <a href="{{ route(auth()->user()->esAdmin() ? 'resenas.index' : 'resenas.mis') }}" class="btn btn-secondary">← Volver</a>
                            <button type="submit" class="btn btn-success">Guardar Reseña</button>
                        </div>
                    </form>
                </div>
            </div>
                </div>
    </div>

<script>
function buscarNombreServicio() {
    const tipo = document.getElementById('tipo-select').value;
    const id = document.getElementById('referencia-id-input').value;
    const preview = document.getElementById('nombre-servicio-preview');

    if (!tipo || !id) {
        preview.textContent = '';
        return;
    }

    fetch(`{{ route('resenas.nombre-servicio') }}?tipo=${tipo}&id=${id}`)
        .then(r => r.json())
        .then(data => {
            if (!data.nombre) {
                preview.innerHTML = '⚠️ No se encontró ningún servicio con ese ID.';
                return;
            }
            let html = `📌 Esta reseña va dirigida a: ${data.nombre}`;
            if (data.relacionados?.length) {
                html += `<br>🔗 Relacionado con: ${data.relacionados.join(', ')}`;
            }
            preview.innerHTML = html;
        });
}

document.getElementById('tipo-select').addEventListener('change', buscarNombreServicio);
document.getElementById('referencia-id-input').addEventListener('input', buscarNombreServicio);
</script>
@endsection
