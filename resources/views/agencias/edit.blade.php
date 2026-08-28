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
                        <img src="{{ $agencia->url_imagen }}" style="max-height:120px;" class="rounded d-block mb-1">
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitud (opcional)</label>
                            <input type="number" step="any" name="latitud" class="form-control"
                                placeholder="Ej: 2.5359" value="{{ old('latitud', $agencia->latitud ?? '') }}">
                            @error('latitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitud (opcional)</label>
                            <input type="number" step="any" name="longitud" class="form-control"
                                placeholder="Ej: -75.5277" value="{{ old('longitud', $agencia->longitud ?? '') }}">
                            @error('longitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Puedes obtenerlas en Google Maps: clic derecho sobre el punto → copiar coordenadas.</small>
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
                        @foreach($tours as $tour)
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
const TOURS_DATA = {!! Js::from($tours->keyBy('id')) !!};
const PAQUETES_EXISTENTES = {!! Js::from($agencia->paquetes->keyBy('tour_id')) !!};
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

            const titulo = document.createElement('h6');
            titulo.className = 'mb-3';
            titulo.textContent = tour.nombre; // textContent: seguro ante XSS
            bloque.appendChild(titulo);

            const row = document.createElement('div');
            row.className = 'row g-2';

            const campo = (cls) => {
                const col = document.createElement('div');
                col.className = cls;
                const label = document.createElement('label');
                label.className = 'form-label small';
                col.appendChild(label);
                return { col, label };
            };

            const p = campo('col-md-4');
            p.label.textContent = 'Precio por persona ($)';
            const i1 = document.createElement('input');
            i1.type = 'number'; i1.step = '0.01'; i1.min = '0';
            i1.className = 'form-control form-control-sm';
            i1.name = `paquetes[${tourId}][precio]`;
            i1.value = precio;
            p.col.appendChild(i1); row.appendChild(p.col);

            const d = campo('col-md-4');
            d.label.textContent = 'Duración (días)';
            const i2 = document.createElement('input');
            i2.type = 'number'; i2.min = '1';
            i2.className = 'form-control form-control-sm';
            i2.name = `paquetes[${tourId}][duracion_dias]`;
            i2.value = duracion;
            d.col.appendChild(i2); row.appendChild(d.col);

            const c = campo('col-md-4');
            c.label.textContent = 'Cupo máximo';
            const i3 = document.createElement('input');
            i3.type = 'number'; i3.min = '1';
            i3.className = 'form-control form-control-sm';
            i3.name = `paquetes[${tourId}][cupo_maximo]`;
            i3.value = cupo;
            c.col.appendChild(i3); row.appendChild(c.col);

            const desc = campo('col-12');
            desc.label.textContent = 'Descripción de este servicio (opcional)';
            const t4 = document.createElement('textarea');
            t4.className = 'form-control form-control-sm'; t4.rows = 2;
            t4.name = `paquetes[${tourId}][descripcion]`;
            t4.textContent = descripcion;
            desc.col.appendChild(t4); row.appendChild(desc.col);

            bloque.appendChild(row);
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
