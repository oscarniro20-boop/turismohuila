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
                            @foreach(\App\Models\Agencia::all() as $agencia)
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
                                <option value="Acevedo" {{ old('municipio', $tour->municipio) == 'Acevedo' ? 'selected' : '' }}>Acevedo</option>
                                <option value="Aipe" {{ old('municipio', $tour->municipio) == 'Aipe' ? 'selected' : '' }}>Aipe</option>
                                <option value="Algeciras" {{ old('municipio', $tour->municipio) == 'Algeciras' ? 'selected' : '' }}>Algeciras</option>
                                <option value="Altamira" {{ old('municipio', $tour->municipio) == 'Altamira' ? 'selected' : '' }}>Altamira</option>
                                <option value="Baraya" {{ old('municipio', $tour->municipio) == 'Baraya' ? 'selected' : '' }}>Baraya</option>
                                <option value="Campoalegre" {{ old('municipio', $tour->municipio) == 'Campoalegre' ? 'selected' : '' }}>Campoalegre</option>
                                <option value="Colombia" {{ old('municipio', $tour->municipio) == 'Colombia' ? 'selected' : '' }}>Colombia</option>
                                <option value="Elías" {{ old('municipio', $tour->municipio) == 'Elías' ? 'selected' : '' }}>Elías</option>
                                <option value="Garzón" {{ old('municipio', $tour->municipio) == 'Garzón' ? 'selected' : '' }}>Garzón</option>
                                <option value="Gigante" {{ old('municipio', $tour->municipio) == 'Gigante' ? 'selected' : '' }}>Gigante</option>
                                <option value="Guadalupe" {{ old('municipio', $tour->municipio) == 'Guadalupe' ? 'selected' : '' }}>Guadalupe</option>
                                <option value="Hobo" {{ old('municipio', $tour->municipio) == 'Hobo' ? 'selected' : '' }}>Hobo</option>
                                <option value="Iquira" {{ old('municipio', $tour->municipio) == 'Iquira' ? 'selected' : '' }}>Iquira</option>
                                <option value="Íquira" {{ old('municipio', $tour->municipio) == 'Íquira' ? 'selected' : '' }}>Íquira</option>
                                <option value="Isnos" {{ old('municipio', $tour->municipio) == 'Isnos' ? 'selected' : '' }}>Isnos</option>
                                <option value="La Argentina" {{ old('municipio', $tour->municipio) == 'La Argentina' ? 'selected' : '' }}>La Argentina</option>
                                <option value="La Plata" {{ old('municipio', $tour->municipio) == 'La Plata' ? 'selected' : '' }}>La Plata</option>
                                <option value="Nátaga" {{ old('municipio', $tour->municipio) == 'Nátaga' ? 'selected' : '' }}>Nátaga</option>
                                <option value="Neiva" {{ old('municipio', $tour->municipio) == 'Neiva' ? 'selected' : '' }}>Neiva</option>
                                <option value="Oporapa" {{ old('municipio', $tour->municipio) == 'Oporapa' ? 'selected' : '' }}>Oporapa</option>
                                <option value="Paicol" {{ old('municipio', $tour->municipio) == 'Paicol' ? 'selected' : '' }}>Paicol</option>
                                <option value="Palermo" {{ old('municipio', $tour->municipio) == 'Palermo' ? 'selected' : '' }}>Palermo</option>
                                <option value="Palestina" {{ old('municipio', $tour->municipio) == 'Palestina' ? 'selected' : '' }}>Palestina</option>
                                <option value="Pitalito" {{ old('municipio', $tour->municipio) == 'Pitalito' ? 'selected' : '' }}>Pitalito</option>
                                <option value="Rivera" {{ old('municipio', $tour->municipio) == 'Rivera' ? 'selected' : '' }}>Rivera</option>
                                <option value="Saladoblanco" {{ old('municipio', $tour->municipio) == 'Saladoblanco' ? 'selected' : '' }}>Saladoblanco</option>
                                <option value="San Agustín" {{ old('municipio', $tour->municipio) == 'San Agustín' ? 'selected' : '' }}>San Agustín</option>
                                <option value="Santa María" {{ old('municipio', $tour->municipio) == 'Santa María' ? 'selected' : '' }}>Santa María</option>
                                <option value="Suaza" {{ old('municipio', $tour->municipio) == 'Suaza' ? 'selected' : '' }}>Suaza</option>
                                <option value="Tarqui" {{ old('municipio', $tour->municipio) == 'Tarqui' ? 'selected' : '' }}>Tarqui</option>
                                <option value="Tesalia" {{ old('municipio', $tour->municipio) == 'Tesalia' ? 'selected' : '' }}>Tesalia</option>
                                <option value="Tello" {{ old('municipio', $tour->municipio) == 'Tello' ? 'selected' : '' }}>Tello</option>
                                <option value="Teruel" {{ old('municipio', $tour->municipio) == 'Teruel' ? 'selected' : '' }}>Teruel</option>
                                <option value="Timana" {{ old('municipio', $tour->municipio) == 'Timana' ? 'selected' : '' }}>Timana</option>
                                <option value="Villavieja" {{ old('municipio', $tour->municipio) == 'Villavieja' ? 'selected' : '' }}>Villavieja</option>
                                <option value="Yaguará" {{ old('municipio', $tour->municipio) == 'Yaguará' ? 'selected' : '' }}>Yaguará</option>
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