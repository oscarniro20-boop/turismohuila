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

                        // Construcción DOM segura: nunca innerHTML con datos dinámicos.
                        function campo(claseCol, textoLabel, nameAttr, atributos, valor) {
                            const col = document.createElement('div');
                            col.className = claseCol;
                            const label = document.createElement('label');
                            label.className = 'form-label small';
                            label.textContent = textoLabel;
                            const input = document.createElement('input');
                            input.type = atributos.tipo;
                            input.name = nameAttr;
                            input.className = 'form-control form-control-sm';
                            input.value = valor || '';
                            if (atributos.min !== undefined) input.min = atributos.min;
                            if (atributos.step) input.step = atributos.step;
                            col.appendChild(label);
                            col.appendChild(input);
                            return col;
                        }

                        const div = document.createElement('div');
                        div.className = 'card mb-3';
                        div.id = 'paquete-agencia-' + agenciaId;

                        const header = document.createElement('div');
                        header.className = 'card-header bg-light';
                        const strong = document.createElement('strong');
                        strong.textContent = 'Paquete con: ' + nombre;
                        header.appendChild(strong);

                        const body = document.createElement('div');
                        body.className = 'card-body row g-2';
                        body.appendChild(campo('col-md-3', 'Precio', `paquetes[${agenciaId}][precio]`, { tipo: 'number', min: '0', step: '0.01' }, valores.precio));
                        body.appendChild(campo('col-md-3', 'Duración (días)', `paquetes[${agenciaId}][duracion_dias]`, { tipo: 'number', min: '1' }, valores.duracion_dias));
                        body.appendChild(campo('col-md-3', 'Cupo máximo', `paquetes[${agenciaId}][cupo_maximo]`, { tipo: 'number', min: '1' }, valores.cupo_maximo));
                        body.appendChild(campo('col-md-3', 'Descripción', `paquetes[${agenciaId}][descripcion]`, { tipo: 'text' }, valores.descripcion));

                        div.appendChild(header);
                        div.appendChild(body);
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
                            <div><img src="{{ $tour->url_imagen }}" alt="Imagen" style="max-height: 150px;"></div>
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