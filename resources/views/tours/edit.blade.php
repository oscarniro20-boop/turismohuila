@extends('layouts.app')

@section('title', 'Editar Tour - TurismoHuila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning">
                <h4 class="mb-0">✏️ Editar Tour</h4>
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

                <form action="{{ route('tours.update', $tour) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Tour</label>
                        <input type="text" name="nombre" class="form-control"
                            value="{{ old('nombre', $tour->nombre) }}">
                        @error('nombre')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Agencias que ofrecen este tour</label>
                        <div id="agencias-checklist" class="mb-2">
@foreach($agencias as $agencia)
                                <div class="form-check">
                                    <input class="form-check-input agencia-checkbox" type="checkbox" name="agencias[]"
                                        value="{{ $agencia->id }}" id="agencia{{ $agencia->id }}"
                                        data-agencia-nombre="{{ $agencia->nombre }}"
                                        {{ $tour->paquetes->pluck('agencia_id')->contains($agencia->id) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="agencia{{ $agencia->id }}">{{ $agencia->nombre }}</label>
                                </div>
                            @endforeach
                        </div>
                        <div id="paquetes-container-tour"></div>
                        <div class="form-text"><a href="{{ route('agencias.create') }}" target="_blank">+ Crear nueva agencia</a></div>
                    </div>

                    <script>
                    function agregarPaqueteAgencia(agenciaId, nombre, valores = {}) {
                        if (document.getElementById('paquete-agencia-' + agenciaId)) return;
                        const div = document.createElement('div');
                        div.className = 'card mb-3';
                        div.id = 'paquete-agencia-' + agenciaId;
                        div.innerHTML = `
                            <div class="card-header bg-light"><strong>Paquete con: ${nombre}</strong></div>
                            <div class="card-body row g-2">
                                <div class="col-md-3">
                                    <label class="form-label small">Precio</label>
                                    <input type="number" name="paquetes[${agenciaId}][precio]" class="form-control form-control-sm" min="0" step="0.01" value="${valores.precio || ''}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Duración (días)</label>
                                    <input type="number" name="paquetes[${agenciaId}][duracion_dias]" class="form-control form-control-sm" min="1" value="${valores.duracion_dias || ''}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Cupo máximo</label>
                                    <input type="number" name="paquetes[${agenciaId}][cupo_maximo]" class="form-control form-control-sm" min="1" value="${valores.cupo_maximo || ''}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">Descripción</label>
                                    <input type="text" name="paquetes[${agenciaId}][descripcion]" class="form-control form-control-sm" value="${valores.descripcion || ''}">
                                </div>
                            </div>`;
                        document.getElementById('paquetes-container-tour').appendChild(div);
                    }

                    document.querySelectorAll('.agencia-checkbox').forEach(function(cb) {
                        cb.addEventListener('change', function() {
                            if (this.checked) {
                                agregarPaqueteAgencia(this.value, this.dataset.agenciaNombre);
                            } else {
                                document.getElementById('paquete-agencia-' + this.value)?.remove();
                            }
                        });
                    });

                    // Precargar paquetes ya existentes de este tour
                    @foreach($tour->paquetes as $paquete)
                        agregarPaqueteAgencia(
                            {{ $paquete->agencia_id }},
                            @json(optional($paquete->agencia)->nombre ?? 'Agencia #' . $paquete->agencia_id),
                            {
                                precio: {{ $paquete->precio || 'null' }},
                                duracion_dias: {{ $paquete->duracion_dias || 'null' }},
                                cupo_maximo: {{ $paquete->cupo_maximo || 'null' }},
                                descripcion: @json($paquete->descripcion)
                            }
                        );
                    @endforeach
                    </script>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4">{{ old('descripcion', $tour->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Municipio</label>
                            <select name="municipio" class="form-select">
                                <option value="">Seleccione municipio...</option>
                                @error('municipio')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @foreach(config('municipios.huila') as $m)
                                    <option value="{{ $m }}" {{ old('municipio', $tour->municipio) == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Duración (días)</label>
                            <input type="number" name="duracion_dias" class="form-control"
                                min="1" value="{{ old('duracion_dias', $tour->duracion_dias) }}">
                            @error('duracion_dias')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitud (opcional)</label>
                            <input type="number" step="any" name="latitud" class="form-control"
                                placeholder="Ej: 2.5359" value="{{ old('latitud', $tour->latitud ?? '') }}">
                            @error('latitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitud (opcional)</label>
                            <input type="number" step="any" name="longitud" class="form-control"
                                placeholder="Ej: -75.5277" value="{{ old('longitud', $tour->longitud ?? '') }}">
                            @error('longitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Puedes obtenerlas en Google Maps: clic derecho sobre el punto → copiar coordenadas.</small>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Precio por persona ($)</label>
                            <input type="number" name="precio" class="form-control"
                                min="0" value="{{ old('precio', $tour->precio) }}">
                            @error('precio')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cupo máximo</label>
                            <input type="number" name="cupo_maximo" class="form-control"
                                min="1" value="{{ old('cupo_maximo', $tour->cupo_maximo) }}">
                            @error('cupo_maximo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">¿Qué incluye?</label>
                        <textarea name="incluye" class="form-control" rows="3">{{ old('incluye', $tour->incluye) }}</textarea>
                        @error('incluye')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Teléfono de Contacto</label>
                        <input type="text" name="telefono_contacto" class="form-control"
                            placeholder="Ej: +573001234567"
                            value="{{ old('telefono_contacto', $tour->telefono_contacto) }}">
                        @error('telefono_contacto')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Imagen actual</label>
                        @if($tour->imagen)
                            <div><img src="{{ asset($tour->imagen) }}" alt="Imagen" style="max-height: 150px;"></div>
                        @endif
                        <input type="file" name="imagen" class="form-control mt-1" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Galería de Imágenes (varias)</label>
                        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tours.index') }}" class="btn btn-secondary">← Volver</a>
                        <button type="submit" class="btn btn-warning">Actualizar Tour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection