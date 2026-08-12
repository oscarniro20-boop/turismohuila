@extends('layouts.app')

@section('title', 'Crear Tour - TurismoHuila')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h4 class="mb-0">🚌 Nuevo Tour</h4>
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

                <form action="{{ route('tours.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nombre del Tour</label>
                        <input type="text" name="nombre" class="form-control"
                            placeholder="Ej: Tour Desierto de la Tatacoa" value="{{ old('nombre') }}">
                        @error('nombre')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Agencias que ofrecen este tour (y su precio)</label>
@foreach($agencias as $agencia)
                            <div class="row g-2 align-items-center mb-2">
                                <div class="col-auto">
                                    <input type="checkbox" name="agencias[]" value="{{ $agencia->id }}" id="ag{{ $agencia->id }}" {{ collect(old('agencias'))->contains($agencia->id) ? 'checked' : '' }}>
                                </div>
                                <div class="col-4"><label for="ag{{ $agencia->id }}">{{ $agencia->nombre }}</label></div>
                                <div class="col-4">
                                    <input type="number" name="precios[{{ $agencia->id }}]" class="form-control form-control-sm" placeholder="Precio" step="0.01" min="0" value="{{ old("precios.$agencia->id") }}">
                                </div>
                            </div>
                        @endforeach
                        <div class="form-text"><a href="{{ route('agencias.create') }}" target="_blank">+ Crear nueva agencia</a></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Descripción</label>
                        <textarea name="descripcion" class="form-control" rows="4"
                            placeholder="Describe el tour...">{{ old('descripcion') }}</textarea>
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
                                    <option value="{{ $m }}" {{ old('municipio') == $m ? 'selected' : '' }}>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Duración (días)</label>
                            <input type="number" name="duracion_dias" class="form-control"
                                min="1" value="{{ old('duracion_dias', 1) }}">
                            @error('duracion_dias')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Latitud (opcional)</label>
                            <input type="number" step="any" name="latitud" class="form-control"
                                placeholder="Ej: 2.5359" value="{{ old('latitud') }}">
                            @error('latitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Longitud (opcional)</label>
                            <input type="number" step="any" name="longitud" class="form-control"
                                placeholder="Ej: -75.5277" value="{{ old('longitud') }}">
                            @error('longitud')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <small class="text-muted d-block mb-3">Puedes obtenerlas en Google Maps: clic derecho sobre el punto → copiar coordenadas.</small>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Precio por persona ($)</label>
                            <input type="number" name="precio" class="form-control"
                                placeholder="0" min="0" value="{{ old('precio') }}">
                            @error('precio')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Cupo máximo</label>
                            <input type="number" name="cupo_maximo" class="form-control"
                                min="1" value="{{ old('cupo_maximo', 10) }}">
                            @error('cupo_maximo')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">¿Qué incluye?</label>
                        <textarea name="incluye" class="form-control" rows="3"
                            placeholder="Ej: Transporte, guía, almuerzo...">{{ old('incluye') }}</textarea>
                        @error('incluye')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Teléfono de Contacto</label>
                        <input type="text" name="telefono_contacto" class="form-control"
                            placeholder="Ej: +573001234567"
                            value="{{ old('telefono_contacto') }}">
                        @error('telefono_contacto')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Imagen</label>
                        <input type="file" name="imagen" class="form-control" accept="image/*">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Galería de Imágenes (varias)</label>
                        <input type="file" name="imagenes[]" class="form-control" accept="image/*" multiple>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tours.index') }}" class="btn btn-secondary">← Volver</a>
                        <button type="submit" class="btn btn-success">Guardar Tour</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection