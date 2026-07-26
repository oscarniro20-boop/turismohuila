@extends('layouts.app')

@section('title', 'Editar Agencia - TurismoHuila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h4 class="mb-0">✏️ Editar Agencia de Viaje</h4>
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger mb-3">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if($agencia->imagen)
                    <div class="mb-2">
                        <img src="{{ asset($agencia->imagen) }}" style="max-height:120px;" class="rounded d-block mb-1">
                        <span class="form-text">Imagen actual (sube una nueva para reemplazarla)</span>
                    </div>
                @endif

                <form action="{{ route('agencias.update', $agencia) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre de la Agencia</label>
                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $agencia->nombre) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Municipio</label>
                        <input type="text" name="municipio" class="form-control" value="{{ old('municipio', $agencia->municipio) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Teléfono de Contacto</label>
                        <input type="text" name="telefono_contacto" class="form-control" value="{{ old('telefono_contacto', $agencia->telefono_contacto) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $agencia->email) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">WhatsApp</label>
                        <input type="text" name="whatsapp" class="form-control" placeholder="Ej: 3001234567"
                            value="{{ old('whatsapp', $agencia->whatsapp) }}">
                    </div>
                     <div class="mb-3">
                        <label class="form-label fw-bold">Imagen de la Agencia</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Galería de Imágenes (Múltiples)</label>
                        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                        <small class="text-muted">Puedes seleccionar múltiples imágenes para la galería</small>
                    </div>

                    <hr>
                    <label class="form-label fw-bold">Tours que ofrece esta agencia</label>
                    <div id="tours-checklist" class="row row-cols-2 row-cols-md-3 g-2 mb-3">
                        @foreach(\App\Models\Tour::all() as $tour)
                            <div class="col">
                                <div class="form-check border rounded p-2">
                                    <input class="form-check-input tour-checkbox" type="checkbox" name="tours[]" value="{{ $tour->id }}"
                                        id="tour{{ $tour->id }}" data-tour-nombre="{{ $tour->nombre }}"
                                        {{ $agencia->tours->pluck('id')->contains($tour->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tour{{ $tour->id }}">{{ $tour->nombre }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div id="paquetes-container" class="mt-3"></div>

                    <div class="d-flex justify-content-between mt-3">
                        <a href="{{ route('agencias.index') }}" class="btn btn-secondary">← Volver</a>
                        <button type="submit" class="btn btn-warning">Actualizar Agencia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
const TOURS_DATA = {!! \App\Models\Tour::all()->keyBy('id')->toJson() !!};
const PAQUETES_EXISTENTES = {!! $agencia->paquetes->keyBy('tour_id')->toJson() !!};
document.addEventListener('DOMContentLoaded', function () {
    const contenedor = document.getElementById('paquetes-container');
    function renderPaquetes() {
        contenedor.innerHTML = '';
        const seleccionados = Array.from(document.querySelectorAll('.tour-checkbox:checked')).map(cb => cb.value);
        seleccionados.forEach(function (tourId) {
            const tour = TOURS_DATA[tourId];
            if (!tour) return;
            const ex = PAQUETES_EXISTENTES[tourId] || {};
            const precio = ex.precio || tour.precio || '';
            const duracion = ex.duracion_dias || tour.duracion_dias || '';
            const cupo = ex.cupo_maximo || tour.cupo_maximo || '';
            const descripcion = ex.descripcion || '';
            const bloque = document.createElement('div');
            bloque.className = 'card mb-3 p-3';
            bloque.innerHTML = `
                <h6 class="mb-3">${tour.nombre}</h6>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label small">Precio por persona ($)</label>
                        <input type="number" step="0.01" min="0" class="form-control form-control-sm" name="paquetes[${tourId}][precio]" value="${precio}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Duración (días)</label>
                        <input type="number" min="1" class="form-control form-control-sm" name="paquetes[${tourId}][duracion_dias]" value="${duracion}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small">Cupo máximo</label>
                        <input type="number" min="1" class="form-control form-control-sm" name="paquetes[${tourId}][cupo_maximo]" value="${cupo}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small">Descripción de este servicio (opcional)</label>
                        <textarea class="form-control form-control-sm" rows="2" name="paquetes[${tourId}][descripcion]">${descripcion}</textarea>
                    </div>
                </div>`;
            contenedor.appendChild(bloque);
        });
    }
    document.querySelectorAll('.tour-checkbox').forEach(function(cb) {
        cb.addEventListener('change', renderPaquetes);
    });
    renderPaquetes();
});
</script>
@endsection
